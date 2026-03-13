@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h5 mb-0">Nouveau transfert</h1>
        <a href="{{ route('stocks.index') }}" class="btn btn-light btn-sm">Retour stocks</a>
    </div>

    @php
        $isCaissier = auth()->user()?->role?->slug === 'caissier';
        $filteredProducts = $isCaissier ? $products->where('type', 'boisson') : $products;
        $productColClass = $isCaissier ? 'col-lg-3 col-md-4' : 'col-lg-3 col-md-4';
        $fieldColClass = $isCaissier ? 'col-lg-3 col-md-4' : 'col-lg-2 col-md-4';
        $typeColClass = $isCaissier ? '' : 'col-lg-3 col-md-6';
    @endphp
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('transfers.store') }}">
                @csrf
                <div class="row g-3">
                    @if($isCaissier)
                        <input type="hidden" name="type" value="boisson">
                    @else
                        <div class="{{ $typeColClass }}">
                            <label class="form-label">Type</label>
                            <div class="d-flex gap-3 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type_nourriture" value="nourriture" @checked(old('type') === 'nourriture') required>
                                    <label class="form-check-label" for="type_nourriture">Nourriture</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type" id="type_boisson" value="boisson" @checked(old('type') === 'boisson') required>
                                    <label class="form-check-label" for="type_boisson">Boisson</label>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="{{ $productColClass }}">
                        <label class="form-label">Produit</label>
                        <select name="product_id" id="transferProduct" class="form-select" required>
                            @forelse($filteredProducts as $product)
                                <option value="{{ $product->id }}" data-type="{{ $product->type }}" @selected(old('product_id') == $product->id)>{{ $product->name }}</option>
                            @empty
                                <option value="" disabled selected>Aucun produit boisson</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="{{ $fieldColClass }}">
                        <label class="form-label">De</label>
                        <input type="hidden" name="from_location" id="fromLocation" value="{{ $isCaissier ? 'serveur' : old('from_location') }}">
                        <input type="text" id="fromLocationDisplay" class="form-control" value="{{ $isCaissier ? 'Stock serveur' : '' }}" readonly>
                    </div>
                    <div class="{{ $fieldColClass }}">
                        <label class="form-label">Vers</label>
                        <input type="hidden" name="to_location" id="toLocation" value="{{ $isCaissier ? 'frigo_vente' : old('to_location') }}" required>
                        <input type="text" id="toLocationDisplay" class="form-control" value="{{ $isCaissier ? 'Frigo boisson' : '' }}" readonly>
                    </div>
                    <div class="{{ $fieldColClass }}">
                        <label class="form-label">Quantite</label>
                        <input type="number" step="1" min="1" name="quantity" class="form-control" value="{{ old('quantity') }}" required>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary">Enregistrer le transfert</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
@if(!$isCaissier)
<script>
    const typeInputs = document.querySelectorAll('input[name="type"]');
    const productSelect = document.getElementById('transferProduct');
    const fromLocation = document.getElementById('fromLocation');
    const toLocation = document.getElementById('toLocation');
    const fromLocationDisplay = document.getElementById('fromLocationDisplay');
    const toLocationDisplay = document.getElementById('toLocationDisplay');
    const getSelectedType = () => document.querySelector('input[name="type"]:checked')?.value ?? '';

    const locationLabels = {
        central_nourriture: 'Stock central nourriture',
        central_boisson: 'Stock central boisson',
        frigo_cuisine: 'Frigo cuisine',
        frigo_vente: 'Frigo boisson',
        serveur: 'Stock serveur',
    };

    const syncSelectByType = (select, type) => {
        let hasSelection = false;
        Array.from(select.options).forEach(opt => {
            if (!opt.dataset.type) return;
            const allowedTypes = opt.dataset.type.split(',');
            const show = allowedTypes.includes(type);
            opt.hidden = !show;
            if (show && opt.selected) {
                hasSelection = true;
            }
        });
        if (!hasSelection) {
            select.value = '';
        }
    };

    const syncFormByType = () => {
        const type = getSelectedType();
        const hasType = type !== '';

        productSelect.disabled = !hasType;

        if (!hasType) {
            productSelect.value = '';
            fromLocation.value = '';
            toLocation.value = '';
            fromLocationDisplay.value = '';
            toLocationDisplay.value = '';
            return;
        }

        syncSelectByType(productSelect, type);

        if (type === 'nourriture') {
            fromLocation.value = 'central_nourriture';
            toLocation.value = 'frigo_cuisine';
        } else if (type === 'boisson') {
            fromLocation.value = 'central_boisson';
            toLocation.value = 'serveur';
        }

        fromLocationDisplay.value = locationLabels[fromLocation.value] ?? '';
        toLocationDisplay.value = locationLabels[toLocation.value] ?? '';
    };

    typeInputs.forEach(input => input.addEventListener('change', syncFormByType));
    syncFormByType();
</script>
@endif
@endpush
