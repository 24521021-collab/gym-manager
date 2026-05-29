<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PtBooking;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Notification;

class PtBookingController extends Controller
{
    // 1. Hiển thị trang đặt lịch với danh sách PT
    public function index()
    {
        // Lấy danh sách user có role là PT (bạn chỉnh lại điều kiện 'role' theo DB của bạn)
        $pts = User::where('role', 'pt')->with('ptProfile')->get(); 
        return view('customer.booking_pt', compact('pts'));
    }

    // 2. API lấy các khung giờ ĐÃ BỊ ĐẶT của một PT trong một ngày cụ thể
    public function getBookedSlots(Request $request)
    {
        $request->validate([
            'pt_id' => 'required|exists:user,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        // Lấy các lịch đặt CHƯA BỊ HỦY của PT trong ngày được chọn
        $bookedSlots = PtBooking::where('pt_id', $request->pt_id)
            ->where('booking_date', $request->date)
            ->where('status', '!=', 'cancelled')
            ->get(['start_time', 'end_time']);

        return response()->json($bookedSlots);
    }

    // 3. Xử lý lưu thông tin đặt lịch từ khách hàng
    public function store(Request $request)
    {
        $request->validate([
            'pt_id' => 'required|exists:user,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
        ]);

        $startTime = Carbon::parse($request->start_time);
        $endTime = (clone $startTime)->addHour(); // Mặc định mỗi ca tập kéo dài 1 tiếng

        // Kiểm tra lại lần cuối để tránh tình trạng 2 khách đặt cùng 1 giờ cùng 1 lúc
        $isConflict = PtBooking::where('pt_id', $request->pt_id)
            ->where('booking_date', $request->booking_date)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime->format('H:i:s'), $endTime->format('H:i:s')])
                      ->orWhereBetween('end_time', [$startTime->format('H:i:s'), $endTime->format('H:i:s')]);
            })->exists();
        if ($isConflict) {
            return back()->with('error', 'Khung giờ này vừa có người đặt mất rồi. Vui lòng chọn giờ khác!');
        }
        // Tạo bản ghi mới
        PtBooking::create([
            'customer_id' => auth()->id(), // ID khách hàng đang đăng nhập
            'pt_id' => $request->pt_id,
            'booking_date' => $request->booking_date,
            'start_time' => $startTime->format('H:i:s'),
            'end_time' => $endTime->format('H:i:s'),
            'price' => 300000, // Giá mặc định cho 1 buổi tập PT riêng
            'status' => 'pending', // Chờ huấn luyện viên duyệt lịch
            'note' => $request->note,
        ]);

        // Gửi thông báo cho khách hàng
        $pt = User::find($request->pt_id);
        Notification::create([
            'user_id' => auth()->id(),
            'type'    => 'booking',
            'title'   => 'Đặt lịch thành công',
            'content' => "Hệ thống KOR: Yêu cầu đặt lịch tập 1-kèm-1 với HLV {$pt->full_name} vào lúc {$startTime->format('H:i d/m/Y')} của bạn đã được gửi đi thành công. Vui lòng đợi HLV xác nhận."
        ]);

        return back()->with('success', 'Đăng ký lịch tập riêng thành công! Vui lòng chờ PT xác nhận.');
    }
}