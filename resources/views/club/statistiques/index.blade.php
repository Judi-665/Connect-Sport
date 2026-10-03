@extends('layouts.app')

@section('title', 'Statistiques des joueurs — Connect Sport')

@section('content')
<div class="container py-5">

    <div class="row align-items-end mb-4 g-3">
        <div class="col-md-8">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div style="width:22px;height:2px;background:#F97316;border-radius:2px;"></div>
                <span class="fw-semibold text-uppercase text-warning" style="font-size:.65rem;letter-spacing:.14em;">
                    Statistiques
                </span>
            </div>
            <h1 class="fw-black mb-0" style="font-family:'Bebas Neue',sans-serif;font-size:clamp(28px,4vw,44px);letter-spacing:.04em;">
                Statistiques des joueurs
            </h1>
            <p class="text-body-secondary mb-0 mt-1" style="font-size:.84rem;">
                Enregistrez et validez les statistiques des joueurs de votre club.
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('club.statistiques.enregistrer') }}" class="btn btn-warning fw-bold rounded-3 px-4">
                <i class="bi bi-plus-circle me-2"></i>Enregistrer des statistiques
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if($joueurs->isEmpty())
        <div class="text-center py-5">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:64px;height:64px;background:var(--bs-tertiary-bg);">
                <i class="bi bi-bar-chart text-body-tertiary fs-3"></i>
            </div>
            <p class="fw-semibold mb-0">Aucun joueur dans l'effectif.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Joueur</th>
                        <th>Matchs</th>
                        <th>Buts</th>
                        <th>Passes</th>
                        <th>En attente</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($joueurs as $joueur)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle fw-black d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:34px;height:34px;background:rgba(249,115,22,.12);color:#F97316;
                                            font-family:'Bebas Neue',sans-serif;font-size:13px;">
                                    {{ strtoupper(substr($joueur->user->name, 0, 2)) }}
                                </div>
                                <span class="fw-semibold" style="font-size:.86rem;">{{ $joueur->user->name }}</span>
                            </div>
                        </td>
                        <td>{{ $joueur->total_matchs ?? 0 }}</td>
                        <td>{{ $joueur->total_buts ?? 0 }}</td>
                        <td>{{ $joueur->total_passes ?? 0 }}</td>
                        <td>
                            @if($joueur->stats_en_attente > 0)
                                <span class="badge rounded-pill" style="background:rgba(249,115,22,.12);color:#F97316;font-size:.7rem;">
                                    {{ $joueur->stats_en_attente }} à valider
                                </span>
                            @else
                                <span class="text-body-secondary" style="font-size:.75rem;">—</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('club.statistiques.joueur', $joueur) }}" class="btn btn-sm btn-outline-secondary rounded-3">
                                Voir détail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $joueurs->links() }}</div>
    @endif

</div>
@endsection