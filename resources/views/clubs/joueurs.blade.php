@extends('layouts.app')

@section('title', 'Joueurs — ' . $club->nom)
@section('description', 'Découvrez les joueurs appartenant à ' . $club->nom . '.')

@section('content')
<div class="container py-5">
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
        <div>
            <a href="{{ route('clubs.show', $club) }}" class="text-muted small text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> Retour au club
            </a>
            <h1 class="fw-bold mt-2 mb-1">Joueurs de {{ $club->nom }}</h1>
            <p class="text-body-secondary mb-0">{{ $joueurs->total() }} joueur(s) actif(s) dans ce club.</p>
        </div>
        @if($club->sport)
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                {{ $club->sport->nom }}
            </span>
        @endif
    </div>

    @if($joueurs->isEmpty())
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-5 text-center text-body-secondary">
                <i class="bi bi-people fs-1 d-block mb-3"></i>
                Aucun joueur public n'est encore enregistré dans ce club.
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($joueurs as $joueur)
            @php
                $nomAffiche = trim(($joueur->user?->prenom ?? '') . ' ' . ($joueur->user?->name ?? '')) ?: 'Joueur';
                $initiale   = strtoupper(substr($joueur->user?->prenom ?? $joueur->user?->name ?? 'J', 0, 1));
                $age        = $joueur->age();

                $totalMatchs = $joueur->total_matchs ?? $joueur->statistiques->sum('matchs_joues');
                $totalButs   = $joueur->total_buts   ?? $joueur->statistiques->sum('buts');
                $totalPasses = $joueur->total_passes ?? $joueur->statistiques->sum('passes_decisives');
            @endphp
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('joueurs.show', $joueur) }}" class="text-decoration-none text-reset">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                @if($joueur->user?->avatar)
                                    <img src="{{ asset('storage/' . $joueur->user->avatar) }}"
                                         class="rounded-circle object-fit-cover flex-shrink-0"
                                         style="width:52px;height:52px;" alt="{{ $nomAffiche }}">
                                @else
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                         style="width:52px;height:52px;">
                                        {{ $initiale }}
                                    </div>
                                @endif
                                <div class="overflow-hidden">
                                    <h5 class="fw-bold mb-1 text-truncate">{{ $nomAffiche }}</h5>
                                    <span class="small text-body-secondary">{{ $joueur->poste ?? 'Joueur' }}</span>
                                </div>
                            </div>

                            {{-- Infos publiques : jamais téléphone/email --}}
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @if($joueur->categorie)
                                    <span class="badge bg-secondary bg-opacity-10 text-body-secondary rounded-pill small">
                                        <i class="bi bi-person me-1"></i>{{ ucfirst($joueur->categorie) }}
                                    </span>
                                @endif
                                @if($age)
                                    <span class="badge bg-secondary bg-opacity-10 text-body-secondary rounded-pill small">
                                        <i class="bi bi-calendar3 me-1"></i>{{ $age }} ans
                                    </span>
                                @endif
                                @if($joueur->nationalite)
                                    <span class="badge bg-secondary bg-opacity-10 text-body-secondary rounded-pill small">
                                        <i class="bi bi-flag me-1"></i>{{ $joueur->nationalite }}
                                    </span>
                                @endif
                                @if($joueur->ville || $joueur->pays)
                                    <span class="badge bg-secondary bg-opacity-10 text-body-secondary rounded-pill small">
                                        <i class="bi bi-geo-alt me-1"></i>{{ $joueur->ville }}{{ $joueur->ville && $joueur->pays ? ', ' : '' }}{{ $joueur->pays }}
                                    </span>
                                @endif
                                @if($joueur->equipe)
                                    <span class="badge bg-secondary bg-opacity-10 text-body-secondary rounded-pill small">
                                        <i class="bi bi-diagram-3 me-1"></i>{{ $joueur->equipe->nom }}
                                    </span>
                                @endif
                            </div>

                            @if($joueur->bio)
                                <p class="text-body-secondary small mb-3" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                    {{ $joueur->bio }}
                                </p>
                            @endif

                            {{-- Statistiques --}}
                            <div class="d-flex justify-content-around text-center pt-3 border-top">
                                <div>
                                    <div class="fw-bold">{{ $totalMatchs }}</div>
                                    <div class="text-body-secondary" style="font-size:.7rem;">Matchs</div>
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $totalButs }}</div>
                                    <div class="text-body-secondary" style="font-size:.7rem;">Buts</div>
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $totalPasses }}</div>
                                    <div class="text-body-secondary" style="font-size:.7rem;">Passes</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $joueurs->links() }}
        </div>
    @endif
</div>
@endsection