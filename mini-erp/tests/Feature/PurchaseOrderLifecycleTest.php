<?php

namespace Tests\Feature;

use App\Enums\PurchaseOrderStatus;
use App\Exceptions\InvalidOrderTransitionException;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\User;
use App\Services\PurchaseOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PurchaseOrderLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private PurchaseOrderService $poService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->poService = app(PurchaseOrderService::class);
    }

    /**
     * Test that transitioning an order to RECEIVED automatically and accurately
     * increments the target product stock quantities.
     */
    public function test_transitioning_order_to_received_automatically_increments_product_stock(): void
    {
        $supplier = Supplier::factory()->create();
        $product  = Product::factory()->create(['stock' => 10]);

        $order = PurchaseOrder::factory()->approved()->create([
            'supplier_id' => $supplier->id,
        ]);

        PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $order->id,
            'product_id'        => $product->id,
            'quantity'          => 7,
            'unit_price'        => 50.00,
            'subtotal'          => 350.00,
        ]);

        // Transition to RECEIVED via service
        $updatedOrder = $this->poService->transitionStatus($order, PurchaseOrderStatus::RECEIVED);

        $this->assertEquals(PurchaseOrderStatus::RECEIVED, $updatedOrder->status);
        $this->assertEquals(17, $product->fresh()->stock); // 10 + 7
    }

    /**
     * Test that receiving an order with multiple line items increments each product accurately.
     */
    public function test_receiving_order_with_multiple_items_increments_all_products_accurately(): void
    {
        $supplier = Supplier::factory()->create();
        $productA = Product::factory()->create(['stock' => 20]);
        $productB = Product::factory()->create(['stock' => 5]);

        $order = PurchaseOrder::factory()->approved()->create([
            'supplier_id' => $supplier->id,
        ]);

        PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $order->id,
            'product_id'        => $productA->id,
            'quantity'          => 15,
            'unit_price'        => 10.00,
            'subtotal'          => 150.00,
        ]);

        PurchaseOrderItem::factory()->create([
            'purchase_order_id' => $order->id,
            'product_id'        => $productB->id,
            'quantity'          => 8,
            'unit_price'        => 25.00,
            'subtotal'          => 200.00,
        ]);

        $this->poService->transitionStatus($order, PurchaseOrderStatus::RECEIVED);

        $this->assertEquals(35, $productA->fresh()->stock); // 20 + 15
        $this->assertEquals(13, $productB->fresh()->stock); // 5 + 8
    }

    /**
     * Test that attempting to transition backwards once in RECEIVED status
     * throws an InvalidOrderTransitionException.
     */
    public function test_transitioning_backwards_from_received_status_throws_exception(): void
    {
        $order = PurchaseOrder::factory()->received()->create();

        $this->expectException(InvalidOrderTransitionException::class);
        $this->expectExceptionMessage("Cannot transition PO from [RECEIVED] to [DRAFT].");

        $this->poService->transitionStatus($order, PurchaseOrderStatus::DRAFT);
    }

    /**
     * Test that attempting to mark an already RECEIVED order as RECEIVED again
     * is rejected because it is immutable.
     */
    public function test_modifying_terminal_received_order_throws_exception(): void
    {
        $order = PurchaseOrder::factory()->received()->create();

        $this->expectException(InvalidOrderTransitionException::class);

        $this->poService->markAsReceived($order);
    }

    /**
     * Test that attempting to transition once in CANCELLED status throws exception.
     */
    public function test_transitioning_from_cancelled_status_throws_exception(): void
    {
        $order = PurchaseOrder::factory()->cancelled()->create();

        $this->expectException(InvalidOrderTransitionException::class);
        $this->expectExceptionMessage("Cannot transition PO from [CANCELLED] to [APPROVED].");

        $this->poService->transitionStatus($order, PurchaseOrderStatus::APPROVED);
    }

    /**
     * Test that attempting to modify a PO or transition backwards via API
     * once in RECEIVED status returns a 422 Unprocessable Entity.
     */
    public function test_api_transition_from_received_status_returns_422(): void
    {
        $user  = User::factory()->create();
        $order = PurchaseOrder::factory()->received()->create();

        Sanctum::actingAs($user);

        $response = $this->withHeader('Accept', 'application/json')
            ->patchJson("/api/purchase-orders/{$order->id}/status", [
                'status' => 'DRAFT',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'error' => 'INVALID_TRANSITION',
            ]);
    }

    /**
     * Test that attempting to transition a CANCELLED order via API returns 422.
     */
    public function test_api_transition_from_cancelled_status_returns_422(): void
    {
        $user  = User::factory()->create();
        $order = PurchaseOrder::factory()->cancelled()->create();

        Sanctum::actingAs($user);

        $response = $this->withHeader('Accept', 'application/json')
            ->patchJson("/api/purchase-orders/{$order->id}/status", [
                'status' => 'APPROVED',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'error' => 'INVALID_TRANSITION',
            ]);
    }
}
