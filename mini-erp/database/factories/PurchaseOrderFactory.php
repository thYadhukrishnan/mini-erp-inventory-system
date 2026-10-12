<?php

namespace Database\Factories;

use App\Enums\PurchaseOrderStatus;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    public function definition(): array
    {
        return [
            'po_number'    => 'PO-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
            'supplier_id'  => Supplier::factory(),
            'status'       => PurchaseOrderStatus::DRAFT,
            'total_amount' => 0.00,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => PurchaseOrderStatus::APPROVED,
        ]);
    }

    public function received(): static
    {
        return $this->state(fn () => [
            'status' => PurchaseOrderStatus::RECEIVED,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => PurchaseOrderStatus::CANCELLED,
        ]);
    }
}
