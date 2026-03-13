<!doctype html>
<html lang="fr" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Resto Fouket' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-blue: #2563eb;
            --light-bg: #f8fafc;
            --pure-white: #ffffff;
            --text-main: #1e293b;
            --accent-blue: #eff6ff;
            --border-soft: #e2e8f0;
            --muted: #64748b;
        }

        body {
            background:
                radial-gradient(circle at 0% 0%, rgba(201, 164, 91, 0.12), transparent 35%),
                radial-gradient(circle at 90% 15%, rgba(127, 29, 66, 0.10), transparent 40%),
                url('/assets/ui-kit/bg-wood-4k.svg');
            background-size: cover;
            background-position: center;
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(248, 250, 252, 0.18);
            pointer-events: none;
            z-index: 0;
        }

        h1, h2, h3, h4, h5, h6,
        .brand-title {
            font-family: 'Playfair Display', serif;
        }

        .app-shell {
            min-height: 100vh;
            position: relative;
            z-index: 1;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.02)),
                url('/assets/ui-kit/bg-wood-4k.svg');
            background-size: cover;
            background-position: center;
        }

        .sidebar {
            width: 270px;
            background: rgba(255, 255, 255, 0.92);
            border-right: 1px solid var(--border-soft);
            color: var(--text-main);
            backdrop-filter: blur(6px);
        }

        main {
            background: transparent;
        }

        .sidebar-link {
            text-decoration: none;
            color: #475569;
            padding: 12px 14px;
            border-radius: 14px;
            font-weight: 700;
            transition: all 0.2s ease;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background-color: var(--brand-blue);
            color: #fff;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.2);
        }

        .card,
        .list-group-item,
        .modal-content {
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid var(--border-soft);
            border-radius: 18px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            color: var(--text-main);
            backdrop-filter: blur(4px);
        }

        .card-header,
        .card-footer {
            background: transparent !important;
            border-color: var(--border-soft);
        }

        .table {
            --bs-table-bg: transparent;
            --bs-table-color: #1e293b;
            --bs-table-border-color: #e2e8f0;
            --bs-table-striped-color: #1e293b;
            --bs-table-striped-bg: #f8fafc;
            --bs-table-hover-bg: #eff6ff;
            --bs-table-hover-color: #1e293b;
            margin-bottom: 0;
        }

        .table thead th {
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-weight: 800;
        }

        .form-control,
        .form-select {
            background: #fff;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        .form-control:focus,
        .form-select:focus {
            background: #fff;
            color: #0f172a;
            border-color: var(--brand-blue);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
        }

        .form-check-input:checked {
            background-color: var(--brand-blue);
            border-color: var(--brand-blue);
        }

        .text-muted,
        .small.text-muted {
            color: var(--muted) !important;
        }

        .btn-primary {
            --bs-btn-bg: var(--brand-blue);
            --bs-btn-border-color: var(--brand-blue);
            --bs-btn-color: #fff;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #1d4ed8;
            --bs-btn-hover-border-color: #1d4ed8;
            --bs-btn-active-color: #fff;
            --bs-btn-active-bg: #1e40af;
            --bs-btn-active-border-color: #1e40af;
            font-weight: 700;
        }

        .btn-outline-primary {
            --bs-btn-color: var(--brand-blue);
            --bs-btn-border-color: #93c5fd;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: var(--brand-blue);
            --bs-btn-hover-border-color: var(--brand-blue);
        }

        .btn-outline-secondary,
        .btn-light,
        .btn-outline-light {
            --bs-btn-color: #475569;
            --bs-btn-border-color: #cbd5e1;
            --bs-btn-hover-color: #1e293b;
            --bs-btn-hover-bg: #f8fafc;
            --bs-btn-hover-border-color: #94a3b8;
            --bs-btn-bg: #fff;
        }

        .alert {
            border: 1px solid #bfdbfe;
            background: #eff6ff;
            color: #1e3a8a;
            border-radius: 14px;
        }

        .badge {
            border-radius: 999px;
        }

        .role-badge {
            font-size: 10px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #1d4ed8;
            background: #dbeafe;
            border-radius: 999px;
            padding: 3px 8px;
            display: inline-block;
            font-weight: 700;
        }

        @media (max-width: 991.98px) {
            .app-shell {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                border-right: 0;
                border-bottom: 1px solid var(--border-soft);
            }
        }
    </style>
</head>
<body>
<div class="app-shell d-flex">
    @auth
    <aside class="sidebar p-3 d-flex flex-column">
        <div class="d-flex align-items-center mb-4">
            <div>
                <div class="fw-bold fs-5 brand-title" style="color: #1e3a8a;">Le Fouquet</div>
                <span class="role-badge">{{ auth()->user()->role?->name ?? 'Utilisateur' }}</span>
            </div>
        </div>
        <nav class="nav flex-column gap-2">
            <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Tableau de bord</a>
            @if(auth()->user()->hasRole('gerante'))
                <a class="sidebar-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">Produits</a>
                <a class="sidebar-link {{ request()->routeIs('stocks.index') ? 'active' : '' }}" href="{{ route('stocks.index') }}">Stocks</a>
                <a class="sidebar-link {{ request()->routeIs('transfers.*') ? 'active' : '' }}" href="{{ route('transfers.create') }}">Transfert stock</a>
                <a class="sidebar-link {{ request()->routeIs('cash.history') ? 'active' : '' }}" href="{{ route('cash.history') }}">Historique ventes</a>
            @endif
            @if(auth()->user()->hasRole('caissier'))
                <a class="sidebar-link {{ request()->routeIs('pos.*') ? 'active' : '' }}" href="{{ route('pos.index') }}">Caisse</a>
                <a class="sidebar-link {{ request()->routeIs('stocks.index') ? 'active' : '' }}" href="{{ route('stocks.index') }}">Stocks</a>
                <a class="sidebar-link {{ request()->routeIs('transfers.*') ? 'active' : '' }}" href="{{ route('transfers.create') }}">Transfert stock</a>
            @endif
        </nav>
        <div class="mt-auto pt-4">
            <div class="small text-muted mb-2">{{ auth()->user()->name }}</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-sm btn-outline-secondary w-100">Deconnexion</button>
            </form>
        </div>
    </aside>
    @endauth

    <main class="flex-grow-1 p-4">
        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
