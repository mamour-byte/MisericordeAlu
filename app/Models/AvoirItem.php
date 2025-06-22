<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Avoir;

class AvoirItem extends Model
{
    protected $fillable = [
        'avoir_id', 'product_id', 'quantity', 'unit_price','no_order','no_avoir'
    ];

    public function avoir()
    {
        return $this->belongsTo(Avoir::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function getTotalPriceAttribute()
    {
        return $this->quantity * $this->price;
    }
}
