@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-header bg-white">Ouverture de caisse</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('cash.open') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button class="btn btn-primary w-100">Ouvrir la caisse</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
