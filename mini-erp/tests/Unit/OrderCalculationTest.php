<?php

namespace Tests\Unit;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use PHPUnit\Framework\TestCase;

class OrderCalculationTest extends TestCase
{
    /**
     * Test that line item subtotals (quantity * unit price) are calculated accurately.
     */
    public function test_line_item_subtotal_is_calculated_accurately(): void
    {
        $quantity = 5;
        $unitPrice = 120.50;

        $calculatedSubtotal = PurchaseOrderItem::calculateSubtotal($quantity, $unitPrice);

        $this->assertEquals(602.50, $calculatedSubtotal);
    }

    /**
     * Test line item subtotal calculation with decimal precision and various quantities.
     */
    public function test_line_item_subtotal_handles_floating_point_precision(): void
    {
        $testCases = [
            ['quantity' => 1, 'unit_price' => 19.99, 'expected' => 19.99],
            ['quantity' => 3, 'unit_price' => 33.33, 'expected' => 99.99],
            ['quantity' => 10, 'unit_price' => 0.05, 'expected' => 0.50],
            ['quantity' => 7, 'unit_price' => 14.99, 'expected' => 104.93],
        ];

        foreach ($testCases as $case) {
            $subtotal = PurchaseOrderItem::calculateSubtotal($case['quantity'], $case['unit_price']);
            $this->assertEquals($case['expected'], $subtotal);
        }
    }

    /**
     * Test that an instantiated line item accurately reflects subtotal calculation without database.
     */
    public function test_line_item_instance_attributes_match_calculation(): void
    {
        $item = new PurchaseOrderItem([
            'quantity'   => 4,
            'unit_price' => 25.75,
            'subtotal'   => PurchaseOrderItem::calculateSubtotal(4, 25.75),
        ]);

        $this->assertSame(4, $item->quantity);
        $this->assertEquals(25.75, $item->unit_price);
        $this->assertEquals(103.00, $item->subtotal);
    }

    /**
     * Test that the overall Purchase Order grand total matches the sum of its line items.
     */
    public function test_purchase_order_grand_total_matches_sum_of_line_items(): void
    {
        $po = new PurchaseOrder(['po_number' => 'PO-TEST-001', 'total_amount' => 0]);

        $items = collect([
            new PurchaseOrderItem(['quantity' => 2, 'unit_price' => 50.00, 'subtotal' => 100.00]),
            new PurchaseOrderItem(['quantity' => 3, 'unit_price' => 25.50, 'subtotal' => 76.50]),
            new PurchaseOrderItem(['quantity' => 1, 'unit_price' => 199.99, 'subtotal' => 199.99]),
        ]);

        $po->setRelation('items', $items);

        $expectedGrandTotal = 100.00 + 76.50 + 199.99; // 376.49
        $calculatedTotal = $po->calculateTotal();

        $this->assertEquals($expectedGrandTotal, $calculatedTotal);
    }

    /**
     * Test grand total calculation when line items have empty or zero values.
     */
    public function test_purchase_order_grand_total_with_empty_items_is_zero(): void
    {
        $po = new PurchaseOrder(['total_amount' => 0]);
        $po->setRelation('items', collect([]));

        $this->assertEquals(0.00, $po->calculateTotal());
    }
}
