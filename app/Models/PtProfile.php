<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PtProfile extends Model
{
    protected $fillable=['user_id','bio','specialization','rating'];

    public function user() { return $this->belongsTo(User::class); }
public function classes() { return $this->hasMany(GymClass::class, 'pt_id'); }
}