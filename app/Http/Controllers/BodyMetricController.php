<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BodyMetric; // Import Model mới
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BodyMetricController extends Controller
{
    public function index()
    {
    // 1. Lấy thông tin User đang đăng nhập để biết "đang vẽ biểu đồ cho ai"
    $user = Auth::user();
    
    // 2. TRUY VẤN DATABASE:
    // - Lấy các bản ghi trong bảng body_metrics của user này
    // - orderBy('measured_at', 'asc'): Sắp xếp từ cũ đến mới (trục X biểu đồ đi từ trái sang phải)
    // - take(10): Chỉ lấy 10 lần đo gần nhất để biểu đồ không bị quá dày, dễ nhìn hơn
    $metricsHistory = BodyMetric::where('user_id', $user->id)
                                ->orderBy('measured_at', 'asc')
                                ->take(10)
                                ->get();

    // 3. XỬ LÝ DỮ LIỆU CHO TRỤC X (Ngày đo):
    // - map(): Duyệt qua từng bản ghi và định dạng lại ngày tháng
    // - Carbon::parse(...)->format('d/m'): Chuyển 2026-05-04 thành 04/05 cho gọn
    // - toArray(): Chuyển tập hợp kết quả thành một mảng đơn giản [ "01/05", "04/05", ... ]
    $labels = $metricsHistory->map(fn($m) => \Carbon\Carbon::parse($m->measured_at)->format('d/m/y'))->toArray();

    // 4. XỬ LÝ DỮ LIỆU CHO TRỤC Y (Chỉ số):
    // - pluck('weight'): Chỉ trích xuất cột 'weight' từ danh sách bản ghi
    // - Kết quả sẽ là mảng số: [ 65.5, 66.2, 64.8, ... ]
    $weights = $metricsHistory->pluck('weight')->toArray();

    // 5. Tương tự bước trên, trích xuất mảng chỉ số BMI
    $bmis = $metricsHistory->pluck('bmi')->toArray();

    // 6. Lấy bản ghi cuối cùng (mới nhất) để hiện thông tin vào các ô Input/Label trên trang Profile
    $latestMetric = $metricsHistory->last();

    // 7. Gửi tất cả mảng dữ liệu này ra View bằng hàm compact
        return view('Body_metric', compact('user', 'latestMetric','labels', 'weights', 'bmis'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'weight' => 'required|numeric|between:20,300',
            'height' => 'required|numeric|between:50,250',
        ]);

        // Tính toán BMI từ dữ liệu gửi lên
        $heightInMeters = $request->height / 100;
        $bmi = round($request->weight / ($heightInMeters * $heightInMeters), 2);

        // TẠO BẢN GHI MỚI TRONG DATABASE (Ảnh image_641be2.png)
        BodyMetric::create([
            'user_id'     => $user->id,
            'weight'      => $request->weight,
            'height'      => $request->height,
            'bmi'         => $bmi,
            'measured_at' => Carbon::now(), // Lưu thời điểm đo hiện tại
        ]);

        return redirect()->back()->with('success', 'Đã cập nhật chỉ số cơ thể mới!');
    }
}