<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BodyMetric;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BodyMetricController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'weight' => 'required|numeric|between:20,300',
            'height' => 'required|numeric|between:50,250',
            'body_fat_percentage' => 'nullable|numeric|between:5,60',
        ]);

        $heightInMeters = $request->height / 100;
        $bmi = round($request->weight / ($heightInMeters * $heightInMeters), 2);
        $bodyFat = $request->body_fat_percentage;

        // CHỈ LƯU VÀO DATABASE NẾU NGƯỜI DÙNG ĐÃ ĐĂNG NHẬP
        if (Auth::check()) {
            BodyMetric::create([
                'user_id'     => Auth::id(),
                'weight'      => $request->weight,
                'height'      => $request->height,
                'bmi'         => $bmi,
                'body_fat_percentage' => $bodyFat,
                'measured_at' => Carbon::now(),
            ]);
            return response()->json(['success' => true, 'message' => 'Đã cập nhật và lưu chỉ số vào hồ sơ!']);
           // return response()->json(['success' => true, 'message' => 'Đã cập nhật và lưu chỉ số vào hồ sơ!', 'bmi' => $bmi]);
        }

        // TRẢ VỀ KẾT QUẢ CHO KHÁCH (GUEST) MÀ KHÔNG LƯU
        return response()->json([
            'success' => true, 
            'is_guest' => true,
            'message' => 'Kết quả tính toán thành công! Đăng ký để lưu lại lịch sử.'
        ]);
    }
}