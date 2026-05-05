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
}
}