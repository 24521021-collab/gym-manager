<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PtLog extends Model
{
    use HasFactory;

    protected $fillable = ['pt_profile_id', 'title', 'content', 'log_date', 'start_time', 'status'];

    public function ptProfile()
    {
        return $this->belongsTo(PtProfile::class, 'pt_profile_id');
    }
    
}