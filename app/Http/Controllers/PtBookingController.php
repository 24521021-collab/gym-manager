<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PtBooking;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Notification;
use App\Models\PtProfile;
class PtBookingController extends Controller
{
    // 1. Hiển thị trang đặt lịch với danh sách PT
    public function index(Request $request)
    {
         $selectedSpec = $request->query('specialization', 'all');

        // Lấy tất cả các chuyên môn duy nhất để hiển thị trên thanh lọc
        $allSpecializations = PtProfile::whereNotNull('specialization')
            ->distinct()
            ->pluck('specialization');

        // Tạo Query Builder để lọc PT
        $ptsQuery = User::where('role', 'pt')->whereHas('PtProfile')->with('PtProfile');

        // Lọc theo chuyên môn nếu có yêu cầu
        if ($selectedSpec !== 'all') {
            $ptsQuery->whereHas('PtProfile', function ($query) use ($selectedSpec) {
                $query->where('specialization', $selectedSpec);
            });
        }

        $pts = $ptsQuery->orderBy('full_name')->get();

        // Trả về JSON nếu là yêu cầu từ Fetch API (để bộ lọc chuyên môn hoạt động mượt mà)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'pts' => $pts,
                'selectedSpec' => $selectedSpec
            ]);
        }

        return view('customer.booking_pt', compact('pts', 'allSpecializations', 'selectedSpec'));
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
            'note' => 'nullable|string|max:500',
        ]);

        $startTime = Carbon::parse($request->start_time);
        $endTime = (clone $startTime)->addHour(); // Mặc định mỗi ca tập kéo dài 1 tiếng
 
    
        
        $isConflict = PtBooking::where('pt_id', $request->pt_id)
            ->where('booking_date', $request->booking_date)
            ->where('status', '!=', 'cancelled')
            ->where('start_time', '<', $endTime->format('H:i:s'))
            ->where('end_time', '>', $startTime->format('H:i:s'))
            ->exists();

        if ($isConflict) {
            return response()->json([
                'success' => false,
                'message' => 'Khung giờ này vừa có người đặt mất rồi. Vui lòng chọn giờ khác!'
            ], 422);
        }
        
        /*
         * BẢO MẬT XSS: Sử dụng strip_tags() để loại bỏ hoàn toàn các thẻ HTML 
         */
        PtBooking::create([
            'customer_id' => auth()->id(), // ID khách hàng đang đăng nhập
            'pt_id' => $request->pt_id,
            'booking_date' => $request->booking_date,
            'start_time' => $startTime->format('H:i:s'),
            'end_time' => $endTime->format('H:i:s'),
            'price' => 300000, // Giá mặc định cho 1 buổi tập PT riêng
            'status' => 'pending', // Chờ huấn luyện viên duyệt lịch
            'note' => $request->note ? strip_tags($request->note) : null,
        ]);

        // Gửi thông báo cho khách hàng
        $pt = User::find($request->pt_id);
        Notification::create([
            'user_id' => auth()->id(),
            'type'    => 'booking',
            'title'   => 'Đặt lịch thành công',
            'content' => "Hệ thống KOR: Yêu cầu đặt lịch tập 1-kèm-1 với HLV {$pt->full_name} vào lúc {$startTime->format('H:i d/m/Y')} của bạn đã được gửi đi thành công. Vui lòng đợi HLV xác nhận."
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký lịch tập riêng thành công! Vui lòng chờ PT xác nhận.'
        ]);
    }
}