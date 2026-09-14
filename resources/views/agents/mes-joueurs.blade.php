@extends('layouts.agent')

@section('title', 'Joueurs représentés')
@section('page-title', 'Joueurs représentés')

@section('sidebar-nav')
    @include('agents.partials.nav')
@endsection

@section('content')
<div class="cs-card">
    <div class="cs-card-header"><div class="cs-card-title"><i class="bi bi-people"></i>Portefeuille joueurs</div></div>
    <div class="table-responsive"><table class="cs-table"><thead><tr><th>Joueur</th><th>Club</th><th>Mandat</th><th>Fin</th><th>Commission</th><th>Statut</th></tr></thead><tbody>
        @forelse($joueurs as $joueur)
            <tr><td>{{ $joueur->nomComplet() }}</td><td>{{ $joueur->club?->nom ?? 'Sans club' }}</td><td>{{ $joueur->pivot->debut_mandat ? \Carbon\Carbon::parse($joueur->pivot->debut_mandat)->format('d/m/Y') : 'Non défini' }}</td><td>{{ $joueur->pivot->fin_mandat ? \Carbon\Carbon::parse($joueur->pivot->fin_mandat)->format('d/m/Y') : 'Ouvert' }}</td><td>{{ $joueur->pivot->commission_pourcentage ?? 0 }} %</td><td><span class="cs-status status-{{ $joueur->pivot->statut }}">{{ ucfirst($joueur->pivot->statut) }}</span></td></tr>
        @empty
            <tr><td colspan="6"><div class="cs-empty"><i class="bi bi-people"></i><p>Aucun joueur représenté pour le moment.</p></div></td></tr>
        @endforelse
    </tbody></table></div>
    @if($joueurs->hasPages())<div class="p-3">{{ $joueurs->links() }}</div>@endif
</div>
@endsection
