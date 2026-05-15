<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymClass extends Model
{
    protected $fillable = ['name', 'pt_id', 'max_capacity', 'schedule_time', 'end_time', 'room_name'];

public function pt() { return $this->belongsTo(PtProfile::class, 'pt_id'); }
public function bookings() { return $this->hasMany(Booking::class, 'class_id'); }
}