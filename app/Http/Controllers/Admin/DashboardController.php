<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\CheckIn;
use Carbon\Carbon;

// Import đúng các Model trong hệ thống của Long
use App\Models\Order;
use App\Models\User;
use App\Models\GymClass;
use App\Models\OrderItem;
use App\Models\PtBooking;
use App\Models\PtProfile;

class DashboardController extends Controller
{
    public function index()
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // 1. CÁC CARD THỐNG KÊ (QUICK STATS) - Lọc theo tháng và năm hiện tại
        // Doanh thu từ Sản phẩm (Shop Revenue)
        $shopRevenue = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'Paid')
            ->where('order_items.item_type', 'product')
            ->whereMonth('orders.order_date', $currentMonth)
            ->whereYear('orders.order_date', $currentYear)
            ->sum('order_items.subtotal');

        // Doanh thu từ Gói tập (Package Revenue)
        $packageRevenue = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'Paid')
            ->where('order_items.item_type', 'package')
            ->whereMonth('orders.order_date', $currentMonth)
            ->whereYear('orders.order_date', $currentYear)
            ->sum('order_items.subtotal');

        // Doanh thu từ Lớp học nhóm (Group Revenue)
        $groupRevenue = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'Paid')
            ->where('order_items.item_type', 'class')
            ->whereMonth('orders.order_date', $currentMonth)
            ->whereYear('orders.order_date', $currentYear)
            ->sum('order_items.subtotal');

        // Doanh thu từ PT 1-kèm-1 (PT Revenue)
        $ptRevenue = PtBooking::whereIn('status', ['confirmed', 'completed'])
            ->whereMonth('booking_date', $currentMonth)
            ->whereYear('booking_date', $currentYear)
            ->sum('price');

        // 1.1 Chi tiết Doanh thu Sản phẩm (Group theo tên)
        $productPerformances = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'Paid')
            ->where('order_items.item_type', 'product')
            ->whereMonth('orders.order_date', $currentMonth)
            ->whereYear('orders.order_date', $currentYear)
            ->select('order_items.name', DB::raw('SUM(order_items.quantity) as total_sold'), DB::raw('SUM(order_items.subtotal) as total_revenue'))
            ->groupBy('order_items.name')
            ->get();

        // 1.2 Chi tiết Doanh thu Gói tập
        $packagePerformances = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'Paid')
            ->where('order_items.item_type', 'package')
            ->whereMonth('orders.order_date', $currentMonth)
            ->whereYear('orders.order_date', $currentYear)
            ->select('order_items.name', DB::raw('COUNT(order_items.id) as total_sold'), DB::raw('SUM(order_items.subtotal) as total_revenue'))
            ->groupBy('order_items.name')
            ->get();

        // 1.3 Chi tiết Doanh thu PT (Tính 20% hoa hồng hệ thống thu được)
        $ptPerformances = PtBooking::join('user', 'pt_bookings.pt_id', '=', 'user.id')
            ->whereIn('pt_bookings.status', ['confirmed', 'completed'])
            ->whereMonth('pt_bookings.booking_date', $currentMonth)
            ->whereYear('pt_bookings.booking_date', $currentYear)
            ->select('user.full_name', DB::raw('SUM(pt_bookings.price) as total_revenue'), DB::raw('SUM(pt_bookings.price) * 0.2 as admin_commission'))
            ->groupBy('pt_bookings.pt_id', 'user.full_name')
            ->get();

        // 2. Dữ liệu điểm danh hôm nay
        $todayCheckins = CheckIn::with('user')
            ->whereDate('check_in_time', Carbon::today())
            ->orderBy('check_in_time', 'desc')
            ->get();
        $checkinCount = $todayCheckins->count();

        // 3. Hiệu suất doanh thu lớp nhóm
        $classPerformances = GymClass::with('pt.user')
            ->leftJoin('order_items', function($join) use ($currentMonth, $currentYear) {
                $join->on('gym_classes.id', '=', 'order_items.item_id')
                     ->where('order_items.item_type', 'class')
                     ->join('orders', 'order_items.order_id', '=', 'orders.id')
                     ->where('orders.payment_status', 'Paid')
                     ->whereMonth('orders.order_date', $currentMonth)
                     ->whereYear('orders.order_date', $currentYear);
            })
            ->select(
                'gym_classes.id',
                'gym_classes.name',
                'gym_classes.price', // Giá cơ bản của lớp học
                'gym_classes.pt_id', // Bắt buộc phải có để load quan hệ pt.user
                DB::raw('COUNT(order_items.id) as total_sold'), // Số lượng suất đã bán
                DB::raw('SUM(order_items.subtotal) as total_revenue') // Tổng doanh thu từ lớp này
            )
            ->groupBy('gym_classes.id', 'gym_classes.name', 'gym_classes.price', 'gym_classes.pt_id')
            ->orderByDesc('total_revenue')
            ->get();

        // 4. Lấy chi tiết toàn bộ lượt điểm danh trong tháng để xuất báo cáo
        $monthCheckinsDetail = CheckIn::with('user')
            ->whereMonth('check_in_time', $currentMonth)
            ->whereYear('check_in_time', $currentYear)
            ->orderBy('check_in_time', 'desc')
            ->get();

        // Truyền dữ liệu sang view
        return view('admin.dashboard', compact(
            'packageRevenue',
            'shopRevenue',
            'ptRevenue',
            'groupRevenue',
            'todayCheckins',
            'checkinCount',
            'classPerformances',
            'productPerformances',
            'packagePerformances',
            'ptPerformances',
            'monthCheckinsDetail'
        ));
    }
}