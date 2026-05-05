<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Membership;
use App\Models\GymPackage;
use Illuminate\Support\Facades\Auth;
class UserMembershipController extends Controller
{
    public function MyMembership(){
    $userId = auth::id();
    // 2. Lấy danh sách các gói tập của người này, kèm theo thông tin chi tiết của gói đó
    // dùng 'with' để lấy luôn tên gói, giá tiền từ bảng gym_packages
    $memberships = Membership::with('package')
                    ->where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->get();

    // 3. Trả về view
    return view('my_membership', compact('memberships'));
    }
    // lưu thông tin gói tập người dùng đăng 
    public function register(Request $request){
      // 1. Lấy thông tin gói tập từ DB
    $package = \App\Models\GymPackage::findOrFail($request->package_id);
    // 2. Tạo bản ghi mới trong bảng memberships (Đây là lúc gói tập "bay" vào trang của khách)
    \App\Models\Membership::create([
        'user_id'    => auth::id(), // ID ông khách đang đăng nhập
        'package_id' => $package->id, // ID gói tập vừa bấm
        'start_date' => now(),        // Ngày bắt đầu là hôm nay
        'end_date'   => now()->addDays($package->duration_days), // Tự tính ngày hết hạn
        'status'     => 'Active'      // Trạng thái mặc định là đang hoạt động
    ]);

    // 3. Trả về phản hồi cho AJAX (Không trả về view)
    return response()->json([
        'success' => true,
        'message' => 'Đăng ký thành công!'
    ]);

    }
}
