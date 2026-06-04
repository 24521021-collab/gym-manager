<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Product;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy danh sách người dùng (Hội viên), PT và Sản phẩm hiện có
        $customers = User::where('role', 'member')->get();
        $pts = User::where('role', 'pt')->get();
        $products = Product::all();

        // Nếu chưa có khách hàng thì không thể tạo review
        if ($customers->isEmpty()) {
            return;
        }

        $reviewData = [
            ['comment' => 'HLV hướng dẫn cực kỳ chuyên nghiệp và nhiệt tình. Bài tập rất hiệu quả cho việc giảm cân.', 'rating' => 5, 'type' => 'pt'],
            ['comment' => 'PT có tâm, chỉnh sửa tư thế rất kỹ giúp mình tránh được chấn thương.', 'rating' => 5, 'type' => 'pt'],
            ['comment' => 'Sản phẩm rất phù hợp với mình', 'rating' => 5, 'type' => 'product'],
            ['comment' => 'Sản phẩm chất lượng tốt, đóng gói cẩn thận, dùng rất ổn định.', 'rating' => 4, 'type' => 'product'],
            ['comment' => 'Sản phẩm đóng gói cẩn thận, giao hàng nhanh chóng.', 'rating' => 5, 'type' => 'product'],
            ['comment' => 'Chương trình tập luyện PT thiết kế riêng rất phù hợp với thể trạng của mình.', 'rating' => 5, 'type' => 'pt'],
        ];

        foreach ($reviewData as $data) {
            $reviewer = $customers->random();
            $targetId = null;
            $targetType = null;

            if ($data['type'] === 'pt' && $pts->isNotEmpty()) {
                $targetId = $pts->random()->id;
                $targetType = User::class;
            } elseif ($data['type'] === 'product' && $products->isNotEmpty()) {
                $targetId = $products->random()->id;
                $targetType = Product::class;
            }

            if ($targetId) {
                Review::updateOrCreate(
                    [
                        'user_id' => $reviewer->id,
                        'reviewable_id' => $targetId,
                        'reviewable_type' => $targetType,
                    ],
                    [
                        'rating' => $data['rating'],
                        'comment' => $data['comment'],
                    ]
                );
            }
        }
    }
}
