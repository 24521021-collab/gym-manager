<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'content',
        'is_read'
    ];

    /**
     * Tự động viết hoa tiêu đề khi lưu vào DB
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = mb_strtoupper($value);
    }
}
