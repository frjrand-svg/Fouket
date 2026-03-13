@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h5 mb-0">Ventes du jour</h1>
        <a href="{{ route('pos.index') }}" class="btn btn-sm btn-outline-secondary">Retour caisse</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                <tr>
                    <th>Produit</th>
                    <th class="text-end">Qte</th>
                    <th class="text-end">Prix U</th>
                    <th class="text-end">Total</th>
                </tr>
                </thead>
                <tbody>
                @forelse($salesToday as $sale)
                    @foreach($sale->items as $item)
                        <tr>
                            <td>{{ $item->product?->name ?? 'Produit supprime' }}</td>
                            <td class="text-end">{{ number_format($item->quantity, 0, ',', ' ') }}</td>
                            <td class="text-end">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                            <td class="text-end">{{ number_format($item->line_total, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    @endforeach
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">Aucune vente aujourdâ€™hui.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
