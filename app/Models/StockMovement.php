<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'shop_id',
        'order_id',
        'type',
        'quantity',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'float',
    ];

    const TYPE_ENTRY = 'entry';
    const TYPE_EXIT = 'exit';

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Boot the model and attach event listeners to recalculate product stock
     * when stock movements are created, updated or deleted.
     */
    protected static function booted(): void
    {
        static::created(function ($movement) {
            self::recalculateProductStock($movement->product_id, $movement->shop_id);
        });

        static::updated(function ($movement) {
            self::recalculateProductStock($movement->product_id, $movement->shop_id);
        });

        static::deleted(function ($movement) {
            self::recalculateProductStock($movement->product_id, $movement->shop_id);
        });
    }

    /**
     * Recalculate the product's stock_quantity for a given product and shop
     * by summing all stock movements (entries - exits).
     */
    protected static function recalculateProductStock($productId, $shopId): void
    {
        if (!$productId) {
            return;
        }

        $totalEntry = self::where('product_id', $productId)
            ->where('shop_id', $shopId)
            ->where('type', self::TYPE_ENTRY)
            ->sum('quantity');

        $totalExit = self::where('product_id', $productId)
            ->where('shop_id', $shopId)
            ->where('type', self::TYPE_EXIT)
            ->sum('quantity');

    // Keep decimal precision for quantities
    $current = (float) ($totalEntry - $totalExit);

        $product = Product::find($productId);
        if ($product) {
            // Ensure stock_quantity stored as a decimal (no negative values)
            $product->stock_quantity = max(0, $current);
            $product->save();
        }
    }
}

