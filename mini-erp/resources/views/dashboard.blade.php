@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')
{{-- ─── Metric Cards Row ─── --}}
<div class="row g-4 mb-4">

    {{-- Total Products --}}
    <div class="col-sm-6 col-xl-3 fade-in-up">
        <div class="metric-card primary">
            <div class="metric-icon primary">
                <i class="bi bi-box-seam-fill"></i>
            </div>
            <div class="metric-value">{{ number_format($activeProducts) }}</div>
            <div class="metric-label">Active Products</div>
        </div>
    </div>

    {{-- Low Stock Alerts --}}
    <div class="col-sm-6 col-xl-3 fade-in-up">
        <div class="metric-card warning">
            <div class="metric-icon warning">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div class="metric-value">{{ number_format($lowStockCount) }}</div>
            <div class="metric-label">Low Stock Alerts</div>
            @if($lowStockCount > 0)
                <a href="{{ route('inventory.index') }}" class="stretched-link" style="opacity:0"></a>
            @endif
        </div>
    </div>

    {{-- Total Expenditure --}}
    <div class="col-sm-6 col-xl-3 fade-in-up">
        <div class="metric-card success">
            <div class="metric-icon success">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="metric-value">${{ number_format($totalExpenditure, 2) }}</div>
            <div class="metric-label">Total Expenditure (Received)</div>
        </div>
    </div>

    {{-- Active POs --}}
    <div class="col-sm-6 col-xl-3 fade-in-up">
        <div class="metric-card info">
            <div class="metric-icon info">
                <i class="bi bi-file-earmark-check-fill"></i>
            </div>
            <div class="metric-value">{{ $latestOrders->count() }}</div>
            <div class="metric-label">Recent Orders (last 5)</div>
        </div>
    </div>
</div>

{{-- ─── Latest Purchase Orders Table ─── --}}
<div class="card-erp fade-in-up">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-clock-history me-2 text-primary"></i>Latest Purchase Orders</span>
        <a href="{{ route('purchase-orders.create') }}" class="btn-erp-primary btn btn-sm">
            <i class="bi bi-plus-lg me-1"></i>New PO
        </a>
    </div>
    <div class="card-body p-0">
        @if($latestOrders->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                No purchase orders yet.
                <a href="{{ route('purchase-orders.create') }}">Create your first PO →</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-erp mb-0">
                    <thead>
                        <tr>
                            <th>PO Number</th>
                            <th>Supplier</th>
                            <th>Status</th>
                            <th class="text-end">Total Amount</th>
                            <th>Created</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($latestOrders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('purchase-orders.show', $order) }}"
                                   class="fw-semibold text-decoration-none"
                                   style="color: var(--erp-primary)">
                                    {{ $order->po_number }}
                                </a>
                            </td>
                            <td>{{ $order->supplier->name ?? '—' }}</td>
                            <td>
                                <span class="badge-status badge-{{ strtolower($order->status->value) }}">
                                    {{ $order->status->label() }}
                                </span>
                            </td>
                            <td class="text-end fw-semibold">${{ number_format($order->total_amount, 2) }}</td>
                            <td class="text-muted" style="font-size:.8rem">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="text-center">
                                <a href="{{ route('purchase-orders.show', $order) }}"
                                   class="btn btn-sm btn-erp-ghost">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
