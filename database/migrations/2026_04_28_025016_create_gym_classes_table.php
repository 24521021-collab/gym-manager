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
       Schema::create('gym_classes', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->foreignId('pt_id')->constrained('pt_profiles')->onDelete('cascade');
        $table->integer('max_capacity');
        $table->text('description')->nullable();
        $table->integer('total_sessions')->default(1); // Tổng số buổi học của khóa
        $table->decimal('price', 12, 2)->default(0);  // Giá tiền trọn gói của lớp học
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gym_classes');
    }
};
