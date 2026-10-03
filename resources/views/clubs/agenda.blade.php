@extends('layouts.app')

@section('title', 'Agenda — ' . $club->nom)
@section('description', 'Calendrier des matchs et événements publics de ' . $club->nom . '.')

@section('content')
<div class="container py-5">

    <a href="{{ route('clubs.show', $club) }}" class="text-muted small text-decoration-none mb-3 d-inline-block">
        <i class="bi bi-arrow-left me-1"></i> Retour au club
    </a>

    <div class="d-flex align-items-center gap-3 mb-4">
        @if($club->logo)
            <img src="{{ Storage::url($club->logo) }}" class="rounded-3" width="52" height="52" style="object-fit:cover;">
        @else
            <div class="rounded-3 bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center" style="width:52px;height:52px;">
                {{ strtoupper(substr($club->nom, 0, 2)) }}
            </div>
        @endif
        <div>
            <h1 class="fw-bold mb-0">Agenda — {{ $club->nom }}</h1>
            <p class="text-body-secondary mb-0" style="font-size:.86rem;">{{ $evenements->total() }} événement(s) public(s)</p>
        </div>
    </div>

    @if($evenements->isEmpty())
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-5 text-center text-body-secondary">
                <i class="bi bi-calendar-x fs-1 d-block mb-3"></i>
                Aucun événement public pour le moment.
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($evenements as $evenement)
            @php
                $estTermine = $evenement->debut_at->isPast();
                $typeBadge = match($evenement->type) {
                    'match'        => ['icon' => 'bi-trophy', 'bg' => 'bg-warning bg-opacity-10', 'color' => 'text-warning-emphasis'],
                    'entrainement' => ['icon' => 'bi-people', 'bg' => 'bg-success bg-opacity-10', 'color' => 'text-success'],
                    default        => ['icon' => 'bi-calendar-event', 'bg' => 'bg-info bg-opacity-10', 'color' => 'text-info'],
                };
                $victoire = $evenement->resultat === 'victoire';
                $defaite  = $evenement->resultat === 'defaite';
            @endphp
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 d-flex flex-column h-100">

                        {{-- En-tête : type, statut, date --}}
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex gap-2">
                                <span class="badge {{ $typeBadge['bg'] }} {{ $typeBadge['color'] }} rounded-pill">
                                    <i class="bi {{ $typeBadge['icon'] }} me-1"></i>{{ ucfirst($evenement->type) }}
                                </span>
                                @if($estTermine)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">Terminé</span>
                                @else
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">À venir</span>
                                @endif
                            </div>
                            <span class="text-body-secondary fw-medium" style="font-size:.78rem;">
                                {{ $evenement->debut_at->translatedFormat('d M Y') }}
                            </span>
                        </div>

                        <h6 class="fw-bold text-truncate mb-3" title="{{ $evenement->titre }}">{{ $evenement->titre }}</h6>

                        {{-- Face à face club vs adversaire --}}
                        @if($evenement->type === 'match')
                        <div class="d-flex align-items-center justify-content-between py-3 my-auto border-top border-bottom">
                            <div class="text-center" style="width:38%;">
                                @if($club->logo)
                                    <img src="{{ Storage::url($club->logo) }}" width="44" height="44" class="rounded-circle object-fit-cover mb-1" alt="{{ $club->nom }}">
                                @else
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary mx-auto d-flex align-items-center justify-content-center fw-bold mb-1" style="width:44px;height:44px;">
                                        {{ strtoupper(substr($club->nom, 0, 2)) }}
                                    </div>
                                @endif
                                <div class="fw-bold text-truncate small">{{ $club->nom }}</div>
                            </div>

                            @if($estTermine && !is_null($evenement->score_nous))
                                <div class="text-center px-2">
                                    <div class="fw-black" style="font-family:'Bebas Neue',sans-serif;font-size:1.6rem;letter-spacing:1px;color:#F97316;">
                                        {{ $evenement->score_nous }} - {{ $evenement->score_eux }}
                                    </div>
                                </div>
                            @else
                                <div class="text-body-secondary fw-bold" style="font-size:.85rem;">VS</div>
                            @endif

                            <div class="text-center" style="width:38%;">
                                @if($evenement->adversaire_logo)
                                    <img src="{{ Storage::url($evenement->adversaire_logo) }}" width="44" height="44" class="rounded-circle object-fit-cover mb-1" alt="{{ $evenement->adversaire_nom }}">
                                @else
                                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning-emphasis mx-auto d-flex align-items-center justify-content-center fw-bold mb-1" style="width:44px;height:44px;">
                                        {{ strtoupper(substr($evenement->adversaire_nom ?? 'ADV', 0, 2)) }}
                                    </div>
                                @endif
                                <div class="fw-bold text-truncate small">{{ $evenement->adversaire_nom ?? 'Adversaire' }}</div>
                            </div>
                        </div>

                        @if($estTermine && $evenement->resultat)
                            <div class="text-center my-2">
                                <span class="badge rounded-pill {{ $victoire ? 'bg-success' : ($defaite ? 'bg-danger' : 'bg-secondary') }} px-3 py-1">
                                    {{ ucfirst($evenement->resultat) }}
                                </span>
                            </div>
                        @endif
                        @else
                            <div class="py-3 my-auto"></div>
                        @endif

                        {{-- Description --}}
                        @if($evenement->description)
                            <p class="text-body-secondary mb-3" style="font-size:.82rem;line-height:1.5;">
                                {{ Str::limit($evenement->description, 100) }}
                            </p>
                        @endif

                        {{-- Infos pratiques : heure + lieu, toujours en bas --}}
                        <div class="d-flex align-items-center justify-content-between pt-2 mt-auto">
                            <span class="text-body-secondary" style="font-size:.78rem;">
                                <i class="bi bi-clock me-1"></i>{{ $evenement->debut_at->format('H:i') }}
                            </span>
                            @if($evenement->lieu)
                            <span class="text-body-secondary text-truncate" style="font-size:.78rem;max-width:60%;">
                                <i class="bi bi-geo-alt me-1"></i>{{ $evenement->lieu }}
                            </span>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $evenements->links() }}</div>
    @endif

</div>
@endsection