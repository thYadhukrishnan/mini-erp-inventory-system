@extends('layouts.app')

@section('title', 'New Purchase Order')
@section('page-title', 'Create Purchase Order')

@section('content')
<form id="po-form" action="{{ route('purchase-orders.store') }}" method="POST" novalidate>
@csrf

<div class="row g-4">
    {{-- Left Column: Order Info --}}
    <div class="col-lg-4">
        <div class="card-erp h-100 fade-in-up">
            <div class="card-header">
                <i class="bi bi-info-circle me-2 text-primary"></i>Order Details
            </div>
            <div class="card-body">
                {{-- Supplier --}}
                <div class="mb-4">
                    <label for="supplier_id" class="form-label fw-semibold" style="font-size:.85rem">
                        Supplier <span class="text-danger">*</span>
                    </label>
                    <select id="supplier_id" name="supplier_id"
                            class="form-select form-select-erp @error('supplier_id') is-invalid @enderror"
                            required>
                        <option value="">— Select Supplier —</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}"
                                {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Grand Total --}}
                <div class="mt-4 p-3 rounded-3"
                     style="background: rgba(99,102,241,.08); border: 1px solid rgba(99,102,241,.2)">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-semibold" style="font-size:.85rem">Grand Total</span>
                        <span id="grand-total-display"
                              class="fw-bold fs-4"
                              style="color: var(--erp-primary)">$0.00</span>
                    </div>
                    <div class="text-muted mt-1" style="font-size:.75rem">
                        <span id="line-item-count">0</span> line item(s)
                    </div>
                </div>
            </div>
            <div class="card-body border-top" style="border-color: var(--erp-border) !important">
                <button type="submit" id="submit-btn" class="btn btn-erp-primary w-100">
                    <i class="bi bi-check-lg me-2"></i>Create Purchase Order
                </button>
                <a href="{{ route('purchase-orders.index') }}"
                   class="btn btn-erp-ghost w-100 mt-2">
                    <i class="bi bi-arrow-left me-1"></i>Cancel
                </a>
            </div>
        </div>
    </div>

    {{-- Right Column: Line Items --}}
    <div class="col-lg-8">
        <div class="card-erp fade-in-up" style="animation-delay:.08s">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-list-ul me-2 text-primary"></i>Line Items</span>
                <button type="button" id="add-row-btn" class="btn btn-sm btn-erp-primary">
                    <i class="bi bi-plus-lg me-1"></i>Add Item
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-erp mb-0" id="items-table">
                        <thead>
                            <tr>
                                <th style="min-width:200px">Product</th>
                                <th style="min-width:100px">Qty</th>
                                <th style="min-width:130px">Unit Price ($)</th>
                                <th class="text-end" style="min-width:120px">Subtotal</th>
                                <th class="text-center" style="width:50px"></th>
                            </tr>
                        </thead>
                        <tbody id="items-body">
                            {{-- Rows injected by JS --}}
                        </tbody>
                    </table>
                </div>

                <div id="empty-state" class="text-center py-4 text-muted" style="font-size:.875rem">
                    <i class="bi bi-plus-circle fs-3 d-block mb-2" style="opacity:.3"></i>
                    Click <strong>Add Item</strong> to add products to this order.
                </div>

                @error('items')
                    <div class="alert alert-danger m-3 py-2" style="font-size:.85rem">
                        <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>
        </div>
    </div>
</div>
</form>

{{-- Product data for JS --}}
<script id="products-data" type="application/json">
    {!! json_encode($products->map(fn($p) => [
        'id'        => $p->id,
        'name'      => $p->name,
        'sku'       => $p->sku,
        'unit_cost' => (float) $p->unit_cost,
    ])->values()) !!}
</script>
@endsection

