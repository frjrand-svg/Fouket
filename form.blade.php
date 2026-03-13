@php
    $types = \App\Models\Product::types();
    $selectedType = old('type', $product->type ?? '');
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nom</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Type</label>
        <select name="type" id="productType" class="form-select" required>
            <option value=""></option>
            @foreach($types as $value => $label)
                <option value="{{ $value }}" @selected($selectedType === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <input type="hidden" name="unit" id="productUnitHidden" value="{{ old('unit', $product->unit ?? '') }}">
    <div class="col-md-3">
        <label class="form-label">Prix U</label>
        <input type="number" step="1" min="0" inputmode="numeric" pattern="[0-9]*" oninput="this.value=this.value.replace(/[^0-9]/g,'')" name="sale_price" class="form-control" value="{{ (int) old('sale_price', $product->sale_price ?? 0) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Alerte Qte stock</label>
        <input type="number" step="1" min="1" inputmode="numeric" pattern="[0-9]*" oninput="this.value=this.value.replace(/[^0-9]/g,'')" name="low_stock_threshold" class="form-control" value="{{ (int) old('low_stock_threshold', $product->low_stock_threshold ?? 0) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Stock initial</label>
        <input type="number" step="1" min="0" inputmode="numeric" pattern="[0-9]*" oninput="this.value=this.value.replace(/[^0-9]/g,'')" name="initial_quantity" class="form-control" value="{{ (int) old('initial_quantity', 0) }}">
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))>
            <label class="form-check-label" for="is_active">Actif</label>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const typeSelect = document.getElementById('productType');
    const unitHidden = document.getElementById('productUnitHidden');
    const setUnitFromType = () => {
        if (!typeSelect || !unitHidden) return;
        if (typeSelect.value === 'nourriture') {
            unitHidden.value = 'plat';
        } else if (typeSelect.value === 'boisson') {
            unitHidden.value = 'bouteille';
        }
    };
    typeSelect?.addEventListener('change', setUnitFromType);
    if (!unitHidden.value) {
        setUnitFromType();
    }
</script>
@endpush


