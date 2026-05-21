<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Membership;
use App\Http\Controllers\CartController; // Import CartController
use App\Models\GymPackage;
use Illuminate\Support\Facades\Auth;
class UserMembershipController extends Controller
{
    public function MyMembership(Request $request){
        $userId = Auth::id();
        $status = $request->query('status');

        // Tự động cập nhật các gói đã hết hạn cho người dùng này trước khi hiển thị
        Membership::where('user_id', $userId)
            ->where('status', 'Active')
            ->whereDate('end_date', '<', now())
            ->update(['status' => 'Expired']);

        // Khởi tạo query lấy danh sách gói tập
        $query = Membership::with('package')
                        ->where('user_id', $userId);

        // Thực hiện lọc theo trạng thái nếu có yêu cầu (Active hoặc Expired)
        if (in_array($status, ['Active', 'Expired'])) {
            $query->where('status', $status);
        }

        $memberships = $query->orderBy('created_at', 'desc')->get();

        return view('my_membership', compact('memberships'));
    }
    // lưu thông tin gói tập người dùng đăng 
    public function register(Request $request){
      // 1. Lấy thông tin gói tập từ DB
    $id = $request->package_id;
    $package = \App\Models\GymPackage::findOrFail($id);
    $rowId = 'package_' . $id;
    $cart = session()->get('cart', []);
    
    // 2. Đưa vào giỏ hàng thay vì session đơn lẻ
    $cart[$rowId] = [
        "row_id"   => $rowId,
        "item_id"   => $package->id,
        "item_type" => "package",
        "name"     => $package->package_name,
        "price"    => $package->price,
        "quantity" => 1,
        "image"    => null,
    ];
    session()->put('cart', $cart);

    return response()->json([
        'success' => true,
        'redirect_url' => route('cart.index')
    ]);
    }
}
