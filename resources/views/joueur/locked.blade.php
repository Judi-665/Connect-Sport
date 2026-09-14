@extends('layouts.app')

@section('title', 'Fonctionnalité verrouillée')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 text-center">
                <div class="card-body p-5">
                    {{-- Icône cadenas --}}
                    <div class="mb-4">
                        <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center p-3">
                            <i class="bi bi-lock fs-1 text-warning"></i>
                        </div>
                    </div>

                    <h3 class="fw-bold mb-2">Fonctionnalité verrouillée</h3>
                    <p class="text-muted mb-4">
                        @if(request()->get('plan') === 'premium')
                            Cette fonctionnalité est disponible uniquement avec l’abonnement <strong class="text-warning">Premium</strong>.
                        @elseif(request()->get('plan') === 'standard')
                            Cette fonctionnalité est disponible uniquement avec l’abonnement <strong class="text-primary">Standard</strong> ou <strong class="text-warning">Premium</strong>.
                        @else
                            Cette fonctionnalité nécessite un abonnement supérieur.
                        @endif
                    </p>

                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-arrow-left me-1"></i> Retour
                        </a>
                        <a href="{{ route('joueur.abonnement.index') }}" class="btn btn-warning rounded-pill px-4 fw-semibold">
                            <i class="bi bi-arrow-up-circle me-1"></i> Voir les abonnements
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection