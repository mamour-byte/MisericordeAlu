<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'orders_id',
        'type',
        'quantity',
        'notes',
    ];

    const TYPE_ENTRY = 'entry';
    const TYPE_EXIT = 'exit';

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'orders_id');
    }
}

