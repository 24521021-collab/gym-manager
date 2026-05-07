<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product; // Nhớ nạp Model Product vào nhé

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Tạ tay cao su 5kg',
                'sku' => 'TA-CS-05',
                'price' => 250000,
                'stock_quantity' => 15,
            ],
            [
                'name' => 'Tạ tay cao su 10kg',
                'sku' => 'TA-CS-10',
                'price' => 450000,
                'stock_quantity' => 10,
            ],
            [
                'name' => 'Thảm tập Yoga Adidas',
                'sku' => 'THAM-AD-01',
                'price' => 600000,
                'stock_quantity' => 20,
            ],
            [
                'name' => 'Bình nước Gym Warrior 1L',
                'sku' => 'BINH-WR-01',
                'price' => 150000,
                'stock_quantity' => 50,
            ],
            [
                'name' => 'Dây kháng lực (Set 5 dây)',
                'sku' => 'DAY-KL-SET5',
                'price' => 320000,
                'stock_quantity' => 5, // Sản phẩm này sắp hết hàng để Long test thông báo lỗi
            ],
            [
                'name' => 'Găng tay tập Gym Nam/Nữ',
                'sku' => 'GANG-GYM-01',
                'price' => 180000,
                'stock_quantity' => 0, // Sản phẩm này hết hàng để Long test nút "Hết hàng"
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}