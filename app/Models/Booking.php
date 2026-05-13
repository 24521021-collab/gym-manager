<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'class_id', 'booking_date', 'status'];
public function user() { return $this->belongsTo(User::class); }
public function gymClass() { return $this->belongsTo(GymClass::class, 'class_id'); }
}