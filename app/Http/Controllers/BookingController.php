<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GymClass;
use App\Models\Booking;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Hiển thị danh sách lớp học để đăng ký
     */
    public function index(Request $request)
    {
        $query = GymClass::with('pt.user')
            ->withCount(['bookings as booked_count' => function ($query) {
                $query->where('status', '!=', 'cancelled');
            }]);

        // Bảo mật: Eloquent tự động sử dụng Prepared Statements chống SQL Injection
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Lọc theo loại lớp dựa trên từ khóa trong tên (Logic xử lý tập trung tại Controller)
        if ($request->filled('category') && $request->category !== 'all') {
            $cat = $request->category;
            $query->where(function($q) use ($cat) {
                if ($cat === 'yoga') {
                    $q->where('name', 'like', '%yoga%')->orWhere('name', 'like', '%pilates%');
                } elseif ($cat === 'cardio') {
                    $q->where('name', 'like', '%cardio%')
                      ->orWhere('name', 'like', '%đạp xe%')
                      ->orWhere('name', 'like', '%spinning%');
                } elseif ($cat === 'boxing') {
                    $q->where('name', 'like', '%box%')->orWhere('name', 'like', '%kick%')->orWhere('name', 'like', '%combat%');
                }
            });
        }

        $classes = $query->orderBy('id', 'asc')->get();

        $bookedClassIds = [];
        if (Auth::check()) {
            $bookedClassIds = Booking::where('user_id', Auth::id())
                ->where('status', '!=', 'cancelled')
                ->pluck('class_id')
                ->toArray();
        }

        if ($request->ajax()) {
            return response()->json([
                'classes' => $classes,
                'bookedClassIds' => $bookedClassIds
            ]);
        }

        return view('classes', compact('classes', 'bookedClassIds'));
    }

    /**
     * Lưu booking vào database
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:gym_classes,id',
        ]);

        // Kiểm tra vai trò: Chỉ member mới được đăng ký
        if (Auth::user()->role !== 'member') {
            $msg = 'Chỉ có hội viên mới đăng ký lớp học';
            if ($request->ajax()) return response()->json(['error' => $msg], 403);
            return redirect()->route('classes.index')->with('error', $msg);
        }

        $classId = $request->class_id;
        $userId  = Auth::id();

        // Kiểm tra đã đăng ký chưa
        $existing = Booking::where('user_id', $userId)
            ->where('class_id', $classId)
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($existing) {
            $msg = 'Bạn đã đăng ký lớp học này rồi!';
            if ($request->ajax()) return response()->json(['error' => $msg], 400);
            return redirect()->route('classes.index')->with('error', $msg);
        }

        // Kiểm tra còn chỗ không
        $gymClass = GymClass::withCount(['bookings as booked_count' => function ($query) {
            $query->where('status', '!=', 'cancelled');
        }])->findOrFail($classId);

        if ($gymClass->booked_count >= $gymClass->max_capacity) {
            $msg = 'Lớp học này đã đầy chỗ. Vui lòng chọn lớp khác.';
            if ($request->ajax()) return response()->json(['error' => $msg], 400);
            return redirect()->route('classes.index')->with('error', $msg);
        }

        // Đẩy thông tin lớp học vào giỏ hàng chuẩn
        $rowId = 'class_' . $classId;
        $cart = session()->get('cart', []);
        
        $cart[$rowId] = [
            "row_id"   => $rowId,
            "item_id"   => $gymClass->id,
            "item_type" => "class",
            "name"     => $gymClass->name,
            "price"    => $gymClass->price,
            "quantity" => 1,
            "image"    => $gymClass->image ?? null,
        ];
        
        session()->put('cart', $cart);

        // Gửi thông báo hệ thống sau khi thêm vào giỏ hàng thành công
        try {
            Notification::create([
                'user_id' => $userId,
                'type'    => 'class',
                'title'   => 'Lớp học mới trong giỏ hàng',
                'content' => "Bạn vừa thêm lớp [{$gymClass->name}] vào giỏ hàng thành công. Hãy hoàn tất thanh toán để giữ suất tập của bạn!"
            ]);
        } catch (\Exception $e) {
            \Log::error("Lỗi tạo thông báo: " . $e->getMessage());
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm lớp học vào giỏ hàng thành công!',
                'redirect_url' => route('cart.index')
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Đã thêm lớp học vào giỏ hàng!');
    }

    /**
     * Hủy booking
     */
    public function cancel(Request $request)
    {
        $request->validate([
            'class_id' => 'required|integer|exists:gym_classes,id',
        ]);

        $booking = Booking::where('user_id', Auth::id())
            ->where('class_id', $request->class_id)
            ->where('status', '!=', 'cancelled')
            ->firstOrFail();

        $booking->update(['status' => 'cancelled']);

        return redirect()->route('classes.index')
            ->with('success', 'Đã hủy đăng ký lớp học thành công.');
    }

}
