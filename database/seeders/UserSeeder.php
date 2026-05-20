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

        // Tạo tài khoản Huấn luyện viên (Trainer)
        User::updateOrCreate(
            ['email' => 'trainer1@gmail.com'],
            [
                'full_name' => 'PT Nguyễn Văn Cơ Bắp',
                'password' => Hash::make('123456'),
                'role' => 'pt',
            ]
        );
        // Danh sách 10 Huấn luyện viên cá nhân (PT) chuyên nghiệp
        $ptsData = [
            ['full_name' => 'PT Nguyễn Văn Cơ Bắp', 'email' => 'trainer1@gmail.com'],
            ['full_name' => 'PT Trần Minh Quân', 'email' => 'trainer2@gmail.com'],
            ['full_name' => 'PT Lê Thu Thảo', 'email' => 'trainer3@gmail.com'],
            ['full_name' => 'PT Phạm Hoàng Nam', 'email' => 'trainer4@gmail.com'],
            ['full_name' => 'PT Vũ Mai Anh', 'email' => 'trainer5@gmail.com'],
            ['full_name' => 'PT Đặng Hùng Sơn', 'email' => 'trainer6@gmail.com'],
            ['full_name' => 'PT Hoàng Ngọc Bích', 'email' => 'trainer7@gmail.com'],
            ['full_name' => 'PT Bùi Tiến Dũng', 'email' => 'trainer8@gmail.com'],
            ['full_name' => 'PT Đỗ Thúy Quỳnh', 'email' => 'trainer9@gmail.com'],
            ['full_name' => 'PT Nguyễn Hải Đăng', 'email' => 'trainer10@gmail.com'],
        ];
        foreach ($ptsData as $pt) {
            User::updateOrCreate(
                ['email' => $pt['email']],
                [
                    'full_name' => $pt['full_name'],
                    'password'  => Hash::make('123456'), // Mật khẩu mẫu đồng bộ theo code cũ của bạn
                    'role'      => 'pt', // Đảm bảo quyền là 'pt'
                ]
            );
        }
    }
}