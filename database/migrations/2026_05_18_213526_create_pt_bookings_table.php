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
    Schema::create('pt_bookings', function (Blueprint $table) {
        $table->id();
        // Khách hàng đặt lịch (liên kết tới ID trong bảng users)
        $table->foreignId('customer_id')->constrained('user')->onDelete('cascade');
        // PT được chọn (liên kết tới ID trong bảng users)
        $table->foreignId('pt_id')->constrained('user')->onDelete('cascade');
        
        $table->date('booking_date'); // Ngày khách muốn đặt
        $table->time('start_time');   // Giờ bắt đầu ca tập (Ví dụ: 09:00:00)
        $table->time('end_time');     // Giờ kết thúc ca tập (Ví dụ: 10:00:00)        
        // Trạng thái lịch hẹn riêng
        $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
       
        $table->text('note')->nullable(); // Ghi chú yêu cầu riêng của khách
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pt_bookings');
    }
};