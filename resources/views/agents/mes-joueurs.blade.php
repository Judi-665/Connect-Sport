@extends('layouts.agent')

@section('title', 'Mes joueurs représentés — Connect Sport')
@section('page-title', 'Joueurs représentés')

@section('sidebar-nav')
    @include('agents.partials.nav')
@endsection

@section('content')
<div class="container py-5">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 fw-bold mb-0">Joueurs représentés</h1>
    </div>

    @if($joueurs->isEmpty())
        <div class="text-center py-5">
            <p class="fw-semibold mb-1">Aucun joueur représenté pour le moment.</p>
            <p class="text-body-secondary" style="font-size:.84rem;">
                Rendez-vous sur la fiche d'un joueur pour lui proposer un mandat.
            </p>
        </div>
    @else
        <div class="d-flex flex-column gap-3">
            @foreach($joueurs as $joueur)
            @php
                $statutConfig = [
                    'en_attente' => ['label' => 'En attente', 'bg' => 'rgba(249,115,22,.12)', 'color' => '#F97316'],
                    'actif'      => ['label' => 'Actif',      'bg' => 'rgba(34,197,94,.12)',  'color' => '#22C55E'],
                    'termine'    => ['label' => 'Terminé',    'bg' => 'rgba(100,116,139,.12)','color' => '#64748B'],
                    'resilie'    => ['label' => 'Résilié',    'bg' => 'rgba(239,68,68,.12)',  'color' => '#EF4444'],
                ][$joueur->pivot->statut];
                $finProche = $joueur->pivot->fin_mandat && \Carbon\Carbon::parse($joueur->pivot->fin_mandat)->lte(now()->addDays(30)) && $joueur->pivot->statut === 'actif';
            @endphp
            <div class="rounded-4 border p-3 p-md-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold" style="font-size:.9rem;">{{ $joueur->user->name }}</span>
                        <span class="badge rounded-pill" style="font-size:.68rem;background:{{ $statutConfig['bg'] }};color:{{ $statutConfig['color'] }};">
                            {{ $statutConfig['label'] }}
                        </span>
                        @if($finProche)
                        <span class="badge rounded-pill" style="font-size:.68rem;background:rgba(239,68,68,.12);color:#EF4444;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>Fin proche
                        </span>
                        @endif
                    </div>
                    @if($joueur->pivot->statut === 'actif')
                    <form action="{{ route('agent.joueurs.mandat.resilier', $joueur) }}" method="POST"
                          onsubmit="return confirm('Résilier ce mandat ?');">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-3">Résilier</button>
                    </form>
                    @endif
                </div>
                <div class="row g-2" style="font-size:.82rem;">
                    <div class="col-4">
                        <span class="text-body-secondary d-block" style="font-size:.7rem;">Début</span>
                        {{ $joueur->pivot->debut_mandat ? \Carbon\Carbon::parse($joueur->pivot->debut_mandat)->format('d/m/Y') : '—' }}
                    </div>
                    <div class="col-4">
                        <span class="text-body-secondary d-block" style="font-size:.7rem;">Fin</span>
                        {{ $joueur->pivot->fin_mandat ? \Carbon\Carbon::parse($joueur->pivot->fin_mandat)->format('d/m/Y') : '—' }}
                    </div>
                    <div class="col-4">
                        <span class="text-body-secondary d-block" style="font-size:.7rem;">Commission</span>
                        {{ $joueur->pivot->commission_pourcentage ?? '—' }}%
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $joueurs->links() }}</div>
    @endif

</div>
@endsection