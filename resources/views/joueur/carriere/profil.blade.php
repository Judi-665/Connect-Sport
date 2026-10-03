@extends('layouts.app')

@section('title', 'Profil carrière — Connect Sport')

@section('content')
<div class="container py-5">

    <div class="d-flex align-items-center gap-2 mb-2">
        <div style="width:22px;height:2px;background:#F97316;border-radius:2px;"></div>
        <span class="fw-semibold text-uppercase text-warning" style="font-size:.65rem;letter-spacing:.14em;">
            Carrière
        </span>
    </div>
    <h1 class="fw-black mb-4" style="font-family:'Bebas Neue',sans-serif;font-size:clamp(28px,4vw,44px);letter-spacing:.04em;">
        Profil carrière
    </h1>

    {{-- ── Stats clés ── --}}
    <div class="row g-3 mb-5">
        @foreach([
            ['label' => 'Clubs',   'val' => $stats['clubs'],   'icon' => 'bi-shield'],
            ['label' => 'Équipes', 'val' => $stats['equipes'], 'icon' => 'bi-people'],
            ['label' => 'Licences','val' => $stats['licences'],'icon' => 'bi-award'],
            ['label' => 'Matchs',  'val' => $stats['matchs'],  'icon' => 'bi-calendar-check'],
            ['label' => 'Buts',    'val' => $stats['buts'],    'icon' => 'bi-bullseye'],
            ['label' => 'Passes',  'val' => $stats['passes'],  'icon' => 'bi-arrow-left-right'],
        ] as $stat)
        <div class="col-6 col-md-4 col-lg-2">
            <div class="rounded-4 border p-3 text-center h-100">
                <i class="bi {{ $stat['icon'] }} text-warning mb-2" style="font-size:1.1rem;"></i>
                <div class="fw-black lh-1" style="font-family:'Bebas Neue',sans-serif;font-size:26px;color:#F97316;">
                    {{ $stat['val'] }}
                </div>
                <div class="text-body-secondary" style="font-size:.7rem;">{{ $stat['label'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Liens rapides ── --}}
    <div class="d-flex flex-wrap gap-2 mb-5">
        <a href="{{ route('joueur.carriere.statistiques') }}" class="btn btn-outline-warning btn-sm rounded-3 fw-semibold">
            <i class="bi bi-graph-up me-2"></i>Statistiques détaillées
        </a>
        <a href="{{ route('joueur.transferts.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 fw-semibold">
            <i class="bi bi-arrow-left-right me-2"></i>Historique des transferts
        </a>
        <a href="{{ route('joueur.carriere.cv') }}" class="btn btn-outline-secondary btn-sm rounded-3 fw-semibold">
            <i class="bi bi-download me-2"></i>Télécharger mon CV
        </a>
    </div>

    {{-- ── Timeline combinée (carrières + transferts) ── --}}
    <h2 class="fw-black mb-3" style="font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:.04em;">
        Parcours complet
    </h2>

    @if(empty($carriereTimeline))
        <div class="text-center py-5">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:64px;height:64px;background:var(--bs-tertiary-bg);">
                <i class="bi bi-clock-history text-body-tertiary fs-3"></i>
            </div>
            <p class="fw-semibold mb-0">Aucun événement pour le moment.</p>
        </div>
    @else
        <div class="position-relative ps-4" style="border-left:2px solid var(--bs-border-color);">
            @foreach($carriereTimeline as $event)
            <div class="position-relative mb-4">
                @php
                    $isTransfert = $event['type'] === 'transfert';
                    $dotColor    = $isTransfert ? '#5B9BD5' : '#F97316';
                @endphp
                <div class="position-absolute rounded-circle"
                     style="width:12px;height:12px;background:{{ $dotColor }};left:-27px;top:6px;border:2px solid var(--bs-body-bg);"></div>

                <div class="rounded-4 border p-3 p-md-4">
                    @if($isTransfert)
                        @php $t = $event['data']; @endphp
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <span class="badge rounded-pill mb-1" style="font-size:.65rem;background:rgba(91,155,213,.12);color:#1A56A0;">
                                    Transfert &bull; {{ ucfirst($t->type) }}
                                </span>
                                <h5 class="fw-bold mb-0" style="font-size:.88rem;">
                                    {{ $t->clubSource->nom }} &rarr; {{ $t->clubDestinataire->nom }}
                                </h5>
                            </div>
                            <span class="text-body-secondary" style="font-size:.75rem;">
                                {{ $t->date_effet?->format('d/m/Y') ?? '—' }}
                            </span>
                        </div>
                    @else
                        @php $c = $event['data']; @endphp
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <span class="badge rounded-pill mb-1" style="font-size:.65rem;background:rgba(249,115,22,.12);color:#F97316;">
                                    {{ ucfirst($c->origine) }}
                                </span>
                                <h5 class="fw-bold mb-0" style="font-size:.88rem;">
                                    {{ $c->club->nom }} &bull; {{ $c->poste ?? 'Poste non renseigné' }}
                                </h5>
                            </div>
                            <span class="text-body-secondary" style="font-size:.75rem;">
                                {{ $c->date_debut->format('d/m/Y') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif

</div>
@endsection