{{-- resources/views/supporter/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Espace Supporter — Connect Sport')

@section('content')
<div class="container py-4 py-lg-5">

    {{-- ═══════════ HERO BANNER SUPPORTER ═══════════ --}}
    <div class="rounded-4 p-4 p-md-5 mb-4 shadow-sm position-relative overflow-hidden text-white"
         style="background: linear-gradient(135deg, #0D2E5C 0%, #1A56A0 55%, #0B1E38 100%);">

        {{-- Glow décoratif (remplace le ::after) --}}
        <div class="position-absolute rounded-circle" style="top:-60px;right:-60px;width:240px;height:240px;
             background: radial-gradient(circle, rgba(249,115,22,.25) 0%, rgba(249,115,22,0) 70%); pointer-events:none;"></div>

        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-warning text-dark fw-bold text-uppercase px-2 py-1 rounded-pill" style="font-size:.72rem;letter-spacing:1px;">
                        <i class="bi bi-star-fill me-1"></i> Espace Supporter
                    </span>
                    @if($supporter->ville || $supporter->pays)
                        <span class="text-white-50 small">
                            <i class="bi bi-geo-alt"></i> {{ $supporter->ville ? $supporter->ville . ', ' : '' }}{{ $supporter->pays }}
                        </span>
                    @endif
                </div>

                @php
                    $heure = (int) date('H');
                    $salutation = match(true) {
                        $heure >= 5 && $heure < 12 => 'Bonjour',
                        $heure >= 12 && $heure < 18 => 'Bon après-midi',
                        default => 'Bonsoir'
                    };
                @endphp

                <h1 class="display-6 fw-bold mb-2 text-white">
                    {{ $salutation }}, {{ auth()->user()->prenom ?? auth()->user()->name }} !
                </h1>
                <p class="lead text-white-50 fs-6 mb-4">
                    Suivez toute l'actualité en direct de vos clubs favoris : calendrier des matchs, scores & résultats, photos et vidéos exclusives.
                </p>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('supporter.decouvrir') }}" class="btn btn-warning rounded-pill px-4 fw-semibold shadow-sm">
                        <i class="bi bi-search me-1"></i> Découvrir des clubs
                    </a>
                    <a href="{{ route('supporter.clubs') }}" class="btn btn-outline-light rounded-pill px-4">
                        <i class="bi bi-shield-check me-1"></i> Gérer mes clubs ({{ $stats['clubs_suivis'] }})
                    </a>
                </div>
            </div>

            {{-- Cartes KPI --}}
            <div class="col-lg-5">
                <div class="row g-3">
                    @foreach([
                        ['clubs_suivis', 'Clubs suivis', 'bi-shield-fill', 'text-warning', 'Abonnements gratuits'],
                        ['prochains_matchs', 'Matchs à venir', 'bi-calendar-event-fill', 'text-info', "Dans l'agenda"],
                        ['derniers_resultats', 'Résultats', 'bi-trophy-fill', 'text-success', 'Matchs récents'],
                        ['medias_recents', 'Médias / Photos', 'bi-images', 'text-danger', 'Publications récentes'],
                    ] as [$key, $label, $icon, $iconColor, $sub])
                    <div class="col-6">
                        <div class="rounded-3 border border-white border-opacity-25 bg-white bg-opacity-10 h-100 p-3"
                             style="backdrop-filter: blur(8px);">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-white-50 small">{{ $label }}</span>
                                <i class="bi {{ $icon }} {{ $iconColor }} fs-5"></i>
                            </div>
                            <div class="fs-3 fw-bold text-white">{{ $stats[$key] }}</div>
                            <div class="text-white-50" style="font-size:.75rem;">{{ $sub }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════ FILTRE PAR CLUB SUIVI ═══════════ --}}
    @if($followedClubs->count() > 0)
    <div class="d-flex align-items-center gap-2 overflow-x-auto pb-3 mb-4">
        <span class="text-body-secondary small fw-semibold me-1 flex-shrink-0">
            <i class="bi bi-funnel me-1"></i> Filtrer :
        </span>

        <a href="{{ route('supporter.dashboard') }}"
           class="rounded-pill px-3 py-2 text-decoration-none d-inline-flex align-items-center gap-2 flex-shrink-0 {{ !$selectedClubId ? 'bg-primary text-white shadow-sm' : 'bg-body-secondary text-body' }}"
           style="font-size:.85rem;font-weight:500;white-space:nowrap;">
            <i class="bi bi-grid-fill"></i> Tous mes clubs ({{ $followedClubs->count() }})
        </a>

        @foreach($followedClubs as $c)
            @php $isSelected = (int)$selectedClubId === $c->id; @endphp
            <a href="{{ route('supporter.dashboard', ['club_id' => $c->id]) }}"
               class="rounded-pill px-3 py-2 text-decoration-none d-inline-flex align-items-center gap-2 flex-shrink-0 {{ $isSelected ? 'bg-primary text-white shadow-sm' : 'bg-body-secondary text-body' }}"
               style="font-size:.85rem;font-weight:500;white-space:nowrap;">
                @if($c->logo)
                    <img src="{{ Storage::url($c->logo) }}" width="20" height="20" class="rounded-circle object-fit-cover" alt="{{ $c->nom }}">
                @else
                    <div class="rounded-circle bg-primary bg-opacity-25 text-primary d-flex align-items-center justify-content-center fw-bold" style="width:20px;height:20px;font-size:10px;">
                        {{ strtoupper(substr($c->nom, 0, 1)) }}
                    </div>
                @endif
                <span>{{ $c->nom }}</span>
                @if($c->pivot->notifications_actives)
                    <i class="bi bi-bell-fill text-warning" style="font-size:.75rem;" title="Notifications actives"></i>
                @endif
            </a>
        @endforeach
    </div>
    @endif

    {{-- ═══════════ ONGLETS (composant Bootstrap natif nav-tabs) ═══════════ --}}
    <ul class="nav nav-tabs mb-4" id="supporterTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="feed-tab" data-bs-toggle="tab" data-bs-target="#feed-pane" type="button" role="tab" aria-selected="true">
                <i class="bi bi-newspaper me-2"></i>Fil d'actualité des clubs
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="clubs-tab" data-bs-toggle="tab" data-bs-target="#clubs-pane" type="button" role="tab" aria-selected="false">
                <i class="bi bi-shield-check me-2"></i>Mes clubs suivis ({{ $followedClubs->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="discover-tab" data-bs-toggle="tab" data-bs-target="#discover-pane" type="button" role="tab" aria-selected="false">
                <i class="bi bi-compass me-2"></i>Découvrir des clubs
            </button>
        </li>
    </ul>

    <div class="tab-content" id="supporterTabsContent">

        {{-- ═══════════ TAB 1 : FIL D'ACTUALITÉ ═══════════ --}}
        <div class="tab-pane fade show active" id="feed-pane" role="tabpanel" tabindex="0">

            @if($followedClubs->isEmpty())
                <div class="card border-0 shadow-sm rounded-4 text-center p-5 my-4">
                    <div class="mx-auto mb-4 rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width:80px;height:80px;">
                        <i class="bi bi-heart fs-1"></i>
                    </div>
                    <h3 class="fw-bold mb-2">Bienvenue sur votre fil supporter !</h3>
                    <p class="text-body-secondary mx-auto mb-4" style="max-width:540px;">
                        Vous ne suivez encore aucun club. Abonnez-vous gratuitement à vos équipes favorites pour voir leurs prochains matchs, scores en direct, résumés et photos.
                    </p>
                    <div>
                        <a href="{{ route('supporter.decouvrir') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
                            <i class="bi bi-search me-2"></i>Explorer et s'abonner à un club
                        </a>
                    </div>
                </div>
            @else

                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill active feed-filter-btn" data-filter="all">
                            <i class="bi bi-collection me-1"></i> Tout
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill feed-filter-btn" data-filter="matchs">
                            <i class="bi bi-calendar-event me-1"></i> Matchs à venir ({{ $prochainsMatchs->count() }})
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill feed-filter-btn" data-filter="resultats">
                            <i class="bi bi-trophy me-1"></i> Derniers résultats ({{ $derniersResultats->count() }})
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill feed-filter-btn" data-filter="medias">
                            <i class="bi bi-images me-1"></i> Photos & Médias ({{ $medias->count() }})
                        </button>
                    </div>

                    @if($selectedClubId)
                        <div class="small">
                            Affichage filtré sur : <strong class="text-primary">{{ $followedClubs->firstWhere('id', $selectedClubId)?->nom }}</strong>
                            <a href="{{ route('supporter.dashboard') }}" class="ms-2 text-decoration-none text-danger small">
                                <i class="bi bi-x-circle"></i> Effacer filtre
                            </a>
                        </div>
                    @endif
                </div>

                @php $hasContent = $prochainsMatchs->count() > 0 || $derniersResultats->count() > 0 || $medias->count() > 0; @endphp

                @if(!$hasContent)
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-body-secondary">
                        <i class="bi bi-bell-slash fs-1 text-muted mb-3"></i>
                        <h5 class="fw-bold">Aucune actualité récente pour le moment</h5>
                        <p class="small mb-0">Les clubs que vous suivez n'ont pas encore publié de match ou de média dans cette sélection.</p>
                    </div>
                @else

                    <div class="row g-4" id="feed-items-container">

                        {{-- PROCHAINS MATCHS --}}
                        @foreach($prochainsMatchs as $match)
                        <div class="col-md-6 col-lg-4 feed-item" data-category="matchs">
                            <div class="border rounded-4 bg-body h-100 p-4 d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 fw-semibold small">
                                        <i class="bi bi-calendar-event me-1"></i> Prochain Match
                                    </span>
                                    <span class="text-body-secondary small fw-medium">
                                        {{ $match->debut_at ? $match->debut_at->translatedFormat('d M Y · H:i') : 'Date à confirmer' }}
                                    </span>
                                </div>

                                <h6 class="fw-bold mb-3 text-truncate" title="{{ $match->titre }}">{{ $match->titre }}</h6>

                                <div class="d-flex align-items-center justify-content-between py-3 my-auto border-top border-bottom">
                                    <div class="text-center" style="width:42%;">
                                        @if($match->club?->logo)
                                            <img src="{{ Storage::url($match->club->logo) }}" width="44" height="44" class="rounded-circle object-fit-cover mb-1 shadow-sm" alt="{{ $match->club->nom }}">
                                        @else
                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary mx-auto d-flex align-items-center justify-content-center fw-bold mb-1" style="width:44px;height:44px;">
                                                {{ strtoupper(substr($match->club?->nom ?? 'C', 0, 2)) }}
                                            </div>
                                        @endif
                                        <div class="fw-bold text-truncate small">{{ $match->club?->nom }}</div>
                                        <span class="badge bg-secondary bg-opacity-10 text-body-secondary" style="font-size:.65rem;">
                                            {{ $match->domicile_exterieur === 'exterieur' ? 'Extérieur' : 'Domicile' }}
                                        </span>
                                    </div>

                                    <div class="rounded-circle bg-body-tertiary text-secondary d-inline-flex align-items-center justify-content-center"
                                         style="font-family:'Bebas Neue',sans-serif;letter-spacing:1px;font-size:1.1rem;width:38px;height:38px;">VS</div>

                                    <div class="text-center" style="width:42%;">
                                        @if($match->adversaire_logo)
                                            <img src="{{ Storage::url($match->adversaire_logo) }}" width="44" height="44" class="rounded-circle object-fit-cover mb-1 shadow-sm" alt="{{ $match->adversaire_nom }}">
                                        @else
                                            <div class="rounded-circle bg-warning bg-opacity-10 text-warning-emphasis mx-auto d-flex align-items-center justify-content-center fw-bold mb-1" style="width:44px;height:44px;">
                                                {{ strtoupper(substr($match->adversaire_nom ?? 'ADV', 0, 2)) }}
                                            </div>
                                        @endif
                                        <div class="fw-bold text-truncate small">{{ $match->adversaire_nom ?? 'Adversaire' }}</div>
                                        <span class="badge bg-secondary bg-opacity-10 text-body-secondary" style="font-size:.65rem;">
                                            {{ $match->domicile_exterieur === 'exterieur' ? 'Domicile' : 'Extérieur' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-3 pt-2 d-flex align-items-center justify-content-between">
                                    <div class="small text-body-secondary text-truncate">
                                        <i class="bi bi-geo-alt me-1 text-primary"></i> {{ $match->lieu ?? 'Lieu non renseigné' }}
                                    </div>
                                    <a href="{{ route('clubs.show', $match->club->slug) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Voir le club</a>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        {{-- DERNIERS RÉSULTATS --}}
                        @foreach($derniersResultats as $res)
                        @php
                            $victoire = $res->resultat === 'victoire' || ($res->score_nous > $res->score_eux);
                            $defaite  = $res->resultat === 'defaite' || ($res->score_nous < $res->score_eux);
                            $badgeClass  = $victoire ? 'bg-success text-white' : ($defaite ? 'bg-danger text-white' : 'bg-secondary text-white');
                            $badgeLabel  = $victoire ? 'Victoire' : ($defaite ? 'Défaite' : 'Match nul');
                            $borderColor = $victoire ? 'border-success' : ($defaite ? 'border-danger' : 'border-secondary');
                        @endphp
                        <div class="col-md-6 col-lg-4 feed-item" data-category="resultats">
                            <div class="border rounded-4 bg-body h-100 p-4 d-flex flex-column border-start border-4 {{ $borderColor }}">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="badge {{ $badgeClass }} rounded-pill px-3 py-1 fw-semibold small">
                                        <i class="bi bi-trophy me-1"></i> {{ $badgeLabel }}
                                    </span>
                                    <span class="text-body-secondary small fw-medium">
                                        {{ $res->debut_at ? $res->debut_at->translatedFormat('d M Y') : 'Terminé' }}
                                    </span>
                                </div>

                                <h6 class="fw-bold mb-3 text-truncate" title="{{ $res->titre }}">{{ $res->titre }}</h6>

                                <div class="p-3 rounded-4 bg-body-tertiary mb-3 my-auto">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                                            @if($res->club?->logo)
                                                <img src="{{ Storage::url($res->club->logo) }}" width="32" height="32" class="rounded-circle object-fit-cover" alt="{{ $res->club->nom }}">
                                            @else
                                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width:32px;height:32px;font-size:11px;">
                                                    {{ strtoupper(substr($res->club?->nom ?? 'C', 0, 2)) }}
                                                </div>
                                            @endif
                                            <span class="fw-semibold text-truncate small">{{ $res->club?->nom }}</span>
                                        </div>

                                        <div class="rounded-3 bg-body shadow-sm border text-primary text-center"
                                             style="font-family:'Bebas Neue',sans-serif;font-size:1.6rem;letter-spacing:2px;line-height:1;padding:6px 14px;">
                                            {{ $res->score_nous }} - {{ $res->score_eux }}
                                        </div>

                                        <div class="d-flex align-items-center gap-2 overflow-hidden justify-content-end text-end">
                                            <span class="fw-semibold text-truncate small">{{ $res->adversaire_nom ?? 'Adversaire' }}</span>
                                            @if($res->adversaire_logo)
                                                <img src="{{ Storage::url($res->adversaire_logo) }}" width="32" height="32" class="rounded-circle object-fit-cover" alt="{{ $res->adversaire_nom }}">
                                            @else
                                                <div class="rounded-circle bg-warning bg-opacity-10 text-warning-emphasis d-flex align-items-center justify-content-center fw-bold" style="width:32px;height:32px;font-size:11px;">
                                                    {{ strtoupper(substr($res->adversaire_nom ?? 'ADV', 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between pt-2">
                                    <span class="small text-body-secondary text-truncate">
                                        <i class="bi bi-geo-alt me-1"></i> {{ $res->lieu ?? 'Stade non précisé' }}
                                    </span>
                                    <a href="{{ route('clubs.show', $res->club->slug) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Fiche club</a>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        {{-- PHOTOS & MÉDIAS --}}
                        @foreach($medias as $med)
                        <div class="col-md-6 col-lg-4 feed-item" data-category="medias">
                            <div class="border rounded-4 bg-body h-100 d-flex flex-column overflow-hidden">
                                <div class="p-3 d-flex align-items-center justify-content-between border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($med->club?->logo)
                                            <img src="{{ Storage::url($med->club->logo) }}" width="32" height="32" class="rounded-circle object-fit-cover" alt="{{ $med->club->nom }}">
                                        @else
                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width:32px;height:32px;font-size:11px;">
                                                {{ strtoupper(substr($med->club?->nom ?? 'C', 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold small lh-1">{{ $med->club?->nom }}</div>
                                            <span class="text-body-secondary" style="font-size:.72rem;">{{ $med->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <span class="badge {{ $med->type === 'video' ? 'bg-danger' : 'bg-primary' }} rounded-pill small">
                                        <i class="bi {{ $med->type === 'video' ? 'bi-play-btn-fill' : 'bi-camera-fill' }} me-1"></i>{{ ucfirst($med->type) }}
                                    </span>
                                </div>

                                <div class="position-relative overflow-hidden bg-dark">
                                    @php
                                        $mediaSrc = $med->chemin ? $med->url() : null;
                                        $thumbSrc = $med->urlMiniature();
                                    @endphp

                                    @if($med->type === 'photo')
                                        <img src="{{ $mediaSrc }}" alt="{{ $med->titre ?? 'Photo' }}" class="w-100 object-fit-cover" style="height:220px;" loading="lazy">
                                    @else
                                        <video class="w-100 d-block" style="height:220px;object-fit:contain;"
                                               controls preload="metadata" playsinline
                                               @if($thumbSrc) poster="{{ $thumbSrc }}" @endif>
                                            <source src="{{ $mediaSrc }}">
                                            Votre navigateur ne prend pas en charge la lecture vidéo.
                                        </video>
                                    @endif

                                    @if($med->duree_secondes)
                                        <span class="position-absolute bottom-0 end-0 m-2 badge bg-dark bg-opacity-75 text-white">{{ $med->dureeFormatee() }}</span>
                                    @endif
                                </div>

                                <div class="p-3 d-flex flex-column flex-grow-1">
                                    <h6 class="fw-bold mb-1 text-truncate">{{ $med->titre ?? 'Publication média' }}</h6>
                                    @if($med->description)
                                        <p class="text-body-secondary small mb-3" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                            {{ $med->description }}
                                        </p>
                                    @endif

                                    @include('clubs.partials.media-reactions', ['media' => $med])

                                    <div class="mt-auto pt-2 d-flex align-items-center justify-content-between">
                                        <span class="text-body-secondary small"><i class="bi bi-eye me-1"></i> {{ $med->vues }} vues</span>
                                        <a href="{{ route('clubs.medias', $med->club->slug) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Galerie du club</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                    </div>
                @endif
            @endif
        </div>

        {{-- ═══════════ TAB 2 : MES CLUBS SUIVIS ═══════════ --}}
        <div class="tab-pane fade" id="clubs-pane" role="tabpanel" tabindex="0">
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="fw-bold mb-1">Clubs que vous soutenez</h4>
                    <p class="text-body-secondary small mb-0">Activez ou coupez les notifications par club, ou gérez vos abonnements gratuits.</p>
                </div>
                <a href="{{ route('supporter.decouvrir') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-plus-circle me-1"></i> Suivre d'autres clubs
                </a>
            </div>

            @if($followedClubs->isEmpty())
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-body-secondary">
                    <i class="bi bi-shield-slash fs-1 text-muted mb-3"></i>
                    <h5 class="fw-bold">Aucun club suivi</h5>
                    <p class="small mb-3">Vous n'avez pas encore d'abonnement actif auprès d'un club.</p>
                    <div><a href="{{ route('supporter.decouvrir') }}" class="btn btn-outline-primary rounded-pill px-4">Découvrir les clubs</a></div>
                </div>
            @else
                <div class="row g-4" id="followed-clubs-grid">
                    @foreach($followedClubs as $club)
                        <div class="col-md-6 col-lg-4" id="club-card-{{ $club->id }}">
                            <div class="border rounded-4 bg-body h-100 p-4 d-flex flex-column">
                                <div class="d-flex align-items-start gap-3 mb-3">
                                    @if($club->logo)
                                        <img src="{{ Storage::url($club->logo) }}" alt="{{ $club->nom }}" class="rounded-3 object-fit-cover shadow-sm flex-shrink-0" width="56" height="56">
                                    @else
                                        <div class="rounded-3 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:56px;height:56px;font-size:1.2rem;">
                                            {{ strtoupper(substr($club->nom, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div class="overflow-hidden">
                                        <h5 class="fw-bold text-truncate mb-1">{{ $club->nom }}</h5>
                                        <div class="d-flex flex-wrap gap-1">
                                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill small">{{ $club->sport?->nom ?? 'Sport' }}</span>
                                            @if($club->ville)<span class="badge bg-secondary bg-opacity-10 text-body-secondary rounded-pill small">{{ $club->ville }}</span>@endif
                                        </div>
                                    </div>
                                </div>

                                <div class="py-2 text-body-secondary small mb-3">
                                    <div class="d-flex justify-content-between py-1 border-bottom">
                                        <span>Supporters inscrits :</span><strong>{{ $club->supporters_count ?? 1 }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between py-1">
                                        <span>Abonnement :</span>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill fw-semibold">Gratuit</span>
                                    </div>
                                </div>

                                <div class="mt-auto pt-3 border-top d-flex flex-column gap-2">
                                    @php $notifOn = (bool) $club->pivot->notifications_actives; @endphp
                                    <button type="button"
                                            class="btn btn-sm {{ $notifOn ? 'btn-outline-success' : 'btn-outline-secondary' }} rounded-pill d-flex align-items-center justify-content-center gap-2 notif-toggle-btn"
                                            data-club-id="{{ $club->id }}"
                                            data-url="{{ route('supporter.club.notifications.toggle', $club) }}"
                                            data-active="{{ $notifOn ? 'true' : 'false' }}">
                                        <i class="bi {{ $notifOn ? 'bi-bell-fill text-success' : 'bi-bell-slash text-muted' }}"></i>
                                        <span class="notif-label">{{ $notifOn ? 'Notifications activées' : 'Notifications coupées' }}</span>
                                    </button>

                                    <div class="d-flex gap-2">
                                        <a href="{{ route('clubs.show', $club->slug) }}" class="btn btn-sm btn-outline-primary rounded-pill flex-grow-1">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> Page club
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 unfollow-btn"
                                                data-club-id="{{ $club->id }}" data-club-nom="{{ $club->nom }}"
                                                data-url="{{ route('supporter.club.quitter', $club) }}" title="Se désabonner">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ═══════════ TAB 3 : DÉCOUVRIR DES CLUBS ═══════════ --}}
        <div class="tab-pane fade" id="discover-pane" role="tabpanel" tabindex="0">
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="fw-bold mb-1">Clubs recommandés pour vous</h4>
                    <p class="text-body-secondary small mb-0">Trouvez un club et abonnez-vous en un clic pour suivre ses matchs et actualités.</p>
                </div>
                <a href="{{ route('supporter.decouvrir') }}" class="btn btn-outline-primary rounded-pill px-4">Voir tout le répertoire des clubs</a>
            </div>

            @if($clubsSuggeres->isEmpty())
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-body-secondary">
                    <i class="bi bi-trophy fs-1 text-warning mb-3"></i>
                    <h5 class="fw-bold">Félicitations ! Vous suivez déjà tous les clubs disponibles</h5>
                    <p class="small mb-0">De nouveaux clubs seront bientôt inscrits sur la plateforme.</p>
                </div>
            @else
                <div class="row g-4">
                    @foreach($clubsSuggeres as $sugg)
                        <div class="col-md-6 col-lg-4" id="sugg-card-{{ $sugg->id }}">
                            <div class="border rounded-4 bg-body h-100 p-4 d-flex flex-column">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    @if($sugg->logo)
                                        <img src="{{ Storage::url($sugg->logo) }}" alt="{{ $sugg->nom }}" class="rounded-3 object-fit-cover shadow-sm flex-shrink-0" width="52" height="52">
                                    @else
                                        <div class="rounded-3 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:52px;height:52px;font-size:1.1rem;">
                                            {{ strtoupper(substr($sugg->nom, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div class="overflow-hidden">
                                        <h6 class="fw-bold text-truncate mb-1">{{ $sugg->nom }}</h6>
                                        <div class="d-flex flex-wrap gap-1">
                                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill small">{{ $sugg->sport?->nom ?? 'Sport' }}</span>
                                            @if($sugg->ville)<span class="badge bg-secondary bg-opacity-10 text-body-secondary rounded-pill small">{{ $sugg->ville }}</span>@endif
                                        </div>
                                    </div>
                                </div>

                                <p class="text-body-secondary small mb-3" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                    {{ $sugg->description ?? 'Club sportif présent sur la plateforme Connect Sport.' }}
                                </p>

                                <div class="mt-auto pt-3 border-top d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill flex-grow-1 follow-btn"
                                            data-club-id="{{ $sugg->id }}" data-club-nom="{{ $sugg->nom }}"
                                            data-url="{{ route('supporter.club.suivre', $sugg) }}">
                                        <i class="bi bi-plus-lg me-1"></i> Suivre ce club
                                    </button>
                                    <a href="{{ route('clubs.show', $sugg->slug) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3" title="Voir le profil">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

</div>

{{-- MODAL DÉSABONNEMENT --}}
<div class="modal fade" id="unfollowConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="mx-auto mb-3 rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width:56px;height:56px;">
                    <i class="bi bi-exclamation-triangle fs-3"></i>
                </div>
                <h5 class="fw-bold mb-2">Se désabonner ?</h5>
                <p class="text-body-secondary small mb-4">
                    Vous ne recevrez plus les alertes et les actualités de <strong id="unfollowModalClubName">ce club</strong>.
                </p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light rounded-pill flex-grow-1" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger rounded-pill flex-grow-1" id="confirmUnfollowBtn">Confirmer</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- TOAST (positionnement natif Bootstrap, pas de classe custom) --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:1090;">
    <div id="ajaxToast" class="toast align-items-center text-white bg-dark border-0 shadow-lg rounded-3" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2">
                <i class="bi bi-info-circle-fill text-warning" id="toastIcon"></i>
                <span id="toastMessage">Action effectuée</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const toastEl = document.getElementById('ajaxToast');
    const bsToast = toastEl ? new bootstrap.Toast(toastEl, { delay: 3500 }) : null;

    function showToast(message, isSuccess = true) {
        if (!toastEl) return;
        document.getElementById('toastMessage').textContent = message;
        document.getElementById('toastIcon').className = isSuccess ? 'bi bi-check-circle-fill text-success fs-5' : 'bi bi-exclamation-triangle-fill text-danger fs-5';
        bsToast.show();
    }

    const filterButtons = document.querySelectorAll('.feed-filter-btn');
    const feedItems = document.querySelectorAll('.feed-item');
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const category = this.getAttribute('data-filter');
            feedItems.forEach(item => {
                item.style.display = (category === 'all' || item.getAttribute('data-category') === category) ? '' : 'none';
            });
        });
    });

    document.querySelectorAll('.notif-toggle-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const url = this.getAttribute('data-url');
            const self = this;
            self.disabled = true;

            fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify({}) })
            .then(res => res.json())
            .then(data => {
                self.disabled = false;
                if (data.success) {
                    const isNowActive = data.notifications_actives;
                    const icon = self.querySelector('i');
                    const label = self.querySelector('.notif-label');
                    if (isNowActive) {
                        self.classList.replace('btn-outline-secondary', 'btn-outline-success');
                        icon.className = 'bi bi-bell-fill text-success';
                        label.textContent = 'Notifications activées';
                    } else {
                        self.classList.replace('btn-outline-success', 'btn-outline-secondary');
                        icon.className = 'bi bi-bell-slash text-muted';
                        label.textContent = 'Notifications coupées';
                    }
                    showToast(data.message, true);
                } else {
                    showToast(data.message || 'Une erreur est survenue.', false);
                }
            })
            .catch(() => { self.disabled = false; showToast('Erreur lors de la mise à jour des notifications.', false); });
        });
    });

    document.querySelectorAll('.follow-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const url = this.getAttribute('data-url');
            const self = this;
            self.disabled = true;
            self.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Abonnement...';

            fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify({}) })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    self.className = 'btn btn-sm btn-success rounded-pill flex-grow-1';
                    self.innerHTML = '<i class="bi bi-check-lg me-1"></i> Abonné';
                    showToast(data.message, true);
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    self.disabled = false;
                    self.innerHTML = '<i class="bi bi-plus-lg me-1"></i> Suivre ce club';
                    showToast(data.message || 'Erreur lors de l\'abonnement.', false);
                }
            })
            .catch(() => { self.disabled = false; self.innerHTML = '<i class="bi bi-plus-lg me-1"></i> Suivre ce club'; showToast('Erreur de connexion au serveur.', false); });
        });
    });

    let pendingUnfollow = null;
    const unfollowModal = new bootstrap.Modal(document.getElementById('unfollowConfirmModal'));

    document.querySelectorAll('.unfollow-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            pendingUnfollow = { clubId: this.getAttribute('data-club-id'), clubNom: this.getAttribute('data-club-nom'), url: this.getAttribute('data-url') };
            document.getElementById('unfollowModalClubName').textContent = pendingUnfollow.clubNom;
            unfollowModal.show();
        });
    });

    document.getElementById('confirmUnfollowBtn').addEventListener('click', function () {
        if (!pendingUnfollow) return;
        const btnConfirm = this;
        btnConfirm.disabled = true;
        btnConfirm.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> En cours...';

        fetch(pendingUnfollow.url, { method: 'DELETE', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: JSON.stringify({}) })
        .then(res => res.json())
        .then(data => {
            btnConfirm.disabled = false;
            btnConfirm.innerHTML = 'Confirmer';
            unfollowModal.hide();
            if (data.success) {
                showToast(data.message, true);
                setTimeout(() => window.location.reload(), 600);
            } else {
                showToast(data.message || 'Erreur lors du désabonnement.', false);
            }
        })
        .catch(() => { btnConfirm.disabled = false; btnConfirm.innerHTML = 'Confirmer'; unfollowModal.hide(); showToast('Erreur réseau lors du désabonnement.', false); });
    });
});
</script>
@endpush
@endsection