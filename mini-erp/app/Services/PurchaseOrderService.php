<?php

namespace App\Services;

use App\Enums\PurchaseOrderStatus;
use App\Exceptions\InvalidOrderTransitionException;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseOrderService
{
    /**
     * Create a new Purchase Order with its line items.
     *
     * @param  array{supplier_id: int, items: array<array{product_id: int, quantity: int, unit_price: float}>}  $data
     */
    public function createOrder(array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($data) {
            $order = PurchaseOrder::create([
                'po_number'   => $this->generatePoNumber(),
                'supplier_id' => $data['supplier_id'],
                'status'      => PurchaseOrderStatus::DRAFT,
                'total_amount' => 0,
            ]);

            $total = $this->attachItems($order, $data['items']);

            $order->update(['total_amount' => $total]);

            return $order->load('items.product', 'supplier');
        });
    }

    /**
     * Transition a PO to a new status.
     * Triggers stock increment atomically when status becomes RECEIVED.
     *
     * @throws InvalidOrderTransitionException
     */
    public function transitionStatus(PurchaseOrder $order, PurchaseOrderStatus $newStatus): PurchaseOrder
    {
        if (! $order->status->canTransitionTo($newStatus)) {
            throw new InvalidOrderTransitionException(
                "Cannot transition PO from [{$order->status->value}] to [{$newStatus->value}]."
            );
        }

        if ($newStatus === PurchaseOrderStatus::RECEIVED) {
            return $this->markAsReceived($order);
        }

        $order->update(['status' => $newStatus]);

        return $order->fresh('items.product', 'supplier');
    }

    /**
     * Mark a PO as RECEIVED and atomically increment stock for each line item.
     * Uses DB::transaction + lockForUpdate() to prevent race conditions.
     *
     * @throws InvalidOrderTransitionException
     */
    public function markAsReceived(PurchaseOrder $order): PurchaseOrder
    {
        if ($order->isImmutable()) {
            throw new InvalidOrderTransitionException(
                "PO [{$order->po_number}] is in a terminal state and cannot be modified."
            );
        }

        DB::transaction(function () use ($order) {
            // Load items with product inside the transaction for safety
            $items = $order->items()->with('product')->get();

            foreach ($items as $item) {
                // Lock the product row to prevent concurrent stock mutations
                $product = Product::lockForUpdate()->find($item->product_id);

                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }

            $order->update(['status' => PurchaseOrderStatus::RECEIVED]);
        });

        return $order->fresh('items.product', 'supplier');
    }

    /**
     * Recalculate and persist line items, returning the total amount.
     *
     * @param  array<array{product_id: int, quantity: int, unit_price: float}>  $items
     */
    private function attachItems(PurchaseOrder $order, array $items): float
    {
        $total = 0.0;

        foreach ($items as $itemData) {
            $subtotal = $itemData['quantity'] * $itemData['unit_price'];
            $total   += $subtotal;

            PurchaseOrderItem::create([
                'purchase_order_id' => $order->id,
                'product_id'        => $itemData['product_id'],
                'quantity'          => $itemData['quantity'],
                'unit_price'        => $itemData['unit_price'],
                'subtotal'          => $subtotal,
            ]);
        }

        return $total;
    }

    /**
     * Generate a unique, human-readable PO number.
     */
    private function generatePoNumber(): string
    {
        do {
            $number = 'PO-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
        } while (PurchaseOrder::where('po_number', $number)->exists());

        return $number;
    }
}
