@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card">
                <div class="card-header bg-white">Fermeture de caisse</div>
                <div class="card-body">
                    <div class="small text-muted mb-3">
                        Caisse ouverte le {{ $session->opened_at?->format('d/m/Y H:i') }}.
                    </div>
                    <form method="POST" action="{{ route('cash.close') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Rapport de la journee (obligatoire)</label>
                            <textarea name="comment" class="form-control" rows="6" maxlength="1000" required></textarea>
                        </div>
                        <button class="btn btn-danger w-100">Fermer la caisse</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
