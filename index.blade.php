@extends('layouts.app')

@section('content')
    <style>
        .stock-card { border-left: 6px solid transparent; }
        .stock-blue { border-left-color: #38bdf8; background: rgba(56, 189, 248, 0.10); }
        .stock-green { border-left-color: #ff8a1f; background: rgba(255, 138, 31, 0.10); }
        .stock-red { border-left-color: #d1242f; background: rgba(209, 36, 47, 0.08); }
    </style>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h5 mb-0">Stocks par emplacement</h1>
        @if(auth()->user()->hasRole('gerante'))
            <a href="{{ route('transfers.create') }}" class="btn btn-primary btn-sm">Nouveau transfert</a>
        @endif
    </div>

    @php
        $order = [
            'central_nourriture',
            'central_boisson',
            'serveur',
            'frigo_cuisine',
            'frigo_vente',
        ];
        $orderedStocks = collect($stocks)
            ->sortBy(fn ($items, $location) => array_search($location, $order, true) === false ? 999 : array_search($location, $order, true));
    @endphp
    <div class="list-group">
        @forelse($orderedStocks as $location => $items)
            @php
                $collapseId = 'stockDetail-' . $location;
            @endphp
            @php
                $locationClass = match ($location) {
                    'central_nourriture' => 'stock-blue',
                    'central_boisson' => 'stock-blue',
                    'frigo_cuisine' => 'stock-green',
                    'frigo_vente' => 'stock-green',
                    'serveur' => 'stock-red',
                    default => ''
                };
            @endphp
            <div class="list-group-item stock-card {{ $locationClass }}">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">{{ \App\Models\Stock::LOCATIONS[$location] ?? $location }}</div>
                        @if(isset($totals[$location]))
                            <div class="small text-muted">
                                Qte: {{ number_format($totals[$location]['qty'], 0, ',', ' ') }}
                                | Valeur: {{ number_format($totals[$location]['value'], 0, ',', ' ') }} FCFA
                            </div>
                        @endif
                    </div>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
                        Détails
                    </button>
                </div>
                <div class="collapse mt-3" id="{{ $collapseId }}">
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Qte</th>
                                <th class="text-nowrap text-center">Alerte Qte stock</th>
                                <th class="text-nowrap">Prix U</th>
                                <th class="text-nowrap">Valeur</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($items as $stock)
                                <tr>
                                    <td>{{ $stock->product?->name }}</td>
                                    <td>{{ number_format($stock->quantity, 0, ',', ' ') }}</td>
                                    <td class="text-center">{{ number_format($stock->product?->low_stock_threshold ?? 0, 0, ',', ' ') }}</td>
                                    <td class="text-nowrap">{{ number_format($stock->product?->sale_price ?? 0, 0, ',', ' ') }} FCFA</td>
                                    <td class="text-nowrap">{{ number_format(($stock->product?->sale_price ?? 0) * (float) $stock->quantity, 0, ',', ' ') }} FCFA</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">Aucune donnee</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="list-group-item text-center text-muted py-3">Aucun stock pour le moment.</div>
        @endforelse
    </div>
@endsection

