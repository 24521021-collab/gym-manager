<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PTProfile;
use App\Models\User;

class PTProfileSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy các user có role là pt(Giả sử bạn đã seed User trước)
        $trainers = User::where('role', 'pt')->get();
        if ($trainers->isEmpty()) {
            return;
        }
        foreach ($trainers as $index => $trainer) {
            PTProfile::create([
                'user_id' => $trainer->id,
                'bio' => 'Huấn luyện viên chuyên nghiệp với hơn ' . (5 + $index) . ' năm kinh nghiệm.',
                'specialization' => match ($index % 3) {
                    0 => 'Thể hình',
                    1 => 'Yoga/Pilate',
                    default => 'Võ Thuật/Boxing',
                },
                'rating' => 4.5 + ($index * 0.1),
                'image' => 'pt.jpg',
                
                // Thông tin hoa hồng và lịch dạy gộp vào profile
                'commission' => 150000, // 150k mỗi buổi dạy
            ]);
        }
        // 1. Định nghĩa bộ thông tin chi tiết riêng biệt cho từng PT dựa theo email đăng ký
        $ptProfilesData = [
            'trainer1@gmail.com' => [
                'specialization'       => 'Thể hình',
                'bio'                  => 'Hơn 5 năm kinh nghiệm huấn luyện thi đấu. Chuyên thiết kế lộ trình siết cơ và thay đổi vóc dáng toàn diện nhanh chóng.',
                'commission' => 150000,
                'image' => 'pt-1.jpg',
            ],
            'trainer2@gmail.com' => [
                'specialization'       => 'Yoga/Pilate',
                'bio'                  => 'Chuyên gia dinh dưỡng và giảm mỡ chuyên sâu cho nữ, đặc biệt là nhân viên văn phòng và mẹ bỉm sữa.',
                'commission' => 180000,
                'image' => 'pt-2.jpg',
            ],
            'trainer3@gmail.com' => [
                'specialization'       => 'Võ Thuật/Boxing',
                'bio'                  => 'Cựu vận động viên Kickboxing quốc gia. Lối tập năng động, nghiêm túc và luôn truyền năng lượng tích cực.',
                'commission' => 200000,
                'image' => 'pt-3.jpg',
            ],
            'trainer4@gmail.com' => [
                'specialization'       => 'Thể hình',
                'bio'                  => 'Tốt nghiệp Đại học Thể dục Thể thao. Chuyên khắc phục các lỗi chấn thương và tối ưu lực đẩy/kéo cơ bản.',
                'commission' => 160000,
                'image' => 'pt-4.jpg',
        
            ],
            'trainer5@gmail.com' => [
                'specialization'       => 'Võ Thuật/Boxing',
                'bio'                  => 'Giúp học viên làm chủ cơ thể, cải thiện sự linh hoạt, dẻo dai và sở hữu vóc dáng thon gọn tự nhiên.',
                'commission' => 150000,
                'image' => 'pt-5.jpg',
            ],
            'trainer6@gmail.com' => [
                'specialization'       => 'Thể hình',
                'bio'                  => 'Sở hữu chứng chỉ PT Quốc tế ISSA. Đồng hành cùng học viên từ chế độ ăn khắt khe đến giáo án chuẩn mực.',
                'commission' => 250000,
                'image' => 'pt-6.jpg',

            ],
            'trainer7@gmail.com' => [
                'specialization'       => 'Võ Thuật/Boxing',
                'bio'                  => 'Vui vẻ, nhiệt tình và nhiều năng lượng. Cam kết giúp học viên giải tỏa stress và đốt cháy tối đa calo.',
                'commission' => 170000,
                'image' => 'pt-7.jpg',
            ],
            'trainer8@gmail.com' => [
                'specialization'       => 'Yoga/Pilate',
                'bio'                  => 'Chuyên sâu về giải phẫu học cơ thể. Phù hợp cho người lớn tuổi hoặc người đang trong giai đoạn hồi phục chấn thương.',
                'commission' => 220000,
                'image' => 'pt-8.jpg',
            ],
            'trainer9@gmail.com' => [
                'specialization'       => 'Thể hình',
                'bio'                  => 'Xây dựng đường cong cơ thể quyến rũ cho phái nữ bằng phương pháp khoa học, không bỏ cuộc.',
                'commission' => 190000,
                'image' => 'pt-9.jpg',

            ],
            'trainer10@gmail.com' => [
                'specialization'       => 'Thể hình',
                'bio'                  => 'Từng là người gầy gò, tôi hiểu rõ lộ trình ăn uống kết hợp tập luyện hiệu quả nhất để bứt phá cân nặng.',
                'commission' => 160000,
                'image' => 'pt-10.jpg',
            ],
        ];
        // 2. Duyệt mảng dữ liệu để tìm User tương ứng và cập nhật cấu hình hồ sơ PT
        foreach ($ptProfilesData as $email => $profile) {
            // Tìm User bằng email đã được tạo từ UserSeeder
            $user = User::where('email', $email)->first();
            if ($user) {
                PTProfile::updateOrCreate(
                    ['user_id' => $user->id], // Khóa ngoại liên kết
                    [
                        'bio'                  => $profile['bio'],
                        'specialization'       => $profile['specialization'],
                        'commission' =>$profile['commission'],
                        'image' => $profile['image'],
                    ]
                );
            }
        }
    }
}
