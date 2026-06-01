<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Membership;
use App\Models\Booking;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOrderController extends Controller
{
    // Hiển thị danh sách đơn hàng và Tìm kiếm
    public function index(Request $request){
        // Lấy đơn hàng cùng thông tin người mua (user)
        // Eager load 'items.product' để dữ liệu đầy đủ trong cachedData tại Frontend
        $query = Order::with(['user', 'items.product'])->orderBy('order_date', 'desc');

        // Tìm kiếm theo Mã đơn hoặc Tên khách hàng
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('full_name', 'like', "%{$search}%");
                  });
            });
        }

        // Lọc theo loại mặt hàng (Sản phẩm, Gói tập, Lớp học)
        if ($request->filled('type')) {
            $type = $request->type;
            $query->whereHas('items', function($q) use ($type) {
                $q->where('item_type', $type);
            });
        }

        // Lọc theo trạng thái thanh toán
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        $orders = $query->paginate(10);

        if ($request->ajax()) {
            return response()->json($orders)->header('Vary', 'X-Requested-With');
        }
        return view('admin.transaction');
    }
        // 1. Validate và Lọc theo trạng thái thanh toán
     public function getProductsData(Request $request){
    $query = DB::table('products');
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where('name', 'LIKE', "%{$search}%")
              ->orWhere('sku', 'LIKE', "%{$search}%");
    }
    $products = $query->orderBy('id', 'desc')->get();
    return response()->json($products);
    }
    // Cập nhật trạng thái đơn hàng (Hủy, Chưa thanh toán, Đã thanh toán)
    public function updateStatus(Request $request, $id){
        $request->validate(
            ['payment_status' => 'required|in:Pending,Paid,Cancelled'],
            ['payment_status.in' => 'Trạng thái đơn hàng không hợp lệ!']
        );

        return DB::transaction(function () use ($request, $id) {
            $order = Order::with(['items', 'user'])->findOrFail($id);
            $oldStatus = $order->payment_status;
            $newStatus = $request->payment_status;

            if ($oldStatus === $newStatus) {
                return response()->json(['success' => true, 'message' => 'Trạng thái không thay đổi.', 'data' => $order]);
            }

            $order->payment_status = $newStatus;
            $order->save();

            // Nếu Admin xác nhận đã thanh toán (Paid)
            if ($newStatus === 'Paid') {
                $hasProducts = false; // Khởi tạo cờ
                foreach ($order->items as $item) {
                    if ($item->item_type === 'package') {
                        // 1. Kích hoạt Membership đang ở trạng thái Inactive
                        Membership::where('user_id', $order->user_id)
                            ->where('package_id', $item->item_id)
                            ->where('status', 'Inactive')
                            ->update(['status' => 'Active']);
                        
                        try {
                            Notification::create([
                                'user_id' => $order->user_id,
                                'type'    => 'membership',
                                'title'   => 'Gói tập đã kích hoạt',
                                'content' => "Cảm ơn bạn! Gói tập [{$item->name}] đã được xác nhận thanh toán và kích hoạt thành công."
                            ]);
                        } catch (\Exception $e) {
                            \Log::error("Lỗi tạo thông báo cho gói tập trong AdminOrderController #ORD-{$order->id}: " . $e->getMessage());
                        }

                        // 2. Nâng cấp Role cho người dùng nếu đang là guest
                        $user = $order->user;
                        if ($user && $user->role === 'guest') {
                            $user->role = 'member';
                            $user->save();
                        }
                    } elseif ($item->item_type === 'class') {
                        // 3. Xác nhận Booking lớp học đang ở trạng thái pending
                        Booking::where('user_id', $order->user_id)
                            ->where('class_id', $item->item_id)
                            ->where('status', 'pending')
                            ->update(['status' => 'confirmed']);
                        
                        try {
                            Notification::create([
                                'user_id' => $order->user_id,
                                'type'    => 'class',
                                'title'   => 'Lớp học đã xác nhận',
                                'content' => "Cảm ơn bạn! Đơn đăng ký lớp [{$item->name}] đã được xác nhận thành công."
                            ]);
                        } catch (\Exception $e) {
                            \Log::error("Lỗi tạo thông báo cho lớp học trong AdminOrderController #ORD-{$order->id}: " . $e->getMessage());
                        }
                    } elseif ($item->item_type === 'product') {
                        $hasProducts = true; // Đặt cờ nếu có sản phẩm
                    }
                }
                // Tạo một thông báo duy nhất cho các sản phẩm trong đơn hàng (nếu có)
                if ($hasProducts) {
                    try {
                        Notification::create([
                            'user_id' => $order->user_id,
                            'type'    => 'order',
                            'title'   => 'Đơn hàng đã xác nhận',
                            'content' => "Cảm ơn bạn! Đơn hàng #ORD-{$order->id} đã được xác nhận thanh toán."
                        ]);
                    } catch (\Exception $e) {
                        \Log::error("Lỗi tạo thông báo cho đơn hàng sản phẩm trong AdminOrderController #ORD-{$order->id}: " . $e->getMessage());
                    }
                }
            } 
            // Nếu Admin Hủy đơn (Cancelled)
            elseif ($newStatus === 'Cancelled') {
                foreach ($order->items as $item) {
                    if ($item->item_type === 'package') {
                        Membership::where('user_id', $order->user_id)
                            ->where('package_id', $item->item_id)
                            ->where('status', 'Inactive')
                            ->update(['status' => 'Cancelled']);
                    } elseif ($item->item_type === 'class') {
                        Booking::where('user_id', $order->user_id)
                            ->where('class_id', $item->item_id)
                            ->where('status', 'pending')
                            ->update(['status' => 'cancelled']);
                    }
                }
            }

            return response()->json(['success' => true, 'message' => 'Cập nhật trạng thái thành công!', 'data' => $order]);
        });
    }
    // show chi tiết các sản phẩm trong đơn 
    public function show($id){
    // Lấy đơn hàng kèm theo các món hàng và thông tin sản phẩm của món đó
    $order = Order::with(['user', 'items.product'])->findOrFail($id);
    // Trả về JSON để JavaScript ở giao diện tự vẽ ra bảng sản phẩm
    return response()->json($order);
    }
}