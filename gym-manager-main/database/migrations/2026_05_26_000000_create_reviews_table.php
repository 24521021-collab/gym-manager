<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('user')->onDelete('cascade');
            $table->morphs('reviewable'); // Creates reviewable_id and reviewable_type
            $table->tinyInteger('rating')->unsigned();
            $table->text('comment');
            $table->timestamps();
            
            // Security: Prevent duplicate reviews for the same entity
            $table->unique(['user_id', 'reviewable_id', 'reviewable_type'], 'user_review_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
