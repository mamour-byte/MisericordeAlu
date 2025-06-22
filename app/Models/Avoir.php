<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AvoirItem;


class avoir extends Model
{
    protected $fillable = [
        'no_avoir', 'order_id', 'user_id', 'shop_id',
        'customer_name', 'customer_email', 'customer_phone',
        'customer_address', 'total_amount', 'status',
    ];

    public function items()
    {
        return $this->hasMany(AvoirItem::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
