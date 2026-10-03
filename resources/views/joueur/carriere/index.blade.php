@extends('layouts.app')

@section('title', 'Ma carrière — Connect Sport')

@section('content')
<div class="container py-5">

    <div class="d-flex align-items-center gap-2 mb-2">
        <div style="width:22px;height:2px;background:#F97316;border-radius:2px;"></div>
        <span class="fw-semibold text-uppercase text-warning" style="font-size:.65rem;letter-spacing:.14em;">
            Carrière
        </span>
    </div>
    <h1 class="fw-black mb-4" style="font-family:'Bebas Neue',sans-serif;font-size:clamp(28px,4vw,44px);letter-spacing:.04em;">
        Mon parcours
    </h1>

    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('joueur.carriere.index') }}"
           class="btn btn-sm rounded-3 fw-semibold {{ request()->routeIs('joueur.carriere.index') ? 'btn-warning' : 'btn-outline-secondary' }}">
            <i class="bi bi-clock-history me-1"></i>Parcours
        </a>
        <a href="{{ route('joueur.carriere.profil') }}"
           class="btn btn-sm rounded-3 fw-semibold {{ request()->routeIs('joueur.carriere.profil') ? 'btn-warning' : 'btn-outline-secondary' }}">
            <i class="bi bi-person-badge me-1"></i>Profil carrière
        </a>
        <a href="{{ route('joueur.carriere.statistiques') }}"
           class="btn btn-sm rounded-3 fw-semibold {{ request()->routeIs('joueur.carriere.statistiques') ? 'btn-warning' : 'btn-outline-secondary' }}">
            <i class="bi bi-bar-chart me-1"></i>Statistiques
        </a>
        <a href="{{ route('joueur.transferts.index') }}"
        class="btn btn-sm rounded-3 fw-semibold {{ request()->routeIs('joueur.transferts.*') ? 'btn-warning' : 'btn-outline-secondary' }}">
            <i class="bi bi-arrow-left-right me-1"></i>Transferts
        </a>
        <a href="{{ route('joueur.carriere.cv') }}"
           class="btn btn-sm rounded-3 fw-semibold btn-outline-secondary">
            <i class="bi bi-download me-1"></i>CV
        </a>
    </div>

    {{-- ── Situation actuelle ── --}}
    <div class="row g-3 mb-5">
        <div class="col-md-6">
            <div class="rounded-4 border p-4 h-100">
                <div class="text-body-secondary text-uppercase mb-2" style="font-size:.68rem;letter-spacing:.08em;">
                    Club actuel
                </div>
                @if($clubActuel)
                    <div class="d-flex align-items-center gap-3">
                        @if($clubActuel->logo)
                            <img src="{{ Storage::url($clubActuel->logo) }}" class="rounded-3 flex-shrink-0"
                                 width="44" height="44" style="object-fit:cover;">
                        @else
                            <div class="rounded-3 fw-black d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:44px;height:44px;background:rgba(91,155,213,.12);color:#1A56A0;
                                        font-family:'Bebas Neue',sans-serif;font-size:16px;">
                                {{ strtoupper(substr($clubActuel->nom, 0, 2)) }}
                            </div>
                        @endif
                        <div>
                            <h5 class="fw-bold mb-0" style="font-size:.92rem;">{{ $clubActuel->nom }}</h5>
                            <div class="text-body-secondary" style="font-size:.75rem;">
                                {{ $clubActuel->ville }}
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-body-secondary mb-2" style="font-size:.84rem;">
                        Vous n'appartenez à aucun club pour le moment.
                    </p>
                    <a href="{{ route('clubs.index') }}" class="btn btn-sm btn-outline-warning rounded-3 fw-semibold">
                        Trouver un club
                    </a>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="rounded-4 border p-4 h-100">
                <div class="text-body-secondary text-uppercase mb-2" style="font-size:.68rem;letter-spacing:.08em;">
                    Équipe(s) actuelle(s)
                </div>
                @if($equipesActuelles->isNotEmpty())
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($equipesActuelles as $equipe)
                        <span class="badge rounded-pill" style="font-size:.72rem;background:rgba(249,115,22,.12);color:#F97316;">
                            {{ $equipe->nom }}
                        </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-body-secondary mb-0" style="font-size:.84rem;">Aucune équipe assignée.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Timeline ── --}}
    <h2 class="fw-black mb-3" style="font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:.04em;">
        Historique des clubs
    </h2>

    @if($historique->isEmpty())
        <div class="text-center py-5">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:64px;height:64px;background:var(--bs-tertiary-bg);">
                <i class="bi bi-clock-history text-body-tertiary fs-3"></i>
            </div>
            <p class="fw-semibold mb-0">Aucun historique pour le moment.</p>
        </div>
    @else
        <div class="position-relative ps-4" style="border-left:2px solid var(--bs-border-color);">
            @foreach($historique as $etape)
            <div class="position-relative mb-4">
                <div class="position-absolute rounded-circle"
                     style="width:12px;height:12px;background:#F97316;left:-27px;top:6px;border:2px solid var(--bs-body-bg);"></div>

                <div class="rounded-4 border p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold mb-0" style="font-size:.9rem;">{{ $etape->club->nom }}</h5>
                            <div class="text-body-secondary" style="font-size:.75rem;">
                                {{ $etape->poste ?? 'Poste non renseigné' }}
                            </div>
                        </div>
                        <span class="badge rounded-pill"
                              style="font-size:.68rem;background:{{ $etape->estActuelle() ? 'rgba(34,197,94,.12)' : 'var(--bs-tertiary-bg)' }};
                                     color:{{ $etape->estActuelle() ? '#22C55E' : 'inherit' }};">
                            {{ $etape->date_debut->format('m/Y') }}
                            —
                            {{ $etape->estActuelle() ? 'Aujourd\'hui' : $etape->date_fin->format('m/Y') }}
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $historique->links() }}</div>
    @endif

</div>
@endsection