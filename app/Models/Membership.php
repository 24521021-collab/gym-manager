<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Membership extends Model{
protected $primaryKey='id';

protected $fillable = ['user_id', 'package_id', 'start_date', 'end_date', 'status'];

public function user() { return $this->belongsTo(User::class,'user_id'); }
//liên kết với GymPackage và MembershipController(User);
public function package() {return $this->belongsTo(GymPackage::class, 'package_id');}
}