<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'status',
        'total_amount',
        'no_quote',
    ];

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }

}
