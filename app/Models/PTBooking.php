<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PtBooking extends Model
{
    protected $fillable = ['customer_id', 'pt_id', 'booking_date', 'start_time', 'end_time', 'price', 'status', 'note'];

    public function customer() {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function pt() {
        return $this->belongsTo(User::class, 'pt_id');
    }
}