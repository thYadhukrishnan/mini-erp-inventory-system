@extends('layouts.app')

@section('title', $order->po_number)
@section('page-title', 'Purchase Order Details')

@section('content')
<div class="row g-4">

    {{-- Left: Order Summary --}}
    <div class="col-lg-4">
        <div class="card-erp fade-in-up">
            <div class="card-header">
                <i class="bi bi-file-earmark-text me-2 text-primary"></i>Order Summary
            </div>
            <div class="card-body">
                <dl class="mb-0" style="font-size:.875rem">
                    <div class="d-flex justify-content-between mb-3">
                        <dt class="fw-normal text-muted">PO Number</dt>
                        <dd class="mb-0 fw-bold" style="color: var(--erp-primary)">{{ $order->po_number }}</dd>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <dt class="fw-normal text-muted">Status</dt>
                        <dd class="mb-0">
                            <span class="badge-status badge-{{ strtolower($order->status->value) }}">
                                {{ $order->status->label() }}
                            </span>
                        </dd>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <dt class="fw-normal text-muted">Supplier</dt>
                        <dd class="mb-0 fw-semibold">{{ $order->supplier->name }}</dd>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <dt class="fw-normal text-muted">Supplier Email</dt>
                        <dd class="mb-0 text-muted">{{ $order->supplier->email }}</dd>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <dt class="fw-normal text-muted">Created</dt>
                        <dd class="mb-0 text-muted">{{ $order->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <hr style="border-color: var(--erp-border)">
                    <div class="d-flex justify-content-between">
                        <dt class="fw-semibold">Grand Total</dt>
                        <dd class="mb-0 fw-bold fs-5" style="color: var(--erp-primary)">
                            ${{ number_format($order->total_amount, 2) }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- ─── Action Buttons ─── --}}
        @if(!$order->isImmutable())
        <div class="card-erp fade-in-up mt-4" style="animation-delay:.06s">
            <div class="card-header">
                <i class="bi bi-lightning me-2 text-warning"></i>Actions
            </div>
            <div class="card-body d-flex flex-column gap-2">

                {{-- DRAFT → APPROVED --}}
                @if($order->status->canTransitionTo(\App\Enums\PurchaseOrderStatus::APPROVED))
                <form action="{{ route('purchase-orders.update-status', $order) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="APPROVED">
                    <button type="submit" class="btn btn-sm w-100"
                            style="background:rgba(56,189,248,.15);color:#38bdf8;border:1px solid rgba(56,189,248,.3);border-radius:8px;padding:.55rem">
                        <i class="bi bi-check-circle me-2"></i>Approve Order
                    </button>
                </form>
                @endif

                {{-- APPROVED → RECEIVED --}}
                @if($order->status->canTransitionTo(\App\Enums\PurchaseOrderStatus::RECEIVED))
                <form action="{{ route('purchase-orders.update-status', $order) }}" method="POST"
                      onsubmit="return confirm('Mark this order as RECEIVED? This will update stock levels and cannot be undone.')">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="RECEIVED">
                    <button type="submit" class="btn btn-sm w-100"
                            style="background:rgba(16,185,129,.15);color:#10b981;border:1px solid rgba(16,185,129,.3);border-radius:8px;padding:.55rem">
                        <i class="bi bi-box-arrow-in-down me-2"></i>Mark as Received
                    </button>
                </form>
                @endif

                {{-- → CANCELLED --}}
                @if($order->status->canTransitionTo(\App\Enums\PurchaseOrderStatus::CANCELLED))
                <form action="{{ route('purchase-orders.update-status', $order) }}" method="POST"
                      onsubmit="return confirm('Cancel this purchase order? This action cannot be undone.')">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="CANCELLED">
                    <button type="submit" class="btn btn-sm w-100"
                            style="background:rgba(239,68,68,.12);color:#ef4444;border:1px solid rgba(239,68,68,.25);border-radius:8px;padding:.55rem">
                        <i class="bi bi-x-circle me-2"></i>Cancel Order
                    </button>
                </form>
                @endif
            </div>
        </div>
        @else
        <div class="card-erp mt-4 fade-in-up" style="animation-delay:.06s">
            <div class="card-body text-center text-muted py-3" style="font-size:.85rem">
                <i class="bi bi-lock-fill d-block mb-2 fs-5"></i>
                This order is <strong>{{ $order->status->label() }}</strong>
                and cannot be modified.
            </div>
        </div>
        @endif

        <div class="mt-3 fade-in-up" style="animation-delay:.12s">
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-erp-ghost w-100">
                <i class="bi bi-arrow-left me-1"></i>Back to Orders
            </a>
        </div>
    </div>

    {{-- Right: Line Items --}}
    <div class="col-lg-8">
        <div class="card-erp fade-in-up" style="animation-delay:.04s">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-list-ul me-2 text-primary"></i>Line Items</span>
                <span class="text-muted" style="font-size:.8rem">{{ $order->items->count() }} item(s)</span>
            </div>
            <div class="card-body p-0">
                @if($order->items->isEmpty())
                    <div class="text-center py-4 text-muted">No line items found.</div>
                @else
                <div class="table-responsive">
                    <table class="table table-erp mb-0">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Product</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td><code class="text-info" style="font-size:.8rem">{{ $item->product->sku }}</code></td>
                                <td class="fw-semibold">{{ $item->product->name }}</td>
                                <td class="text-center">{{ number_format($item->quantity) }}</td>
                                <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end fw-semibold">${{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="border-top: 2px solid var(--erp-border)">
                                <td colspan="4" class="text-end fw-bold pt-3">Grand Total</td>
                                <td class="text-end fw-bold pt-3 fs-5"
                                    style="color: var(--erp-primary)">
                                    ${{ number_format($order->total_amount, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endif
            </div>
        </div>

        {{-- Received Stock Info --}}
        @if($order->status === \App\Enums\PurchaseOrderStatus::RECEIVED)
        <div class="mt-4 p-4 rounded-3 fade-in-up"
             style="background: rgba(16,185,129,.07); border: 1px solid rgba(16,185,129,.2)">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                <strong class="text-success">Stock Updated</strong>
            </div>
            <p class="text-muted mb-0" style="font-size:.85rem">
                All product stock levels have been incremented based on the quantities in this order.
            </p>
        </div>
        @endif
    </div>
</div>
@endsection
