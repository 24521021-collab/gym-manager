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
                if ($products->isEmpty()) break;
                $product = $products->random();
                $qty = rand(1, 2);
                $subtotal = $product->price * $qty;
                $totalAmount += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'item_type' => 'product',
                    'item_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $qty,
                    'subtotal' => $subtotal
                ]);
            }
            $order->update(['total_amount' => $totalAmount]);
        }

        // --- 2. Tạo các đơn hàng chỉ chứa GÓI TẬP ---
        for ($i = 0; $i < 5; $i++) { // Tạo 5 đơn hàng chỉ chứa gói tập
            if ($packages->isEmpty()) break;
            $user = $users->random();
            $method = $paymentMethods[array_rand($paymentMethods)];
            $status = 'Paid'; // Giả định đã thanh toán cho các đơn hàng gói tập

            $package = $packages->random();
            $totalAmount = $package->price;

            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'payment_status' => $status,
                'payment_method' => $method,
                'order_date' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 24)),
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => null,
                'item_type' => 'package',
                'item_id' => $package->id,
                'name' => $package->package_name,
                'price' => $package->price,
                'quantity' => 1,
                'subtotal' => $package->price
            ]);

            // Tạo bản ghi Membership tương ứng
            \App\Models\Membership::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'start_date' => $order->order_date,
                'end_date' => (clone $order->order_date)->addDays($package->duration_days),
                'status' => 'Active'
            ]);
        }

        // --- 3. Tạo các đơn hàng chỉ chứa LỚP HỌC ---
        for ($i = 0; $i < 5; $i++) { // Tạo 5 đơn hàng chỉ chứa lớp học
            if ($classes->isEmpty()) break;
            $user = $users->random();
            $method = $paymentMethods[array_rand($paymentMethods)];
            $status = 'Paid'; // Giả định đã thanh toán cho các đơn hàng lớp học

            $class = $classes->random();
            $totalAmount = $class->price;

            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'payment_status' => $status,
                'payment_method' => $method,
                'order_date' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 24)),
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => null,
                'item_type' => 'class',
                'item_id' => $class->id,
                'name' => $class->name,
                'price' => $class->price,
                'quantity' => 1,
                'subtotal' => $class->price
            ]);

            // Tạo bản ghi Booking tương ứng
            \App\Models\Booking::create([
                'user_id' => $user->id,
                'class_id' => $class->id,
                'booking_date' => $order->order_date,
                'status' => 'confirmed'
            ]);
        }

        // --- 4. Tạo một số đơn hàng hỗn hợp với trạng thái Pending/Cancelled để đa dạng dữ liệu ---
        for ($i = 0; $i < 3; $i++) { // Tạo 3 đơn hàng hỗn hợp
            $user = $users->random();
            $method = $paymentMethods[array_rand($paymentMethods)];
            $status = ['Pending', 'Cancelled'][array_rand(['Pending', 'Cancelled'])]; // Trạng thái chờ hoặc hủy

            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => 0,
                'payment_status' => $status,
                'payment_method' => $method,
                'order_date' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 24)),
            ]);

            $totalAmount = 0;
            $itemCount = rand(1, 2); // 1 đến 2 mặt hàng trong đơn

            for ($j = 0; $j < $itemCount; $j++) {
                $type = ['product', 'package', 'class'][rand(0, 2)]; // Chọn ngẫu nhiên loại mặt hàng
                $itemId = null; $name = ''; $price = 0; $qty = 1;

                if ($type === 'product' && !$products->isEmpty()) {
                    $item = $products->random();
                    $itemId = $item->id; $name = $item->name; $price = $item->price; $qty = rand(1, 2);
                } elseif ($type === 'package' && !$packages->isEmpty()) {
                    $item = $packages->random();
                    $itemId = $item->id; $name = $item->package_name; $price = $item->price;
                } elseif ($type === 'class' && !$classes->isEmpty()) {
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
            $order->update(['total_amount' => $totalAmount]); // Cập nhật tổng tiền cho đơn hàng
        }
    }
}
