<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Định nghĩa lớp PtLog, đại diện cho bảng 'pt_logs' trong cơ sở dữ liệu.
class PtLog extends Model
{
    use HasFactory;

    protected $fillable = ['pt_profile_id', 'title', 'content', 'log_date', 'start_time', 'status'];

    // Định nghĩa mối quan hệ "thuộc về" (belongsTo) với model PtProfile.
    // Một PtLog thuộc về một PtProfile.
    public function ptProfile()
    {
        return $this->belongsTo(PtProfile::class, 'pt_profile_id');
    }
}