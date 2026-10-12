<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class InventoryService
{
    /**
     * Return a paginated list of all products.
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Product::orderBy('name')->paginate($perPage);
    }

    /**
     * Return all products where stock is below the low_stock_threshold.
     */
    public function getLowStock(): Collection
    {
        return Product::whereColumn('stock', '<', 'low_stock_threshold')
                      ->orderBy('stock')
                      ->get();
    }

    /**
     * Count products currently below their low-stock threshold.
     */
    public function countLowStock(): int
    {
        return Product::whereColumn('stock', '<', 'low_stock_threshold')->count();
    }
}
