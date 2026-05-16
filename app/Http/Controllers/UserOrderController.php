<?php
namespace App\Http\Controllers;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserOrderController extends Controller
{
    // Hiển thị trang danh sách
    public function index(Request $request)
    {
        $status = $request->query('status');
        $query = Order::where('user_id', Auth::id());

        // Lọc theo trạng thái nếu có
        if (in_array($status, ['Pending', 'Paid', 'Cancelled'])) {
            $query->where('payment_status', $status);
        }

        $orders = $query->orderBy('order_date', 'desc')->paginate(10);
        return view('UserOrders', compact('orders'));
    }

    // Trả về JSON để hiển thị trong Modal (Giống Admin)
    public function show($id)
    {
        $order = Order::with(['items.product'])
                      ->where('user_id', Auth::id()) // Bảo mật: Chỉ lấy đơn của mình
                      ->findOrFail($id);
                      
        return response()->json($order);
    }

    // Khách hàng tự hủy đơn
    public function cancel($id)
    {
        $order = Order::where('user_id', Auth::id())
                      ->where('payment_status', 'Pending') // Chỉ cho phép hủy khi đang chờ xử lý
                      ->findOrFail($id);
        
        $order->payment_status = 'Cancelled';
        $order->save();

        return redirect()->back()->with('success', 'Đã hủy đơn hàng thành công!');
    }
}