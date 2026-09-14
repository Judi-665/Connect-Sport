@extends('layouts.agent')

@section('title', 'Mes transferts')
@section('page-title', 'Négociations et transferts')

@section('sidebar-nav')
    @include('agents.partials.nav')
@endsection

@section('content')
<div class="cs-card">
    <div class="cs-card-header"><div class="cs-card-title"><i class="bi bi-arrow-left-right"></i>Transferts de mes joueurs</div><a href="{{ route('agent.transferts.create') }}" class="btn-cs btn-cs-primary"><i class="bi bi-send"></i>Nouvelle offre</a></div>
    <div class="table-responsive"><table class="cs-table"><thead><tr><th>Joueur</th><th>Trajet</th><th>Type</th><th>Montant</th><th>Statut</th><th>Action</th></tr></thead><tbody>
        @forelse($transferts as $transfert)
            <tr><td>{{ $transfert->joueur?->nomComplet() ?? 'Joueur' }}</td><td>{{ $transfert->clubSource?->nom ?? 'Club source' }} → {{ $transfert->clubDestinataire?->nom ?? 'Club destinataire' }}</td><td>{{ ucfirst($transfert->type) }}</td><td>{{ $transfert->montantAffiche() }}</td><td><span class="cs-status status-{{ $transfert->statut }}">{{ str_replace('_', ' ', ucfirst($transfert->statut)) }}</span></td><td><div class="d-flex gap-1"><a href="{{ route('agent.transferts.show', $transfert) }}" class="btn-cs btn-cs-ghost">Détails</a>@if($transfert->clubDestinataire?->user)<a href="{{ route('messages.conversation', $transfert->clubDestinataire->user) }}" class="btn-cs btn-cs-ghost" title="Contacter le club"><i class="bi bi-chat-dots"></i></a>@endif</div></td></tr>
        @empty
            <tr><td colspan="6"><div class="cs-empty"><i class="bi bi-arrow-left-right"></i><p>Aucun transfert à suivre.</p></div></td></tr>
        @endforelse
    </tbody></table></div>
    @if($transferts->hasPages())<div class="p-3">{{ $transferts->links() }}</div>@endif
</div>
@endsection
