<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GymClass;
use App\Models\PTProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GymClassSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tắt bảo vệ khóa ngoại tạm thời và xóa dữ liệu cũ
        Schema::disableForeignKeyConstraints();
        DB::table('gym_classes')->truncate();
        Schema::enableForeignKeyConstraints();

        $pts = PTProfile::all();
        if ($pts->isEmpty()) {
            return;
        }

        $data = [
            [
                'name' => 'Lớp Gym Cơ Bản Tăng Cơ',
                'pt_id' => $pts[0]->id,
                'max_capacity' => 4,
                'description' => 'Lớp học tập trung vào các kỹ thuật căn bản giúp kích thích phát triển cơ bắp, phù hợp cho người mới bắt đầu hoặc muốn củng cố nền tảng.',
                'total_sessions' => 12,
                'price' => 2400000,
                'image' => 'gym-class.jpg',
            ],
            [
                'name' => 'Yoga Phục Hồi Chuyên Sâu',
                'pt_id' => $pts->get(1)->id ?? $pts[0]->id,
                'max_capacity' => 4,
                'description' => 'Tập trung vào hơi thở và các tư thế thư giãn, giúp giảm căng thẳng và phục hồi năng lượng sau những giờ làm việc mệt mỏi.',
                'total_sessions' => 24,
                'price' => 3500000,
                'image' => 'yoga-class.jpg',
            ],
            [
                'name' => 'Kickboxing Đốt Mỡ Cấp Tốc',
                'pt_id' => $pts->get(2)->id ?? $pts[0]->id,
                'max_capacity' => 4,
                'description' => 'Sự kết hợp hoàn hảo giữa võ thuật và cardio cường độ cao. Đốt cháy hàng nghìn calo trong mỗi buổi tập.',
                'total_sessions' => 10,
                'price' => 1800000,
                'image' => 'boxing-class.jpg',
            ],
            [
                'name' => 'HIIT - Thử Thách Sức Bền',
                'pt_id' => $pts->get(3)->id ?? $pts[0]->id,
                'max_capacity' => 4,
                'description' => 'Tăng cường sức mạnh tim mạch và sự dẻo dai thông qua các bài tập cường độ cao ngắt quãng.',
                'total_sessions' => 15,
                'price' => 2000000,
                'image' => 'HIIT-class.jpg',
            ],
            [
                'name' => 'Pilates Cân Bằng Cơ Thể',
                'pt_id' => $pts->get(4)->id ?? $pts[0]->id,
                'max_capacity' => 4,
                'description' => 'Tập trung vào vùng lõi (core) để cải thiện tư thế, sự linh hoạt và sức mạnh toàn thân một cách tinh tế.',
                'total_sessions' => 20,
                'price' => 4200000,
                'image' => 'pilate-class.jpg',
            ],
            [
                'name' => 'Lớp đạp xe spinning đốt mỡ ',
                'pt_id' => $pts->get(4)->id ?? $pts[0]->id,
                'max_capacity' => 4,
                'description' => 'Đạp xe nhịp điệu tốc độ cao trên nền nhạc bốc lửa giúp tối ưu hệ tim mạch và đốt mỡ thừa siêu tốc cho nhóm tự hẹn lịch',
                'total_sessions' => 20,
                'price' => 4200000,
                'image' => 'spinning-class.jpg',
            ],
        ];

        foreach ($data as $item) {
            GymClass::create($item);
        }
    }
}