<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\GymClass;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\GymPackage;
use App\Models\Membership;
use Carbon\Carbon;
use App\Models\Notification;

class CheckoutController extends Controller{
    public function index(){
        // Giỏ hàng phải có ít nhất 1 item để tiến hành thanh toán
        if(!session('cart') || count(session('cart')) == 0) {
            return redirect()->route('products.index')->with('error', 'Giỏ hàng trống! Vui lòng thêm sản phẩm vào giỏ.');
        }
        return view('checkout');
    }
    public function processCheckout(Request $request) {
        $total = $this->calculateTotal();
        if ($request->payment_method == 'COD') {
            $order = $this->createOrder($total, 'Pending', 'COD');
            session()->forget('cart'); // Xóa toàn bộ giỏ hàng sau khi tạo đơn
            return redirect()->route('products.index')->with('success', 'Đặt hàng thành công! Vui lòng chờ shipper.');
        } 
        if ($request->payment_method == 'Bank_QR') {
            $order = $this->createOrder($total, 'Pending', 'Bank_QR');
            session()->forget('cart'); // Xóa toàn bộ giỏ hàng sau khi tạo đơn
            return redirect()->route('checkout.bank_qr', ['order_id' => $order->id]);
        }

        return redirect()->back()->with('error', 'Phương thức thanh toán không hợp lệ.');
    }
    public function showBankQR($order_id) {
        $order = Order::with('user')->findOrFail($order_id); // Lấy thông tin đơn hàng để hiển thị QR
        return view('bank_qr_payment', compact('order')); // Trả về view hiển thị QR
    }

