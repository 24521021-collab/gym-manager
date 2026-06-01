<?php

namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Seeder;

class GymPackageSeeder extends Seeder
{
    public function run(): void 
{
    Schema::disableForeignKeyConstraints();
    DB::table('gym_packages')->truncate();
    Schema::enableForeignKeyConstraints();

    \App\Models\GymPackage::create([
        'package_name' => 'Kỳ Học Sơ Cấp',
        'duration_days' => 30,
        'price' => 300000,
        'description' => "• Tập luyện khung giờ thấp điểm (6:00 - 16:00)\n• Full máy móc Cardio & Weight Training\n• Miễn phí phòng tắm & máy sấy tóc"
    ]);

    \App\Models\GymPackage::create([
        'package_name' => 'Học Kỳ Khỏe Mạnh',
        'duration_days' => 90,
        'price' => 800000,
        'description' => "• Không giới hạn khung giờ (6:00 - 22:00)\n• Đo chỉ số InBody miễn phí đầu kỳ\n• Tặng 01 bình nước thể thao KOR"
    ]);

    \App\Models\GymPackage::create([
        'package_name' => 'Bảo Lưu Kết Quả',
        'duration_days' => 180,
        'price' => 1500000,
        'description' => "• Toàn quyền lợi không giới hạn giờ giấc\n• Hỗ trợ bảo lưu thẻ 15 ngày mùa thi/Tết\n• Ưu đãi chỉ 250.000 VNĐ/tháng"
    ]);
    \App\Models\GymPackage::create([
    'package_name' => 'Gói Xả Stress',
    'duration_days' => 30,
    'price' => 550000,
    'description' => "• Tập luyện không giới hạn thời gian (Rất hợp cho người hay tăng ca, thích tập muộn).\n• Miễn phí sử dụng tủ locker khóa từ an toàn để đồ công sở.\n• Đo và phân tích chỉ số cơ thể InBody hàng tháng."
    ]);

    \App\Models\GymPackage::create([
    'package_name' => 'Gói Dáng Chuẩn Công Sở',
    'duration_days' => 180,
    'price' => 2700000,
    'description' => "• Full quyền lợi gói 1 tháng.\n• Tặng thêm: 01 buổi hướng dẫn tư thế tập luyện độc quyền cùng PT.\n • Được phép bảo lưu thẻ tối đa 20 ngày khi đi công tác."
    ]);

    \App\Models\GymPackage::create([
    'package_name' => 'Gói Cam Kết Tác Phong',
    'duration_days' => 365,
    'price' => 4800000,
    'description' => "• Full quyền lợi cao cấp nhất của khu vực tự tập.\n• Được phép dẫn theo 1 người bạn đi tập cùng 2 lần/tháng (Người đi cùng miễn phí vé).\n• Đóng băng thẻ lên đến 45 ngày.\n• Tặng áo thun và túi tập gym cao cấp."
    ]);
    \App\Models\GymPackage::create([
    'package_name' => 'Gói Cam Kết Tác Phong',
    'duration_days' => 365,
    'price' => 4800000,
    'description' => "• Full quyền lợi cao cấp nhất của khu vực tự tập.\n• Được phép dẫn theo 1 người bạn đi tập cùng 2 lần/tháng (Người đi cùng miễn phí vé).\n• Đóng băng thẻ lên đến 45 ngày.\n• Tặng áo thun và túi tập gym cao cấp."
    ]);
    }
}