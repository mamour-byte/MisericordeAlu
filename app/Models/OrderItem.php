<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'id',
        'orders_id',
        'product_id',
        'quantity',
        'unit_price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class , 'orders_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
