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

        $paymentMethods = ['COD', 'Bank_QR'];
        $statuses = ['Pending', 'Paid', 'Cancelled'];

        // --- 1. Tạo đơn hàng hỗn hợp SẢN PHẨM ---
        // Tăng số lượng lên 15 đơn để Dashboard trông đầy đặn
        for ($i = 1; $i <= 5; $i++) {
            $user = $users->random();
            $method = $paymentMethods[array_rand($paymentMethods)];
            // Tăng tỷ lệ đơn 'Paid' để thấy doanh thu trên biểu đồ
            $status = (rand(1, 10) > 3) ? 'Paid' : $statuses[array_rand($statuses)];
            
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => 0,
                'payment_status' => $status,
                'payment_method' => $method,
                // Ngẫu nhiên từ ngày 1 đến ngày 5 tháng 6
                'order_date' => Carbon::create(2026, 6, rand(1, 5), rand(8, 21), rand(0, 59)),
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
        for ($i = 0; $i < 15; $i++) { 
            if ($packages->isEmpty()) break;
            $user = $users->random();
            $method = $paymentMethods[array_rand($paymentMethods)];
            $package = $packages->random();
            $totalAmount = $package->price;

            // Logic tạo 3 trạng thái khác nhau cho Membership
            $type_rand = rand(1, 3);
            if ($type_rand == 1) {
                // TRƯỜNG HỢP 1: Đang hoạt động (Ngày đặt trong khoảng 01-05/06/2026)
                $orderDate = Carbon::create(2026, 6, rand(1, 5), rand(8, 21), rand(0, 59));
                $orderStatus = 'Paid';
                $membershipStatus = 'Active';
            } elseif ($type_rand == 2) {
                // TRƯỜNG HỢP 2: Đã hết hạn (Ngày đặt lùi về tháng trước)
                // Đặt lùi lại 40 ngày để chắc chắn gói 30 ngày đã hết hạn
                $orderDate = Carbon::create(2026, 6, rand(1, 5))->subDays(40);
                $orderStatus = 'Paid';
                $membershipStatus = 'Expired';
            } else {
                // TRƯỜNG HỢP 3: Đã hủy (Gán trạng thái đơn hàng Cancelled)
                $orderDate = Carbon::create(2026, 6, rand(1, 5), rand(8, 21), rand(0, 59));
                $orderStatus = 'Cancelled';
                $membershipStatus = 'Cancelled';
            }

            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'payment_status' => $orderStatus,
                'payment_method' => $method,
                'order_date' => $orderDate,
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
                'start_date' => $orderDate,
                'end_date' => (clone $orderDate)->addDays($package->duration_days),
                'status' => $membershipStatus
            ]);
        }

        // --- 3. Tạo các đơn hàng chỉ chứa LỚP HỌC ---
        for ($i = 0; $i < 10; $i++) { 
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
                // Ngẫu nhiên từ ngày 1 đến ngày 5 tháng 6
                'order_date' => Carbon::create(2026, 6, rand(1, 5), rand(8, 21), rand(0, 59)),
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
                // Ngẫu nhiên từ ngày 1 đến ngày 5 tháng 6
                'order_date' => Carbon::create(2026, 6, rand(1, 5), rand(8, 21), rand(0, 59)),
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
