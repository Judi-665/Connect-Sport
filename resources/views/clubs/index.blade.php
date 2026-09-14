{{--
    resources/views/clubs/index.blade.php
    Annuaire des clubs — Bootstrap 5, dark/light natif.
    Données exemples qui disparaissent une à une au fur et à mesure
    que de vrais clubs s'inscrivent ($clubs->count() détermine combien
    d'exemples restent visibles).
    Aucun emoji, aucun sticker. Dev senior.
--}}
@extends('layouts.app')

@section('title', 'Clubs sportifs — Connect Sport')
@section('description', 'Découvrez tous les clubs inscrits sur Connect Sport à travers l\'Afrique francophone.')

@section('content')

@php
// ── Données exemples (disparaissent une à une) ────────────────────────────
$exemples = [
    [
        'slug'        => null,
        'nom'         => 'AS Cotonou FC',
        'sport'       => 'Football',
        'ville'       => 'Cotonou',
        'pays'        => 'Bénin',
        'niveau'      => 'Régional',
        'plan'        => 'premium',
        'joueurs'     => 28,
        'equipes'     => 2,
        'description' => 'Club de football fondé en 2005, évoluant en championnat régional du Bénin. Deux équipes actives : masculine senior et cadets.',
        'realisations'=> ['Champion régional 2022', 'Finaliste coupe nationale 2023'],
        'categories'  => ['Junior', 'Senior'],
    ],
    [
        'slug'        => null,
        'nom'         => 'HC Lomé Stars',
        'sport'       => 'Handball',
        'ville'       => 'Lomé',
        'pays'        => 'Togo',
        'niveau'      => 'National',
        'plan'        => 'standard',
        'joueurs'     => 22,
        'equipes'     => 2,
        'description' => 'Club de handball de référence au Togo, avec une section féminine performante au niveau national.',
        'realisations'=> ['Vice-champion national 2024', 'Meilleure défense du championnat'],
        'categories'  => ['Cadet', 'Senior'],
    ],
    [
        'slug'        => null,
        'nom'         => 'BC Abidjan',
        'sport'       => 'Basketball',
        'ville'       => 'Abidjan',
        'pays'        => 'Côte d\'Ivoire',
        'niveau'      => 'National',
        'plan'        => 'premium',
        'joueurs'     => 15,
        'equipes'     => 1,
        'description' => 'Club de basketball évoluant dans les compétitions nationales ivoiriennes. Reconnu pour sa formation de jeunes talents.',
        'realisations'=> ['3 joueurs sélectionnés en équipe nationale U20', 'Champion de Côte d\'Ivoire 2023'],
        'categories'  => ['Junior', 'Cadet', 'Senior'],
    ],
    [
        'slug'        => null,
        'nom'         => 'VC Dakar Élite',
        'sport'       => 'Volleyball',
        'ville'       => 'Dakar',
        'pays'        => 'Sénégal',
        'niveau'      => 'National',
        'plan'        => 'standard',
        'joueurs'     => 18,
        'equipes'     => 2,
        'description' => 'Club de volleyball sénégalais avec une longue tradition de formation et de compétition au plus haut niveau national.',
        'realisations'=> ['Finaliste championnat national 2024'],
        'categories'  => ['Cadet', 'Senior'],
    ],
    [
        'slug'        => null,
        'nom'         => 'AC Douala United',
        'sport'       => 'Football',
        'ville'       => 'Douala',
        'pays'        => 'Cameroun',
        'niveau'      => 'Régional',
        'plan'        => 'gratuit',
        'joueurs'     => 32,
        'equipes'     => 3,
        'description' => 'Grand club populaire de Douala réunissant trois équipes : seniors masculins, féminines et cadets. Reconnu pour son ambiance et sa formation.',
        'realisations'=> ['Meilleur club formateur région Littoral 2023'],
        'categories'  => ['Junior', 'Cadet', 'Senior'],
    ],
    [
        'slug'        => null,
        'nom'         => 'SC Ouaga Sport',
        'sport'       => 'Athlétisme',
        'ville'       => 'Ouagadougou',
        'pays'        => 'Burkina Faso',
        'niveau'      => 'National',
        'plan'        => 'gratuit',
        'joueurs'     => 12,
        'equipes'     => 1,
        'description' => 'Club d\'athlétisme burkinabé spécialisé dans les épreuves de demi-fond et de sprint. Plusieurs médaillés aux championnats nationaux.',
        'realisations'=> ['4 médailles aux championnats nationaux 2024'],
        'categories'  => ['Junior', 'Senior'],
    ],
];

