<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BodyMetric extends Model
{
    protected $fillable = ['user_id', 'weight', 'height', 'bmi', 'body_fat_percentage', 'measured_at'];

// Trong cả 2 file:
public function user() { return $this->belongsTo(User::class); }
}