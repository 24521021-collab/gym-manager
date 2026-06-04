<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymPackage extends Model
{
    protected $primaryKey ='id'; 
    protected $fillable = [
        'package_name', 
        'duration_days',
        'price',
        'description', 
        'status',
        ];

public function memberships() { return $this->hasMany(Membership::class, 'package_id'); }
}