<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PtBooking;
use Illuminate\Http\Request;

class AdminPt_BookingController extends Controller
{
    public function index()
    {
        $query = PtBooking::with(['customer', 'pt']);

        // Lọc theo trạng thái
        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        // Tìm kiếm theo tên PT hoặc tên Hội viên
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('customer', function ($sub) use ($search) {
                    $sub->where('full_name', 'like', "%{$search}%");
                })->orWhereHas('pt', function ($sub) use ($search) {
                    $sub->where('full_name', 'like', "%{$search}%");
                });
            });
        }

        $bookings = $query
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(10);

        return request()->ajax() ? response()->json($bookings) : view('admin.pt_bookings', compact('bookings'));
    }
}
