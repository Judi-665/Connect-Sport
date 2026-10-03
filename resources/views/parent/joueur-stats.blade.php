{{-- resources/views/parent/joueur-stats.blade.php --}}
@extends('layouts.app')

@section('title', 'Statistiques de ' . ($joueur->user->prenom ?? $joueur->user->name) . ' — Espace Parent')

@section('content')
<div class="container py-4 py-lg-5">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}" class="text-decoration-none">Espace Parent</a></li>
            <li class="breadcrumb-item"><a href="{{ route('parent.joueur.show', $joueur) }}" class="text-decoration-none">{{ $joueur->user->prenom ?? $joueur->user->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Statistiques</li>
        </ol>
    </nav>

    @php $prenom = $joueur->user->prenom ?? $joueur->user->name; @endphp

    {{-- Header --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm flex-shrink-0"
                     style="width:54px;height:54px;background:linear-gradient(135deg,#0D2E5C,#0D9488);font-size:18px;">
                    @if($joueur->user->avatar)
                        <img src="{{ Storage::url($joueur->user->avatar) }}" alt="{{ $prenom }}" class="w-100 h-100 rounded-circle object-fit-cover">
                    @else
                        {{ strtoupper(substr($prenom, 0, 2)) }}
                    @endif
                </div>
                <div>
                    <h1 class="h4 fw-bold mb-1">Statistiques de {{ $prenom }} {{ $joueur->user->name }}</h1>
                    <div class="d-flex gap-2 flex-wrap">
                        @if($joueur->club)
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">
                                <i class="bi bi-shield me-1"></i>{{ $joueur->club->nom }}
                            </span>
                        @endif
                        @if($joueur->poste)
                            <span class="badge bg-body-secondary text-body rounded-pill">
                                {{ ucfirst($joueur->poste) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <a href="{{ route('parent.joueur.show', $joueur) }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Retour au profil
            </a>
        </div>
    </div>

    {{-- KPI Agrégeats --}}
    <div class="row g-3 mb-4">
        @foreach([
            ['Matchs joués',      $aggregats['matchs_joues'],     'bi-trophy-fill',         'text-warning'],
            ['Buts',              $aggregats['buts'],             'bi-bullseye',             'text-danger'],
            ['Passes décisives',  $aggregats['passes_decisives'], 'bi-arrow-up-right-circle-fill', 'text-success'],
            ['Minutes jouées',    $aggregats['minutes_jouees'] . "'", 'bi-clock-fill',      'text-info'],
            ['Cartons jaunes',    $aggregats['cartons_jaunes'],   'bi-square-fill',         'text-warning'],
            ['Cartons rouges',    $aggregats['cartons_rouges'],   'bi-square-fill',         'text-danger'],
        ] as [$label, $val, $icon, $color])
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center h-100">
                <i class="bi {{ $icon }} {{ $color }} fs-4 mb-1"></i>
                <div class="fs-4 fw-bold">{{ $val }}</div>
                <div class="text-body-secondary small" style="font-size:11px;">{{ $label }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Tableau des statistiques par match --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-body py-3 px-4 border-bottom">
            <h5 class="fw-bold mb-0">Détails des matchs enregistrés</h5>
        </div>

        @if($statsSaison->isEmpty())
            <div class="p-5 text-center text-body-secondary">
                <i class="bi bi-bar-chart fs-1 mb-2 d-block text-muted"></i>
                <h6 class="fw-bold">Aucune statistique enregistrée</h6>
                <p class="small mb-0">Les statistiques validées par le club après chaque match apparaîtront ici.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase" style="letter-spacing:1px;font-size:11px;">
                        <tr>
                            <th class="ps-4">Match / Événement</th>
                            <th class="text-center">Min.</th>
                            <th class="text-center">Buts</th>
                            <th class="text-center">Passes</th>
                            <th class="text-center">CJ</th>
                            <th class="text-center">CR</th>
                            <th class="text-center pe-4">Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($statsSaison as $stat)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold">
                                    {{ $stat->evenement?->titre ?? ('Saison ' . ($stat->saison ?? '—')) }}
                                </div>
                                <div class="text-body-secondary small" style="font-size:11px;">
                                    {{ $stat->created_at->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="text-center">{{ $stat->minutes_jouees ?? 0 }}'</td>
                            <td class="text-center fw-bold {{ $stat->buts > 0 ? 'text-success' : '' }}">{{ $stat->buts ?? 0 }}</td>
                            <td class="text-center fw-bold {{ $stat->passes_decisives > 0 ? 'text-info' : '' }}">{{ $stat->passes_decisives ?? 0 }}</td>
                            <td class="text-center">{{ $stat->cartons_jaunes ?? 0 }}</td>
                            <td class="text-center">{{ $stat->cartons_rouges ?? 0 }}</td>
                            <td class="text-center pe-4">
                                @if($stat->note_moyenne)
                                    <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill fw-bold">
                                        {{ $stat->note_moyenne }}/10
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
