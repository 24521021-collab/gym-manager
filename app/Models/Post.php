<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'title',
        'slug',
        'header_image',
        'content',
        'author_id',
        'status'
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (static::where('slug', $post->slug)->exists()) {
                $post->slug = $post->slug . '-' . uniqid();
            }
        });
    }
}