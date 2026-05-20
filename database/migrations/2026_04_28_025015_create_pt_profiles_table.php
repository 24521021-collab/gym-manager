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
        Schema::create('pt_profiles', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('user')->onDelete('cascade');
        $table->text('bio')->nullable();
        $table->string('specialization');
        $table->decimal('rating', 3, 2)->default(0);
        $table->decimal('commission', 12, 2)->default(0); // Số tiền nhận được mỗi buổi dạy
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pt_profiles');
    }
};
