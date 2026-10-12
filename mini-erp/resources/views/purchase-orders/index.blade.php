@extends('layouts.app')

@section('title', 'Purchase Orders')
@section('page-title', 'Purchase Orders')

@section('content')
<div class="card-erp fade-in-up">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-file-earmark-text me-2 text-primary"></i>All Purchase Orders</span>
        <a href="{{ route('purchase-orders.create') }}" class="btn btn-sm btn-erp-primary">
            <i class="bi bi-plus-lg me-1"></i>New PO
        </a>
    </div>
    <div class="card-body p-0">
        @if($orders->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-file-earmark-x fs-2 d-block mb-2"></i>
                No purchase orders found.
                <a href="{{ route('purchase-orders.create') }}">Create your first PO →</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-erp mb-0">
                    <thead>
                        <tr>
                            <th>PO Number</th>
                            <th>Supplier</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Total Amount</th>
                            <th>Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('purchase-orders.show', $order) }}"
                                   class="fw-semibold text-decoration-none"
                                   style="color: var(--erp-primary)">
                                    {{ $order->po_number }}
                                </a>
                            </td>
                            <td>{{ $order->supplier->name ?? '—' }}</td>
                            <td class="text-center">
                                <span class="badge-status badge-{{ strtolower($order->status->value) }}">
                                    {{ $order->status->label() }}
                                </span>
                            </td>
                            <td class="text-end fw-semibold">${{ number_format($order->total_amount, 2) }}</td>
                            <td class="text-muted" style="font-size:.8rem">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="text-center">
                                <a href="{{ route('purchase-orders.show', $order) }}"
                                   class="btn btn-sm btn-erp-ghost">
                                    <i class="bi bi-eye me-1"></i>View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($orders->hasPages())
            <div class="p-3 d-flex justify-content-end">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
            @endif
        @endif
    </div>
</div>
@endsection
