{{--
    resources/views/opportunites/index.blade.php
    Annuaire des opportunités — Bootstrap 5, dark/light natif.
    Données exemples qui disparaissent une à une au fur et à mesure
    que de vraies opportunités sont publiées ($opportunites->count()).
    Aucun emoji, aucun sticker.
--}}
@extends(auth()->check() && auth()->user()->role === 'agent' ? 'layouts.agent' : 'layouts.app')

@section('title', 'Opportunités sportives — Connect Sport')
@section('description', 'Transferts, recrutements, stages, bourses — toutes les opportunités pour les joueurs et clubs d\'Afrique francophone.')

@if(auth()->check() && auth()->user()->role === 'agent')
    @section('page-title', 'Opportunités sportives')
    @section('sidebar-nav')
        @include('agents.partials.nav')
    @endsection
@endif

@section('content')

@php
// ── Données exemples (disparaissent une à une) ────────────────────────────
$exemples = [
    [
        'titre'           => 'Recrutement attaquant senior — AS Cotonou FC',
        'type'            => 'offre_club',
        'club'            => 'AS Cotonou FC',
        'lieu'            => 'Cotonou, Bénin',
        'date_limite'     => '30/06/2026',
        'categorie_cible' => 'joueur',
        'description'     => 'Le club recherche un attaquant senior de moins de 28 ans, expérimenté en championnat régional ou national. Essai possible dès juillet 2026.',
        'realisations'    => ['Hébergement pris en charge', 'Contrat semi-professionnel'],
    ],
    [
        'titre'           => 'Stage de formation technique — Lomé',
        'type'            => 'stage',
        'club'            => 'HC Lomé Stars',
        'lieu'            => 'Lomé, Togo',
        'date_limite'     => '15/07/2026',
        'categorie_cible' => 'joueur',
        'description'     => 'Stage intensif de 10 jours en handball pour joueurs cadets et juniors. Encadrement par des entraîneurs certifiés IHF. Places limitées à 20 participants.',
        'realisations'    => ['Attestation de participation', 'Repas inclus'],
    ],
    [
        'titre'           => 'Bourse sportive — Université de Dakar',
        'type'            => 'bourse',
        'club'            => 'Université de Dakar',
        'lieu'            => 'Dakar, Sénégal',
        'date_limite'     => '01/08/2026',
        'categorie_cible' => 'joueur',
        'description'     => 'Bourse complète couvrant la scolarité et le logement pour un athlète de haut niveau (basketball ou volleyball) souhaitant poursuivre des études supérieures.',
        'realisations'    => ['Scolarité + logement couverts', 'Niveau Bac requis'],
    ],
    [
        'titre'           => 'Transfert milieu de terrain — BC Abidjan',
        'type'            => 'offre_club',
        'club'            => 'BC Abidjan',
        'lieu'            => 'Abidjan, Côte d\'Ivoire',
        'date_limite'     => '20/06/2026',
        'categorie_cible' => 'joueur',
        'description'     => 'Club de basketball ivoirien en recherche d\'un meneur de jeu expérimenté pour renforcer l\'effectif senior masculin pour la saison 2026-2027.',
        'realisations'    => ['Indemnité de transfert négociable', 'Contrat 1 saison renouvelable'],
    ],
    [
        'titre'           => 'Formation coaching jeunes — Douala',
        'type'            => 'formation',
        'club'            => 'AC Douala United',
        'lieu'            => 'Douala, Cameroun',
        'date_limite'     => '10/07/2026',
        'categorie_cible' => 'tous',
        'description'     => 'Formation de 5 jours sur le coaching sportif des jeunes (6-16 ans) dispensée par des formateurs CAF. Ouverte aux éducateurs sportifs et anciens joueurs.',
        'realisations'    => ['Certificat CAF délivré', 'Frais d\'inscription : 15 000 FCFA'],
    ],
    [
        'titre'           => 'Opportunité internationale — Championnat UEMOA',
        'type'            => 'international',
        'club'            => 'Fédération Nationale',
        'lieu'            => 'Ouagadougou, Burkina Faso',
        'date_limite'     => '05/08/2026',
        'categorie_cible' => 'joueur',
        'description'     => 'Sélection de joueurs pour représenter la zone UEMOA dans le championnat inter-fédérations d\'athlétisme. Dossier de candidature à soumettre via la fédération nationale.',
        'realisations'    => ['Prise en charge voyage + hébergement', 'Prime de sélection prévue'],
    ],
];

