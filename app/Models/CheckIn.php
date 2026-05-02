<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckIn extends Model
{
    //

// Trong cả 2 file:
public function user() { return $this->belongsTo(User::class); }
}