<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Orchid\Screen\AsSource;

    class Fabrication extends Model
        {
            protected $fillable = [
                'user_id',
                'customer_name',
                'customer_phone',
                'customer_email',
                'customer_address',
                'status',
            ];

            public function items(): HasMany
            {
                return $this->hasMany(FabricationItem::class);
            }
        }