@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h5 mb-0">Historique ventes</h1>
    </div>

    @forelse($sessionsByDay as $dayKey => $sessions)
        @php
            try {
                $dayLabel = \Carbon\Carbon::createFromFormat('Y-m-d', $dayKey, config('app.timezone'))->format('d/m/Y');
            } catch (\Throwable $e) {
                $dayLabel = $dayKey;
            }
            $collapseId = 'day-detail-' . $dayKey;
            $sales = $sessions->flatMap->sales;
            $totals = [
                'count' => $sales->count(),
                'total_sales' => (int) $sales->sum('total_amount'),
            ];
        @endphp
        <div class="mb-2 day-trame">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="h6 text-uppercase text-muted mb-0">Arrete de vente du {{ $dayLabel }}</h2>
                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
                    Details
                </button>
            </div>
            <div class="collapse mt-3" id="{{ $collapseId }}">
                <div class="card mb-3">
                    <div class="card-body d-flex flex-wrap gap-3 small">
                        <span><strong>Total ventes:</strong> {{ number_format($totals['total_sales'], 0, ',', ' ') }} FCFA</span>
                        <span><strong>Nombre ventes:</strong> {{ number_format($totals['count'], 0, ',', ' ') }}</span>
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
                                            data-bs-toggle="collapse"
                                            data-bs-target="#sale-detail-{{ $sale->id }}"
                                            aria-expanded="false"
                                            aria-controls="sale-detail-{{ $sale->id }}"
                                            data-sale-id="{{ $sale->id }}"
                                            data-url="{{ route('sales.detail', $sale) }}"
                                        >Voir details</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="sale-detail-cell">
                                        <div class="collapse sale-detail-wrap" id="sale-detail-{{ $sale->id }}">
                                            <div class="sale-detail-loading small">Chargement...</div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-3">Aucune vente pour cette journee.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3"></div>
    @empty
        <div class="card">
            <div class="card-body text-muted">Aucun historique.</div>
        </div>
    @endforelse
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.js-sale-toggle').forEach((btn) => {
        const saleId = btn.dataset.saleId;
        const collapseEl = document.getElementById(`sale-detail-${saleId}`);
        if (!collapseEl) return;

        collapseEl.addEventListener('show.bs.collapse', async () => {
            if (collapseEl.dataset.loaded === '1') return;
            try {
                const response = await fetch(btn.dataset.url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                const data = await response.json();
                collapseEl.innerHTML = data.html || '<div class="sale-detail-loading small">Aucun detail.</div>';
                collapseEl.dataset.loaded = '1';
            } catch (error) {
                collapseEl.innerHTML = '<div class="text-danger small">Erreur de chargement des details.</div>';
            }
        });

        collapseEl.addEventListener('shown.bs.collapse', () => {
            btn.textContent = 'Masquer details';
        });

        collapseEl.addEventListener('hidden.bs.collapse', () => {
            btn.textContent = 'Voir details';
        });
    });
</script>
@endpush

<style>
    .day-trame {
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid #bfdbfe;
        background:
            linear-gradient(0deg, rgba(255, 255, 255, 0.94), rgba(255, 255, 255, 0.94)),
            repeating-linear-gradient(
                45deg,
                rgba(37, 99, 235, 0.06),
                rgba(37, 99, 235, 0.06) 6px,
                transparent 6px,
                transparent 12px
            );
    }

    .sale-detail-cell {
        background: #eff6ff !important;
        border-top: 1px solid #bfdbfe;
    }

    .sale-detail-wrap {
        margin-top: 4px;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        padding: 10px;
        background: #ffffff;
    }

    .sale-detail-loading {
        color: #64748b;
    }

    .sale-detail-wrap .table {
        --bs-table-color: #1e293b;
        --bs-table-border-color: #dbeafe;
    }

    .sale-detail-wrap .table thead th {
        color: #2563eb;
    }

    .sale-detail-wrap .text-muted {
        color: #64748b !important;
    }
</style>

