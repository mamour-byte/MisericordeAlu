<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * StockMovement Model
 *
 * Represents a stock movement (entry or exit) for a product.
 * The product's stock_quantity is automatically recalculated when
 * movements are created, updated, or deleted.
 *
 * @property int $id
 * @property int $product_id
 * @property int $shop_id
 * @property int|null $order_id
 * @property string $type (entry, exit)
 * @property float $quantity
 * @property string|null $notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
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

    /** Stock entry (increase) */
    public const TYPE_ENTRY = 'entry';
    
    /** Stock exit (decrease) */
    public const TYPE_EXIT = 'exit';

    /**
     * Get the shop where this movement occurred.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the product associated with this movement.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the order that triggered this movement (if any).
     */
    public function order(): BelongsTo
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

