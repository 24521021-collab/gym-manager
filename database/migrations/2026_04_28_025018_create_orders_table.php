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
    // Nếu User bị xóa, đơn hàng vẫn nên giữ lại để làm báo cáo tài chính (dùng restrict hoặc null)
    // Nhưng ở đây mình để mặc định theo thiết kế của bạn là nối với users.id
        $table->foreignId('user_id')->constrained('user')->onDelete('cascade');
    
        // Tổng tiền của đơn hàng
        $table->decimal('total_amount', 12, 2)->default(0); 
    
        // Trạng thái thanh toán
        $table->string('payment_status')->default('Pending'); // Paid, Pending
    
    // Laravel tự có cột created_at thay cho order_date (cực kỳ tiện lợi)
    // Nhưng nếu bạn muốn đúng tên order_date như thiết kế:
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
