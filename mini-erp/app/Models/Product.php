<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'unit_cost',
        'stock',
        'low_stock_threshold',
    ];

    protected $casts = [
        'unit_cost'           => 'decimal:2',
        'stock'               => 'integer',
        'low_stock_threshold' => 'integer',
    ];

    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Determine if this product is below its low-stock threshold.
     */
    public function isLowStock(): bool
    {
        return $this->stock < $this->low_stock_threshold;
    }
}
