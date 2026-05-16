<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Import đúng các Model trong hệ thống của Long
use App\Models\Order; 
use App\Models\User;
use App\Models\GymClass;
use App\Models\GymPackage;
use App\Models\Membership;
use App\Models\OrderItem;

class DashboardController extends Controller
{
    public function index()
    {
        $currentYear = Carbon::now()->year;

        // --- 1. CÁC CARD THỐNG KÊ (QUICK STATS) ---
        // Doanh thu từ đơn hàng sản phẩm (Chỉ tính đơn đã thanh toán/hoàn thành)
        $productRevenue = Order::where('payment_status', 'Paid')->sum('total_amount');
        
        // Doanh thu từ đăng ký gói tập (Join với bảng packages để lấy giá)
        $packageRevenue = Membership::join('gym_packages', 'memberships.package_id', '=', 'gym_packages.id')
            ->where('memberships.status', 'Active')
            ->sum('gym_packages.price'); 
        
        // Tổng doanh thu thực tế của phòng gym
        $totalRevenue = $productRevenue + $packageRevenue;

        // Số lớp học đang mở
        $totalClasses = GymClass::count();

        // Học viên mới trong năm nay (Loại trừ Admin và Trainer nếu có)
        $totalNewMembers = User::whereYear('created_at', $currentYear)
            ->where('role', 'member')
            ->count();

        // Tổng số đơn hàng bán lẻ sản phẩm
        $totalOrders = Order::where('payment_status', 'Paid')->count();


        // --- 2. BIỂU ĐỒ ĐƯỜNG: DOANH THU 12 THÁNG (LINE CHART) ---
        // Gom doanh thu đơn hàng theo tháng
        $monthlyOrderRevenue = Order::select(
                DB::raw('SUM(total_amount) as total'),
                DB::raw('MONTH(order_date) as month')
            )
            ->whereYear('order_date', $currentYear)
            ->where('payment_status', 'Paid')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Gom doanh thu gói tập theo tháng
        $monthlyPackageRevenue = Membership::join('gym_packages', 'memberships.package_id', '=', 'gym_packages.id')
            ->select(
                DB::raw('SUM(gym_packages.price) as total'),
                DB::raw('MONTH(memberships.created_at) as month')
            )
            ->whereYear('memberships.created_at', $currentYear)
            ->where('memberships.status', 'Active')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Cộng dồn doanh thu 2 mảng và chuẩn hóa đủ 12 tháng cho Chart.js
        $monthsLabels = [];
        $revenueChartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthsLabels[] = "Tháng $i";
            $orderAmt = $monthlyOrderRevenue[$i] ?? 0;
            $packageAmt = $monthlyPackageRevenue[$i] ?? 0;
            $revenueChartData[] = $orderAmt + $packageAmt;
        }

        $revenueData = [
            'labels' => $monthsLabels,
            'data'   => $revenueChartData
        ];


        // --- 3. BIỂU ĐỒ TRÒN 1: CƠ CẤU DOANH THU (GÓI TẬP VS SẢN PHẨM) ---
        $structureData = [
            'labels' => ['Đăng ký gói tập', 'Mua lẻ sản phẩm phụ kiện'],
            'data'   => [(int)$packageRevenue, (int)$productRevenue]
        ];


        // --- 4. BIỂU ĐỒ TRÒN 2: TỶ LỆ CÁC GÓI TẬP ĐƯỢC ĐĂNG KÝ ---
        $packageStats = DB::table('memberships')
            ->join('gym_packages', 'memberships.package_id', '=', 'gym_packages.id')
            ->select('gym_packages.package_name', DB::raw('count(*) as total'))
            ->groupBy('gym_packages.package_name')
            ->get();

        $totalSubs = $packageStats->sum('total');
        $pkgLabels = [];
        $pkgData   = [];

        foreach ($packageStats as $stat) {
            $pkgLabels[] = $stat->package_name;
            // Tính toán % sẵn từ Backend để Frontend chỉ việc hiển thị
            $pkgData[]   = $totalSubs > 0 ? round(($stat->total / $totalSubs) * 100, 1) : 0;
        }

        $packageData = [
            'labels' => $pkgLabels,
            'data'   => $pkgData
        ];


        // --- 5. BIỂU ĐỒ CỘT: TĂNG TRƯỞNG THÀNH VIÊN MỚI (BAR CHART) ---
        $usersPerMonth = User::select(
                DB::raw('COUNT(*) as count'),
                DB::raw('MONTH(created_at) as month')
            )
            ->whereYear('created_at', $currentYear)
            ->where('role', 'member')
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $newMemberChartData = [];
        $shortMonthLabels = [];
        for ($i = 1; $i <= 12; $i++) {
            $shortMonthLabels[] = "T$i";
            $newMemberChartData[] = $usersPerMonth[$i] ?? 0;
        }

        $newMemberData = [
            'labels' => $shortMonthLabels,
            'data'   => $newMemberChartData
        ];


        // --- 6. BIỂU ĐỒ HOÀN CHỈNH: TOP SẢN PHẨM BÁN CHẠY (Mặc định 30 ngày gần nhất) ---
        $endDateDefault = Carbon::now()->format('Y-m-d');
        $startDateDefault = Carbon::now()->subDays(30)->format('Y-m-d');
        $productStats = $this->queryTopProductsData($startDateDefault, $endDateDefault);


        return view('admin.dashboard', compact(
            'totalRevenue', 'totalClasses', 'totalNewMembers', 'totalOrders',
            'revenueData', 'structureData', 'packageData', 'newMemberData', 'productStats'
        ));
    }

    // --- 7. HÀM API AJAX PHỤC VỤ BỘ LỌC NGÀY THÁNG CỦA BIỂU ĐỒ SẢN PHẨM ---
    public function filterTopProducts(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        if (!$startDate || !$endDate) {
            $endDate = Carbon::now()->format('Y-m-d');
            $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        }

        $productStats = $this->queryTopProductsData($startDate, $endDate);

        return response()->json($productStats);
    }

    // Hàm bổ trợ đóng gói câu SQL truy vấn lồng nhóm sản phẩm theo danh mục (Giống file mẫu 100%)
    private function queryTopProductsData($startDate, $endDate)
    {
        $rawProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.name as product_name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.subtotal) as total_revenue') 
            )
            ->where('orders.payment_status', 'Paid')
            ->whereBetween('orders.order_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_qty', 'desc')
            ->limit(10)
            ->get();

        // Vì cấu trúc của bạn không có bảng Category rõ ràng trong context, 
        // tôi sẽ gom tất cả vào một nhóm chung "Sản phẩm" để đảm bảo biểu đồ vẫn hiển thị được.
        $productStats = [
            '1' => [
                'name' => 'Tất cả sản phẩm',
                'products' => [],
                'data' => [],
                'quantities' => []
            ]
        ];

        foreach ($rawProducts as $item) {
            $productStats['1']['products'][] = $item->product_name;
            $productStats['1']['data'][] = (int)$item->total_revenue;
            $productStats['1']['quantities'][] = (int)$item->total_qty;
        }

        return $productStats;
    }
}