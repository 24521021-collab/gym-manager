<?php

namespace App\Http\Controllers\Pt;

use App\Http\Controllers\Controller;
use App\Models\GymClass;
use App\Models\PtBooking;
use Illuminate\Http\Request;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PtDashboardController extends Controller
{
    /**
     * Hiển thị trang thống kê dành riêng cho PT
     */
    public function index()
    {
        $user = Auth::user();
        $ptProfile = $user->ptProfile;

        if (!$ptProfile) {
            return redirect('/')->with('error', 'Hồ sơ PT của bạn chưa được thiết lập.');
        }

        // 1. Lấy danh sách lớp học và đếm số lượng học viên thực tế (không tính đã hủy)
        $classes = GymClass::where('pt_id', $ptProfile->id)
            ->withCount(['bookings' => function ($query) {
                $query->where('status', '!=', 'cancelled');
            }])
            ->get();

        $totalClassStudents = $classes->sum('bookings_count');

        // 2. Tính toán tổng hoa hồng (Commission) cho PT
        // Hoa hồng từ các lớp học phụ trách (50% mỗi học viên)
        $classCommission = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('gym_classes', 'order_items.item_id', '=', 'gym_classes.id')
            ->where('orders.payment_status', 'Paid')
            ->where('order_items.item_type', 'class')
            ->where('gym_classes.pt_id', $ptProfile->id)
            ->sum('order_items.subtotal') * 0.5;

        // Hoa hồng từ lịch đặt riêng (80% mỗi buổi)
        $privateBookings = PtBooking::where('pt_id', $user->id)->get();
        $privateCommission = $privateBookings->whereIn('status', ['confirmed', 'completed'])->sum('price') * 0.8;

        $totalCommission = $classCommission + $privateCommission;
        $totalPrivateClients = $privateBookings->where('status', 'confirmed')->count();

        return view('pt.dashboard', compact('classes', 'totalClassStudents', 'totalPrivateClients', 'totalCommission'));
    }

    /**
     * Trang danh sách lớp học và CRUD
     */
    public function classes()
    {
        $user = Auth::user();
        $classes = GymClass::where('pt_id', $user->ptProfile->id)
            ->withCount(['bookings' => function ($query) {
                $query->where('status', '!=', 'cancelled');
            }])
            ->get();

        return view('pt.classes', compact('classes'));
    }

    public function storeClass(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'max_capacity' => 'required|numeric|min:1',
            'description' => 'nullable|string',
            'total_sessions' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data['pt_id'] = Auth::user()->ptProfile->id;

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/classes'), $imageName);
            $data['image'] = $imageName;
        }

        GymClass::create($data);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Tạo lớp học mới thành công!']);
        }

        return back()->with('success', 'Tạo lớp học mới thành công!');
    }

    public function updateClass(Request $request, $id)
    {
        $class = GymClass::where('pt_id', Auth::user()->ptProfile->id)->findOrFail($id);
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'max_capacity' => 'required|numeric|min:1',
            'description' => 'nullable|string',
            'total_sessions' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('image')) {
            if ($class->image && File::exists(public_path('uploads/classes/' . $class->image))) {
                File::delete(public_path('uploads/classes/' . $class->image));
            }
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/classes'), $imageName);
            $data['image'] = $imageName;
        }

        $class->update($data);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Cập nhật lớp học thành công!']);
        }

        return back()->with('success', 'Cập nhật lớp học thành công!');
    }

    public function destroyClass($id)
    {
        $class = GymClass::where('pt_id', Auth::user()->ptProfile->id)->findOrFail($id);
        if ($class->image && File::exists(public_path('uploads/classes/' . $class->image))) {
            File::delete(public_path('uploads/classes/' . $class->image));
        }
        $class->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Đã xóa lớp học!']);
        }

        return back()->with('success', 'Đã xóa lớp học!');
    }

    /**
     * Trang quản lý đặt lịch riêng
     */
    public function bookings()
    {
        $bookings = PtBooking::where('pt_id', Auth::id())
            ->with('customer')
            ->orderBy('booking_date', 'desc')
            ->paginate(15);

        return view('pt.bookings', compact('bookings'));
    }

    public function updateBookingStatus(Request $request, $id)
    {
        $booking = PtBooking::where('pt_id', Auth::id())->findOrFail($id);
        $booking->update(['status' => $request->status]);
        return back()->with('success', 'Đã cập nhật trạng thái lịch hẹn!');
    }
}
