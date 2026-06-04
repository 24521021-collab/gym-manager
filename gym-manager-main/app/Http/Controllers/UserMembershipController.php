<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Membership;
use App\Http\Controllers\CartController; // Import CartController
use App\Models\GymPackage;
use Illuminate\Support\Facades\Auth;
class UserMembershipController extends Controller
{
    // lưu thông tin gói tập người dùng đăng 
    public function register(Request $request){
        // Thay vì tự thực hiện các bước kiểm tra (Hội viên active, Giỏ hàng),
        // ta ủy quyền hoàn toàn cho CartController xử lý.
        $packageId = $request->package_id;
        // Đảm bảo request có tham số 'type' mà CartController yêu cầu
        $request->merge(['type' => 'package']);
        // Gọi method add của CartController
        $response = app(CartController::class)->add($request, $packageId);
        // Nếu CartController trả về lỗi (ví dụ đã có gói active), trả về lỗi đó cho Frontend
        if ($response->getStatusCode() !== 200) {
            return $response;
        }
        // Nếu thành công, bổ sung thêm redirect_url để Frontend chuyển hướng sang trang giỏ hàng
        return response()->json([
            'success' => true,
            'redirect_url' => route('cart.index')
        ]);
    }
}
