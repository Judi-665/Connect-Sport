@extends('layouts.app')

@section('title', 'Mon abonnement joueur')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-body p-4 p-lg-5 text-center">
                    @php
                        $couleur = $planActif?->slug === 'premium' ? 'warning' : ($planActif?->slug === 'standard' ? 'primary' : 'secondary');
                        $icone   = $planActif?->slug === 'premium' ? 'bi-gem' : ($planActif?->slug === 'standard' ? 'bi-star-fill' : 'bi-star');
                    @endphp
                    <div class="rounded-circle bg-{{ $couleur }} bg-opacity-10 text-{{ $couleur }} d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                        <i class="bi {{ $icone }} fs-3"></i>
                    </div>
                    <p class="text-uppercase small fw-bold text-{{ $couleur }} mb-1">Espace joueur</p>
                    <h1 class="h3">Mon abonnement</h1>

                    @if($abonnement)
                        <p class="text-muted mb-1">
                            Votre profil joueur est actuellement sur le plan
                            <strong>{{ ucfirst($planActif->nom ?? $abonnement->plan) }}</strong>.
                        </p>
                        <p class="fw-semibold" style="color:#0D2E5C;">
                            {{ $abonnement->montantAffiche() }}
                            @if($planActif?->frequence) / {{ $planActif->frequence }} @endif
                        </p>

                        @if($abonnement->fin_at)
                            @php $jours = $abonnement->joursRestants(); @endphp
                            <div class="alert alert-{{ $jours <= 3 ? 'danger' : ($jours <= 7 ? 'warning' : 'info') }} text-start mt-4 mb-0">
                                <i class="bi bi-calendar-event me-2"></i>
                                @if($jours > 0)
                                    Votre abonnement expire dans <strong>{{ $jours }} jour(s)</strong>
                                    (le {{ $abonnement->fin_at->format('d/m/Y') }}).
                                @else
                                    Votre abonnement a expiré.
                                @endif
                            </div>
                        @else
                            <div class="alert alert-secondary text-start mt-4 mb-0">
                                <i class="bi bi-infinity me-2"></i>
                                Plan Gratuit — sans date d'expiration.
                            </div>
                        @endif
                    @else
                        <p class="text-muted">Vous n'avez pas encore d'abonnement actif.</p>
                    @endif

                    <a href="{{ route('joueur.abonnement.choisir') }}" class="btn text-white rounded-pill mt-4 px-4" style="background:#F97316;">
                        <i class="bi bi-arrow-repeat me-1"></i>
                        {{ $abonnement && $planActif?->slug !== 'gratuit' ? 'Changer de formule' : 'Voir les formules' }}
                    </a>
                    <a href="{{ route('joueur.dashboard') }}" class="btn btn-outline-secondary rounded-pill mt-4 ms-2">
                        <i class="bi bi-arrow-left me-1"></i>Retour au dashboard
                    </a>
                </div>
            </div>

            @if($historique->isNotEmpty())
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h2 class="h6 mb-3">Historique</h2>
                    <ul class="list-group list-group-flush">
                        @foreach($historique as $h)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>{{ ucfirst($h->subscriptionPlan?->nom ?? $h->plan) }} — {{ $h->montantAffiche() }}</span>
                            <span class="badge bg-{{ $h->statut === 'actif' ? 'success' : ($h->statut === 'en_attente' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($h->statut) }}
                            </span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection