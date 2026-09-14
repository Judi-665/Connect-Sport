@extends('layouts.app')

@section('title', 'Abonnement confirmé')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 text-center">
                <div class="card-body p-4 p-lg-5">
                    @if($abonnement && $abonnement->statut === 'actif')
                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center mb-3" style="width:72px;height:72px;">
                            <i class="bi bi-check-lg fs-1"></i>
                        </div>
                        <h1 class="h4">Abonnement confirmé !</h1>
                        <p class="text-muted">
                            Vous êtes maintenant sur le plan
                            <strong>{{ ucfirst($abonnement->subscriptionPlan?->nom ?? $abonnement->plan) }}</strong>.
                        </p>
                        @if($abonnement->fin_at)
                            <p class="small text-muted">Valable jusqu'au {{ $abonnement->fin_at->format('d/m/Y') }}.</p>
                        @endif
                    @elseif($abonnement && $abonnement->statut === 'en_attente')
                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-inline-flex align-items-center justify-content-center mb-3" style="width:72px;height:72px;">
                            <i class="bi bi-hourglass-split fs-1"></i>
                        </div>
                        <h1 class="h4">Paiement en cours de vérification</h1>
                        <p class="text-muted">Votre abonnement sera activé dès confirmation du paiement.</p>
                    @else
                        <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-inline-flex align-items-center justify-content-center mb-3" style="width:72px;height:72px;">
                            <i class="bi bi-x-lg fs-1"></i>
                        </div>
                        <h1 class="h4">Paiement non confirmé</h1>
                        <p class="text-muted">Le paiement n'a pas pu être validé. Vous pouvez réessayer.</p>
                    @endif

                    <a href="{{ route('joueur.abonnement.index') }}" class="btn text-white rounded-pill mt-3" style="background:#1A56A0;">
                        Voir mon abonnement
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection