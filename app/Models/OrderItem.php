<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    //
    protected $fillable = [
        'order_id', 
        'product_id', 
        'item_type', 
        'item_id', 
        'name', 
        'price', 
        'quantity', 
        'subtotal'
    ];
    public $timestamps = true; // Sửa lại cho khớp với migration có $table->timestamps()
    
public function order() { return $this->belongsTo(Order::class); }
public function product() { return $this->belongsTo(Product::class); }
}