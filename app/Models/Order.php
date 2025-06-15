<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Order extends Model
{


    protected $fillable = [
        'shop_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'status',
        'total_amount',
        'user_id',
        'invoice_id',
        'quote_id',
    ];

    public function items()
        {
            return $this->hasMany(OrderItem::class , 'order_id');
        }

    public function user()
        {
            return $this->belongsTo(User::class);
        }

    public function invoice()
        {
            return $this->belongsTo(Invoice::class);
        }

    public function quote()
        {
            return $this->belongsTo(Quote::class);
        }


    public function getContent()
    {
        return [
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'customer_address' => $this->customer_address,
            'status' => $this->status,
            'total_amount' => $this->total_amount,
            'user_id' => $this->user_id,
            'invoice_id' => $this->invoice_id,
            'quote_id' => $this->quote_id,
        ];
    }

}

