<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 
        'description', 
        'price', 
        'stock_quantity', 
        'stock_min', 
        'categorie_id',
        'shop_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    public function scopeByShop($query, $shop_id)
    {
        return $query->where('shop_id', $shop_id);
    }

}
