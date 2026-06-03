<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\BodyMetric;
use Carbon\Carbon;

class BodyMetricSeeder extends Seeder
{
    /**
     * Tự động tạo chỉ số sức khỏe cho tất cả hội viên.
     */
    public function run(): void
    {
        // Lấy danh sách những người dùng là Hội viên
        $members = User::where('role', 'member')->get();

        foreach ($members as $member) {
            $height = rand(155, 185); // cm
            $weight = rand(50, 95);   // kg
            
            // Tính toán BMI sơ bộ: kg / (m^2)
            $heightInMeters = $height / 100;
            $bmi = round($weight / ($heightInMeters * $heightInMeters), 1);
            
            BodyMetric::create([
                'user_id'             => $member->id,
                'height'              => $height,
                'weight'              => $weight,
                'bmi'                 => $bmi,
                'body_fat_percentage' => rand(12, 28), // Tỉ lệ mỡ ngẫu nhiên
                'measured_at'         => Carbon::create(2026, 6, 1, rand(7, 10)),
            ]);
        }
    }
}
