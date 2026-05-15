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
        $table->timestamp('schedule_time');
        $table->timestamp('end_time')->nullable();
        $table->string('room_name');
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
