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
        
        if ($request->payment_method == 'VNPAY') {
            // Cấu hình VNPAY (Thông số test)
            $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
            $vnp_Returnurl = route('vnpay.return');
            $vnp_TmnCode = env('VNP_TMN_CODE', 'BA7TF3EI'); // Nên dùng env
            $vnp_HashSecret = env('VNP_HASH_SECRET', 'D1EDEE8F3UDW1SO2IHO8MN2R3Q54T47E'); 
            $vnp_TxnRef = time(); // Mã đơn hàng
            $vnp_OrderInfo = "Thanh toan don hang GymPro";
            $vnp_OrderType = "billpayment";
            $vnp_Amount = $total * 100;
            $vnp_Locale = "vn";
            $vnp_BankCode = "";
            $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
            $inputData = array(
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => $vnp_Amount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => $vnp_TxnRef
            );
            ksort($inputData);
            $query = "";
            $i = 0;
            $hashdata = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) { $hashdata .= '&' . urlencode($key) . "=" . urlencode($value); } 
                else { $hashdata .= urlencode($key) . "=" . urlencode($value); $i = 1; }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            $vnp_Url = $vnp_Url . "?" . $query;
            if (isset($vnp_HashSecret)) {
                $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
            }
            return redirect()->away($vnp_Url);
        }
    }

    public function vnpayReturn(Request $request) {
        if($request->vnp_ResponseCode == '00') {
            $total = $this->calculateTotal();
            
            $this->createOrder($total, 'Paid', 'VNPAY'); // Tạo đơn hàng với trạng thái Paid
            session()->forget('cart'); // Xóa toàn bộ giỏ hàng sau khi thanh toán thành công
            return redirect()->route('products.index')->with('success', 'Thanh toán qua VNPAY thành công!');
        }
        return redirect()->route('checkout')->with('error', 'Thanh toán thất bại hoặc đã bị hủy.');
    }

    public function showBankQR($order_id) {
        $order = Order::with('user')->findOrFail($order_id); // Lấy thông tin đơn hàng để hiển thị QR
        return view('bank_qr_payment', compact('order')); // Trả về view hiển thị QR
    }

    private function createOrder($total, $status, $method) {
        return DB::transaction(function () use ($total, $status, $method) {
            $type = session('checkout_type', 'cart');
            $data = session('checkout_data');
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => $total,
                'payment_status' => $status,
                'payment_method' => $method,
                'order_date' => now()
            ]);
            foreach(session('cart', []) as $rowId => $details) {
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
                } elseif ($details['item_type'] === 'package') {
                    $package = GymPackage::findOrFail($details['item_id']);
                    // Nếu thanh toán xong (Paid) thì kích hoạt Active, ngược lại Inactive
                    $membershipStatus = ($status === 'Paid') ? 'Active' : 'Inactive';
                    Membership::create([
                        'user_id'    => Auth::id(),
                        'package_id' => $package->id,
                        'start_date' => now(),
                        'end_date'   => now()->addDays($package->duration_days),
                        'status'     => $membershipStatus
                    ]);
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