    private function createOrder($total, $status, $method) {
        return DB::transaction(function () use ($total, $status, $method) {
            $type = session('checkout_type', 'cart');
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => $total,
                'payment_status' => $status,
                'payment_method' => $method,
                'order_date' => now()
            ]);

            foreach(session('cart', []) as $rowId => $details) {
                $hasProducts = false; // Khởi tạo cờ
                // Lưu vết vào bảng order_items (cho tất cả các loại mặt hàng)
                // Mục đích: để có thể xem lại đơn hàng đã mua những gì, và tính tổng tiền
                // product_id sẽ là null nếu không phải sản phẩm vật lý
                // item_type và item_id sẽ giúp truy ngược lại thông tin gốc
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => ($details['item_type'] === 'product') ? $details['item_id'] : null,
                    'item_type'  => $details['item_type'], // Loại mặt hàng (product, package, class)
                    'item_id'    => $details['item_id'],   // ID gốc của mặt hàng
                    'name'       => $details['name'], 
                    'price'      => $details['price'],
                    'quantity'   => $details['quantity'],
                    'subtotal'   => $details['price'] * $details['quantity']
                ]);
                // Xử lý logic riêng cho từng loại mặt hàng sau khi thanh toán
                if ($details['item_type'] === 'product') {
                    // Trừ kho sản phẩm
                    $product = Product::findOrFail($details['item_id']);
                    if ($product->stock_quantity < $details['quantity']) {
                        throw new \Exception("Sản phẩm {$product->name} không đủ hàng trong kho.");
                    }
                    $product->decrement('stock_quantity', $details['quantity']);
                    $hasProducts = true; // Đặt cờ nếu có sản phẩm
                } elseif ($details['item_type'] === 'package') {
                    $package = GymPackage::findOrFail($details['item_id']);
                    // Nếu thanh toán xong (Paid) thì kích hoạt Active, ngược lại Inactive
                    $membershipStatus = ($status === 'Paid') ? 'Active' : 'Inactive';

                    // Tạo membership
                    // This part should be inside a try-catch as well if it can fail
                    Membership::create([
                        'user_id'    => Auth::id(),
                        'package_id' => $package->id,
                        'start_date' => now(),
                        'end_date'   => now()->addDays($package->duration_days),
                        'status'     => $membershipStatus
                    ]);

                    try {
                        if ($status === 'Paid') {
                            Notification::create([
                                'user_id' => Auth::id(),
                                'type'    => 'membership',
                                'title'   => 'Kích hoạt gói tập',
                                'content' => "Cảm ơn bạn! Đơn hàng mua [{$package->package_name}] đã thanh toán. Gói tập đã được kích hoạt thành công!"
                            ]);
                        } else {
                            Notification::create([
                                'user_id' => Auth::id(),
                                'type'    => 'membership',
                                'title'   => 'Gói tập chờ thanh toán',
                                'content' => "Đơn hàng mua [{$package->package_name}] của bạn đang chờ xác nhận thanh toán để kích hoạt."
                            ]);
                        }
                    } catch (\Exception $e) {
                        \Log::error("Lỗi tạo thông báo cho gói tập trong đơn hàng #ORD-{$order->id}: " . $e->getMessage());
                    }

                    // Nâng cấp Role cho User nếu thanh toán thành công và đang là guest
                    if ($status === 'Paid') {
                        $user = Auth::user();
                        if ($user->role === 'guest') {
                            $user->role = 'member';
                            /** @var \App\Models\User $user **/
                            $user->save();
                        }
                    }
                } elseif ($details['item_type'] === 'class') {
                    // Tạo bản ghi booking cho lớp học
                    Booking::create([
                        'user_id'      => Auth::id(),
                        'class_id'     => $details['item_id'],
                        'booking_date' => now(),
                        'status'       => ($status === 'Paid') ? 'confirmed' : 'pending', // Trạng thái booking
                    ]);

                    try {
                        if ($status === 'Paid') {
                            Notification::create([
                                'user_id' => Auth::id(),
                                'type'    => 'class',
                                'title'   => 'Tham gia lớp học',
                                'content' => "Cảm ơn bạn! Đơn đăng ký lớp [{$details['name']}] đã hoàn tất. Chúc bạn có những giờ tập luyện hiệu quả!"
                            ]);
                        } else {
                            Notification::create([
                                'user_id' => Auth::id(),
                                'type'    => 'class',
                                'title'   => 'Chờ xác nhận lớp học',
                                'content' => "Đơn đăng ký lớp học [{$details['name']}] của bạn đang chờ xác nhận thanh toán."
                            ]);
                        }
                    } catch (\Exception $e) {
                        \Log::error("Lỗi tạo thông báo cho lớp học trong đơn hàng #ORD-{$order->id}: " . $e->getMessage());
                    }
                }
            }

            // Tạo một thông báo duy nhất cho các sản phẩm trong đơn hàng (nếu có)
            if ($hasProducts) {
                try {
                    if ($status === 'Paid') {
                        Notification::create([
                            'user_id' => Auth::id(),
                            'type'    => 'order',
                            'title'   => 'Thanh toán đơn hàng',
                            'content' => "Cảm ơn bạn! Đơn hàng #ORD-{$order->id} của bạn đã hoàn tất thanh toán. Admin đang tiến hành chuẩn bị đơn hàng cho bạn."
                        ]);
                    } else {
                        Notification::create([
                            'user_id' => Auth::id(),
                            'type'    => 'order',
                            'title'   => 'Đang chờ xác nhận',
                            'content' => "Đơn hàng #ORD-{$order->id} của bạn đang chờ xác nhận thanh toán từ hệ thống."
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error("Lỗi tạo thông báo cho đơn hàng sản phẩm #ORD-{$order->id}: " . $e->getMessage());
                }
            }
            return $order;
        });
    }
    /**
     * Tính tổng tiền của tất cả các item trong giỏ hàng session.
     */
    private function calculateTotal() {
        $total = 0;
        foreach(session('cart', []) as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    // Hàm này không còn cần thiết vì chúng ta luôn xử lý toàn bộ giỏ hàng
    // private function clearCheckoutSession() {
    //     if (session('checkout_type') === 'cart') {
    //         session()->forget('cart');
    //     }
    //     session()->forget(['checkout_type', 'checkout_data']);
    // }
}