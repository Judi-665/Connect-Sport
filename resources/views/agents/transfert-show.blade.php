@extends('layouts.agent')

@section('title', 'Détail du transfert')
@section('page-title', 'Détail de la négociation')

@section('sidebar-nav')
    @include('agents.partials.nav')
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><div><h1 class="h3 mb-1">{{ $transfert->joueur?->nomComplet() ?? 'Transfert' }}</h1><p class="text-secondary mb-0">{{ $transfert->clubSource?->nom }} → {{ $transfert->clubDestinataire?->nom }}</p></div><span class="cs-status status-{{ $transfert->statut }}">{{ str_replace('_', ' ', ucfirst($transfert->statut)) }}</span></div>
<div class="row g-4">
    <div class="col-xl-7"><div class="cs-card"><div class="cs-card-header"><div class="cs-card-title"><i class="bi bi-pencil-square"></i>Mettre à jour la négociation</div></div><div class="cs-card-body"><form method="POST" action="{{ route('agent.negocier', $transfert) }}" class="row g-3">@csrf<div class="col-md-6"><label class="form-label">Statut</label><select name="statut" class="form-select"><option value="en_negociation" @selected($transfert->statut === 'en_negociation')>En négociation</option><option value="accepte" @selected($transfert->statut === 'accepte')>Accepté</option><option value="refuse" @selected($transfert->statut === 'refuse')>Refusé</option><option value="annule" @selected($transfert->statut === 'annule')>Annulé</option></select></div><div class="col-12"><label class="form-label">Note</label><textarea name="note" rows="4" class="form-control">{{ $transfert->agent_note }}</textarea></div><div class="col-12"><button class="btn-cs btn-cs-primary"><i class="bi bi-check2"></i>Enregistrer la décision</button></div></form></div></div></div>
    <div class="col-xl-5"><div class="cs-card"><div class="cs-card-header"><div class="cs-card-title"><i class="bi bi-clock-history"></i>Historique</div></div>@forelse($transfert->historique as $event)<div class="px-3 py-3 border-bottom"><div class="d-flex justify-content-between"><strong>{{ str_replace('_', ' ', ucfirst($event->nouveau_statut)) }}</strong><small class="text-secondary">{{ $event->created_at->format('d/m/Y H:i') }}</small></div><small class="text-secondary">{{ $event->user?->prenom ?? $event->user?->name }}</small>@if($event->note)<p class="mb-0 mt-1 small">{{ $event->note }}</p>@endif</div>@empty<div class="cs-empty"><p>Aucun événement enregistré.</p></div>@endforelse</div></div>
</div>
@endsection
