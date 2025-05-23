<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'status',
        'total_amount',
        'user_id',
    ];

    public function items()
        {
            return $this->hasMany(OrderItem::class , 'order_id');
        }

    public function user()
        {
            return $this->belongsTo(User::class);
        }


        public function getContent()
    {
        return [
            'customer' => [
                'name' => $this->customer_name,
                'email' => $this->customer_email,
                'phone' => $this->customer_phone,
                'address' => $this->customer_address,
            ],
            'items' => $this->items->map(function($item) {
                return [
                    'product' => $item->Product->name ?? 'N/A',
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->price * $item->quantity
                ];
            }),
            'total' => $this->total_amount,
            'status' => $this->status,
            'date' => $this->created_at->format('Y-m-d H:i:s')
        ];
    }

}