$typeConfig = [
    'offre_club'    => ['label' => 'Offre de club',  'bg' => 'rgba(59,130,246,.12)',  'color' => '#3B82F6',  'icon' => 'bi-trophy-fill'],
    'stage'         => ['label' => 'Stage',          'bg' => 'rgba(16,185,129,.12)',  'color' => '#10B981',  'icon' => 'bi-mortarboard-fill'],
    'formation'     => ['label' => 'Formation',      'bg' => 'rgba(249,115,22,.12)',  'color' => '#F97316',  'icon' => 'bi-book-fill'],
    'bourse'        => ['label' => 'Bourse',         'bg' => 'rgba(139,92,246,.12)',  'color' => '#8B5CF6',  'icon' => 'bi-award-fill'],
    'international' => ['label' => 'International',  'bg' => 'rgba(234,179,8,.12)',   'color' => '#CA8A04',  'icon' => 'bi-globe2'],
];

$cibleConfig = [
    'joueur' => ['label' => 'Joueur',  'bg' => 'rgba(26,86,160,.1)',   'color' => '#1A56A0'],
    'club'   => ['label' => 'Club',    'bg' => 'rgba(249,115,22,.1)',  'color' => '#F97316'],
    'agent'  => ['label' => 'Agent',   'bg' => 'rgba(139,92,246,.1)', 'color' => '#8B5CF6'],
    'tous'   => ['label' => 'Tous',    'bg' => 'rgba(100,116,139,.1)','color' => '#64748B'],
];

