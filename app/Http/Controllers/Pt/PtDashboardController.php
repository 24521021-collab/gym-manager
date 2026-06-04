<?php

namespace App\Http\Controllers\Pt;

use App\Http\Controllers\Controller;
use App\Models\PtBooking;
use App\Models\Notification;
use App\Models\PtLog;
use App\Models\GymClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PtDashboardController extends Controller
{
    /**
     * Hiển thị bảng điều khiển dành cho PT
     */
    public function index(Request $request)
    {
        $pt = Auth::user();
        $ptId = $pt->id;

        // Lấy danh sách lịch hẹn riêng (Bao gồm cả lịch đã xử lý để giữ lại thông tin hiển thị)
        $privateBookings = PtBooking::where('pt_id', $ptId)
            ->whereIn('status', ['pending', 'confirmed', 'cancelled', 'completed'])
            ->with('customer')
            ->orderByRaw("FIELD(status, 'pending', 'confirmed', 'completed', 'cancelled')")
            ->orderBy('booking_date', 'desc')
            ->get();

        // Lấy thông tin Profile và các lớp học nhóm đảm nhận
        $PtProfile = $pt->PtProfile;
        $classes = [];
        $classCommission = 0;
        $totalClassStudents = 0;

        if ($PtProfile) {
            $classes = GymClass::where('pt_id', $PtProfile->id)
                ->with('bookings.user.latestBodyMetric')
                ->get();
            
            $totalClassStudents = $classes->sum(function($class) {
                return $class->bookings->count();
            });
            
            // Tính hoa hồng lớp nhóm (50% giá trị đơn hàng đã thanh toán)
            $classCommission = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('gym_classes', 'order_items.item_id', '=', 'gym_classes.id')
                ->where('gym_classes.pt_id', $PtProfile->id)
                ->where('order_items.item_type', 'class')
                ->where('orders.payment_status', 'Paid')
                ->sum('order_items.subtotal') * 0.5;
        }

        // Tính hoa hồng lịch riêng (80% phí tập 1-kèm-1)
        $privateCommission = PtBooking::where('pt_id', $ptId)
            ->whereIn('status', ['confirmed', 'completed'])
            ->sum('price') * 0.8;
            
        $totalPrivateClients = PtBooking::where('pt_id', $ptId)
            ->whereIn('status', ['confirmed', 'completed'])
            ->distinct('customer_id')
            ->count();

        $totalCommission = $privateCommission + $classCommission;

        // 2. Lấy danh sách Nhật ký huấn luyện (Sắp xếp theo ngày mới nhất)
        $logs = PtLog::where('pt_profile_id', $ptId)
            ->orderBy('log_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        // 3. Chuẩn bị danh sách "Đối tượng" cho dropdown
        // PT có thể chọn từ các lớp mình dạy hoặc các học viên đã từng đặt lịch
        $logTargets = collect();
        foreach ($classes as $class) {
            $logTargets->push( $class->name);
        }
        
        // Lấy danh sách học viên kèm theo email, loại bỏ trùng lặp dựa trên email
        $customers = PtBooking::where('pt_id', $ptId)
            ->with('customer')
            ->get()
            ->map(function($booking) {
                return [
                    'display' => 'Học viên 1 kèm 1: ' . $booking->customer->full_name . ' (' . $booking->customer->email . ')'
                ];
            })
            ->unique('display');

        foreach ($customers as $c) {
            $logTargets->push($c['display']);
        }

        // Xử lý lấy danh sách nhật ký qua AJAX (Fetch API) - Đặt ở cuối để đảm bảo đầy đủ các biến truyền vào View
        if ($request->ajax()) {
            return response()->json([
                'success' => true, 
                'html' => view('pt.dashboard', compact(
                    'privateBookings', 'classes', 'privateCommission', 'classCommission', 'totalCommission', 'logs', 'logTargets', 'totalClassStudents', 'totalPrivateClients'
                ))->fragment('logs_list')
            ]);
        }

        return view('pt.dashboard', compact(
            'privateBookings', // Thêm biến privateBookings vào đây
            'classes',
            'privateCommission',
            'classCommission',
            'totalCommission',
            'logs',
            'logTargets',
            'totalClassStudents',
            'totalPrivateClients'
        ));
    }

    /**
     * Xử lý cập nhật trạng thái lịch hẹn (Chấp nhận/Từ chối)
     */
    public function updateBookingStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:confirmed,cancelled'
        ]);

        $booking = PtBooking::findOrFail($id);

        // Bảo mật: Đảm bảo PT chỉ có thể xử lý lịch hẹn của chính mình
        if ($booking->pt_id !== Auth::id()) {
            return response()->json(['message' => 'Bạn không có quyền xử lý lịch hẹn này.'], 403);
        }

        $booking->status = $request->status;
        $booking->save();

        $ptName = Auth::user()->full_name;
        $dateStr = Carbon::parse($booking->booking_date)->format('d/m/Y');
        $timeStr = Carbon::parse($booking->start_time)->format('H:i');

        if ($request->status === 'confirmed') {
            // Gửi thông báo yêu cầu thanh toán khi chấp nhận
            Notification::create([
                'user_id' => $booking->customer_id,
                'type'    => 'booking',
                'title'   => 'Lịch hẹn PT đã được chấp nhận',
                'content' => "HLV {$ptName} đã xác nhận lịch tập 1-kèm-1 của bạn vào lúc {$timeStr} ngày {$dateStr}. Vui lòng tiến hành thanh toán 300.000đ tại quầy hoặc qua ứng dụng để hoàn tất xác nhận ca tập."
            ]);
            $msg = 'Đã chấp nhận lịch hẹn và gửi yêu cầu thanh toán tới hội viên.';
        } else {
            // Gửi thông báo xin lỗi khi từ chối
            Notification::create([
                'user_id' => $booking->customer_id,
                'type'    => 'booking',
                'title'   => 'Lịch hẹn PT bị từ chối',
                'content' => "HLV {$ptName} rất tiếc phải từ chối lịch hẹn của bạn vào lúc {$timeStr} ngày {$dateStr} do lịch trình thay đổi đột xuất. Rất mong bạn thông cảm và hẹn gặp bạn vào một khung giờ khác!"
            ]);
            $msg = 'Đã từ chối lịch hẹn và gửi lời xin lỗi tới hội viên.';
        }

        return response()->json(['success' => true, 'message' => $msg]);
    }

    /**
     * Lưu nhật ký huấn luyện mới
     */
    public function storeLog(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'log_date' => 'required|date',
            'status' => 'required|in:completed,draft,upcoming'
        ]);

        $log = PtLog::create([
            'pt_profile_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'log_date' => $request->log_date,
            'start_time' => $request->start_time ?? now()->format('H:i'),
            'status' => $request->status,
        ]);

        // Trả về HTML fragment của bản ghi mới để JS chèn trực tiếp vào giao diện
        // Render the new partial view directly
        // Bỏ qua việc render partial để tránh lỗi 500 nếu thiếu file, vì frontend sẽ fetch lại toàn bộ danh sách
        return response()->json([
            'success' => true, 
            'message' => 'Đã lưu nhật ký huấn luyện thành công!',
            'data' => $log,
        ]);
    }
}
