<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Order Model
 *
 * Represents a customer order in the system. Orders can be associated with
 * either an Invoice (for confirmed sales) or a Quote (for estimates).
 * When an order is modified, it is archived and a credit note (Avoir) is created.
 *
 * @property int $id
 * @property int $shop_id
 * @property int|null $user_id
 * @property int|null $invoice_id
 * @property int|null $quote_id
 * @property string $customer_name
 * @property string|null $customer_email
 * @property string|null $customer_phone
 * @property string|null $customer_address
 * @property string $status (pending, approved, canceled)
 * @property float $total_amount
 * @property float $remise Discount amount
 * @property string $archived (oui, non)
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class Order extends Model
{
    use Filterable, SoftDeletes;

    protected $fillable = [
        'shop_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'status',
        'total_amount',
        'remise',
        'user_id',
        'invoice_id',
        'quote_id',
        'archived',
    ];

    /**
     * Get the order items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /**
     * Get the user (vendeur) who created the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the associated invoice (if order is confirmed).
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the associated quote (if order is a quote).
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * Get the shop that owns the order.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the order content as an array.
     *
     * @return array<string, mixed>
     */
    public function getContent(): array
    {
        return [
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'customer_address' => $this->customer_address,
            'status' => $this->status,
            'total_amount' => $this->total_amount,
            'remise' => $this->remise,
            'user_id' => $this->user_id,
            'invoice_id' => $this->invoice_id,
            'quote_id' => $this->quote_id,
        ];
    }

    /**
     * Check if the order is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the order is archived.
     */
    public function isArchived(): bool
    {
        return $this->archived === 'oui';
    }

    /**
     * Get the final total after discount.
     */
    public function getFinalTotal(): float
    {
        return max(0, $this->total_amount - $this->remise);
    }
}

