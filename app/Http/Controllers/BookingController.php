<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GymClass;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Hiển thị danh sách lớp học để đăng ký
     */
    public function index()
    {
        $classes = GymClass::with('pt.user')
            ->withCount(['bookings as booked_count' => function ($query) {
                $query->where('status', '!=', 'cancelled');
            }])
            ->orderBy('id', 'asc')
            ->get();

        $bookedClassIds = [];
        if (Auth::check()) {
            $bookedClassIds = Booking::where('user_id', Auth::id())
                ->where('status', '!=', 'cancelled')
                ->pluck('class_id')
                ->toArray();
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

        $classId = $request->class_id;
        $userId  = Auth::id();

        // Kiểm tra đã đăng ký chưa
        $existing = Booking::where('user_id', $userId)
            ->where('class_id', $classId)
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($existing) {
            return redirect()->route('classes.index')
                ->with('error', 'Bạn đã đăng ký lớp học này rồi!');
        }

        // Kiểm tra còn chỗ không
        $gymClass = GymClass::withCount(['bookings as booked_count' => function ($query) {
            $query->where('status', '!=', 'cancelled');
        }])->findOrFail($classId);

        if ($gymClass->booked_count >= $gymClass->max_capacity) {
            return redirect()->route('classes.index')
                ->with('error', 'Lớp học này đã đầy chỗ. Vui lòng chọn lớp khác.');
        }

        Booking::create([
            'user_id'      => $userId,
            'class_id'     => $classId,
            'booking_date' => now(),
            'status'       => 'confirmed',
        ]);

        return redirect()->route('classes.index')
            ->with('success', 'Đăng ký lớp "' . $gymClass->name . '" thành công!');
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
