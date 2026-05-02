<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymClass extends Model
{

public function pt() { return $this->belongsTo(PtProfile::class, 'pt_id'); }
public function bookings() { return $this->hasMany(Booking::class, 'class_id'); }
}