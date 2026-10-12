<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Mini ERP – Inventory & Purchase Order Management System">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') – Mini ERP</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --erp-primary:    #6366f1;
            --erp-primary-hover: #4f46e5;
            --erp-surface:    #0f172a;
            --erp-surface-2:  #1e293b;
            --erp-surface-3:  #334155;
            --erp-border:     rgba(99, 102, 241, 0.15);
            --erp-text:       #e2e8f0;
            --erp-muted:      #94a3b8;
            --erp-success:    #10b981;
            --erp-warning:    #f59e0b;
            --erp-danger:     #ef4444;
            --erp-info:       #38bdf8;
            --sidebar-width:  260px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--erp-surface);
            color: var(--erp-text);
            min-height: 100vh;
        }

        /* ─── Sidebar ─── */
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--erp-surface-2);
            border-right: 1px solid var(--erp-border);
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: transform .25s ease;
        }

        .sidebar-brand {
            padding: 1.5rem 1.5rem 1rem;
            border-bottom: 1px solid var(--erp-border);
        }

        .sidebar-brand .brand-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--erp-primary), #818cf8);
            display: inline-flex;
            align-items: center; justify-content: center;
            font-size: 1.2rem;
            color: #fff;
            margin-right: .6rem;
        }

        .sidebar-brand .brand-name {
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: -.02em;
            color: var(--erp-text);
        }

        .sidebar-brand .brand-tagline {
            font-size: .7rem;
            color: var(--erp-muted);
            margin-top: 1px;
        }

        .sidebar-nav { padding: 1rem 0; flex: 1; }

        .nav-section-label {
            font-size: .65rem;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--erp-muted);
            padding: .75rem 1.5rem .4rem;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .6rem 1.5rem;
            border-radius: 0;
            color: var(--erp-muted);
            font-size: .875rem;
            font-weight: 500;
            transition: all .2s;
            position: relative;
        }

        .sidebar-nav .nav-link i { font-size: 1.05rem; }

        .sidebar-nav .nav-link:hover {
            color: var(--erp-text);
            background: rgba(99,102,241,.08);
        }

        .sidebar-nav .nav-link.active {
            color: var(--erp-primary);
            background: rgba(99,102,241,.12);
        }

        .sidebar-nav .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: var(--erp-primary);
            border-radius: 0 3px 3px 0;
        }

        .sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--erp-border);
        }

        /* ─── Main Content ─── */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: var(--erp-surface-2);
            border-bottom: 1px solid var(--erp-border);
            padding: .85rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
            backdrop-filter: blur(12px);
        }

        .topbar .page-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--erp-text);
            margin: 0;
        }

        .topbar .user-badge {
            display: flex;
            align-items: center;
            gap: .5rem;
            font-size: .85rem;
            color: var(--erp-muted);
        }

        .topbar .user-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--erp-primary), #818cf8);
            display: inline-flex;
            align-items: center; justify-content: center;
            font-size: .8rem;
            font-weight: 700;
            color: #fff;
        }

        .content-area { padding: 2rem; flex: 1; }

        /* ─── Cards ─── */
        .card-erp {
            background: var(--erp-surface-2);
            border: 1px solid var(--erp-border);
            border-radius: 14px;
        }

        .card-erp .card-header {
            background: transparent;
            border-bottom: 1px solid var(--erp-border);
            padding: 1rem 1.5rem;
            font-weight: 600;
            font-size: .9rem;
        }

        .card-erp .card-body { padding: 1.5rem; }

        /* ─── Metric Cards ─── */
        .metric-card {
            background: var(--erp-surface-2);
            border: 1px solid var(--erp-border);
            border-radius: 14px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            transition: transform .2s, box-shadow .2s;
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,.3);
        }

        .metric-card::after {
            content: '';
            position: absolute;
            top: -40px; right: -40px;
            width: 120px; height: 120px;
            border-radius: 50%;
            opacity: .07;
        }

        .metric-card.primary::after   { background: var(--erp-primary); }
        .metric-card.success::after   { background: var(--erp-success); }
        .metric-card.warning::after   { background: var(--erp-warning); }
        .metric-card.info::after      { background: var(--erp-info); }

        .metric-icon {
            width: 46px; height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center; justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }

        .metric-icon.primary { background: rgba(99,102,241,.15); color: var(--erp-primary); }
        .metric-icon.success { background: rgba(16,185,129,.15); color: var(--erp-success); }
        .metric-icon.warning { background: rgba(245,158,11,.15);  color: var(--erp-warning); }
        .metric-icon.info    { background: rgba(56,189,248,.15);  color: var(--erp-info); }

        .metric-value {
            font-size: 1.9rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: .25rem;
            letter-spacing: -.02em;
        }

        .metric-label {
            font-size: .8rem;
            color: var(--erp-muted);
            font-weight: 500;
        }

        /* ─── Tables ─── */
        .table-erp { color: var(--erp-text); margin: 0; }
        .table-erp thead th {
            background: var(--erp-surface-3);
            color: var(--erp-muted);
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            border-color: var(--erp-border);
            padding: .85rem 1rem;
        }
        .table-erp tbody td {
            border-color: var(--erp-border);
            padding: .85rem 1rem;
            vertical-align: middle;
            font-size: .875rem;
        }
        .table-erp tbody tr:hover { background: rgba(99,102,241,.04); }
        .table-erp tbody tr.low-stock-row { background: rgba(239,68,68,.06); }
        .table-erp tbody tr.low-stock-row:hover { background: rgba(239,68,68,.1); }

        /* ─── Status Badges ─── */
        .badge-status {
            font-size: .7rem;
            font-weight: 600;
            padding: .3em .75em;
            border-radius: 50px;
            letter-spacing: .04em;
        }
        .badge-draft      { background: rgba(148,163,184,.15); color: #94a3b8; border: 1px solid rgba(148,163,184,.2); }
        .badge-approved   { background: rgba(56,189,248,.15);  color: #38bdf8;  border: 1px solid rgba(56,189,248,.2); }
        .badge-received   { background: rgba(16,185,129,.15);  color: #10b981;  border: 1px solid rgba(16,185,129,.2); }
        .badge-cancelled  { background: rgba(239,68,68,.15);   color: #ef4444;  border: 1px solid rgba(239,68,68,.2); }

        /* ─── Buttons ─── */
        .btn-erp-primary {
            background: var(--erp-primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: .5rem 1.2rem;
            font-size: .875rem;
            font-weight: 500;
            transition: background .2s, transform .1s;
        }
        .btn-erp-primary:hover {
            background: var(--erp-primary-hover);
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-erp-ghost {
            background: transparent;
            color: var(--erp-muted);
            border: 1px solid var(--erp-border);
            border-radius: 8px;
            padding: .5rem 1.2rem;
            font-size: .875rem;
            font-weight: 500;
            transition: all .2s;
        }
        .btn-erp-ghost:hover {
            color: var(--erp-text);
            border-color: rgba(99,102,241,.4);
            background: rgba(99,102,241,.06);
        }

        /* ─── Forms ─── */
        .form-control-erp, .form-select-erp {
            background: var(--erp-surface-3);
            border: 1px solid var(--erp-border);
            color: var(--erp-text);
            border-radius: 8px;
        }
        .form-control-erp:focus, .form-select-erp:focus {
            background: var(--erp-surface-3);
            border-color: var(--erp-primary);
            color: var(--erp-text);
            box-shadow: 0 0 0 3px rgba(99,102,241,.15);
        }

        /* ─── Alerts ─── */
        .alert-erp {
            border-radius: 10px;
            font-size: .875rem;
            border: 1px solid;
        }

        /* ─── Scrollbar ─── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--erp-surface); }
        ::-webkit-scrollbar-thumb { background: var(--erp-surface-3); border-radius: 3px; }

        /* ─── Animations ─── */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up { animation: fadeInUp .35s ease both; }
        .fade-in-up:nth-child(2) { animation-delay: .05s; }
        .fade-in-up:nth-child(3) { animation-delay: .10s; }
        .fade-in-up:nth-child(4) { animation-delay: .15s; }

        /* ─── Low stock pill ─── */
        .stock-badge-low {
            background: rgba(239,68,68,.15);
            color: #ef4444;
            border: 1px solid rgba(239,68,68,.25);
            font-size: .7rem;
            padding: .2em .6em;
            border-radius: 50px;
        }
        .stock-badge-ok {
            background: rgba(16,185,129,.12);
            color: #10b981;
            border: 1px solid rgba(16,185,129,.2);
            font-size: .7rem;
            padding: .2em .6em;
            border-radius: 50px;
        }

        /* Mobile sidebar toggle */
        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- ─── Sidebar ─── --}}
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand d-flex align-items-center">
        <span class="brand-icon"><i class="bi bi-boxes"></i></span>
        <div>
            <div class="brand-name">Mini ERP</div>
            <div class="brand-tagline">Inventory & Procurement</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Main</div>
        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>

        <div class="nav-section-label mt-2">Inventory</div>
        <a href="{{ route('inventory.index') }}"
           class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> Products
        </a>

        <div class="nav-section-label mt-2">Procurement</div>
        <a href="{{ route('purchase-orders.index') }}"
           class="nav-link {{ request()->routeIs('purchase-orders.index') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text"></i> Purchase Orders
        </a>
        <a href="{{ route('purchase-orders.create') }}"
           class="nav-link {{ request()->routeIs('purchase-orders.create') ? 'active' : '' }}">
            <i class="bi bi-plus-circle"></i> New Order
        </a>
    </nav>

    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-erp-ghost w-100 text-start d-flex align-items-center gap-2">
                <i class="bi bi-box-arrow-left"></i> Logout
            </button>
        </form>
    </div>
</aside>

{{-- ─── Main Content ─── --}}
<div class="main-content">
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary d-lg-none"
                    onclick="document.getElementById('sidebar').classList.toggle('open')"
                    aria-label="Toggle sidebar">
                <i class="bi bi-list"></i>
            </button>
            <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
        </div>
        <div class="user-badge">
            <span class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
            <span class="d-none d-md-inline">{{ auth()->user()->name ?? 'User' }}</span>
        </div>
    </header>

    <main class="content-area">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-erp alert-success border-success mb-3 fade-in-up d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-check-circle-fill text-success"></i>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-erp alert-danger border-danger mb-3 fade-in-up d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</div>

{{-- Bootstrap 5 JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')
</body>
</html>
