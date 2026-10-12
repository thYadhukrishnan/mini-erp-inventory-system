<?php

namespace App\Http\Controllers;

use App\Enums\PurchaseOrderStatus;
use App\Exceptions\InvalidOrderTransitionException;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\PurchaseOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    public function __construct(private readonly PurchaseOrderService $poService) {}

    public function index(): View
    {
        $orders = PurchaseOrder::with('supplier')
            ->latest()
            ->paginate(15);

        return view('purchase-orders.index', compact('orders'));
    }

    public function create(): View
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products  = Product::orderBy('name')->get();

        return view('purchase-orders.create', compact('suppliers', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id'        => ['required', 'integer', 'exists:suppliers,id'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0.01'],
        ]);

        $order = $this->poService->createOrder($validated);

        return redirect()
            ->route('purchase-orders.show', $order)
            ->with('success', "Purchase Order [{$order->po_number}] created successfully.");
    }

    public function show(PurchaseOrder $purchaseOrder): View
    {
        $purchaseOrder->load('supplier', 'items.product');

        return view('purchase-orders.show', ['order' => $purchaseOrder]);
    }

    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'string'],
        ]);

        try {
            $newStatus = PurchaseOrderStatus::from($request->input('status'));
            $this->poService->transitionStatus($purchaseOrder, $newStatus);

            return redirect()
                ->route('purchase-orders.show', $purchaseOrder)
                ->with('success', "PO status updated to [{$newStatus->label()}].");
        } catch (InvalidOrderTransitionException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        } catch (\ValueError) {
            return redirect()
                ->back()
                ->with('error', 'Invalid status value provided.');
        }
    }
}
