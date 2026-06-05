<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CheckIn;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckInSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Dọn dẹp dữ liệu cũ
        Schema::disableForeignKeyConstraints();
        DB::table('check_ins')->truncate();
        Schema::enableForeignKeyConstraints();

        // 2. Lấy danh sách những người dùng là Hội viên (Member)
        $members = User::where('role', 'member')->get();

        if ($members->isEmpty()) {
            return;
        }
        $methods = ['QR Code'];
        // 3. Tạo 20 lượt điểm danh mẫu rải rác trong 30 ngày qua
        for ($i = 0; $i < 20; $i++) {
            CheckIn::create([
                'user_id'       => $members->random()->id,
                // Ngẫu nhiên từ ngày 1 đến ngày 5 tháng 6, rải rác từ 6h sáng đến 22h đêm
                'check_in_time' => Carbon::create(2026, 6, rand(1, 5), rand(6, 22), rand(0, 59)),
                'method'        => $methods[array_rand($methods)],
            ]);
        }
    }
}
