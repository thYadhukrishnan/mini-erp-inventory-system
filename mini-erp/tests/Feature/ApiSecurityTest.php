<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiSecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that unauthorized requests to /api/purchase-orders without a Sanctum token
     * return a 401 Unauthorized status.
     */
    public function test_unauthorized_post_request_without_token_returns_401(): void
    {
        $response = $this->withHeader('Accept', 'application/json')
            ->postJson('/api/purchase-orders', [
                'supplier_id' => 1,
                'items'       => [
                    ['product_id' => 1, 'quantity' => 2, 'unit_price' => 10.00],
                ],
            ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    /**
     * Test that unauthorized PATCH request to update PO status without token returns 401.
     */
    public function test_unauthorized_patch_status_without_token_returns_401(): void
    {
        $order = PurchaseOrder::factory()->create();

        $response = $this->withHeader('Accept', 'application/json')
            ->patchJson("/api/purchase-orders/{$order->id}/status", [
                'status' => 'APPROVED',
            ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    /**
     * Test that creating a PO with missing required fields (e.g. missing supplier_id)
     * fails validation with a 422 status and proper error keys.
     */
    public function test_creating_po_with_missing_supplier_id_fails_validation_with_422(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create();

        Sanctum::actingAs($user);

        $payload = [
            // missing 'supplier_id'
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity'   => 5,
                    'unit_price' => 29.99,
                ],
            ],
        ];

        $response = $this->withHeader('Accept', 'application/json')
            ->postJson('/api/purchase-orders', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['supplier_id']);
    }

    /**
     * Test that creating a PO with an empty item lines array fails validation with a 422 status.
     */
    public function test_creating_po_with_empty_items_array_fails_validation_with_422(): void
    {
        $user     = User::factory()->create();
        $supplier = Supplier::factory()->create();

        Sanctum::actingAs($user);

        $payload = [
            'supplier_id' => $supplier->id,
            'items'       => [], // Empty line items array
        ];

        $response = $this->withHeader('Accept', 'application/json')
            ->postJson('/api/purchase-orders', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items'])
            ->assertJsonFragment([
                'items' => ['At least one line item is required.'],
            ]);
    }

    /**
     * Test that creating a PO with invalid line item data fails validation with proper error keys.
     */
    public function test_creating_po_with_invalid_line_item_data_fails_validation(): void
    {
        $user     = User::factory()->create();
        $supplier = Supplier::factory()->create();

        Sanctum::actingAs($user);

        $payload = [
            'supplier_id' => $supplier->id,
            'items'       => [
                [
                    'product_id' => 999999, // non-existent product
                    'quantity'   => 0,      // invalid min:1
                    'unit_price' => -5.00,  // invalid min:0.01
                ],
            ],
        ];

        $response = $this->withHeader('Accept', 'application/json')
            ->postJson('/api/purchase-orders', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'items.0.product_id',
                'items.0.quantity',
                'items.0.unit_price',
            ]);
    }

    /**
     * Test that an authorized user with valid payload creates a PO successfully with 201 status.
     */
    public function test_authorized_user_can_create_purchase_order_successfully(): void
    {
        $user     = User::factory()->create();
        $supplier = Supplier::factory()->create();
        $product  = Product::factory()->create(['unit_cost' => 50.00]);

        Sanctum::actingAs($user);

        $payload = [
            'supplier_id' => $supplier->id,
            'items'       => [
                [
                    'product_id' => $product->id,
                    'quantity'   => 3,
                    'unit_price' => 50.00,
                ],
            ],
        ];

        $response = $this->withHeader('Accept', 'application/json')
            ->postJson('/api/purchase-orders', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'po_number',
                    'status',
                    'status_label',
                    'total_amount',
                    'supplier' => ['id', 'name', 'email'],
                    'items' => [
                        '*' => [
                            'id',
                            'quantity',
                            'unit_price',
                            'subtotal',
                            'product' => ['id', 'sku', 'name'],
                        ],
                    ],
                    'created_at',
                ],
            ])
            ->assertJsonPath('data.total_amount', 150)
            ->assertJsonPath('data.status', 'DRAFT');

        $this->assertDatabaseHas('purchase_orders', [
            'supplier_id' => $supplier->id,
            'status'      => 'DRAFT',
            'total_amount' => 150.00,
        ]);
    }
}
