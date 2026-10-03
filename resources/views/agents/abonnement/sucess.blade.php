@extends('layouts.agent')

@section('title', 'Abonnement confirmé')
@section('page-title', 'Confirmation')

@section('sidebar-nav')
    @include('agents.partials.nav')
@endsection

@section('content')
<div class="cs-card text-center py-5">
    @if($abonnement && $abonnement->statut === 'actif')
        <i class="bi bi-check-circle-fill fs-1 text-success mb-3"></i>
        <h2 class="h4 mb-2">Abonnement activé !</h2>
        <p class="text-secondary mb-4">Vous êtes maintenant sur le plan {{ $abonnement->subscriptionPlan?->nom }}.</p>
    @else
        <i class="bi bi-hourglass-split fs-1 text-warning mb-3"></i>
        <h2 class="h4 mb-2">Paiement en cours de vérification</h2>
        <p class="text-secondary mb-4">Actualisez cette page dans quelques instants.</p>
    @endif
    <a href="{{ route('agent.abonnement.index') }}" class="btn-cs btn-cs-primary">Voir mon abonnement</a>
</div>
@endsection