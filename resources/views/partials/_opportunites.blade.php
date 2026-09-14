{{--
    resources/views/partials/_opportunites.blade.php
    Opportunités — Bootstrap 5, dark/light adaptatif.
    Données par défaut : disparaissent une à une dès que de vraies opportunités
    sont publiées (comparaison $opportunites->count() vs count($defaut)).
    Aucun emoji, aucun sticker.
--}}

@php
    $defautAll = [
        [
            'id'      => null,
            'type'    => 'Recrutement',
            'titre'   => 'Recherche ailier gauche — Seniors',
            'club'    => 'AS Cotonou FC',
            'sport'   => 'Football',
            'ville'   => 'Cotonou',
            'poste'   => 'Ailier gauche',
            'age'     => '18-28 ans',
            'color'   => '#3B82F6',
        ],
        [
            'id'      => null,
            'type'    => 'Transfert',
            'titre'   => 'Pivot disponible pour transfert',
            'club'    => 'HC Lomé Stars',
            'sport'   => 'Handball',
            'ville'   => 'Lomé',
            'poste'   => 'Pivot',
            'age'     => 'Toutes catégories',
            'color'   => '#10B981',
        ],
        [
            'id'      => null,
            'type'    => 'Formation',
            'titre'   => 'Stage intensif gardiens de but',
            'club'    => 'BC Abidjan',
            'sport'   => 'Basketball',
            'ville'   => 'Abidjan',
            'poste'   => 'Gardien',
            'age'     => '16-22 ans',
            'color'   => '#F97316',
        ],
        [
            'id'      => null,
            'type'    => 'Recrutement',
            'titre'   => 'Libero expérimenté recherché',
            'club'    => 'VC Dakar Élite',
            'sport'   => 'Volleyball',
            'ville'   => 'Dakar',
            'poste'   => 'Libero',
            'age'     => '20-32 ans',
            'color'   => '#8B5CF6',
        ],
        [
            'id'      => null,
            'type'    => 'Sponsoring',
            'titre'   => 'Partenaire maillot saison 2025-2026',
            'club'    => 'AC Douala United',
            'sport'   => 'Football',
            'ville'   => 'Douala',
            'poste'   => 'Sponsor maillot',
            'age'     => 'Entreprise',
            'color'   => '#F59E0B',
        ],
    ];

    // Nombre d'opportunités réelles déjà publiées
    $realCount = isset($opportunites) ? $opportunites->count() : 0;

    // Retirer les données par défaut une à une à mesure que les vraies arrivent
    // (on retire depuis le début de la liste)
    $defautVisible = array_slice($defautAll, $realCount);

    // Construire la liste affichée : réelles en premier, puis défauts restants
    $displayItems = [];

    if ($realCount > 0) {
        foreach ($opportunites as $o) {
            $displayItems[] = ['real' => true, 'data' => $o];
        }
    }
    foreach ($defautVisible as $d) {
        $displayItems[] = ['real' => false, 'data' => $d];
    }
@endphp

