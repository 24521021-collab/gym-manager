<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    //

public function user() { return $this->belongsTo(User::class); }
public function gymClass() { return $this->belongsTo(GymClass::class, 'class_id'); }
}