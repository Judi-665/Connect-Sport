@extends('layouts.app')

@section('title', 'Statistiques — ' . $joueur->nomComplet())

@section('content')
<div class="container py-5">

    <div class="d-flex align-items-center gap-2 mb-2">
        <div style="width:22px;height:2px;background:#F97316;border-radius:2px;"></div>
        <span class="fw-semibold text-uppercase text-warning" style="font-size:.65rem;letter-spacing:.14em;">
            Joueur
        </span>
    </div>
    <h1 class="fw-black mb-4" style="font-family:'Bebas Neue',sans-serif;font-size:clamp(26px,4vw,40px);letter-spacing:.04em;">
        Statistiques — {{ $joueur->nomComplet() }}
    </h1>

    {{-- ── Filtre saison ── --}}
    @if($saisons->isNotEmpty())
    <form method="GET" class="mb-4">
        <select name="saison" class="form-select rounded-3" style="max-width:200px;" onchange="this.form.submit()">
            @foreach($saisons as $s)
                <option value="{{ $s }}" @selected($saisonSelectionnee === $s)>{{ $s }}</option>
            @endforeach
        </select>
    </form>
    @endif

    @if($stats->isEmpty())
        <div class="text-center py-5">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:64px;height:64px;background:var(--bs-tertiary-bg);">
                <i class="bi bi-bar-chart text-body-tertiary fs-3"></i>
            </div>
            <p class="fw-semibold mb-0">Aucune statistique pour cette saison.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Événement</th>
                        <th>Matchs</th>
                        <th>Buts</th>
                        <th>Passes</th>
                        <th>Note</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats as $stat)
                    <tr>
                        <td>{{ $stat->evenement->titre ?? '—' }} <span class="text-body-secondary" style="font-size:.75rem;">{{ $stat->evenement?->debut_at?->format('d/m/Y') }}</span></td>
                        <td>{{ $stat->matchs_joues }}</td>
                        <td>{{ $stat->buts }}</td>
                        <td>{{ $stat->passes_decisives }}</td>
                        <td>{{ $stat->note_moyenne ?? '—' }}</td>
                        <td>
                            @if($stat->valide)
                                <span class="badge rounded-pill" style="background:rgba(34,197,94,.12);color:#22C55E;font-size:.68rem;">Validée</span>
                            @else
                                <span class="badge rounded-pill" style="background:rgba(249,115,22,.12);color:#F97316;font-size:.68rem;">En attente</span>
                            @endif
                        </td>
                        <td>
                            @if(!$stat->valide && auth()->user()->role === 'club')
                            <form action="{{ route('club.statistiques.valider', $stat) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-outline-success rounded-3">Valider</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @endif

</div>
@endsection