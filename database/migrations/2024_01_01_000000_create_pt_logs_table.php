<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pt_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pt_id')->constrained('user')->onDelete('cascade');
            $table->string('title'); // Tiêu đề: Tên lớp học hoặc tên học viên
            $table->text('content'); // Nội dung nhật ký
            $table->date('log_date'); // Ngày ghi nhận
            $table->time('start_time')->nullable(); // Khung giờ (để hiển thị timeline)
            $table->string('status')->default('completed'); // completed (Hoàn thành), draft (Lưu nháp), upcoming (Sắp tới)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pt_logs');
    }
};