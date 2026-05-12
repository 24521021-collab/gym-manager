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
        Schema::create('orders', function (Blueprint $table) {
        $table->id(); 
    // Khóa ngoại nối với bảng users
        $table->foreignId('user_id')->constrained('user')->onDelete('cascade');
    
        // Tổng tiền của đơn hàng
        $table->decimal('total_amount', 12, 2)->default(0); 
    
        // Trạng thái thanh toán
        $table->string('payment_status')->default('Pending'); // Paid, Pending

        $table->string('payment_method')->default('COD');//cod, vnpay;
    
         $table->timestamp('order_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
