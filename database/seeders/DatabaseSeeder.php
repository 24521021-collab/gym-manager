<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Nhớ dùng Model User
use Illuminate\Support\Facades\Hash; // Để mã hóa mật khẩu

class DatabaseSeeder extends Seeder
{
    //sau nay phai fix lai thanh user.seeder
    public function run(): void{
        // Thứ tự gọi seeder quan trọng vì quan hệ khóa ngoại
        $this->call([
            UserSeeder::class,
            ProductSeeder::class,
            GymPackageSeeder::class,
            PTProfileSeeder::class,
            GymClassSeeder::class,
            OrderSeeder::class,
            PtBookingSeeder::class,
        ]);
    }
}