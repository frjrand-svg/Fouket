<div class="table-responsive">
    <table class="table table-sm mb-0">
        <thead>
        <tr>
            <th>Produit</th>
            <th class="text-end">Qte</th>
            <th class="text-end">Prix U</th>
            <th class="text-end">Total ligne</th>
        </tr>
        </thead>
        <tbody>
        @forelse($sale->items as $item)
            <tr>
                <td>{{ $item->product?->name ?? 'Produit supprime' }}</td>
                <td class="text-end">{{ number_format($item->quantity, 0, ',', ' ') }}</td>
                <td class="text-end">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">{{ number_format($item->line_total, 0, ',', ' ') }} FCFA</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-muted">Aucune ligne de commande.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