<div class="card border shadow-sm rounded-4">
    <div class="card-body p-4">

        {{-- En-tête --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div style="width:18px;height:2px;background:#F97316;border-radius:2px;"></div>
                    <span class="fw-semibold text-uppercase"
                          style="font-size:.63rem;letter-spacing:.13em;color:#F97316;">
                        En ce moment
                    </span>
                </div>
                <h5 class="fw-bold mb-0" style="font-size:.95rem;">Opportunités</h5>
            </div>
            <a href="{{ route('opportunites.index') }}"
               class="btn btn-sm rounded-pill px-3 fw-semibold"
               style="font-size:.76rem;
                      background:rgba(249,115,22,.1);
                      color:#F97316;
                      border:1px solid rgba(249,115,22,.25);">
                Voir toutes
                <svg width="12" height="12" viewBox="0 0 16 16" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round"
                     style="margin-left:4px;">
                    <path d="M3 8h10M9 4l4 4-4 4"/>
                </svg>
            </a>
        </div>

        {{-- Liste --}}
        <div class="d-flex flex-column gap-2">

            @forelse($displayItems as $item)
                @php $isReal = $item['real']; $d = $item['data']; @endphp

                @if($isReal)
                    {{-- Opportunité réelle --}}
                    <a href="{{ route('opportunites.show', $d->id) }}"
                       class="text-decoration-none text-body cs-oppo-card d-flex align-items-center gap-3 p-3 rounded-3 border">

                        {{-- Pastille type --}}
                        <div class="rounded-3 flex-shrink-0 d-flex align-items-center justify-content-center"
                             style="width:44px;height:44px;
                                    background:rgba(26,86,160,.1);
                                    font-family:'Bebas Neue',sans-serif;
                                    font-size:11px;letter-spacing:1px;
                                    color:#1A56A0;
                                    text-align:center;line-height:1.2;">
                            {{ strtoupper(substr($d->type ?? 'OPP', 0, 3)) }}
                        </div>

                        {{-- Contenu --}}
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="fw-semibold text-truncate mb-1" style="font-size:.84rem;">
                                {{ $d->titre }}
                            </div>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary"
                                      style="font-size:.65rem;">
                                    {{ $d->sport->nom ?? 'Sport' }}
                                </span>
                                @if($d->club)
                                <span class="text-body-secondary" style="font-size:.72rem;">
                                    {{ $d->club->nom }}
                                </span>
                                @endif
                                @if($d->ville)
                                <span class="text-body-secondary" style="font-size:.72rem;">
                                    &bull; {{ $d->ville }}
                                </span>
                                @endif
                            </div>
                        </div>

                        {{-- Flèche --}}
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none"
                             stroke="var(--bs-primary)" stroke-width="2" stroke-linecap="round"
                             class="flex-shrink-0">
                            <path d="M3 8h10M9 4l4 4-4 4"/>
                        </svg>
                    </a>

                @else
                    {{-- Donnée par défaut (disparaît une à une) --}}
                    <a href="{{ route('register') }}"
                       class="text-decoration-none text-body cs-oppo-card cs-oppo-defaut d-flex align-items-center gap-3 p-3 rounded-3 border">

                        <div class="rounded-3 flex-shrink-0 d-flex align-items-center justify-content-center"
                             style="width:44px;height:44px;
                                    background:{{ str_replace(')', ',0.1)', str_replace('rgb', 'rgba', $d['color'])) }};
                                    font-family:'Bebas Neue',sans-serif;
                                    font-size:11px;letter-spacing:1px;
                                    color:{{ $d['color'] }};
                                    text-align:center;line-height:1.2;">
                            {{ strtoupper(substr($d['type'], 0, 3)) }}
                        </div>

                        <div class="flex-grow-1 overflow-hidden">
                            <div class="fw-semibold text-truncate mb-1" style="font-size:.84rem;">
                                {{ $d['titre'] }}
                            </div>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="badge rounded-pill"
                                      style="font-size:.65rem;
                                             background:{{ str_replace(')', ',0.1)', str_replace('rgb', 'rgba', $d['color'])) }};
                                             color:{{ $d['color'] }};">
                                    {{ $d['sport'] }}
                                </span>
                                <span class="text-body-secondary" style="font-size:.72rem;">
                                    {{ $d['club'] }}
                                </span>
                                <span class="text-body-secondary" style="font-size:.72rem;">
                                    &bull; {{ $d['ville'] }}
                                </span>
                                <span class="text-body-secondary" style="font-size:.72rem;">
                                    &bull; {{ $d['poste'] }} &mdash; {{ $d['age'] }}
                                </span>
                            </div>
                        </div>

                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none"
                             stroke="{{ $d['color'] }}" stroke-width="2" stroke-linecap="round"
                             class="flex-shrink-0">
                            <path d="M3 8h10M9 4l4 4-4 4"/>
                        </svg>
                    </a>
                @endif

            @empty
                {{-- Aucune donnée du tout --}}
                <div class="text-center py-4 text-body-secondary" style="font-size:.84rem;">
                    Aucune opportunité pour le moment.
                    <a href="{{ route('register') }}" class="text-primary fw-semibold">
                        Publiez la première.
                    </a>
                </div>
            @endforelse

        </div>

        {{-- Notice données par défaut --}}
        @if(count($defautVisible) > 0)
        <div class="d-flex align-items-center gap-2 mt-3 pt-3 border-top">
            <div style="width:6px;height:6px;border-radius:50%;background:#F97316;flex-shrink:0;opacity:.6;"></div>
            <p class="text-body-secondary mb-0 fst-italic" style="font-size:.72rem;">
                {{ count($defautVisible) }} exemple(s) affiché(s) —
                ils disparaissent à mesure que des opportunités sont publiées.
            </p>
        </div>
        @endif

    </div>
</div>

<style>
.cs-oppo-card {
    transition: box-shadow .18s, transform .18s, border-color .18s;
}
.cs-oppo-card:hover {
    box-shadow: 0 4px 14px rgba(var(--bs-primary-rgb), .1);
    transform: translateX(3px);
    border-color: var(--bs-primary) !important;
}
.cs-oppo-defaut {
    opacity: .8;
}
.cs-oppo-defaut:hover {
    opacity: 1;
}
</style>