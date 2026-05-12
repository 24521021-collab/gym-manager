<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GymClass;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function store($id)
    {
        // 1. Lấy thông tin người dùng đang đăng nhập
        $user = Auth::user();

        // 2. Kiểm tra xem lớp học có tồn tại không
        $gymClass = GymClass::findOrFail($id);

        // 3. Kiểm tra xem người dùng đã đăng ký lớp này chưa để tránh trùng lặp
        // Sử dụng quan hệ 'enrolledClasses' đã thiết lập trong Model User
        if ($user->enrolledClasses()->where('gym_id', $id)->exists()) {
            return back()->with('error', 'Bạn đã đăng ký lớp học này rồi!');
        }

        // 4. Lưu vào bảng trung gian 'bookings'
        // Phương thức attach() sẽ tự động điền user_id và gym_id
        $user->enrolledClasses()->attach($id, [
            'booking_date' => now(),
            'status' => 'confirmed'
        ]);

        return back()->with('success', 'Đăng ký lớp học thành công!');
    }
}
