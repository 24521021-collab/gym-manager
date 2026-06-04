<?php

namespace App\Http\Controllers;

use App\Models\BodyMetric;
use App\Services\FitnessCatalogRecommendationService;
use App\Services\HealthRecommendationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BodyMetricController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'weight' => 'required|numeric|between:20,300',
            'height' => 'required|numeric|between:50,250',
            'body_fat_percentage' => 'nullable|numeric|between:5,60',
        ]);

        $heightInMeters = (float) $request->height / 100;
        $bmi = round((float) $request->weight / ($heightInMeters * $heightInMeters), 2);
        $bodyFat = $request->filled('body_fat_percentage')
            ? (float) $request->body_fat_percentage
            : null;

        $previousMetric = Auth::check()
            ? BodyMetric::where('user_id', Auth::id())->latest('measured_at')->first()
            : null;

        $recommendation = HealthRecommendationService::build(
            $bmi,
            $bodyFat,
            $previousMetric?->bmi ? (float) $previousMetric->bmi : null
        );
        $recommendation['catalog'] = FitnessCatalogRecommendationService::forBmiAsArray($bmi);

        if (Auth::check()) {
            BodyMetric::create([
                'user_id' => Auth::id(),
                'weight' => $request->weight,
                'height' => $request->height,
                'bmi' => $bmi,
                'body_fat_percentage' => $bodyFat,
                'measured_at' => Carbon::now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Da cap nhat chi so va tao de xuat ca nhan hoa!',
                'bmi' => $bmi,
                'recommendation' => $recommendation,
            ]);
        }

        return response()->json([
            'success' => true,
            'is_guest' => true,
            'message' => 'Tinh BMI thanh cong. Dang ky de luu lich su va theo doi tien do.',
            'bmi' => $bmi,
            'recommendation' => $recommendation,
        ]);
    }
}
