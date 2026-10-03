@extends('layouts.agent')

@section('title', 'Mon abonnement')
@section('page-title', 'Abonnement')

@section('sidebar-nav')
    @include('agents.partials.nav')
@endsection

@section('content')
<div class="mb-4">
    <h1 class="h3 mb-1">Mon abonnement</h1>
    <p class="text-secondary mb-0">Plan actuel et historique de facturation.</p>
</div>

<div class="cs-card mb-4">
    <div class="cs-card-header">
        <div class="cs-card-title"><i class="bi bi-gem"></i>Plan actuel</div>
        <a href="{{ route('agent.abonnement.choisir') }}" class="btn-cs btn-cs-primary">Changer de plan</a>
    </div>
    <div class="p-4">
        @if($planActif)
            <h3 class="h5 mb-1">{{ $planActif->nom }}</h3>
            <p class="text-secondary mb-2">{{ $planActif->prixFormate() }} {{ $planActif->frequenceLabel() }}</p>
            @if($abonnement?->fin_at)
                <p class="mb-0" style="font-size:.85rem;">Renouvellement le {{ $abonnement->fin_at->format('d/m/Y') }} ({{ $abonnement->joursRestants() }} jours restants)</p>
            @endif
        @else
            <p class="mb-0">Aucun abonnement actif — vous êtes sur le plan gratuit par défaut.</p>
        @endif
    </div>
</div>

<div class="cs-card">
    <div class="cs-card-header"><div class="cs-card-title"><i class="bi bi-receipt"></i>Historique</div></div>
    <div class="table-responsive">
        <table class="cs-table">
            <thead><tr><th>Plan</th><th>Montant</th><th>Statut</th><th>Date</th></tr></thead>
            <tbody>
                @forelse($historique as $h)
                <tr>
                    <td>{{ $h->subscriptionPlan?->nom ?? $h->plan }}</td>
                    <td>{{ $h->montantAffiche() }}</td>
                    <td><span class="cs-status">{{ ucfirst($h->statut) }}</span></td>
                    <td>{{ $h->created_at->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="4"><div class="cs-empty"><p>Aucun historique.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection