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
        'subcategory_id',
    ];
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }
    public function scopeFilteredBySubcategory($query, $value)
    {
        return $query->where('subcategory_id', $value);
    }


}
