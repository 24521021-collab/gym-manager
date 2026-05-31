<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PtProfile extends Model
{
    protected $fillable=['user_id','bio','specialization','rating','image','commission',];
    public function user() { return $this->belongsTo(User::class); }
public function classes() { return $this->hasMany(GymClass::class, 'pt_id'); }
  public function getSelectionNameAttribute()
    {
        return ($this->user->full_name ?? 'N/A') . ' - ' . ($this->specialization ?? 'Chưa có chuyên môn');
    }
}