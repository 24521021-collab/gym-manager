<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use App\Models\GymPackage;
use App\Models\GymClass;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();
        Schema::enableForeignKeyConstraints();

        $users = User::where('role', 'member')->get();
        $products = Product::all();
        $packages = GymPackage::all();
        $classes = GymClass::all();

        if ($users->isEmpty()) return;

        $paymentMethods = ['COD', 'Bank_QR', 'VNPAY'];
        $statuses = ['Pending', 'Paid', 'Cancelled'];

        // Tạo 14 giao dịch mẫu rải rác trong 30 ngày qua
        for ($i = 1; $i <= 14; $i++) {
            $user = $users->random();
            $method = $paymentMethods[array_rand($paymentMethods)];
            // 10 đơn đầu tiên cho Paid để Dashboard hiển thị doanh thu cao
            $status = ($i <= 10) ? 'Paid' : $statuses[array_rand($statuses)];
            
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => 0,
                'payment_status' => $status,
                'payment_method' => $method,
                'order_date' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 24)),
            ]);

            $totalAmount = 0;
            $itemCount = rand(1, 3);

            for ($j = 0; $j < $itemCount; $j++) {
                $type = ['product', 'package', 'class'][rand(0, 2)];
                $itemId = null; $name = ''; $price = 0; $qty = 1;

                if ($type === 'product' && !$products->isEmpty()) {
                    $item = $products->random();
                    $itemId = $item->id; $name = $item->name; $price = $item->price; $qty = rand(1, 2);
                } elseif ($type === 'package' && !$packages->isEmpty()) {
                    $item = $packages->random();
                    $itemId = $item->id; $name = $item->package_name; $price = $item->price;
                } elseif (!$classes->isEmpty()) {
                    $item = $classes->random();
                    $itemId = $item->id; $name = $item->name; $price = $item->price;
                }

                if ($itemId) {
                    $subtotal = $price * $qty;
                    $totalAmount += $subtotal;
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => ($type === 'product') ? $itemId : null,
                        'item_type' => $type,
                        'item_id' => $itemId,
                        'name' => $name,
                        'price' => $price,
                        'quantity' => $qty,
                        'subtotal' => $subtotal
                    ]);
                }
            }
            $order->update(['total_amount' => $totalAmount]);
        }
    }
}
