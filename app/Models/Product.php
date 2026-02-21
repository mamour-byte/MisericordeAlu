<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filterable;

/**
 * Product Model
 *
 * Represents a product in the inventory. Each product belongs to a shop
 * and has its stock quantity automatically calculated from stock movements.
 *
 * @property int $id
 * @property int $shop_id
 * @property int $categorie_id
 * @property string $name
 * @property string|null $description
 * @property float $price Unit price
 * @property float $stock_quantity Current stock (auto-calculated from movements)
 * @property float $stock_min Minimum stock threshold for alerts
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class Product extends Model
{
    use Filterable, SoftDeletes;

    protected $fillable = [
        'name', 
        'description', 
        'price', 
        'stock_quantity', 
        'stock_min', 
        'categorie_id',
        'shop_id',
    ];

    protected $casts = [
        'price' => 'float',
        'stock_quantity' => 'float',
        'stock_min' => 'float',
    ];

    /**
     * Get the product's category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'categorie_id');
    }

    /**
     * Get all stock movements for this product.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get all order items containing this product.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    
    /**
     * Scope to filter products by shop.
     *
     * @param Builder $query
     * @param int|null $shopId
     * @return Builder
     */
    public function scopeByShop(Builder $query, ?int $shopId): Builder
    {
        return $query->where('shop_id', $shopId);
    }

    /**
     * Get the shop that owns this product.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Check if the product stock is below the minimum threshold.
     */
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->stock_min;
    }

    /**
     * Check if the product has enough stock for a given quantity.
     *
     * @param float $quantity The quantity to check
     * @return bool
     */
    public function hasEnoughStock(float $quantity): bool
    {
        return $this->stock_quantity >= $quantity;
    }
}
