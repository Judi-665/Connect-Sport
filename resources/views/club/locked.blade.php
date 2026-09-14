{{-- resources/views/club/locked.blade.php --}}
@extends('layouts.app')
@section('title', 'Fonctionnalité Premium')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">

            {{-- Icône cadenas --}}
            <div class="mb-4">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                     style="width:80px;height:80px;background:rgba(249,115,22,.1);">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none"
                         stroke="#F97316" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <h1 class="h3 fw-bold mb-2">Fonctionnalité verrouillée</h1>
                <p class="text-muted">
                    {{ $message ?? 'Cette fonctionnalité nécessite un abonnement supérieur.' }}
                </p>
            </div>

            {{-- Plan requis --}}
            @if(isset($planRequis))
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    @php
                        $couleur = match($planRequis) {
                            'premium'  => '#F97316',
                            'standard' => '#1A56A0',
                            default    => '#6c757d',
                        };
                        $icon = match($planRequis) {
                            'premium'  => 'bi-trophy-fill',
                            'standard' => 'bi-star-fill',
                            default    => 'bi-person-fill',
                        };
                    @endphp
                    <p class="text-muted small mb-3">Plan requis pour accéder à cette fonctionnalité</p>
                    <div class="d-inline-flex align-items-center gap-3 px-4 py-3 rounded-3"
                         style="background:{{ $couleur }}1a;">
                        <i class="bi {{ $icon }} fs-4" style="color:{{ $couleur }};"></i>
                        <div class="text-start">
                            <div class="fw-bold" style="color:{{ $couleur }};">
                                Plan {{ ucfirst($planRequis) }}
                            </div>
                            <div class="text-muted small">Requis pour cette fonctionnalité</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Plan actuel --}}
                @php
                    $clubObj    = auth()->user()->club;
                    $subActif   = $clubObj?->subscriptionActive();
                    $planActuel = $subActif?->getRelationValue('plan');
                @endphp

            @if($planActuel)
            <div class="alert alert-secondary rounded-3 text-start mb-4 small">
                <i class="bi bi-info-circle me-2"></i>
                Vous êtes actuellement sur le plan
                <strong>{{ $planActuel->nom }}</strong>.
                @if($subActif->expire_le)
                    Expire le {{ $subActif->expire_le->format('d/m/Y') }}.
                @endif
            </div>
            @endif

            {{-- Actions --}}
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ url()->previous() }}"
                   class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Retour
                </a>
                <a href="{{ route('club.abonnement.choisir') }}"
                   class="btn btn-warning rounded-pill px-4 fw-semibold">
                    <i class="bi bi-arrow-up-circle me-1"></i> Upgrader mon abonnement
                </a>
            </div>

        </div>
    </div>
</div>
@endsection