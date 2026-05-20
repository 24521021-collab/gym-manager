<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Order;
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

        // Lọc theo trạng thái thanh toán
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        $orders = $query->paginate(10);

        if ($request->ajax()) {
            return response()->json($orders);
        }
        return view('admin.orders');
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
        // Validate: chỉ cho phép 3 trạng thái này
        $request->validate(
            ['payment_status' => 'required|in:Pending,Paid,Cancelled'],
            ['payment_status.in' => 'Trạng thái đơn hàng không hợp lệ!']
            );
        $order = Order::findOrFail($id);
        $order->payment_status = $request->payment_status;
        $order->save();
        return response()->json(['success' => true, 'message' => 'Cập nhật trạng thái thành công!', 'data' => $order]);
    }
    // show chi tiết các sản phẩm trong đơn 
    public function show($id){
    // Lấy đơn hàng kèm theo các món hàng và thông tin sản phẩm của món đó
    $order = Order::with(['user', 'items.product'])->findOrFail($id);
    // Trả về JSON để JavaScript ở giao diện tự vẽ ra bảng sản phẩm
    return response()->json($order);
    }
}