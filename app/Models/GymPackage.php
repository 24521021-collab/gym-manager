<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymPackage extends Model
{
    //

public function memberships() { return $this->hasMany(Membership::class, 'package_id'); }
}