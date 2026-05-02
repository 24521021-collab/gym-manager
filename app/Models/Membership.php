<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    //

public function user() { return $this->belongsTo(User::class); }
public function package() { return $this->belongsTo(GymPackage::class, 'package_id'); }
}