@push('scripts')
<script>
(function () {
    const products   = JSON.parse(document.getElementById('products-data').textContent);
    const tbody      = document.getElementById('items-body');
    const emptyState = document.getElementById('empty-state');
    const totalEl    = document.getElementById('grand-total-display');
    const countEl    = document.getElementById('line-item-count');

    let rowIndex = 0;

    function buildProductOptions(selectedId) {
        return products.map(p =>
            `<option value="${p.id}" data-cost="${p.unit_cost}"
                ${p.id == selectedId ? 'selected' : ''}>
                [${p.sku}] ${p.name}
             </option>`
        ).join('');
    }

    function addRow(productId = '', qty = 1, price = '') {
        const idx  = rowIndex++;
        const row  = document.createElement('tr');
        row.dataset.idx = idx;

        row.innerHTML = `
            <td>
                <select name="items[${idx}][product_id]"
                        class="form-select form-select-sm form-select-erp product-select"
                        required>
                    <option value="">— Select —</option>
                    ${buildProductOptions(productId)}
                </select>
            </td>
            <td>
                <input type="number" name="items[${idx}][quantity]"
                       class="form-control form-control-sm form-control-erp qty-input"
                       value="${qty}" min="1" required>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text" style="background:var(--erp-surface-3);border-color:var(--erp-border);color:var(--erp-muted)">$</span>
                    <input type="number" name="items[${idx}][unit_price]"
                           class="form-control form-control-erp price-input"
                           value="${price}" step="0.01" min="0.01" required>
                </div>
            </td>
            <td class="text-end fw-semibold subtotal-cell">$0.00</td>
            <td class="text-center">
                <button type="button" class="btn btn-sm text-danger remove-row"
                        title="Remove row" style="background:rgba(239,68,68,.1);border:none;border-radius:6px;padding:.25rem .5rem">
                    <i class="bi bi-trash3"></i>
                </button>
            </td>
        `;

        tbody.appendChild(row);
        updateRow(row);
        syncUI();
        return row;
    }

    function updateRow(row) {
        const select  = row.querySelector('.product-select');
        const qtyInput   = row.querySelector('.qty-input');
        const priceInput = row.querySelector('.price-input');
        const subtotalEl = row.querySelector('.subtotal-cell');

        // Auto-fill unit price when product changes
        select.addEventListener('change', function () {
            const opt  = this.options[this.selectedIndex];
            const cost = parseFloat(opt.dataset.cost || 0);
            if (cost > 0) priceInput.value = cost.toFixed(2);
            recalcRow(row);
            recalcTotal();
        });

        qtyInput.addEventListener('input', () => { recalcRow(row); recalcTotal(); });
        priceInput.addEventListener('input', () => { recalcRow(row); recalcTotal(); });

        // If product was pre-selected, trigger fill
        if (select.value) {
            const opt  = select.options[select.selectedIndex];
            const cost = parseFloat(opt.dataset.cost || 0);
            if (!priceInput.value && cost > 0) priceInput.value = cost.toFixed(2);
        }

        recalcRow(row);
    }

    function recalcRow(row) {
        const qty   = parseFloat(row.querySelector('.qty-input').value)   || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const sub   = qty * price;
        row.querySelector('.subtotal-cell').textContent = '$' + sub.toFixed(2);
    }

    function recalcTotal() {
        let total = 0;
        tbody.querySelectorAll('tr').forEach(row => {
            const qty   = parseFloat(row.querySelector('.qty-input')?.value)   || 0;
            const price = parseFloat(row.querySelector('.price-input')?.value) || 0;
            total += qty * price;
        });
        totalEl.textContent = '$' + total.toFixed(2);
        countEl.textContent = tbody.querySelectorAll('tr').length;
    }

    function syncUI() {
        const hasRows = tbody.querySelectorAll('tr').length > 0;
        emptyState.style.display = hasRows ? 'none' : 'block';
        recalcTotal();
    }

    // Add Row button
    document.getElementById('add-row-btn').addEventListener('click', () => addRow());

    // Remove Row (delegated)
    tbody.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-row');
        if (btn) {
            btn.closest('tr').remove();
            syncUI();
        }
    });

    // Form validation: ensure at least 1 item
    document.getElementById('po-form').addEventListener('submit', function (e) {
        const rows = tbody.querySelectorAll('tr');
        if (rows.length === 0) {
            e.preventDefault();
            alert('Please add at least one line item before submitting.');
            return;
        }
        // Check for empty product selections
        let valid = true;
        rows.forEach(row => {
            const select = row.querySelector('.product-select');
            if (!select.value) {
                select.classList.add('is-invalid');
                valid = false;
            } else {
                select.classList.remove('is-invalid');
            }
        });
        if (!valid) {
            e.preventDefault();
            alert('Please select a product for all line items.');
        }
    });

    // Bootstrap 5 form validation styles
    document.querySelectorAll('.form-select-erp').forEach(el => {
        el.classList.add('form-select');
    });

    // Add one row by default
    addRow();
}());
</script>
@endpush
