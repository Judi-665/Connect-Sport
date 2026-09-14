{{--
    resources/views/partials/_clubs_recents.blade.php
    Carousel Bootstrap 5 — clubs récents.
    Dark/light géré via variables Bootstrap 5. Aucun emoji. Aucun sticker.
--}}

@php
    $clubsDefaut = [
        ['initiales'=>'AS','nom'=>'AS Cotonou FC',    'sport'=>'Football',   'ville'=>'Cotonou',    'joueurs'=>28, 'plan'=>'gratuit'],
        ['initiales'=>'HC','nom'=>'HC Lomé Stars',    'sport'=>'Handball',   'ville'=>'Lomé',       'joueurs'=>22, 'plan'=>'standard'],
        ['initiales'=>'BC','nom'=>'BC Abidjan',       'sport'=>'Basketball', 'ville'=>'Abidjan',    'joueurs'=>15, 'plan'=>'premium'],
        ['initiales'=>'VC','nom'=>'VC Dakar Élite',   'sport'=>'Volleyball', 'ville'=>'Dakar',      'joueurs'=>18, 'plan'=>'standard'],
        ['initiales'=>'AC','nom'=>'AC Douala United', 'sport'=>'Football',   'ville'=>'Douala',     'joueurs'=>32, 'plan'=>'premium'],
        ['initiales'=>'SC','nom'=>'SC Ouaga Sport',   'sport'=>'Athlétisme', 'ville'=>'Ouagadougou','joueurs'=>12, 'plan'=>'gratuit'],
    ];
    $planBadge = [
        'premium'  => ['bg'=>'rgba(249,115,22,.12)',  'color'=>'#F97316', 'label'=>'Premium'],
        'standard' => ['bg'=>'rgba(59,130,246,.12)',   'color'=>'#3B82F6', 'label'=>'Standard'],
        'gratuit'  => ['bg'=>'rgba(100,116,139,.12)',  'color'=>'#64748B', 'label'=>'Gratuit'],
    ];
    $useReal = isset($clubs) && $clubs->count() > 0;
    $chunks  = $useReal
        ? $clubs->chunk(3)
        : collect(array_chunk($clubsDefaut, 3));
    $totalChunks = $chunks->count();
@endphp

