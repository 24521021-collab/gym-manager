<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GymClass;
use App\Models\PtProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class GymClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tạo các HLV mẫu (PT)
        // Chúng ta tạo User trước, sau đó tạo PtProfile tương ứng
        $pt1 = User::firstOrCreate(
            ['email' => 'pt_anh@gympro.com'],
            [
                'full_name' => 'Nguyễn Văn Anh (HLV Yoga)',
                'password' => Hash::make('12345678'),
                'role' => 'pt'
            ]
        );

        $profile1 = PtProfile::firstOrCreate(
            ['user_id' => $pt1->id],
            [
                'specialization' => 'Yoga & Meditation',
                'bio' => 'Chuyên gia Yoga với hơn 5 năm kinh nghiệm giảng dạy.',
                'rating' => 5.0
            ]
        );

        $pt2 = User::firstOrCreate(
            ['email' => 'pt_cuong@gympro.com'],
            [
                'full_name' => 'Trần Mạnh Cường (HLV Thể Hình)',
                'password' => Hash::make('12345678'),
                'role' => 'pt'
            ]
        );

        $profile2 = PtProfile::firstOrCreate(
            ['user_id' => $pt2->id],
            [
                'specialization' => 'Bodybuilding & Powerlifting',
                'bio' => 'Chuyên đào tạo các vận động viên thể hình chuyên nghiệp.',
                'rating' => 4.8
            ]
        );

        // 2. Tạo danh sách lớp học mẫu
        $classes = [
            [
                'name' => 'Yoga Chào Ngày Mới',
                'pt_id' => $profile1->id,
                'max_capacity' => 20,
                'schedule_time' => Carbon::now()->addDays(1)->setTime(6, 30),
                'room_name' => 'Phòng Studio A',
            ],
            [
                'name' => 'Zumba Sôi Động',
                'pt_id' => $profile1->id,
                'max_capacity' => 25,
                'schedule_time' => Carbon::now()->addDays(1)->setTime(18, 00),
                'room_name' => 'Phòng Studio B',
            ],
            [
                'name' => 'Tập Cơ Bụng Chuyên Sâu',
                'pt_id' => $profile2->id,
                'max_capacity' => 12,
                'schedule_time' => Carbon::now()->addDays(2)->setTime(17, 30),
                'room_name' => 'Khu vực Tạ',
            ],
        ];

        foreach ($classes as $classData) {
            GymClass::create($classData);
        }
    }
}