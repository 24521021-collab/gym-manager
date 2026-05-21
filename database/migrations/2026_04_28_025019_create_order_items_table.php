<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->string('item_type'); // Để phân biệt: 'product', 'package', 'class'
            $table->unsignedBigInteger('item_id'); // ID của sản phẩm/gói/lớp tương ứng
            $table->string('name'); // Lưu tên mặt hàng tại thời điểm mua (để làm hóa đơn)
            $table->decimal('price', 12, 2); // Lưu giá tại thời điểm mua
            $table->integer('quantity');// so luong mua
            $table->decimal('subtotal',12,2);//thanh tien
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
