<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PtLog extends Model
{
    use HasFactory;

    protected $fillable = ['pt_id', 'title', 'content', 'log_date', 'start_time', 'status'];

    public function trainer()
    {
        return $this->belongsTo(User::class, 'pt_id');
    }
}