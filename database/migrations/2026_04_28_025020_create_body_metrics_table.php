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
       Schema::create('body_metrics', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('user');
        $table->decimal('weight', 5, 2);
        $table->decimal('height', 5, 2);
        $table->float('body_fat_percentage')->nullable();
        $table->decimal('bmi', 5, 2);
        $table->timestamp('measured_at');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('body_metrics');
    }
};
