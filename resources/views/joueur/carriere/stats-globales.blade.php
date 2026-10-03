@extends('layouts.app')

@section('title', 'Statistiques — Connect Sport')

@section('content')
<div class="container py-5">

    <div class="d-flex align-items-center gap-2 mb-2">
        <div style="width:22px;height:2px;background:#F97316;border-radius:2px;"></div>
        <span class="fw-semibold text-uppercase text-warning" style="font-size:.65rem;letter-spacing:.14em;">
            Carrière
        </span>
    </div>
    <h1 class="fw-black mb-4" style="font-family:'Bebas Neue',sans-serif;font-size:clamp(28px,4vw,44px);letter-spacing:.04em;">
        Statistiques globales
    </h1>

    @if(!$stats || is_null($stats->saisons) || $stats->saisons == 0)
        <div class="text-center py-5">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:64px;height:64px;background:var(--bs-tertiary-bg);">
                <i class="bi bi-bar-chart text-body-tertiary fs-3"></i>
            </div>
            <p class="fw-semibold mb-0">Aucune statistique enregistrée pour le moment.</p>
            <p class="text-body-secondary mb-0" style="font-size:.84rem;">
                Elles apparaîtront ici dès que votre club en aura saisi après un match.
            </p>
        </div>
    @else

        {{-- ── Totaux ── --}}
        <div class="row g-3 mb-5">
            @foreach([
                ['label' => 'Saisons',   'val' => $stats->saisons,                        'icon' => 'bi-calendar3'],
                ['label' => 'Matchs',    'val' => $stats->matchs_total,                   'icon' => 'bi-calendar-check'],
                ['label' => 'Titulaire', 'val' => $stats->titulaire,                      'icon' => 'bi-person-check'],
                ['label' => 'Buts',      'val' => $stats->buts,                           'icon' => 'bi-bullseye'],
                ['label' => 'Passes',    'val' => $stats->passes,                         'icon' => 'bi-arrow-left-right'],
                ['label' => 'Note moy.', 'val' => $stats->note_moyenne ? number_format($stats->note_moyenne, 1) . '/10' : '—', 'icon' => 'bi-star'],
            ] as $stat)
            <div class="col-6 col-md-4 col-lg-2">
                <div class="rounded-4 border p-3 text-center h-100">
                    <i class="bi {{ $stat['icon'] }} text-warning mb-2" style="font-size:1.1rem;"></i>
                    <div class="fw-black lh-1" style="font-family:'Bebas Neue',sans-serif;font-size:24px;color:#F97316;">
                        {{ $stat['val'] }}
                    </div>
                    <div class="text-body-secondary" style="font-size:.7rem;">{{ $stat['label'] }}</div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ── Détail discipline ── --}}
        <div class="row g-3 mb-5">
            <div class="col-md-4">
                <div class="rounded-4 border p-4 h-100">
                    <div class="text-body-secondary text-uppercase mb-1" style="font-size:.68rem;letter-spacing:.08em;">
                        Temps de jeu
                    </div>
                    <div class="fw-black" style="font-family:'Bebas Neue',sans-serif;font-size:22px;">
                        {{ $stats->minutes }} min
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="rounded-4 border p-4 h-100">
                    <div class="text-body-secondary text-uppercase mb-1" style="font-size:.68rem;letter-spacing:.08em;">
                        Remplaçant
                    </div>
                    <div class="fw-black" style="font-family:'Bebas Neue',sans-serif;font-size:22px;">
                        {{ $stats->remplacant }} fois
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="rounded-4 border p-4 h-100">
                    <div class="text-body-secondary text-uppercase mb-1" style="font-size:.68rem;letter-spacing:.08em;">
                        Cartons
                    </div>
                    <div class="d-flex gap-3 align-items-center">
                        <span class="badge rounded-pill" style="background:rgba(234,179,8,.15);color:#CA8A04;font-size:.75rem;">
                            {{ $stats->cartons_jaunes }} jaunes
                        </span>
                        <span class="badge rounded-pill" style="background:rgba(239,68,68,.12);color:#EF4444;font-size:.75rem;">
                            {{ $stats->cartons_rouges }} rouges
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Courbe d'évolution ── --}}
        <h2 class="fw-black mb-3" style="font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:.04em;">
            Évolution par saison
        </h2>

        <div class="rounded-4 border p-4">
            <canvas id="evolutionChart" height="90"></canvas>
        </div>

    @endif

</div>

@if($parSaison->isNotEmpty())
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('evolutionChart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($parSaison->pluck('saison')),
            datasets: [
                {
                    label: 'Buts',
                    data: @json($parSaison->pluck('buts')),
                    borderColor: '#F97316',
                    backgroundColor: 'rgba(249,115,22,.1)',
                    tension: .3,
                    fill: true,
                },
                {
                    label: 'Passes décisives',
                    data: @json($parSaison->pluck('passes')),
                    borderColor: '#5B9BD5',
                    backgroundColor: 'rgba(91,155,213,.1)',
                    tension: .3,
                    fill: true,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } },
            scales: { y: { beginAtZero: true } },
        },
    });
</script>
@endif
@endsection