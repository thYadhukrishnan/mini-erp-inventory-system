<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InventoryApiController extends Controller
{
    public function __construct(private readonly InventoryService $inventoryService) {}

    /**
     * GET /api/inventory
     * Returns a paginated list of all products.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage  = (int) $request->query('per_page', 15);
        $products = $this->inventoryService->getPaginated($perPage);

        return ProductResource::collection($products);
    }

    /**
     * GET /api/inventory/low-stock
     * Returns all products where stock < low_stock_threshold.
     */
    public function lowStock(): JsonResponse
    {
        $products = $this->inventoryService->getLowStock();

        return response()->json([
            'data'  => ProductResource::collection($products),
            'count' => $products->count(),
        ]);
    }
}
