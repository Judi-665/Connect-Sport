{{-- resources/views/parent/joueur-profil.blade.php --}}
@extends('layouts.app')

@section('title', 'Profil de ' . ($joueur->user->prenom ?? $joueur->user->name) . ' — Espace Parent')

@section('content')
<div class="container py-4 py-lg-5">

    {{-- Fil d'ariane --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}" class="text-decoration-none">Espace Parent</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $joueur->user->prenom ?? $joueur->user->name }}</li>
        </ol>
    </nav>

    @php
        $prenom    = $joueur->user->prenom ?? $joueur->user->name;
        $nom       = $joueur->user->name ?? '';
        $initiales = strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1));
        $lienLabel = match($lien->lien) {
            'pere'   => 'Père',
            'mere'   => 'Mère',
            'tuteur' => 'Tuteur légal',
            default  => 'Parent',
        };
    @endphp

    {{-- ══════════════════ HERO BANNER JOUEUR ══════════════════ --}}
    <div class="rounded-4 p-4 p-md-5 mb-4 shadow-sm position-relative overflow-hidden text-white"
         style="background: linear-gradient(135deg, #0D2E5C 0%, #0D9488 60%, #064E3B 100%);">

        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-4 flex-wrap">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow flex-shrink-0"
                         style="width:84px;height:84px;background:#fff;color:#0D2E5C;font-size:28px;">
                        @if($joueur->user->avatar)
                            <img src="{{ Storage::url($joueur->user->avatar) }}" alt="{{ $prenom }}" class="w-100 h-100 rounded-circle object-fit-cover">
                        @else
                            {{ $initiales }}
                        @endif
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                            <span class="badge fw-bold text-uppercase px-2 py-1 rounded-pill"
                                  style="background:#2DD4BF;color:#064E3B;font-size:.72rem;">
                                <i class="bi bi-heart-fill me-1"></i> {{ $lienLabel }}
                            </span>
                            @if($joueur->poste)
                                <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-2 py-1" style="font-size:.72rem;">
                                    {{ ucfirst($joueur->poste) }}
                                </span>
                            @endif
                            @if($joueur->categorie)
                                <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-2 py-1" style="font-size:.72rem;">
                                    {{ ucfirst($joueur->categorie) }}
                                </span>
                            @endif
                        </div>

                        <h1 class="display-6 fw-bold mb-1 text-white">{{ $prenom }} {{ $nom }}</h1>

                        <div class="d-flex align-items-center gap-3 text-white-50 small flex-wrap">
                            @if($joueur->club)
                                <span><i class="bi bi-shield-fill text-warning me-1"></i>Club : <strong>{{ $joueur->club->nom }}</strong></span>
                            @else
                                <span><i class="bi bi-shield text-white-50 me-1"></i>Sans club actuellement</span>
                            @endif

                            @if($joueur->date_naissance)
                                <span><i class="bi bi-cake2 me-1"></i>{{ $joueur->date_naissance->format('d/m/Y') }} ({{ $joueur->date_naissance->age }} ans)</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions rapides --}}
            <div class="col-lg-4 text-lg-end">
                <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                    <a href="{{ route('parent.joueur.stats', $joueur) }}" class="btn btn-warning rounded-pill px-3 fw-semibold">
                        <i class="bi bi-bar-chart-fill me-1"></i> Statistiques
                    </a>
                    <a href="{{ route('parent.joueur.agenda', $joueur) }}" class="btn btn-outline-light rounded-pill px-3">
                        <i class="bi bi-calendar3 me-1"></i> Agenda
                    </a>
                    @if($joueur->club)
                        <a href="{{ route('parent.joueur.contacter-club', $joueur) }}" class="btn btn-outline-light rounded-pill px-3">
                            <i class="bi bi-chat-dots me-1"></i> Écrire au club
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════ NAVIGATION SECONDAIRE ══════════════════ --}}
    <div class="d-flex flex-wrap gap-2 mb-4 pb-2 border-bottom">
        <a href="{{ route('parent.joueur.show', $joueur) }}" class="btn btn-primary rounded-pill px-4 fw-semibold" style="background:#0D9488;border-color:#0D9488;">
            <i class="bi bi-person-lines-fill me-1"></i> Vue d'ensemble
        </a>
        <a href="{{ route('parent.joueur.stats', $joueur) }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-bar-chart me-1"></i> Statistiques
        </a>
        <a href="{{ route('parent.joueur.agenda', $joueur) }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-calendar3 me-1"></i> Agenda
        </a>
        <a href="{{ route('parent.joueur.signalement', $joueur) }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-exclamation-triangle me-1"></i> Difficultés / Vœux
        </a>
        <a href="{{ route('parent.joueur.transferts', $joueur) }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left-right me-1"></i> Transferts
        </a>
    </div>

    <div class="row g-4">

        {{-- ══════════ CARTE LICENCE ══════════ --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-patch-check-fill text-success"></i> Licence sportive
                </h5>

                @if($licenceActive)
                    @php
                        $expireAt = $licenceActive->date_expiration;
                        $joursRestants = $expireAt ? (int) now()->diffInDays($expireAt, false) : 0;
                        $urgence = $joursRestants <= 30;
                    @endphp
                    <div class="p-3 rounded-3 mb-3 {{ $urgence ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} d-flex align-items-center gap-3">
                        <i class="bi bi-patch-{{ $urgence ? 'exclamation' : 'check' }}-fill fs-2"></i>
                        <div>
                            <div class="fw-bold">Licence active</div>
                            <div class="small">
                                @if($expireAt)
                                    Expire le {{ $expireAt->format('d/m/Y') }} ({{ $joursRestants > 0 ? "dans $joursRestants jours" : 'Expirée' }})
                                @else
                                    En cours de validité
                                @endif
                            </div>
                        </div>
                    </div>

                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                        <li class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-body-secondary">Numéro de licence :</span>
                            <span class="fw-semibold">{{ $licenceActive->numero_licence ?? '—' }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-body-secondary">Catégorie :</span>
                            <span class="fw-semibold">{{ ucfirst($licenceActive->categorie ?? $joueur->categorie ?? '—') }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-1">
                            <span class="text-body-secondary">Club émetteur :</span>
                            <span class="fw-semibold">{{ $licenceActive->club?->nom ?? $joueur->club?->nom ?? '—' }}</span>
                        </li>
                    </ul>
                @else
                    <div class="p-4 rounded-3 bg-warning-subtle text-warning-emphasis text-center my-auto">
                        <i class="bi bi-exclamation-triangle fs-2 mb-2 d-block"></i>
                        <h6 class="fw-bold mb-1">Aucune licence active</h6>
                        <p class="small mb-0">Le joueur n'a pas encore de licence valide enregistrée pour cette saison.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ══════════ STATISTIQUES SAISON ══════════ --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-bar-chart-fill text-primary"></i> Saison en cours
                    </h5>
                    <a href="{{ route('parent.joueur.stats', $joueur) }}" class="small text-decoration-none">Détails <i class="bi bi-chevron-right"></i></a>
                </div>

                <div class="row g-3 my-auto">
                    <div class="col-6">
                        <div class="p-3 rounded-3 bg-body-tertiary text-center">
                            <div class="fs-3 fw-bold text-primary">{{ $aggregats['matchs_joues'] ?? 0 }}</div>
                            <div class="text-body-secondary small">Matchs joués</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3 bg-body-tertiary text-center">
                            <div class="fs-3 fw-bold text-success">{{ $aggregats['buts'] ?? 0 }}</div>
                            <div class="text-body-secondary small">Buts marqués</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3 bg-body-tertiary text-center">
                            <div class="fs-3 fw-bold text-info">{{ $aggregats['passes_decisives'] ?? 0 }}</div>
                            <div class="text-body-secondary small">Passes décisives</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3 bg-body-tertiary text-center">
                            <div class="fs-3 fw-bold text-warning">{{ $aggregats['minutes_jouees'] ?? 0 }}'</div>
                            <div class="text-body-secondary small">Minutes jouées</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════ PROCHAIN MATCH DU CLUB ══════════ --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-calendar-event text-danger"></i> Prochain événement du club
                    </h5>
                    <a href="{{ route('parent.joueur.agenda', $joueur) }}" class="small text-decoration-none">Voir tout l'agenda <i class="bi bi-chevron-right"></i></a>
                </div>

                @if($prochainMatch)
                    <div class="p-3 rounded-3 bg-body-tertiary d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center fw-bold" style="width:48px;height:48px;">
                                <i class="bi bi-calendar-date fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">{{ $prochainMatch->titre }}</h6>
                                <div class="text-body-secondary small">
                                    <i class="bi bi-clock me-1"></i>{{ $prochainMatch->date_debut ? \Carbon\Carbon::parse($prochainMatch->date_debut)->format('d/m/Y à H:i') : 'Date à venir' }}
                                    @if($prochainMatch->lieu)
                                        · <i class="bi bi-geo-alt me-1"></i>{{ $prochainMatch->lieu }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('parent.joueur.agenda', $joueur) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            Consulter l'agenda
                        </a>
                    </div>
                @else
                    <p class="text-body-secondary mb-0 small">
                        Aucun match ou entraînement prévu pour le moment.
                    </p>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection
