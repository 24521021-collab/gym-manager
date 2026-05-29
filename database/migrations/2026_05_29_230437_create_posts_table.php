<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('title');
            $table->string('slug')->unique(); // Dùng để làm đường dẫn URL đẹp
            $table->string('header_image')->nullable(); // Ảnh bìa bài viết
            $table->text('content'); // Nội dung bài viết
            
            // Liên kết với bảng users (đảm bảo tên bảng là 'users' theo chuẩn Laravel)
            // Nếu bảng người dùng của bạn tên là 'user', hãy đổi lại dòng dưới
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            
            $table->string('status')->default('Draft'); // Trạng thái bài viết
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};