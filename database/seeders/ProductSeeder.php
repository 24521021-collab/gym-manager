<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product; // Nhớ nạp Model Product
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema; // <--- THÊM DÒNG NÀY VÀO TRÊN CÙNG

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        //1. Tắt bảo vệ khóa ngoại tạm thời
        Schema::disableForeignKeyConstraints();

        // 2. Xóa sạch dữ liệu cũ và reset ID về 1
        DB::table('products')->truncate();

        // 3. Bật lại bảo vệ khóa ngoại ngay lập tức cho an toàn
        Schema::enableForeignKeyConstraints();

        $products = [
            [
                'name' => 'Tạ tay cao su 5kg',
                'sku' => 'TA-CS-05',
                'price' => 250000,
                'stock_quantity' => 15,
                //'image' => 'products/dumbbell-5kg.jpg',
            ],
            [
                'name' => 'Tạ tay cao su 10kg',
                'sku' => 'TA-CS-10',
                'price' => 450000,
                'stock_quantity' => 10,
               // 'image' => 'products/dumbbell-10kg.jpg',
            ],
            [
                'name' => 'Thảm tập Yoga Adidas',
                'sku' => 'THAM-AD-01',
                'price' => 600000,
                'stock_quantity' => 20,
                //'image' => 'products/yoga-mat.jpg',
            ],
            [
                'name' => 'Bình nước Gym Warrior 1L',
                'image' => 'default-product.jpg',
                'sku' => 'BINH-WR-01',
                'price' => 150000,
                'stock_quantity' => 50,
            ],
            [
                'name' => 'Dây kháng lực (Set 5 dây)',
                'sku' => 'DAY-KL-SET5',
                'price' => 320000,
                'stock_quantity' => 5,
                //'image' => 'products/resistance-bands.jpg',
            ],
            [
                'name' => 'Găng tay tập Gym Nam/Nữ',
                'sku' => 'GANG-GYM-01',
                'price' => 180000,
                'stock_quantity' => 0,
               // 'image' => 'products/gym-gloves.jpg',
            ],
            [
                'name' => 'Ghế tập tạ đa năng',
                'sku' => 'GHE-TA-01',
                'price' => 250000,
                'stock_quantity' => 10,
                //'image' => 'products/weight-bench.jpg',
            ],
            [
                'name' => 'Đòn tạ tay Ziczac 1.2m',
                'sku' => 'DON-ZIC-01',
                'price' => 350000,
                'stock_quantity' => 20,
              //  'image' => 'products/ez-curl-bar.jpg',
            ],
            [
                'name' => 'Con lăn tập bụng 4 bánh',
                'sku' => 'LAN-BUNG-04',
                'price' => 150000,
                'stock_quantity' => 45,
               // 'image' => 'products/ab-roller.jpg',
            ],
            [
                'name' => 'Xà đơn gắn cửa thông minh',
                'sku' => 'XA-DON-01',
                'price' => 220000,
                'stock_quantity' => 30,
               // 'image' => 'products/pull-up-bar.jpg',
            ],
            [
                'name' => 'Dây nhảy thể lực lõi thép',
                'sku' => 'DAY-NHAY-01',
                'price' => 850000,
                'stock_quantity' => 100,
             //   'image' => 'products/jump-rope.jpg',
            ],
            [
                'name' => 'Băng quấn cổ tay xỏ ngón',
                'sku' => 'BANG-TAY-01',
                'price' => 65000,
                'stock_quantity' => 60,
             //   'image' => 'products/wrist-wrap.jpg',
            ],
            [
                'name' => 'Đai lưng cứng gánh tạ (Squat)',
                'sku' => 'DAI-LUNG-01',
                'price' => 380000,
                'stock_quantity' => 15,
              //  'image' => 'products/lifting-belt.jpg',
            ],
            [
                'name' => 'Bánh tạ gang 5kg',
                'sku' => 'BANH-GANG-05',
                'price' => 120000,
                'stock_quantity' => 50,
              //  'image' => 'products/weight-plate.jpg',
            ],
            [
                'name' => 'Gạch tập Yoga EVA siêu nhẹ',
                'sku' => 'GACH-YOGA-01',
                'price' => 45000,
                'stock_quantity' => 80,
              //  'image' => 'products/yoga-block.jpg',
            ],
            [
                'name' => 'Túi trống thể thao Gym Pro',
                'sku' => 'TUI-GYM-01',
                'price' => 250000,
                'stock_quantity' => 25,
               // 'image' => 'products/punching-bag.jpg',
            ],
             [
                'name' => 'test ',
                'sku' => 'test-01',
                'price' => 5000,
                'stock_quantity' => 100,
                //'image' => null,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}