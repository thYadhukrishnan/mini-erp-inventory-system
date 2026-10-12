<?php

namespace App\Http\Controllers\Api;

use App\Enums\PurchaseOrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseOrderRequest;
use App\Http\Requests\UpdatePurchaseOrderStatusRequest;
use App\Http\Resources\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use App\Services\PurchaseOrderService;
use Illuminate\Http\JsonResponse;

class PurchaseOrderApiController extends Controller
{
    public function __construct(private readonly PurchaseOrderService $poService) {}

    /**
     * POST /api/purchase-orders
     */
    public function store(StorePurchaseOrderRequest $request): JsonResponse
    {
        $order = $this->poService->createOrder($request->validated());

        return (new PurchaseOrderResource($order))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * PATCH /api/purchase-orders/{id}/status
     */
    public function updateStatus(
        UpdatePurchaseOrderStatusRequest $request,
        PurchaseOrder $purchaseOrder
    ): JsonResponse {
        $newStatus = PurchaseOrderStatus::from($request->validated('status'));

        $order = $this->poService->transitionStatus($purchaseOrder, $newStatus);

        return (new PurchaseOrderResource($order))->response();
    }
}
