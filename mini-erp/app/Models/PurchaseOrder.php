<?php

namespace App\Models;

use App\Enums\PurchaseOrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'supplier_id',
        'status',
        'total_amount',
    ];

    protected $casts = [
        'status'       => PurchaseOrderStatus::class,
        'total_amount' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Determine if the order is in a terminal (immutable) state.
     */
    public function isImmutable(): bool
    {
        return in_array($this->status, [
            PurchaseOrderStatus::RECEIVED,
            PurchaseOrderStatus::CANCELLED,
        ], true);
    }

    /**
     * Calculate grand total from attached line items collection.
     */
    public function calculateTotal(): float
    {
        return (float) round($this->items->sum(function ($item) {
            return (float) ($item->subtotal ?? ($item->quantity * $item->unit_price));
        }), 2);
    }
}
