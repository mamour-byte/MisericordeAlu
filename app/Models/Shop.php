<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
     protected $fillable = [ 'manager_id' , 'name', 'location'];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