<div class="card border shadow-sm rounded-4">
    <div class="card-body p-4">

        {{-- En-tête --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div style="width:18px;height:2px;background:var(--bs-primary);border-radius:2px;"></div>
                    <span class="fw-semibold text-uppercase text-primary"
                          style="font-size:.63rem;letter-spacing:.13em;">
                        Ils nous font confiance
                    </span>
                </div>
                <h5 class="fw-bold mb-0" style="font-size:.95rem;">Clubs récents</h5>
            </div>
            <a href="{{ route('clubs.index') }}"
               class="btn btn-outline-primary btn-sm rounded-pill px-3"
               style="font-size:.76rem;">
                Voir tous
                <svg width="12" height="12" viewBox="0 0 16 16" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round"
                     style="margin-left:4px;">
                    <path d="M3 8h10M9 4l4 4-4 4"/>
                </svg>
            </a>
        </div>

        {{-- Carousel --}}
        <div id="carouselClubs" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">

            <div class="carousel-inner">
                @foreach($chunks as $chunkIndex => $chunk)
                <div class="carousel-item {{ $chunkIndex === 0 ? 'active' : '' }}">
                    <div class="row g-2">
                        @foreach($chunk as $item)
                        <div class="col-4">

                            @if($useReal)
                                <a href="{{ route('clubs.show', $item->slug) }}"
                                   class="text-decoration-none text-body cs-club-card d-block rounded-3 border overflow-hidden h-100">
                                    {{-- Bandeau haut --}}
                                    <div class="d-flex align-items-center justify-content-center"
                                         style="height:56px;background:linear-gradient(135deg,#0D2E5C,#1A56A0);">
                                        @if($item->logo)
                                            <img src="{{ Storage::url($item->logo) }}"
                                                 alt="{{ $item->nom }}"
                                                 style="height:36px;width:36px;object-fit:cover;border-radius:6px;">
                                        @else
                                            <span style="font-family:'Bebas Neue',sans-serif;font-size:18px;
                                                         color:#fff;letter-spacing:2px;">
                                                {{ strtoupper(substr($item->nom, 0, 2)) }}
                                            </span>
                                        @endif
                                    </div>
                                    {{-- Infos --}}
                                    <div class="p-2 text-center">
                                        <div class="fw-semibold text-truncate mb-1" style="font-size:.76rem;">
                                            {{ $item->nom }}
                                        </div>
                                        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary"
                                              style="font-size:.65rem;">
                                            {{ $item->sport->nom ?? 'Sport' }}
                                        </span>
                                        <div class="text-body-secondary mt-1" style="font-size:.68rem;">
                                            {{ $item->joueurs_count ?? 0 }} joueurs
                                            @if($item->ville)
                                                &bull; {{ $item->ville }}
                                            @endif
                                        </div>
                                    </div>
                                </a>

                            @else
                                @php $plan = $planBadge[$item['plan']] ?? $planBadge['gratuit']; @endphp
                                <a href="{{ route('register') }}"
                                   class="text-decoration-none text-body cs-club-card d-block rounded-3 border overflow-hidden h-100">
                                    <div class="d-flex align-items-center justify-content-center"
                                         style="height:56px;background:linear-gradient(135deg,#0D2E5C,#1A56A0);">
                                        <span style="font-family:'Bebas Neue',sans-serif;font-size:18px;
                                                     color:#fff;letter-spacing:2px;">
                                            {{ $item['initiales'] }}
                                        </span>
                                    </div>
                                    <div class="p-2 text-center">
                                        <div class="fw-semibold text-truncate mb-1" style="font-size:.76rem;">
                                            {{ $item['nom'] }}
                                        </div>
                                        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary"
                                              style="font-size:.65rem;">
                                            {{ $item['sport'] }}
                                        </span>
                                        <div class="text-body-secondary mt-1" style="font-size:.68rem;">
                                            {{ $item['joueurs'] }} joueurs &bull; {{ $item['ville'] }}
                                        </div>
                                        <span class="badge rounded-pill mt-1"
                                              style="font-size:.62rem;
                                                     background:{{ $plan['bg'] }};
                                                     color:{{ $plan['color'] }};">
                                            {{ $plan['label'] }}
                                        </span>
                                    </div>
                                </a>
                            @endif

                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Contrôles --}}
            <button class="carousel-control-prev" type="button"
                    data-bs-target="#carouselClubs" data-bs-slide="prev"
                    style="width:28px;opacity:.4;">
                <span class="carousel-control-prev-icon"
                      style="filter:invert(1) brightness(0.35);"></span>
            </button>
            <button class="carousel-control-next" type="button"
                    data-bs-target="#carouselClubs" data-bs-slide="next"
                    style="width:28px;opacity:.4;">
                <span class="carousel-control-next-icon"
                      style="filter:invert(1) brightness(0.35);"></span>
            </button>

            {{-- Indicateurs --}}
            <div class="carousel-indicators position-relative mt-3" style="bottom:unset;">
                @for($i = 0; $i < $totalChunks; $i++)
                <button type="button"
                        data-bs-target="#carouselClubs"
                        data-bs-slide-to="{{ $i }}"
                        class="{{ $i === 0 ? 'active' : '' }}"
                        style="width:6px;height:6px;border-radius:50%;
                               background:var(--bs-primary);border:none;opacity:.3;">
                </button>
                @endfor
            </div>

        </div>

        {{-- Notice données par défaut --}}
        @if(!$useReal)
        <p class="text-body-secondary text-center mb-0 mt-2 fst-italic"
           style="font-size:.72rem;">
            Données d'exemple — remplacées automatiquement dès les premières inscriptions.
        </p>
        @endif

    </div>
</div>

<style>
.cs-club-card { transition: box-shadow .18s, transform .18s; }
.cs-club-card:hover {
    box-shadow: 0 6px 18px rgba(var(--bs-primary-rgb), .12);
    transform: translateY(-2px);
}
</style>