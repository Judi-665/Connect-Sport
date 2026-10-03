{{--
    resources/views/joueurs/index.blade.php
    Annuaire des joueurs — Bootstrap 5, dark/light natif.
    Couleurs : orange/warning (même palette qu'opportunites/index).
    Routes : joueurs.index / joueurs.show (préfixe défini dans web.php).
--}}
@extends(auth()->check() && auth()->user()->role === 'agent' ? 'layouts.agent' : 'layouts.app')

@section('title', 'Joueurs disponibles — Connect Sport')
@section('description', 'Découvrez les joueurs inscrits sur Connect Sport, disponibles pour un club ou une opportunité.')

@if(auth()->check() && auth()->user()->role === 'agent')
    @section('page-title', 'Joueurs disponibles')
    @section('sidebar-nav')
        @include('agents.partials.nav')
    @endsection
@endif

@section('content')

@php
$exemples = [
    [
        'prenom'      => 'Koffi',
        'nom'         => 'Mensah',
        'poste'       => 'Attaquant',
        'sport'       => 'Football',
        'pays'        => 'Bénin',
        'ville'       => 'Cotonou',
        'age'         => 23,
        'taille'      => 178,
        'pied'        => 'Droit',
        'disponible'  => true,
        'biographie'  => 'Attaquant rapide évoluant en championnat régional béninois. Buteur régulier, à la recherche d\'un contrat semi-professionnel.',
        'stats'       => ['Buts' => 14, 'Matchs' => 22, 'Passes' => 6],
        'realisations'=> ['Meilleur buteur régional 2024', 'Sélection U23 Bénin'],
    ],
    [
        'prenom'      => 'Awa',
        'nom'         => 'Diallo',
        'poste'       => 'Pivot',
        'sport'       => 'Handball',
        'pays'        => 'Sénégal',
        'ville'       => 'Dakar',
        'age'         => 21,
        'taille'      => 172,
        'pied'        => 'Gauche',
        'disponible'  => true,
        'biographie'  => 'Pivot expérimentée en championnat national sénégalais. Recherche un club ambitieux pour la saison 2026-2027.',
        'stats'       => ['Buts' => 89, 'Matchs' => 18, 'Assists' => 12],
        'realisations'=> ['Vice-championne nationale 2024'],
    ],
    [
        'prenom'      => 'Yao',
        'nom'         => 'Kouassi',
        'poste'       => 'Meneur',
        'sport'       => 'Basketball',
        'pays'        => 'Côte d\'Ivoire',
        'ville'       => 'Abidjan',
        'age'         => 25,
        'taille'      => 185,
        'pied'        => 'Droit',
        'disponible'  => false,
        'biographie'  => 'Meneur de jeu créatif, vision du jeu exceptionnelle. Expérience en championnat national ivoirien, ouvert à une opportunité internationale.',
        'stats'       => ['Points' => 18, 'Matchs' => 30, 'Passes' => 7],
        'realisations'=> ['MVP championnat U23 2023', '3 sélections nationale'],
    ],
    [
        'prenom'      => 'Fatou',
        'nom'         => 'Traoré',
        'poste'       => 'Libéro',
        'sport'       => 'Volleyball',
        'pays'        => 'Mali',
        'ville'       => 'Bamako',
        'age'         => 22,
        'taille'      => 168,
        'pied'        => 'Droit',
        'disponible'  => true,
        'biographie'  => 'Libéro technique et combative, excellente en réception. Recherche un club national ou sous-régional pour progresser au plus haut niveau.',
        'stats'       => ['Récept.' => 94, 'Matchs' => 20, 'Digs' => 112],
        'realisations'=> ['Meilleure libéro championnat Mali 2024'],
    ],
    [
        'prenom'      => 'Seun',
        'nom'         => 'Adeyemi',
        'poste'       => 'Défenseur central',
        'sport'       => 'Football',
        'pays'        => 'Bénin',
        'ville'       => 'Porto-Novo',
        'age'         => 27,
        'taille'      => 183,
        'pied'        => 'Droit',
        'disponible'  => true,
        'biographie'  => 'Défenseur central solide avec 5 ans d\'expérience en championnat national. Leader naturel, bon jeu de tête. Libre de tout contrat.',
        'stats'       => ['Matchs' => 35, 'Tacles' => 78, 'Interc.' => 54],
        'realisations'=> ['Capitaine AS Porto-Novo 2023-2024'],
    ],
    [
        'prenom'      => 'Ibrahim',
        'nom'         => 'Sawadogo',
        'poste'       => 'Sprinter 100m / 200m',
        'sport'       => 'Athlétisme',
        'pays'        => 'Burkina Faso',
        'ville'       => 'Ouagadougou',
        'age'         => 20,
        'taille'      => 176,
        'pied'        => null,
        'disponible'  => true,
        'biographie'  => 'Sprinteur burkinabé, record personnel 10"32 sur 100m. Cherche un encadrement technique pour viser les compétitions continentales.',
        'stats'       => ['100m' => '10"32', '200m' => '20"91', 'Médailles' => 4],
        'realisations'=> ['Médaille d\'or championnats nationaux 2024'],
    ],
];

$disponibleConfig = [
    true  => ['label' => 'Disponible',   'bg' => 'rgba(249,115,22,.12)',  'color' => '#F97316'],
    false => ['label' => 'Sous contrat', 'bg' => 'rgba(100,116,139,.12)', 'color' => '#64748B'],
];

$realCount       = isset($joueurs) ? $joueurs->count() : 0;
$exemplesVisible = array_slice($exemples, $realCount);
@endphp

<div class="container py-5">

    {{-- ── En-tête ── --}}
    <div class="row align-items-end mb-5 g-3">
        <div class="col-md-8">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div style="width:22px;height:2px;background:var(--bs-warning);border-radius:2px;"></div>
                <span class="fw-semibold text-uppercase text-warning"
                      style="font-size:.65rem;letter-spacing:.14em;">
                    Talents
                </span>
            </div>
            <h1 class="fw-black mb-1"
                style="font-family:'Bebas Neue',sans-serif;
                       font-size:clamp(32px,5vw,52px);
                       letter-spacing:.04em;">
                Joueurs disponibles
            </h1>
            <p class="text-body-secondary mb-0" style="font-size:.88rem;">
                Découvrez les joueurs inscrits sur Connect Sport, prêts à relever de nouveaux défis.
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            @auth
                @if(auth()->user()->role === 'joueur')
                <a href="{{ route('joueurs.edit', auth()->user()->id) }}"
                   class="btn btn-outline-warning fw-semibold rounded-3 px-4">
                    <i class="bi bi-pencil-square me-2"></i>Compléter mon profil
                </a>
                @endif
            @else
                <a href="{{ route('register') }}"
                   class="btn btn-warning fw-bold rounded-3 px-4">
                    <i class="bi bi-person-plus me-2"></i>Créer mon profil joueur
                </a>
            @endauth
        </div>
    </div>

    {{-- ── Filtres ── --}}
    <div class="card border rounded-4 shadow-sm mb-5">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('joueurs.index') }}"
                  class="row g-3 align-items-end">

                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-1" style="font-size:.78rem;">Nom / prénom</label>
                    <input type="text" name="search"
                           class="form-control rounded-3"
                           placeholder="Rechercher un joueur..."
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold mb-1" style="font-size:.78rem;">Sport</label>
                    <select name="sport" class="form-select rounded-3">
                        <option value="">Tous</option>
                        @foreach(['Football','Handball','Basketball','Volleyball','Athlétisme','Natation'] as $sp)
                        <option value="{{ $sp }}" @selected(request('sport') === $sp)>{{ $sp }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold mb-1" style="font-size:.78rem;">Poste</label>
                    <select name="poste" class="form-select rounded-3">
                        <option value="">Tous</option>
                        @foreach(['Gardien','Défenseur','Milieu','Attaquant','Pivot','Meneur','Libéro','Ailier','Sprinter'] as $p)
                        <option value="{{ $p }}" @selected(request('poste') === $p)>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold mb-1" style="font-size:.78rem;">Disponibilité</label>
                    <select name="disponible" class="form-select rounded-3">
                        <option value="">Tous</option>
                        <option value="1" @selected(request('disponible') === '1')>Disponible</option>
                        <option value="0" @selected(request('disponible') === '0')>Sous contrat</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold mb-1" style="font-size:.78rem;">Pays</label>
                    <input type="text" name="pays"
                           class="form-control rounded-3"
                           placeholder="Bénin, Mali..."
                           value="{{ request('pays') }}">
                </div>

                <div class="col-md-1">
                    <button type="submit" class="btn btn-warning w-100 rounded-3 fw-semibold">
                        <i class="bi bi-search"></i>
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- ── Grille joueurs ── --}}
    @php $totalAffiche = $realCount + count($exemplesVisible); @endphp

    @if($totalAffiche > 0)

        {{-- Compteur --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <p class="text-body-secondary mb-0" style="font-size:.82rem;">
                {{ $realCount }} joueur(s) inscrit(s)
                @if(count($exemplesVisible) > 0)
                    &bull; {{ count($exemplesVisible) }} exemple(s) affiché(s)
                @endif
            </p>
            @if(request('search') || request('sport') || request('poste') || request('pays') || request('disponible'))
            <a href="{{ route('joueurs.index') }}"
               class="text-decoration-none text-body-secondary"
               style="font-size:.78rem;">
                Réinitialiser les filtres
            </a>
            @endif
        </div>

        <div class="row g-4">

            {{-- Vrais joueurs --}}
            @if(isset($joueurs))
            @foreach($joueurs as $joueur)
            @php
                $dispo = $joueur->disponible ?? true;
                $dc    = $disponibleConfig[$dispo];
                $nomAffiche = trim(($joueur->user?->prenom ?? '') . ' ' . ($joueur->user?->name ?? '')) ?: 'Joueur';
                $initiale   = strtoupper(substr($joueur->user?->prenom ?? $joueur->user?->name ?? '?', 0, 1) . substr($joueur->user?->name ?? '', 0, 1));
            @endphp
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('joueurs.show', $joueur->id) }}"
                   class="text-decoration-none text-body cs-joueur-card d-block rounded-4 border h-100">
                    <div class="p-4 h-100 d-flex flex-column">

                        {{-- Header avec photo --}}
                        <div class="d-flex align-items-center gap-3 mb-3">
                            @if($joueur->user?->avatar)
                                <img src="{{ asset('storage/' . $joueur->user->avatar) }}"
                                     alt="{{ $nomAffiche }}"
                                     class="rounded-circle flex-shrink-0 border"
                                     width="60" height="60"
                                     style="object-fit:cover;border-width:2px!important;border-color:#F97316!important;">
                            @else
                                <div class="rounded-circle fw-black d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:60px;height:60px;
                                            background:rgba(249,115,22,.12);
                                            color:#F97316;
                                            font-family:'Bebas Neue',sans-serif;
                                            font-size:22px;">
                                    {{ $initiale }}
                                </div>
                            @endif
                            <div class="overflow-hidden flex-grow-1">
                                <h5 class="fw-bold text-truncate mb-0" style="font-size:.93rem;">
                                    {{ $nomAffiche }}
                                </h5>
                                <div class="text-body-secondary" style="font-size:.75rem;">
                                    {{ $joueur->poste ?? 'Poste non renseigné' }}
                                    @if($joueur->sport) &bull; {{ $joueur->sport->nom ?? '' }} @endif
                                </div>
                            </div>
                            <span class="badge rounded-pill flex-shrink-0"
                                  style="font-size:.65rem;background:{{ $dc['bg'] }};color:{{ $dc['color'] }};">
                                {{ $dc['label'] }}
                            </span>
                        </div>

                        {{-- Badges infos --}}
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @if($joueur->pays)
                            <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary" style="font-size:.68rem;">
                                <i class="bi bi-geo-alt me-1"></i>{{ $joueur->pays }}
                            </span>
                            @endif
                            @if($joueur->age())
                            <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary" style="font-size:.68rem;">
                                <i class="bi bi-calendar3 me-1"></i>{{ $joueur->age() }} ans
                            </span>
                            @endif
                            @if($joueur->taille)
                            <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary" style="font-size:.68rem;">
                                <i class="bi bi-arrow-up me-1"></i>{{ $joueur->taille }} cm
                            </span>
                            @endif
                            @if($joueur->pied_fort)
                            <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning" style="font-size:.68rem;">
                                Pied {{ $joueur->pied_fort }}
                            </span>
                            @endif
                        </div>

                        {{-- Biographie --}}
                        <p class="text-body-secondary flex-grow-1 mb-3"
                           style="font-size:.80rem;line-height:1.6;">
                            {{ Str::limit($joueur->biographie ?? 'Aucune biographie renseignée.', 110) }}
                        </p>

                        {{-- Stats --}}
                        @if($joueur->statistiques->isNotEmpty())
                        <div class="mb-3 p-2 rounded-3 d-flex gap-3 justify-content-around"
                             style="background:var(--bs-tertiary-bg);border:1px solid var(--bs-border-color);">
                            @foreach([
                                'Matchs' => $joueur->statistiques->sum('matchs_joues'),
                                'Buts'   => $joueur->statistiques->sum('buts'),
                                'Passes' => $joueur->statistiques->sum('passes_decisives'),
                            ] as $label => $val)
                            <div class="text-center">
                                <div class="fw-black lh-1"
                                     style="font-family:'Bebas Neue',sans-serif;font-size:20px;color:#F97316;">
                                    {{ $val }}
                                </div>
                                <div class="text-body-secondary" style="font-size:.68rem;">{{ $label }}</div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        {{-- Footer --}}
                        <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                            <span class="text-body-secondary" style="font-size:.72rem;">
                                Membre depuis {{ $joueur->created_at->format('Y') }}
                            </span>
                            <span class="fw-semibold" style="font-size:.72rem;color:#F97316;">
                                Voir le profil &rarr;
                            </span>
                        </div>

                    </div>
                </a>
            </div>
            @endforeach
            @endif

            {{-- Exemples (disparaissent un à un) --}}
            @foreach($exemplesVisible as $ex)
            @php
                $dispo = $ex['disponible'];
                $dc    = $disponibleConfig[$dispo];
                $initiale = strtoupper(substr($ex['prenom'], 0, 1) . substr($ex['nom'], 0, 1));
            @endphp
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('register') }}"
                   class="text-decoration-none text-body cs-joueur-card cs-joueur-exemple d-block rounded-4 border h-100">
                    <div class="p-4 h-100 d-flex flex-column">

                        {{-- Header avec initiales --}}
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle fw-black d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:60px;height:60px;
                                        background:rgba(249,115,22,.12);
                                        color:#F97316;
                                        font-family:'Bebas Neue',sans-serif;
                                        font-size:22px;">
                                {{ $initiale }}
                            </div>
                            <div class="overflow-hidden flex-grow-1">
                                <h5 class="fw-bold text-truncate mb-0" style="font-size:.93rem;">
                                    {{ $ex['prenom'] }} {{ $ex['nom'] }}
                                </h5>
                                <div class="text-body-secondary" style="font-size:.75rem;">
                                    {{ $ex['poste'] }} &bull; {{ $ex['sport'] }}
                                </div>
                            </div>
                            <span class="badge rounded-pill flex-shrink-0"
                                  style="font-size:.65rem;background:{{ $dc['bg'] }};color:{{ $dc['color'] }};">
                                {{ $dc['label'] }}
                            </span>
                        </div>

                        {{-- Badges infos --}}
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary" style="font-size:.68rem;">
                                <i class="bi bi-geo-alt me-1"></i>{{ $ex['pays'] }}
                            </span>
                            <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary" style="font-size:.68rem;">
                                <i class="bi bi-calendar3 me-1"></i>{{ $ex['age'] }} ans
                            </span>
                            <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary" style="font-size:.68rem;">
                                <i class="bi bi-arrow-up me-1"></i>{{ $ex['taille'] }} cm
                            </span>
                            @if($ex['pied'])
                            <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning" style="font-size:.68rem;">
                                Pied {{ $ex['pied'] }}
                            </span>
                            @endif
                        </div>

                        {{-- Biographie --}}
                        <p class="text-body-secondary flex-grow-1 mb-3"
                           style="font-size:.80rem;line-height:1.6;">
                            {{ $ex['biographie'] }}
                        </p>

                        {{-- Stats --}}
                        <div class="mb-3 p-2 rounded-3 d-flex gap-3 justify-content-around"
                             style="background:var(--bs-tertiary-bg);border:1px solid var(--bs-border-color);">
                            @foreach($ex['stats'] as $label => $val)
                            <div class="text-center">
                                <div class="fw-black lh-1"
                                     style="font-family:'Bebas Neue',sans-serif;font-size:20px;color:#F97316;">
                                    {{ $val }}
                                </div>
                                <div class="text-body-secondary" style="font-size:.68rem;">{{ $label }}</div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Footer --}}
                        <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                            <span class="badge bg-warning bg-opacity-10 text-warning" style="font-size:.68rem;">
                                Profil Démonstration
                            </span>
                            <span class="fw-semibold text-warning" style="font-size:.72rem;">
                                Créer mon profil &rarr;
                            </span>
                        </div>

                    </div>
                </a>
            </div>
            @endforeach

        </div>

        {{-- Pagination --}}
        @if(isset($joueurs) && method_exists($joueurs, 'hasPages') && $joueurs->hasPages())
        <div class="mt-5">
            {{ $joueurs->links() }}
        </div>
        @endif

        {{-- Notice exemples --}}
        @if(count($exemplesVisible) > 0)
        <div class="d-flex align-items-center gap-2 mt-4 p-3 rounded-3"
             style="background:var(--bs-tertiary-bg);border:1px solid var(--bs-border-color);">
            <div class="rounded-circle flex-shrink-0"
                 style="width:7px;height:7px;background:#F97316;"></div>
            <p class="text-body-secondary mb-0" style="font-size:.76rem;">
                Vous êtes joueur ? 
                <a href="{{ route('register') }}" class="text-warning fw-semibold ms-1">
                    Rejoignez Connect Sport gratuitement pour être visible auprès des clubs et agents.
                </a>
            </p>
        </div>
        @endif

    @else
        {{-- État vide --}}
        <div class="text-center py-5">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:64px;height:64px;background:var(--bs-tertiary-bg);">
                <i class="bi bi-people text-body-tertiary fs-3"></i>
            </div>
            <p class="fw-semibold mb-1">Aucun joueur ne correspond à vos critères.</p>
            <p class="text-body-secondary mb-3" style="font-size:.84rem;">
                Modifiez vos filtres ou réinitialisez la recherche.
            </p>
            <a href="{{ route('joueurs.index') }}"
               class="btn btn-outline-warning rounded-3 fw-semibold">
                Réinitialiser
            </a>
        </div>
    @endif

</div>

<style>
.cs-joueur-card {
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    background: var(--bs-body-bg);
}
.cs-joueur-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(249,115,22,.1);
    border-color: #F97316 !important;
}
.cs-joueur-exemple { opacity: .9; }
.cs-joueur-exemple:hover { opacity: 1; }
</style>

@endsection
