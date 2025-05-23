<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

    class FabricationItem extends Model
    {
        protected $fillable = [
            'fabrication_id',
            'type',
            'width',
            'height',
            'price_meter',
            'quantity',
            'note',
        ];

        public function fabrication()
        {
            return $this->belongsTo(Fabrication::class);
        }
    }