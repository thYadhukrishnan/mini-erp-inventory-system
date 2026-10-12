<?php

namespace Tests\Unit;

use App\Exceptions\InvalidStockQuantityException;
use App\Models\Product;
use App\Services\InventoryService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryStockTest extends TestCase
{
    use RefreshDatabase;

    private InventoryService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new InventoryService();
    }

    /**
     * Test that negative stock increment throws a domain exception.
     */
    public function test_negative_stock_increment_throws_domain_exception(): void
    {
        $this->expectException(InvalidStockQuantityException::class);
        $this->expectExceptionMessage('Stock increment quantity must be positive, [-5] given.');

        $product = Product::factory()->create(['stock' => 10]);

        $this->service->incrementStock($product, -5);
    }

    /**
     * Test that zero stock increment is rejected with domain exception.
     */
    public function test_zero_stock_increment_throws_domain_exception(): void
    {
        $this->expectException(DomainException::class);

        $product = Product::factory()->create(['stock' => 10]);

        $this->service->incrementStock($product, 0);
    }

    /**
     * Test that valid positive stock increment accurately updates stock.
     */
    public function test_valid_positive_stock_increment_increases_stock(): void
    {
        $product = Product::factory()->create(['stock' => 10]);

        $updatedProduct = $this->service->incrementStock($product, 15);

        $this->assertEquals(25, $updatedProduct->stock);
        $this->assertEquals(25, $product->fresh()->stock);
    }
}
