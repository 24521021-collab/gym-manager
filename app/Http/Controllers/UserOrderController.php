<?php
namespace App\Http\Controllers;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class UserOrderController extends Controller
{
    // Hiển thị trang danh sách
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->orderBy('order_date', 'desc')->paginate(10);
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
}