<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = ['user_id', 'total_amount', 'payment_status', 'payment_method', 'order_date'];
    public $timestamps = false; // Vì migration của bạn không có timestamps()

public function user() { return $this->belongsTo(User::class); }
public function items() { return $this->hasMany(OrderItem::class); }
}