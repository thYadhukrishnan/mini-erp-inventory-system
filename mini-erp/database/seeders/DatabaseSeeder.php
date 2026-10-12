<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin User ──────────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );

        // ── Suppliers ───────────────────────────────────────────────────
        $suppliers = [
            ['name' => 'TechSupply Co.',    'email' => 'procurement@techsupply.com',  'phone' => '+1-800-123-4567'],
            ['name' => 'Global Parts Ltd.', 'email' => 'sales@globalparts.com',       'phone' => '+1-800-234-5678'],
            ['name' => 'FastShip Inc.',     'email' => 'orders@fastship.com',         'phone' => '+1-800-345-6789'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(['email' => $supplier['email']], $supplier);
        }

        // ── Products ────────────────────────────────────────────────────
        $products = [
            ['sku' => 'PRD-0001', 'name' => 'Laptop Pro 15"',         'unit_cost' => 1299.99, 'stock' => 25, 'low_stock_threshold' => 10],
            ['sku' => 'PRD-0002', 'name' => 'Wireless Keyboard',       'unit_cost' => 79.99,  'stock' => 8,  'low_stock_threshold' => 15],
            ['sku' => 'PRD-0003', 'name' => '27" 4K Monitor',          'unit_cost' => 549.99, 'stock' => 12, 'low_stock_threshold' => 5],
            ['sku' => 'PRD-0004', 'name' => 'USB-C Hub (7-port)',      'unit_cost' => 49.99,  'stock' => 3,  'low_stock_threshold' => 20],
            ['sku' => 'PRD-0005', 'name' => 'Mechanical Mouse',        'unit_cost' => 59.99,  'stock' => 50, 'low_stock_threshold' => 10],
            ['sku' => 'PRD-0006', 'name' => 'Office Chair (Ergonomic)','unit_cost' => 399.99, 'stock' => 6,  'low_stock_threshold' => 8],
            ['sku' => 'PRD-0007', 'name' => 'Standing Desk',           'unit_cost' => 699.99, 'stock' => 4,  'low_stock_threshold' => 5],
            ['sku' => 'PRD-0008', 'name' => 'Webcam HD 1080p',         'unit_cost' => 89.99,  'stock' => 0,  'low_stock_threshold' => 10],
            ['sku' => 'PRD-0009', 'name' => 'Noise-Cancelling Headset','unit_cost' => 249.99, 'stock' => 18, 'low_stock_threshold' => 10],
            ['sku' => 'PRD-0010', 'name' => 'HDMI Cable (2m)',         'unit_cost' => 14.99,  'stock' => 100,'low_stock_threshold' => 25],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(['sku' => $product['sku']], $product);
        }

        $this->command->info('✓  Seeded: 1 admin, 3 suppliers, 10 products.');
    }
}
