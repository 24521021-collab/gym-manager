<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User; // Nhớ dùng Model User
use Illuminate\Support\Facades\Hash; // Để mã hóa mật khẩu

class DatabaseSeeder extends Seeder
{
    //sau nay phai fix lai thanh user.seeder
    public function run(): void
    {
        $this->call([
            ProductSeeder::class,
        ]);
    }
}
    // 1. Tạo tài khoản Admin
        ##User::create([
         ##   'full_name' => 'Quản trị viên',
         ##   'email' => 'adminnew@gmail.com',
         ##   'password' => Hash::make('123456'), // Mật khẩu là 123456
         ##   'role' => 'admin',
        ##]);

        // 2. Tạo tài khoản Hội viên (User thường)
        ## User::create([
           ## 'full_name' => 'Nguyễn Văn Khách',
           ## 'email' => 'khachhang@gmail.com',
           ## 'password' => Hash::make('123456'),
           // 'role' => 'member',
        // ]);