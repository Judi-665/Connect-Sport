{{-- resources/views/parent/agenda-joueur.blade.php --}}
@extends('layouts.app')

@php $prenom = $joueur->user->prenom ?? $joueur->user->name; @endphp

@section('title', 'Agenda de ' . $prenom . ' — Espace Parent')

@section('content')
<div class="container py-4 py-lg-5">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}" class="text-decoration-none">Espace Parent</a></li>
            <li class="breadcrumb-item"><a href="{{ route('parent.joueur.stats', $joueur) }}" class="text-decoration-none">{{ $prenom }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Agenda</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm flex-shrink-0"
                     style="width:54px;height:54px;background:rgba(13,148,136,.12);color:#0D9488;font-size:24px;">
                    <i class="bi bi-calendar3-week-fill"></i>
                </div>
                <div>
                    <h1 class="h4 fw-bold mb-1">Agenda de {{ $prenom }}</h1>
                    <div class="text-body-secondary small">
                        @if($club)
                            <i class="bi bi-shield me-1 text-primary"></i>Club : <strong>{{ $club->nom }}</strong>
                        @else
                            <span class="text-muted fst-italic">Aucun club rattaché</span>
                        @endif
                    </div>
                </div>
            </div>
            <a href="{{ route('parent.joueur.stats', $joueur) }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Retour au profil
            </a>
        </div>
    </div>

    @if(!$club)
        <div class="card border-0 shadow-sm rounded-4 text-center p-5">
            <div class="mx-auto mb-3 rounded-circle bg-body-tertiary d-flex align-items-center justify-content-center text-muted" style="width:70px;height:70px;">
                <i class="bi bi-calendar-x fs-1"></i>
            </div>
            <h5 class="fw-bold mb-1">Aucun club actif</h5>
            <p class="text-body-secondary small mb-3">{{ $prenom }} ne fait pas encore partie d'un club — l'agenda des matchs et entraînements sera disponible dès son affiliation.</p>
            <div>
                <a href="{{ route('parent.joueur.stats', $joueur) }}" class="btn btn-outline-secondary rounded-pill px-4">Retour au profil</a>
            </div>
        </div>
    @else

        {{-- ══════════ PROCHAINS ÉVÉNEMENTS ══════════ --}}
        <div class="mb-5">
            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-calendar-check text-primary"></i> Prochains événements ({{ $aVenir->count() }})
            </h5>

            @if($aVenir->isEmpty())
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center text-body-secondary">
                    <p class="mb-0 small">Aucun événement à venir programmé pour le moment.</p>
                </div>
            @else
                <div class="row g-3">
                    @foreach($aVenir as $ev)
                    @php
                        $estMatch = $ev->type === 'match';
                        $typeIcon = match($ev->type) {
                            'match'        => 'bi-trophy',
                            'entrainement' => 'bi-stopwatch',
                            default        => 'bi-calendar-event',
                        };
                        $lieuType = match($ev->domicile_exterieur) {
                            'domicile'  => 'Domicile',
                            'exterieur' => 'Extérieur',
                            'neutre'    => 'Terrain neutre',
                            default     => null,
                        };
                    @endphp
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 d-flex flex-column">

                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 fw-semibold small">
                                    <i class="bi {{ $typeIcon }} me-1"></i>{{ ucfirst($ev->type ?? 'Événement') }}
                                </span>
                                <span class="text-body-secondary small">{{ $ev->debut_at?->translatedFormat('d M Y') }}</span>
                            </div>

                            <h6 class="fw-bold mb-2 text-truncate" title="{{ $ev->titre }}">{{ $ev->titre }}</h6>

                            {{-- Face à face : club de l'enfant VS adversaire --}}
                            @if($estMatch)
                            <div class="d-flex align-items-center justify-content-between py-3 my-2 border-top border-bottom">
                                <div class="text-center" style="width:40%;">
                                    @if($club->logo)
                                        <img src="{{ Storage::url($club->logo) }}" width="44" height="44" class="rounded-circle object-fit-cover mb-1" alt="{{ $club->nom }}">
                                    @else
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary mx-auto d-flex align-items-center justify-content-center fw-bold mb-1" style="width:44px;height:44px;">
                                            {{ strtoupper(substr($club->nom, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div class="fw-bold text-truncate small">{{ $club->nom }}</div>
                                </div>

                                <div class="text-body-secondary fw-bold small">VS</div>

                                <div class="text-center" style="width:40%;">
                                    @if($ev->adversaire_logo)
                                        <img src="{{ Storage::url($ev->adversaire_logo) }}" width="44" height="44" class="rounded-circle object-fit-cover mb-1" alt="{{ $ev->adversaire_nom }}">
                                    @else
                                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning-emphasis mx-auto d-flex align-items-center justify-content-center fw-bold mb-1" style="width:44px;height:44px;">
                                            {{ strtoupper(substr($ev->adversaire_nom ?? 'ADV', 0, 2)) }}
                                        </div>
                                    @endif
                                    <div class="fw-bold text-truncate small">{{ $ev->adversaire_nom ?? 'Adversaire' }}</div>
                                </div>
                            </div>
                            @if($lieuType)
                                <div class="text-center mb-2">
                                    <span class="badge bg-secondary bg-opacity-10 text-body-secondary rounded-pill" style="font-size:.68rem;">{{ $lieuType }}</span>
                                </div>
                            @endif
                            @endif

                            @if($ev->description)
                                <p class="text-body-secondary small mb-3 flex-grow-1">{{ Str::limit($ev->description, 100) }}</p>
                            @endif

                            <div class="mt-auto pt-3 border-top small text-body-secondary d-flex flex-column gap-1">
                                <div><i class="bi bi-clock me-1 text-primary"></i>{{ $ev->debut_at?->format('H:i') }}</div>
                                @if($ev->lieu)
                                    <div class="text-truncate"><i class="bi bi-geo-alt me-1 text-danger"></i>{{ $ev->lieu }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ══════════ ÉVÉNEMENTS PASSÉS ══════════ --}}
        <div>
            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-muted"></i> Événements passés récents
            </h5>

            @if($passes->isEmpty())
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center text-body-secondary">
                    <p class="mb-0 small">Aucun événement passé enregistré.</p>
                </div>
            @else
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="list-group list-group-flush">
                        @foreach($passes as $ev)
                        @php
                            $estMatch  = $ev->type === 'match';
                            $aScore    = !is_null($ev->score_nous) && !is_null($ev->score_eux);
                            $resultatBadge = match($ev->resultat) {
                                'victoire' => ['bg-success', 'Victoire'],
                                'defaite'  => ['bg-danger', 'Défaite'],
                                'nul'      => ['bg-secondary', 'Match nul'],
                                default    => null,
                            };
                        @endphp
                        <div class="list-group-item p-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                <span class="text-body-secondary small">
                                    {{ $ev->debut_at?->translatedFormat('d M Y · H:i') }}
                                    @if($ev->lieu) · {{ $ev->lieu }} @endif
                                </span>
                                <span class="badge bg-secondary bg-opacity-10 text-body-secondary rounded-pill small">
                                    {{ ucfirst($ev->type ?? 'Événement') }}
                                </span>
                            </div>

                            @if($estMatch)
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <span class="fw-semibold small text-truncate" style="width:38%;">{{ $club->nom }}</span>
                                    @if($aScore)
                                        <span class="fw-black" style="font-family:'Bebas Neue',sans-serif;font-size:1.3rem;letter-spacing:1px;color:#F97316;">
                                            {{ $ev->score_nous }} - {{ $ev->score_eux }}
                                        </span>
                                    @else
                                        <span class="text-body-secondary fw-bold small">VS</span>
                                    @endif
                                    <span class="fw-semibold small text-truncate text-end" style="width:38%;">{{ $ev->adversaire_nom ?? 'Adversaire' }}</span>
                                </div>
                                @if($resultatBadge)
                                    <div class="text-center mt-2">
                                        <span class="badge rounded-pill {{ $resultatBadge[0] }} px-3">{{ $resultatBadge[1] }}</span>
                                    </div>
                                @endif
                            @else
                                <div class="fw-semibold">{{ $ev->titre }}</div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

    @endif

</div>
@endsection