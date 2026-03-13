@extends('layouts.app')

@section('content')
    <style>
        .dash-card {
            border: 1px solid #e2e8f0;
            background: #ffffff;
            transition: all 0.25s ease;
        }

        .dash-card:hover {
            transform: translateY(-3px);
            border-color: rgba(37, 99, 235, 0.45);
        }

        .dash-value {
            color: #2563eb;
            font-weight: 800;
        }

        .dash-header {
            border-bottom: 1px solid #e2e8f0;
            background: transparent;
        }

        .dash-link {
            color: #2563eb;
            text-decoration: none;
        }

        .dash-link:hover {
            color: #1d4ed8;
        }
    </style>

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
        <div>
            <h1 class="h3 mb-1">Bonjour, {{ $user->name }}</h1>
            @if($user?->hasRole('caissier'))
                <p class="text-muted mb-0">Vue caissier : ventes, stocks et transferts.</p>
            @else
                <p class="text-muted mb-0">Vue gerante : suivi global, stocks et flux.</p>
            @endif
        </div>
        <div class="d-flex gap-2 mt-2 mt-md-0">
            <a href="{{ route('transfers.create') }}" class="btn btn-outline-secondary btn-sm">Transferer du stock</a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="card h-100 dash-card">
                <div class="card-body text-center py-4">
                    <p class="text-muted mb-1">CA aujourd'hui</p>
                    <h2 class="h4 mb-0 dash-value">{{ number_format($salesToday, 0, ',', ' ') }} FCFA</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 dash-card">
                <div class="card-body text-center py-4">
                    <p class="text-muted mb-1">Produits actifs</p>
                    <h2 class="h4 mb-0">{{ $productsCount }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 dash-card">
                <div class="card-body text-center py-4">
                    <p class="text-muted mb-1">Alertes stock</p>
                    <h2 class="h4 mb-0">{{ $lowStocks->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="d-flex flex-column gap-3 h-100">
                <div class="card dash-card">
                    <div class="card-body">
                        <p class="text-muted mb-1">Valeur stock nourriture</p>
                        <h2 class="h5 mb-0 dash-value">{{ number_format($stockValueNourriture, 0, ',', ' ') }} FCFA</h2>
                    </div>
                </div>
                <div class="card dash-card">
                    <div class="card-body">
                        <p class="text-muted mb-1">Valeur stock boisson</p>
                        <h2 class="h5 mb-0 dash-value">{{ number_format($stockValueBoisson, 0, ',', ' ') }} FCFA</h2>
                    </div>
                </div>
                <div class="card dash-card">
                    <div class="card-body">
                        <p class="text-muted mb-1">Valeur stock total</p>
                        <h2 class="h5 mb-0 dash-value">{{ number_format($stockValueTotal, 0, ',', ' ') }} FCFA</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100 dash-card">
                <div class="card-header dash-header d-flex justify-content-between align-items-center">
                    <strong>Stocks faibles</strong>
                    <a href="{{ route('stocks.index') }}" class="small dash-link">Voir stocks</a>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Stock actuel</th>
                            <th class="text-end">Qte</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($lowStocks as $stock)
                            <tr>
                                <td>{{ $stock->product?->name }}</td>
                                <td>{{ \App\Models\Stock::LOCATIONS[$stock->location] ?? $stock->location }}</td>
                                <td class="text-end">{{ number_format($stock->quantity ?? 0, 0, ',', ' ') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">Aucun stock faible.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100 dash-card">
                <div class="card-header dash-header d-flex justify-content-between align-items-center">
                    <strong>Derniers transferts</strong>
                    <a href="{{ route('transfers.create') }}" class="small dash-link">Nouveau transfert</a>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                        <tr>
                            <th>Produit</th>
                            <th>De -> Vers</th>
                            <th class="text-end">Qte</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($lastTransfers as $transfer)
                            <tr>
                                <td>{{ $transfer->product?->name }}</td>
                                <td>
                                    {{ $transfer->from_location ? (\App\Models\Stock::LOCATIONS[$transfer->from_location] ?? $transfer->from_location) : '-' }}
                                    ->
                                    {{ \App\Models\Stock::LOCATIONS[$transfer->to_location] ?? $transfer->to_location }}
                                </td>
                                <td class="text-end">{{ number_format($transfer->quantity, 0, ',', ' ') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">Aucun transfert enregistre.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


