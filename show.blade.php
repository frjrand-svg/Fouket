@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h5 mb-0">Detail vente {{ $sale->reference }}</h1>
        <a href="{{ route('pos.index') }}" class="btn btn-light btn-sm">Retour caisse</a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-3">
                    <div class="border rounded p-2 h-100">
                        <div class="small text-muted">Date</div>
                        <div class="fw-semibold">{{ $sale->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-2 h-100">
                        <div class="small text-muted">Statut</div>
                        <div class="fw-semibold text-capitalize">{{ $sale->status }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-2 h-100">
                        <div class="small text-muted">Paiement</div>
                        <div class="fw-semibold text-capitalize">{{ str_replace('_', ' ', $sale->payment_method) }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-2 h-100">
                        <div class="small text-muted">Total</div>
                        <div class="fw-semibold">{{ number_format($sale->total_amount, 0, ',', ' ') }} FCFA</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white">Produits de la vente</div>
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                <tr>
                    <th>Produit</th>
                    <th>Source stock</th>
                    <th class="text-end">Qte</th>
                    <th class="text-end">Prix U</th>
                    <th class="text-end">Total ligne</th>
                </tr>
                </thead>
                <tbody>
                @forelse($sale->items as $item)
                    <tr>
                        <td>{{ $item->product?->name ?? 'Produit supprime' }}</td>
                        <td>{{ \App\Models\Stock::LOCATIONS[$item->stock_source] ?? $item->stock_source }}</td>
                        <td class="text-end">{{ number_format($item->quantity, 0, ',', ' ') }}</td>
                        <td class="text-end">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                        <td class="text-end">{{ number_format($item->line_total, 0, ',', ' ') }} FCFA</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">Aucun produit sur cette vente.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
