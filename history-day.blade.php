@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 day-trame">
        <div>
            <h1 class="h5 mb-1">Arrete de vente du {{ $day->format('d/m/Y') }}</h1>
            <div class="text-muted small">{{ $totals['count'] }} ventes</div>
        </div>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('cash.history') }}">Retour</a>
    </div>

    <div class="card mb-3">
        <div class="card-body d-flex flex-wrap gap-3 small">
            <span><strong>Total ventes:</strong> {{ number_format($totals['total_sales'], 0, ',', ' ') }} FCFA</span>
        </div>
    </div>

    @php($paymentLabels = ['cash' => 'Cash', 'mobile' => 'Mobile money'])
    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                <tr>
                    <th>Heure</th>
                    <th>Statut</th>
                    <th>Paiement</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($sales as $sale)
                    <tr>
                        <td>{{ $sale->created_at?->timezone(config('app.timezone'))->format('H:i') }}</td>
                        <td>{{ $sale->status }}</td>
                        <td>{{ $paymentLabels[$sale->payment_method] ?? str_replace('_', ' ', $sale->payment_method) }}</td>
                        <td class="text-end">{{ number_format($sale->total_amount, 0, ',', ' ') }} FCFA</td>
                        <td class="text-end">
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-primary js-sale-toggle"
                                data-sale-id="{{ $sale->id }}"
                                data-url="{{ route('sales.detail', $sale) }}"
                            >Voir details</button>
                        </td>
                    </tr>
                    <tr id="sale-detail-{{ $sale->id }}" class="d-none">
                        <td colspan="7" class="bg-light">
                            <div class="text-muted small">Chargement...</div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-3">Aucune vente pour cette journee.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.js-sale-toggle').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const saleId = btn.dataset.saleId;
            const row = document.getElementById(`sale-detail-${saleId}`);
            if (!row) return;

            const isHidden = row.classList.contains('d-none');
            if (!isHidden) {
                row.classList.add('d-none');
                btn.textContent = 'Voir details';
                return;
            }

            row.classList.remove('d-none');
            btn.textContent = 'Masquer details';

            if (row.dataset.loaded === '1') {
                return;
            }

            try {
                const response = await fetch(btn.dataset.url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                const data = await response.json();
                row.querySelector('td').innerHTML = data.html || '<div class="text-muted small">Aucun detail.</div>';
                row.dataset.loaded = '1';
            } catch (error) {
                row.querySelector('td').innerHTML = '<div class="text-danger small">Erreur de chargement des details.</div>';
            }
        });
    });
</script>
@endpush

<style>
    .day-trame {
        padding: 12px 14px;
        border-radius: 10px;
        border: 1px solid rgba(15, 33, 61, 0.08);
        background:
            linear-gradient(0deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.9)),
            repeating-linear-gradient(
                45deg,
                rgba(15, 98, 254, 0.08),
                rgba(15, 98, 254, 0.08) 6px,
                transparent 6px,
                transparent 12px
            );
    }
</style>
