<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Membership;
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