$planConfig = [
    'premium'  => ['label' => 'Premium',  'bg' => 'rgba(249,115,22,.12)', 'color' => '#F97316'],
    'standard' => ['label' => 'Standard', 'bg' => 'rgba(59,130,246,.12)', 'color' => '#3B82F6'],
    'gratuit'  => ['label' => 'Gratuit',  'bg' => 'rgba(100,116,139,.12)','color' => '#64748B'],
];

$niveauConfig = [
    'National'     => ['bg' => 'rgba(16,185,129,.1)',  'color' => '#10B981'],
    'Régional'     => ['bg' => 'rgba(59,130,246,.1)',  'color' => '#3B82F6'],
    'Départemental'=> ['bg' => 'rgba(139,92,246,.1)',  'color' => '#8B5CF6'],
    'Loisir'       => ['bg' => 'rgba(100,116,139,.1)', 'color' => '#64748B'],
];

// Nombre de vrais clubs en base
$realCount = isset($clubs) ? $clubs->count() : 0;

// Exemples restants (disparaissent du début à mesure que les vrais arrivent)
$exemplesVisibles = array_slice($exemples, $realCount);
@endphp

<div class="container py-5">

    {{-- ── En-tête ── --}}
    <div class="row align-items-end mb-5 g-3">
        <div class="col-md-8">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div style="width:22px;height:2px;background:var(--bs-primary);border-radius:2px;"></div>
                <span class="fw-semibold text-uppercase text-primary"
                      style="font-size:.65rem;letter-spacing:.14em;">
                    Annuaire
                </span>
            </div>
            <h1 class="fw-black mb-1"
                style="font-family:'Bebas Neue',sans-serif;
                       font-size:clamp(32px,5vw,52px);
                       letter-spacing:.04em;">
                Clubs sportifs
            </h1>
            <p class="text-body-secondary mb-0" style="font-size:.88rem;">
                Découvrez les clubs inscrits sur Connect Sport à travers l'Afrique francophone.
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            @auth
                @if(auth()->user()->role === 'club')
                <a href="{{ route('clubs.create') }}"
                   class="btn btn-warning fw-bold rounded-3 px-4">
                    Ajouter mon club
                </a>
                @endif
            @else
                <a href="{{ route('register') }}"
                   class="btn btn-outline-primary fw-semibold rounded-3 px-4">
                    Inscrire mon club gratuitement
                </a>
            @endauth
        </div>
    </div>

    {{-- ── Filtres ── --}}
    <div class="card border rounded-4 shadow-sm mb-5">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('clubs.index') }}"
                  class="row g-3 align-items-end">

                <div class="col-md-4">
                    <label class="form-label fw-semibold mb-1"
                           style="font-size:.78rem;">
                        Rechercher
                    </label>
                    <input type="text" name="search"
                           class="form-control rounded-3"
                           placeholder="Nom du club, ville, pays..."
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-1"
                           style="font-size:.78rem;">
                        Sport
                    </label>
                    <select name="sport" class="form-select rounded-3">
                        <option value="">Tous les sports</option>
                        @foreach(['Football','Handball','Basketball','Volleyball','Athlétisme','Natation'] as $sport)
                        <option value="{{ $sport }}"
                                @selected(request('sport') === $sport)>
                            {{ $sport }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-1"
                           style="font-size:.78rem;">
                        Niveau
                    </label>
                    <select name="niveau" class="form-select rounded-3">
                        <option value="">Tous niveaux</option>
                        @foreach(['National','Régional','Départemental','Loisir'] as $niv)
                        <option value="{{ $niv }}"
                                @selected(request('niveau') === $niv)>
                            {{ $niv }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit"
                            class="btn btn-primary w-100 rounded-3 fw-semibold">
                        Filtrer
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- ── Grille clubs ── --}}
    @php $totalAffiche = $realCount + count($exemplesVisibles); @endphp

    @if($totalAffiche > 0)

        {{-- Compteur --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <p class="text-body-secondary mb-0" style="font-size:.82rem;">
                {{ $realCount }} club(s) inscrit(s)
                @if(count($exemplesVisibles) > 0)
                    &bull; {{ count($exemplesVisibles) }} exemple(s) affiché(s)
                @endif
            </p>
            @if(request('search') || request('sport') || request('niveau'))
            <a href="{{ route('clubs.index') }}"
               class="text-decoration-none text-body-secondary"
               style="font-size:.78rem;">
                Réinitialiser les filtres
            </a>
            @endif
        </div>

        <div class="row g-4">

            {{-- Vrais clubs --}}
            @if(isset($clubs))
            @foreach($clubs as $club)
            @php
                $niv = $club->niveau ?? 'Régional';
                $nc  = $niveauConfig[$niv] ?? $niveauConfig['Régional'];
            @endphp
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('clubs.show', $club->slug) }}"
                   class="text-decoration-none text-body cs-club-card d-block rounded-4 border h-100">
                    <div class="p-4 h-100 d-flex flex-column">

                        {{-- Header --}}
                        <div class="d-flex align-items-center gap-3 mb-3">
                            @if($club->logo)
                                <img src="{{ Storage::url($club->logo) }}"
                                     alt="{{ $club->nom }}"
                                     class="rounded-3 flex-shrink-0"
                                     width="52" height="52"
                                     style="object-fit:cover;">
                            @else
                                <div class="rounded-3 bg-primary bg-opacity-10
                                            text-primary fw-black d-flex
                                            align-items-center justify-content-center flex-shrink-0"
                                     style="width:52px;height:52px;
                                            font-family:'Bebas Neue',sans-serif;
                                            font-size:18px;letter-spacing:1px;">
                                    {{ strtoupper(substr($club->nom, 0, 2)) }}
                                </div>
                            @endif
                            <div class="overflow-hidden">
                                <h5 class="fw-bold text-truncate mb-0"
                                    style="font-size:.93rem;">
                                    {{ $club->nom }}
                                </h5>
                                <div class="text-body-secondary"
                                     style="font-size:.75rem;">
                                    {{ $club->ville ?? '' }}
                                    @if($club->pays)
                                        &bull; {{ $club->pays }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Badges --}}
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary"
                                  style="font-size:.68rem;">
                                {{ $club->sport->nom ?? 'Sport' }}
                            </span>
                            <span class="badge rounded-pill"
                                  style="font-size:.68rem;
                                         background:{{ $nc['bg'] }};
                                         color:{{ $nc['color'] }};">
                                {{ $niv }}
                            </span>
                        </div>

                        {{-- Description --}}
                        <p class="text-body-secondary flex-grow-1 mb-3"
                           style="font-size:.80rem;line-height:1.6;">
                            {{ Str::limit($club->description ?? 'Aucune description.', 110) }}
                        </p>

                        {{-- Réalisations --}}
                        @if($club->realisations && count($club->realisations) > 0)
                        <div class="mb-3 p-2 rounded-3"
                             style="background:var(--bs-tertiary-bg);border:1px solid var(--bs-border-color);">
                            @foreach(array_slice($club->realisations, 0, 2) as $r)
                            <div class="d-flex align-items-center gap-2"
                                 style="font-size:.73rem;{{ !$loop->last ? 'margin-bottom:4px;' : '' }}">
                                <div class="rounded-circle flex-shrink-0"
                                     style="width:5px;height:5px;background:#F97316;"></div>
                                <span class="text-body-secondary">{{ $r }}</span>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        {{-- Footer --}}
                        <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                            <div class="d-flex gap-3">
                                <span class="text-body-secondary" style="font-size:.72rem;">
                                    {{ $club->joueurs_count ?? 0 }} joueurs
                                </span>
                                <span class="text-body-secondary" style="font-size:.72rem;">
                                    {{ $club->equipes_count ?? 1 }} équipe(s)
                                </span>
                            </div>
                            <span class="fw-semibold" style="font-size:.72rem;color:var(--bs-primary);">
                                Voir le profil &rarr;
                            </span>
                        </div>

                    </div>
                </a>
            </div>
            @endforeach
            @endif

            {{-- Exemples (disparaissent un à un) --}}
            @foreach($exemplesVisibles as $ex)
            @php
                $niv = $ex['niveau'];
                $nc  = $niveauConfig[$niv] ?? $niveauConfig['Régional'];
                $pl  = $planConfig[$ex['plan']] ?? $planConfig['gratuit'];
            @endphp
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('register') }}"
                   class="text-decoration-none text-body cs-club-card cs-club-exemple d-block rounded-4 border h-100">
                    <div class="p-4 h-100 d-flex flex-column">

                        {{-- Header --}}
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center
                                        fw-black flex-shrink-0"
                                 style="width:52px;height:52px;
                                        background:{{ $pl['bg'] }};
                                        color:{{ $pl['color'] }};
                                        font-family:'Bebas Neue',sans-serif;
                                        font-size:18px;letter-spacing:1px;">
                                {{ strtoupper(substr($ex['nom'], 0, 2)) }}
                            </div>
                            <div class="overflow-hidden">
                                <h5 class="fw-bold text-truncate mb-0"
                                    style="font-size:.93rem;">
                                    {{ $ex['nom'] }}
                                </h5>
                                <div class="text-body-secondary"
                                     style="font-size:.75rem;">
                                    {{ $ex['ville'] }} &bull; {{ $ex['pays'] }}
                                </div>
                            </div>
                        </div>

                        {{-- Badges --}}
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary"
                                  style="font-size:.68rem;">
                                {{ $ex['sport'] }}
                            </span>
                            <span class="badge rounded-pill"
                                  style="font-size:.68rem;
                                         background:{{ $nc['bg'] }};
                                         color:{{ $nc['color'] }};">
                                {{ $niv }}
                            </span>
                            <span class="badge rounded-pill"
                                  style="font-size:.68rem;
                                         background:{{ $pl['bg'] }};
                                         color:{{ $pl['color'] }};">
                                {{ $pl['label'] }}
                            </span>
                        </div>

                        {{-- Description --}}
                        <p class="text-body-secondary flex-grow-1 mb-3"
                           style="font-size:.80rem;line-height:1.6;">
                            {{ Str::limit($ex['description'], 110) }}
                        </p>

                        {{-- Réalisations --}}
                        <div class="mb-3 p-2 rounded-3"
                             style="background:var(--bs-tertiary-bg);border:1px solid var(--bs-border-color);">
                            @foreach($ex['realisations'] as $r)
                            <div class="d-flex align-items-center gap-2"
                                 style="font-size:.73rem;{{ !$loop->last ? 'margin-bottom:4px;' : '' }}">
                                <div class="rounded-circle flex-shrink-0"
                                     style="width:5px;height:5px;background:#F97316;"></div>
                                <span class="text-body-secondary">{{ $r }}</span>
                            </div>
                            @endforeach
                        </div>

                        {{-- Footer --}}
                        <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                            <div class="d-flex gap-3">
                                <span class="text-body-secondary" style="font-size:.72rem;">
                                    {{ $ex['joueurs'] }} joueurs
                                </span>
                                <span class="text-body-secondary" style="font-size:.72rem;">
                                    {{ $ex['equipes'] }} équipe(s)
                                </span>
                            </div>
                            <span class="fw-semibold text-warning" style="font-size:.72rem;">
                                &bull; Rejoindre
                            </span>
                        </div>

                    </div>
                </a>
            </div>
            @endforeach

        </div>

        {{-- Pagination (vrais clubs uniquement) --}}
        @if(isset($clubs) && $clubs->hasPages())
        <div class="mt-5">
            {{ $clubs->links() }}
        </div>
        @endif

        {{-- Notice exemples --}}
        @if(count($exemplesVisibles) > 0)
        <div class="d-flex align-items-center gap-2 mt-4 p-3 rounded-3"
             style="background:var(--bs-tertiary-bg);border:1px solid var(--bs-border-color);">
            <div class="rounded-circle flex-shrink-0"
                 style="width:7px;height:7px;background:#F97316;"></div>
            <p class="text-body-secondary mb-0" style="font-size:.76rem;">
                <a href="{{ route('register') }}" class="text-primary fw-semibold">
                    Inscrivez le vôtre gratuitement.
                </a>
            </p>
        </div>
        @endif

    @else
        {{-- État vide --}}
        <div class="text-center py-5">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:64px;height:64px;background:var(--bs-tertiary-bg);">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none"
                     stroke="var(--bs-secondary-color)" stroke-width="1.5"
                     stroke-linecap="round">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
            </div>
            <p class="fw-semibold mb-1">Aucun club ne correspond à vos critères.</p>
            <p class="text-body-secondary mb-3" style="font-size:.84rem;">
                Essayez de modifier vos filtres ou réinitialisez la recherche.
            </p>
            <a href="{{ route('clubs.index') }}"
               class="btn btn-outline-primary rounded-3 fw-semibold">
                Réinitialiser
            </a>
        </div>
    @endif

</div>

<style>
.cs-club-card {
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    background: var(--bs-body-bg);
}
.cs-club-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(var(--bs-primary-rgb), .1);
    border-color: var(--bs-primary) !important;
}
.cs-club-exemple { opacity: .85; }
.cs-club-exemple:hover { opacity: 1; }
</style>

@endsection