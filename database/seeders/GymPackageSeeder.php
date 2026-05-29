<?php

namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
class GymPackageSeeder extends Seeder
{
    public function run(): void 
{
    \App\Models\GymPackage::create([
        'package_name' => 'Gói Cơ Bản',
        'duration_days' => 30,
        'price' => 500000,
        'description' => 'Tập luyện tự do'
    ]);

    \App\Models\GymPackage::create([
        'package_name' => 'Gói Nâng Cao',
        'duration_days' => 30,
        'price' => 900000,
        'description' => 'Tập luyện với PT'
    ]);
       \App\Models\GymPackage::create([
        'package_name' => 'Gói Elite Vip',
        'duration_days' => 30,
        'price' => 1000000,
        'description' => 'Tập toàn diện , có chế độ dinh dưỡng'
    ]);
       \App\Models\GymPackage::create([
        'package_name' => 'Gói Nâng Cao Siu Vip',
        'duration_days' => 30,
        'price' => 1900000,
        'description' => 'Tập luyện với PT, có 1-1 tại giường '
    ]);
    
    }
}