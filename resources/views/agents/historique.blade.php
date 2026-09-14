@extends('layouts.agent')

@section('title', 'Historique des transferts')
@section('page-title', 'Historique des négociations')

@section('sidebar-nav')
    @include('agents.partials.nav')
@endsection

@section('content')
<div class="cs-card"><div class="cs-card-header"><div class="cs-card-title"><i class="bi bi-clock-history"></i>Négociations terminées et contrats finalisés</div></div><div class="table-responsive"><table class="cs-table"><thead><tr><th>Joueur</th><th>Clubs</th><th>Statut</th><th>Finalisé le</th><th></th></tr></thead><tbody>@forelse($transferts as $transfert)<tr><td>{{ $transfert->joueur?->nomComplet() }}</td><td>{{ $transfert->clubSource?->nom }} → {{ $transfert->clubDestinataire?->nom }}</td><td><span class="cs-status status-{{ $transfert->statut }}">{{ str_replace('_', ' ', ucfirst($transfert->statut)) }}</span></td><td>{{ $transfert->finalise_at?->format('d/m/Y') ?? $transfert->updated_at->format('d/m/Y') }}</td><td><a href="{{ route('agent.transferts.show', $transfert) }}" class="btn-cs btn-cs-ghost">Détails</a></td></tr>@empty<tr><td colspan="5"><div class="cs-empty"><i class="bi bi-clock-history"></i><p>Aucun historique pour le moment.</p></div></td></tr>@endforelse</tbody></table></div>@if($transferts->hasPages())<div class="p-3">{{ $transferts->links() }}</div>@endif</div>
@endsection
