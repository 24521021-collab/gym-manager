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
                'password' => Hash::make('12345678'), // Mật khẩu là 12345678
                'role' => 'admin',
            ]
        );

        // Tạo tài khoản Hội viên (User thường)
        User::updateOrCreate(
            ['email' => 'khachhang@gmail.com'],
            [
                'full_name' => 'Nguyễn Văn Khách',
                'password' => Hash::make('12345678'),
                'role' => 'member',
            ]
        );

        // Tạo tài khoản Huấn luyện viên (Trainer)
        User::updateOrCreate(
            ['email' => 'trainer1@gmail.com'],
            [
                'full_name' => 'PT Nguyễn Văn Cơ Bắp',
                'password' => Hash::make('12345678'),
                'role' => 'pt',
            ]
        );
        // Danh sách 10 Huấn luyện viên cá nhân (PT) chuyên nghiệp
        $ptsData = [
            ['full_name' => 'PT Nguyễn Văn Lực', 'email' => 'trainer1@gmail.com'],
            ['full_name' => 'PT Trần Minh Quân', 'email' => 'trainer2@gmail.com'],
            ['full_name' => 'PT Đặng Hùng Sơn', 'email' => 'trainer3@gmail.com'],
            ['full_name' => 'PT Phạm Hoàng Nam', 'email' => 'trainer4@gmail.com'],
            ['full_name' => 'PT Vũ Mai Anh', 'email' => 'trainer5@gmail.com'],
            ['full_name' => 'PT Lê Thu Thảo', 'email' => 'trainer6@gmail.com'],
            ['full_name' => 'PT Bùi Tiến Dũng', 'email' => 'trainer7@gmail.com'],
            ['full_name' => 'PT Hoàng Ngọc Bích', 'email' => 'trainer8@gmail.com'],
            ['full_name' => 'PT Đỗ Thúy Quỳnh', 'email' => 'trainer9@gmail.com'],
            ['full_name' => 'PT Hà Ngọc Bích', 'email' => 'trainer10@gmail.com'],
        ];
        foreach ($ptsData as $pt) {
            User::updateOrCreate(
                ['email' => $pt['email']],
                [
                    'full_name' => $pt['full_name'],
                    'password'  => Hash::make('12345678'), // Mật khẩu mẫu đồng bộ theo code cũ của bạn
                    'role'      => 'pt', // Đảm bảo quyền là 'pt'
                ]
                );
            }
            $newAdminsData = [
            ['full_name' => 'Admin Nguyễn Văn Thắng', 'email' => 'admin.thang@gmail.com'],
            ['full_name' => 'Admin Lê Thị Hồng Đào', 'email' => 'admin.hongdao@gmail.com'],
                ];
        foreach ($newAdminsData as $admin) {
        User::updateOrCreate(
            ['email' => $admin['email']],
            [
                'full_name' => $admin['full_name'],
                'password'  => Hash::make('12345678'),
                'role'      => 'admin',
            ]
            );
        }
        // Tạo tài khoản Hội viên ban đầu
    User::updateOrCreate(
        ['email' => 'khachhang@gmail.com'],
        [
            'full_name' => 'Nguyễn Văn Khách',
            'password' => Hash::make('12345678'),
            'role' => 'member',
        ]
    );

    // =========================================================================
    // TẠO THÊM 10 TÀI KHOẢN HỘI VIÊN (MEMBER) MỚI
    // =========================================================================
    $newMembersData = [
        ['full_name' => 'Phan Văn Hoàng', 'email' => 'phanhoang@gmail.com'],
        ['full_name' => 'Trần Thị Mỹ Linh', 'email' => 'mylinh.tran@gmail.com'],
        ['full_name' => 'Lê Hoàng Quốc Bảo', 'email' => 'quocbao.le@gmail.com'],
        ['full_name' => 'Nguyễn Diệu Hương', 'email' => 'dieuhuong@gmail.com'],
        ['full_name' => 'Đặng Anh Tuấn', 'email' => 'anhtuan.dang@gmail.com'],
        ['full_name' => 'Bùi Minh Tuyết', 'email' => 'minhtuyet@gmail.com'],
        ['full_name' => 'Phạm Đức Duy', 'email' => 'ducduy.pham@gmail.com'],
        ['full_name' => 'Vũ Thu Trang', 'email' => 'thutrang.vu@gmail.com'],
        ['full_name' => 'Đỗ Gia Bảo', 'email' => 'giabao.do@gmail.com'],
        ['full_name' => 'Ngô Phương Thảo', 'email' => 'phuongthao.ngo@gmail.com'],
    ];
    foreach ($newMembersData as $member) {
        User::updateOrCreate(
            ['email' => $member['email']],
            [
                'full_name' => $member['full_name'],
                'password'  => Hash::make('12345678'),
                'role'      => 'member',
            ]
        );
    }

    }
}