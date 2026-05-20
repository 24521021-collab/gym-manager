<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gym_classes', function (Blueprint $table) {
            // Thêm cột image kiểu chuỗi, cho phép null, mặc định lấy ảnh default.jpg
            $table->string('image')->nullable()->default('default-class.jpg')->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('gym_classes', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};