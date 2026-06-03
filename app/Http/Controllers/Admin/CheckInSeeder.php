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

        $methods = ['QR Code', 'RFID', 'Fingerprint'];

        // 3. Tạo 300 lượt điểm danh mẫu rải rác trong 30 ngày qua
        for ($i = 0; $i < 300; $i++) {
            CheckIn::create([
                'user_id'       => $members->random()->id,
                'check_in_time' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
                'method'        => $methods[array_rand($methods)],
            ]);
        }
    }
}
