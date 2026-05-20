<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CheckIn;
use App\Models\User;
use App\Models\Membership; // Đảm bảo đúng tên Model quản lý gói tập 
use Illuminate\Http\Request;
use Carbon\Carbon;

class CheckInController extends Controller
{
    // 1. Hiển thị trang quét cho Admin
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = CheckIn::with('user');

        if ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%");
            });
        }
        // Lấy 10 lượt check-in mới nhất, kèm thông tin User (Eager Loading) để hiện danh sách phía dưới
        $recentCheckins = $query->orderBy('check_in_time', 'desc')->paginate(10);
        return view('admin.checkin', compact('recentCheckins'));
    }

    // 2. Xử lý logic khi quét mã QR
    public function store(Request $request)
    {
        // Lấy user_id từ dữ liệu quét được (JavaScript gửi lên qua AJAX)
        $request->validate([
            'user_id' => 'required|integer|exists:user,id', // Validate user_id
        ]);

        $userId = $request->user_id;

        // BƯỚC A: Kiểm tra xem User có tồn tại không
        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'success' => false, 
                'message' => 'Không tìm thấy hội viên này!'
            ], 404);
        }

        // BƯỚC B: Kiểm tra gói tập (Membership) còn hạn không
        // Logic: Trạng thái là 'Đang tập' VÀ ngày hết hạn phải >= ngày hôm nay
        $activeMembership = Membership::where('user_id', $userId)
            ->where('status', 'Active')
            ->where('end_date', '>=', Carbon::today())
            ->first();

        if (!$activeMembership) {
            return response()->json([
                'success' => false,
                'message' => 'Cảnh báo: Hội viên chưa mua gói hoặc gói tập đã hết hạn!'
            ], 403);
        }

        // BƯỚC C: Ghi nhận vào bảng check_ins
        $checkIn = CheckIn::create([
            'user_id' => $user->id,
            'check_in_time' => Carbon::now(),
            'method' => 'QR Code',
        ]);

        // Trả về kết quả thành công cho JavaScript để hiển thị lên màn hình Admin
        return response()->json([
            'success' => true,
            'message' => 'Check-in thành công!',
            'user_name' => $user->full_name, // Hoặc $user->full_name tùy DB của bạn
            'package_name' => $activeMembership->package_name, // Hiện tên gói khách đang tập
            'check_in_at' => $checkIn->check_in_time->format('H:i:s d/m/Y'),
        ]);
    }
}