<?php

namespace App\Http\Controllers;

use App\Enums\PurchaseOrderStatus;
use App\Models\PurchaseOrder;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly InventoryService $inventoryService) {}

    public function index(): View
    {
        $activeProducts = \App\Models\Product::count();
        $lowStockCount  = $this->inventoryService->countLowStock();

        $totalExpenditure = PurchaseOrder::where('status', PurchaseOrderStatus::RECEIVED)
            ->sum('total_amount');

        $latestOrders = PurchaseOrder::with('supplier')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'activeProducts',
            'lowStockCount',
            'totalExpenditure',
            'latestOrders'
        ));
    }
}
