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
        Schema::create('memberships',function(Blueprint $table){
    $table->id();
    $table->foreignId('user_id')->constrained('user')->onDelete('cascade');
    $table->foreignId('package_id')->constrained('gym_packages')->onDelete('cascade');
    $table->date('start_date');
    $table->date('end_date');
    $table->string('status')->default('Active'); // Active, Expired, Canceled
    $table->timestamps();
        });
    }

    /** 
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
