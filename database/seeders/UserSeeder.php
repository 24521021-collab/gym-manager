<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo tài khoản Admin
        User::updateOrCreate(
            ['email' => 'adminnew@gmail.com'],
            [
                'full_name' => 'Quản trị viên',
                'password' => Hash::make('123456'), // Mật khẩu là 123456
                'role' => 'admin',
            ]
        );

        // Tạo tài khoản Hội viên (User thường)
        User::updateOrCreate(
            ['email' => 'khachhang@gmail.com'],
            [
                'full_name' => 'Nguyễn Văn Khách',
                'password' => Hash::make('123456'),
                'role' => 'member',
            ]
        );
    }
}