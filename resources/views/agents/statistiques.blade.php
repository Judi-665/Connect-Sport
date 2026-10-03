@extends('layouts.agent')

@section('title', $estPremium ? 'Statistiques avancées' : 'Statistiques')
@section('page-title', $estPremium ? 'Statistiques avancées' : 'Statistiques')

@section('sidebar-nav')
    @include('agents.partials.nav')
@endsection

@section('content')
<div class="mb-4">
    <div class="text-uppercase small fw-bold" style="color:var(--cs-orange);">{{ $estPremium ? 'Plan Premium' : 'Plan Standard' }}</div>
    <h1 class="h3 mb-1">{{ $estPremium ? 'Statistiques avancées' : 'Statistiques de suivi' }}</h1>
    <p class="text-secondary mb-0">Analysez votre activité de représentation et vos transferts.</p>
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['joueurs_actifs', 'Joueurs actifs', 'bi-people'],
        ['transferts_total', 'Transferts suivis', 'bi-arrow-left-right'],
        ['transferts_acceptes', 'Transferts finalisés', 'bi-check2-circle'],
        ['mandats_expirant', 'Mandats à échéance', 'bi-calendar-event'],
    ] as [$key, $label, $icon])
        <div class="col-12 col-sm-6 col-xl-3"><div class="cs-kpi h-100"><div class="cs-kpi-icon" style="background:#FEF3C7;color:#92400E"><i class="bi {{ $icon }}"></i></div><div class="cs-kpi-val">{{ $stats[$key] }}</div><div class="cs-kpi-lbl">{{ $label }}</div></div></div>
    @endforeach
</div>

<div class="row g-4">
    @if($estPremium)
        <div class="col-12 col-lg-6"><div class="cs-card p-4 h-100"><h2 class="h5 mb-3">Valeur des transferts finalisés</h2><div class="display-6 fw-bold">{{ number_format($stats['montant_transferts'], 0, ',', ' ') }} FCFA</div><p class="text-secondary mb-0 mt-2">Montant cumulé des transferts acceptés.</p></div></div>
        <div class="col-12 col-lg-6"><div class="cs-card p-4 h-100"><h2 class="h5 mb-3">Répartition des transferts</h2>@forelse($transfertsParStatut as $statut => $total)<div class="d-flex justify-content-between border-bottom py-2"><span>{{ str_replace('_', ' ', ucfirst($statut)) }}</span><strong>{{ $total }}</strong></div>@empty<p class="text-secondary mb-0">Aucun transfert enregistré.</p>@endforelse</div></div>
    @else
        <div class="col-12"><div class="cs-card p-4"><h2 class="h5 mb-2">Suivi Standard</h2><p class="text-secondary mb-0">Le plan Standard vous donne accès aux volumes de votre activité et à vos échéances. Passez au Premium pour analyser la valeur et la répartition détaillée de vos transferts.</p></div></div>
    @endif
</div>
@if(!$estPremium)
    <div class="cs-card mt-4 p-4 border-warning"><strong><i class="bi bi-stars text-warning me-2"></i>Passez au Premium</strong><p class="text-secondary mb-3 mt-2">Débloquez la mise en avant dans l’annuaire et les analyses avancées.</p><a href="{{ route('agent.abonnement.choisir') }}" class="btn-cs btn-cs-primary">Voir les plans Premium</a></div>
@endif
@endsection
