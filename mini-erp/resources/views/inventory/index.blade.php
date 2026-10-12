@extends('layouts.app')

@section('title', 'Inventory')
@section('page-title', 'Product Inventory')

@section('content')
<div class="card-erp fade-in-up">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-box-seam me-2 text-primary"></i>All Products</span>
        <div class="d-flex gap-2 align-items-center">
            <span class="text-muted" style="font-size:.8rem">
                {{ $products->total() }} products total
            </span>
        </div>
    </div>
    <div class="card-body p-0">
        @if($products->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                No products found.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-erp mb-0">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Product Name</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">Threshold</th>
                            <th class="text-end">Unit Cost</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr class="{{ $product->isLowStock() ? 'low-stock-row' : '' }}">
                            <td>
                                <code class="text-info" style="font-size:.8rem">{{ $product->sku }}</code>
                            </td>
                            <td class="fw-semibold">{{ $product->name }}</td>
                            <td class="text-center">
                                <span class="fw-bold {{ $product->isLowStock() ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($product->stock) }}
                                </span>
                            </td>
                            <td class="text-center text-muted">{{ $product->low_stock_threshold }}</td>
                            <td class="text-end">${{ number_format($product->unit_cost, 2) }}</td>
                            <td class="text-center">
                                @if($product->isLowStock())
                                    <span class="stock-badge-low">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>Low Stock
                                    </span>
                                @else
                                    <span class="stock-badge-ok">
                                        <i class="bi bi-check-circle-fill me-1"></i>OK
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($products->hasPages())
            <div class="p-3 d-flex justify-content-end">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
            @endif
        @endif
    </div>
</div>

{{-- Low Stock Legend --}}
<div class="mt-3 d-flex align-items-center gap-3" style="font-size:.78rem; color: var(--erp-muted)">
    <span><span class="stock-badge-low">Low Stock</span> — Stock is below the configured threshold</span>
    <span><span class="stock-badge-ok">OK</span> — Stock is at a healthy level</span>
</div>
@endsection
