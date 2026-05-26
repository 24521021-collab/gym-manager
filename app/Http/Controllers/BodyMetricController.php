<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BodyMetric; // Import Model mới
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BodyMetricController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'weight' => 'required|numeric|between:20,300',
            'height' => 'required|numeric|between:50,250',
            'body_fat_percentage' => 'nullable|numeric|between:5,60', // Thêm validation cho tỉ lệ mỡ
        ]);

        // Tính toán BMI từ dữ liệu gửi lên
        $heightInMeters = $request->height / 100;
        $bmi = round($request->weight / ($heightInMeters * $heightInMeters), 2);
        $bodyFat = $request->body_fat_percentage;

        // TẠO BẢN GHI MỚI TRONG DATABASE (Ảnh image_641be2.png)
        BodyMetric::create([
            'user_id'     => $user->id,
            'weight'      => $request->weight,
            'height'      => $request->height,
            'bmi'         => $bmi,
            'body_fat_percentage' => $bodyFat, // Lưu tỉ lệ mỡ
            'measured_at' => Carbon::now(), // Lưu thời điểm đo hiện tại
        ]);

        // Nếu yêu cầu từ AJAX (fetch), trả về JSON
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Đã cập nhật chỉ số cơ thể mới!']);
        }

        return back()->with('success', 'Đã cập nhật chỉ số cơ thể mới!');
    }
}