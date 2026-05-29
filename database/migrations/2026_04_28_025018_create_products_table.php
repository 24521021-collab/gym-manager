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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable()->default('default-product.jpg');
            $table->string('name');
            $table->string('type')->nullable(); // Thêm cột thể loại (Thực phẩm bổ sung / Phụ kiện)
            $table->text('description')->nullable(); // Thêm cột mô tả sản phẩm theo ý bạn
            $table->string('sku')->unique()->nullable(); // Cho phép nullable để tránh lỗi dữ liệu Seeder cũ
            $table->decimal('price', 12, 2); // Kiểu decimal chuẩn cho tiền tệ
            $table->integer('stock_quantity')->default(10); // Đặt mặc định là 10 để tránh lỗi trống dữ liệu
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};