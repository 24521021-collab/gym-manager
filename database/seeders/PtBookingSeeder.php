<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PtBooking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PtBookingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Dọn dẹp dữ liệu cũ
        Schema::disableForeignKeyConstraints();
        DB::table('pt_bookings')->truncate();
        Schema::enableForeignKeyConstraints();

        // 2. Lấy danh sách khách hàng và PT
        $customers = User::where('role', 'member')->get();
        $pts = User::where('role', 'pt')->get();

        if ($customers->isEmpty() || $pts->isEmpty()) {
            return;
        }

        // 3. Tạo 16 bản ghi mẫu
        for ($i = 1; $i <= 16; $i++) {
            $customer = $customers->random();
            $pt = $pts->random();
            
            // Phân bổ ngày từ mùng 1 đến mùng 5 tháng 6 để có cả lịch cũ và lịch mới
            $date = Carbon::create(2026, 6, rand(1, 5));
            
            // Ngẫu nhiên giờ bắt đầu từ 6h sáng đến 20h tối
            $startHour = rand(6, 20);
            $startTime = Carbon::createFromTime($startHour, 0, 0);
            $endTime = (clone $startTime)->addHour(); // Mỗi ca tập 1 tiếng

            // Đảm bảo có đủ 4 trạng thái: pending, confirmed, completed, cancelled
            $allStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];
            $status = $allStatuses[($i - 1) % 4]; // Chia đều 4 trạng thái cho 16 bản ghi

            PtBooking::create([
                'customer_id'  => $customer->id,
                'pt_id'        => $pt->id,
                'booking_date' => $date->format('Y-m-d'),
                'start_time'   => $startTime->format('H:i:s'),
                'end_time'     => $endTime->format('H:i:s'),
                'price'        => 300000,
                'status'       => $status,
                'note'         => "Yêu cầu cho buổi tập số $i: Khách hàng cần tập trung vào " . (rand(0, 1) ? 'giảm mỡ bụng' : 'tăng sức bền') . ".",
            ]);
        }
    }
}
