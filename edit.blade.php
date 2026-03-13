@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h5 mb-0">Modifier produit</h1>
        <a href="{{ route('products.index') }}" class="btn btn-light btn-sm">Retour</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('products.update', $product) }}">
                @csrf
                @method('PUT')
                @include('products.partials.form', ['product' => $product])
                <div class="mt-4">
                    <button class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
@endsection