$realCount       = isset($opportunites) ? $opportunites->count() : 0;
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
                    À saisir
                </span>
            </div>
            <h1 class="fw-black mb-1"
                style="font-family:'Bebas Neue',sans-serif;
                       font-size:clamp(32px,5vw,52px);
                       letter-spacing:.04em;">
                Opportunités sportives
            </h1>
            <p class="text-body-secondary mb-0" style="font-size:.88rem;">
                Transferts, recrutements, stages, bourses — trouvez votre prochain défi en Afrique francophone.
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            @auth
                @if(auth()->user()->role === 'club')
                <a href="{{ route('club.opportunites.create') }}"
                   class="btn btn-warning fw-bold rounded-3 px-4">
                    <i class="bi bi-plus-circle me-2"></i>Publier une opportunité
                </a>
                @endif
            @else
                <a href="{{ route('register') }}"
                   class="btn btn-outline-warning fw-semibold rounded-3 px-4">
                    Publier une opportunité
                </a>
            @endauth
        </div>
    </div>

    {{-- ── Filtres ── --}}
    <div class="card border rounded-4 shadow-sm mb-5">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('opportunites.index') }}"
                  class="row g-3 align-items-end">

                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-1" style="font-size:.78rem;">Rechercher</label>
                    <input type="text" name="search"
                           class="form-control rounded-3"
                           placeholder="Titre, club, poste..."
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-1" style="font-size:.78rem;">Type</label>
                    <select name="type" class="form-select rounded-3">
                        <option value="">Tous les types</option>
                        @foreach($typeConfig as $key => $tc)
                        <option value="{{ $key }}" @selected(request('type') === $key)>{{ $tc['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-1" style="font-size:.78rem;">Cible</label>
                    <select name="categorie_cible" class="form-select rounded-3">
                        <option value="">Tous profils</option>
                        @foreach($cibleConfig as $key => $cc)
                        <option value="{{ $key }}" @selected(request('categorie_cible') === $key)>{{ $cc['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-1" style="font-size:.78rem;">Lieu</label>
                    <div class="input-group">
                        <input type="text" name="lieu"
                               class="form-control rounded-start-3"
                               placeholder="Pays, ville..."
                               value="{{ request('lieu') }}">
                        <button type="submit" class="btn btn-primary rounded-end-3 fw-semibold px-3">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    {{-- ── Grille opportunités ── --}}
    @php $totalAffiche = $realCount + count($exemplesVisible); @endphp

    @if($totalAffiche > 0)

        {{-- Compteur --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <p class="text-body-secondary mb-0" style="font-size:.82rem;">
                {{ $realCount }} opportunité(s) publiée(s)
                @if(count($exemplesVisible) > 0)
                    &bull; {{ count($exemplesVisible) }} exemple(s) affiché(s)
                @endif
            </p>
            @if(request('search') || request('type') || request('lieu') || request('categorie_cible'))
            <a href="{{ route('opportunites.index') }}"
               class="text-decoration-none text-body-secondary"
               style="font-size:.78rem;">
                Réinitialiser les filtres
            </a>
            @endif
        </div>

        <div class="row g-4">

            {{-- Vraies opportunités --}}
            @if(isset($opportunites))
            @foreach($opportunites as $opp)
            @php
                $tc = $typeConfig[$opp->type]   ?? $typeConfig['offre_club'];
                $cc = $cibleConfig[$opp->categorie_cible ?? 'tous'] ?? $cibleConfig['tous'];
            @endphp
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('opportunites.show', $opp) }}"
                   class="text-decoration-none text-body cs-opp-card d-block rounded-4 border h-100">
                    <div class="p-4 h-100 d-flex flex-column">

                        {{-- Header --}}
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:52px;height:52px;background:{{ $tc['bg'] }};">
                                <i class="bi {{ $tc['icon'] }}" style="color:{{ $tc['color'] }};font-size:22px;"></i>
                            </div>
                            <div class="overflow-hidden">
                                <div class="fw-bold text-uppercase mb-0"
                                     style="font-size:.65rem;letter-spacing:.1em;color:{{ $tc['color'] }};">
                                    {{ $tc['label'] }}
                                </div>
                                <h5 class="fw-bold mb-0 text-truncate" style="font-size:.93rem;">
                                    {{ $opp->titre }}
                                </h5>
                            </div>
                        </div>

                        {{-- Badges --}}
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge rounded-pill"
                                  style="font-size:.68rem;background:{{ $cc['bg'] }};color:{{ $cc['color'] }};">
                                <i class="bi bi-person me-1"></i>{{ $cc['label'] }}
                            </span>
                            @if($opp->lieu)
                            <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary"
                                  style="font-size:.68rem;">
                                <i class="bi bi-geo-alt me-1"></i>{{ $opp->lieu }}
                            </span>
                            @endif
                            @if($opp->date_limite)
                            <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger"
                                  style="font-size:.68rem;">
                                <i class="bi bi-calendar3 me-1"></i>{{ $opp->date_limite->format('d/m/Y') }}
                            </span>
                            @endif
                        </div>

                        {{-- Description --}}
                        <p class="text-body-secondary flex-grow-1 mb-3"
                           style="font-size:.80rem;line-height:1.6;">
                            {{ Str::limit($opp->description ?? 'Aucune description.', 110) }}
                        </p>

                        {{-- Footer --}}
                        <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                            <span class="text-body-secondary" style="font-size:.72rem;">
                                {{ $opp->created_at->diffForHumans() }}
                            </span>
                            <span class="fw-semibold" style="font-size:.72rem;color:var(--bs-primary);">
                                Voir l'offre &rarr;
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
                $tc = $typeConfig[$ex['type']]                     ?? $typeConfig['offre_club'];
                $cc = $cibleConfig[$ex['categorie_cible'] ?? 'tous'] ?? $cibleConfig['tous'];
            @endphp
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('register') }}"
                   class="text-decoration-none text-body cs-opp-card cs-opp-exemple d-block rounded-4 border h-100">
                    <div class="p-4 h-100 d-flex flex-column">

                        {{-- Header --}}
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:52px;height:52px;background:{{ $tc['bg'] }};">
                                <i class="bi {{ $tc['icon'] }}" style="color:{{ $tc['color'] }};font-size:22px;"></i>
                            </div>
                            <div class="overflow-hidden">
                                <div class="fw-bold text-uppercase mb-0"
                                     style="font-size:.65rem;letter-spacing:.1em;color:{{ $tc['color'] }};">
                                    {{ $tc['label'] }}
                                </div>
                                <h5 class="fw-bold mb-0 text-truncate" style="font-size:.93rem;">
                                    {{ $ex['titre'] }}
                                </h5>
                            </div>
                        </div>

                        {{-- Badges --}}
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge rounded-pill"
                                  style="font-size:.68rem;background:{{ $cc['bg'] }};color:{{ $cc['color'] }};">
                                <i class="bi bi-person me-1"></i>{{ $cc['label'] }}
                            </span>
                            <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary"
                                  style="font-size:.68rem;">
                                <i class="bi bi-geo-alt me-1"></i>{{ $ex['lieu'] }}
                            </span>
                            <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger"
                                  style="font-size:.68rem;">
                                <i class="bi bi-calendar3 me-1"></i>{{ $ex['date_limite'] }}
                            </span>
                        </div>

                        {{-- Description --}}
                        <p class="text-body-secondary flex-grow-1 mb-3"
                           style="font-size:.80rem;line-height:1.6;">
                            {{ Str::limit($ex['description'], 110) }}
                        </p>

                        {{-- Avantages --}}
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

                        {{-- Club --}}
                        <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary"
                                  style="font-size:.68rem;">
                                <i class="bi bi-shield me-1"></i>{{ $ex['club'] }}
                            </span>
                            <span class="fw-semibold text-warning" style="font-size:.72rem;">
                                &bull; Rejoindre
                            </span>
                        </div>

                    </div>
                </a>
            </div>
            @endforeach

        </div>

        {{-- Pagination --}}
        @if(isset($opportunites) && $opportunites->hasPages())
        <div class="mt-5">
            {{ $opportunites->links() }}
        </div>
        @endif

        {{-- Notice exemples --}}
        @if(count($exemplesVisible) > 0)
        <div class="d-flex align-items-center gap-2 mt-4 p-3 rounded-3"
             style="background:var(--bs-tertiary-bg);border:1px solid var(--bs-border-color);">
            <div class="rounded-circle flex-shrink-0"
                 style="width:7px;height:7px;background:#F97316;"></div>
            <p class="text-body-secondary mb-0" style="font-size:.76rem;">
                <a href="{{ route('register') }}" class="text-primary fw-semibold ms-1">
                    Rejoignez nous  gratuitement.
                </a>
            </p>
        </div>
        @endif

    @else
        {{-- État vide --}}
        <div class="text-center py-5">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:64px;height:64px;background:var(--bs-tertiary-bg);">
                <i class="bi bi-lightning text-body-tertiary fs-3"></i>
            </div>
            <p class="fw-semibold mb-1">Aucune opportunité ne correspond à vos critères.</p>
            <p class="text-body-secondary mb-3" style="font-size:.84rem;">
                Modifiez vos filtres ou réinitialisez la recherche.
            </p>
            <a href="{{ route('opportunites.index') }}"
               class="btn btn-outline-warning rounded-3 fw-semibold">
                Réinitialiser
            </a>
        </div>
    @endif

</div>

<style>
.cs-opp-card {
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    background: var(--bs-body-bg);
}
.cs-opp-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(249,115,22,.1);
    border-color: #F97316 !important;
}
.cs-opp-exemple { opacity: .85; }
.cs-opp-exemple:hover { opacity: 1; }
</style>

@endsection