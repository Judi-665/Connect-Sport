@extends('layouts.app')

@section('title', 'Agenda — Connect Sport')

@section('content')
<div class="container py-5">

    <div class="d-flex align-items-center gap-2 mb-2">
        <div style="width:22px;height:2px;background:#F97316;border-radius:2px;"></div>
        <span class="fw-semibold text-uppercase text-warning" style="font-size:.65rem;letter-spacing:.14em;">
            Mon club
        </span>
    </div>
    <h1 class="fw-black mb-4" style="font-family:'Bebas Neue',sans-serif;font-size:clamp(28px,4vw,44px);letter-spacing:.04em;">
        Agenda {{ $club ? '— ' . $club->nom : '' }}
    </h1>

    @if(!$club)
        <div class="text-center py-5">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:64px;height:64px;background:var(--bs-tertiary-bg);">
                <i class="bi bi-calendar-x text-body-tertiary fs-3"></i>
            </div>
            <p class="fw-semibold mb-1">Vous n'appartenez à aucun club pour le moment.</p>
            <p class="text-body-secondary mb-0" style="font-size:.84rem;">
                Les événements de votre club apparaîtront ici une fois rattaché.
            </p>
        </div>
    @else

        {{-- ── Prochains événements ── --}}
        <h2 class="fw-black mb-3" style="font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:.04em;">
            Prochains matchs
        </h2>

        @if($prochains->isEmpty())
            <p class="text-body-secondary mb-5" style="font-size:.84rem;">Aucun événement à venir programmé.</p>
        @else
            <div class="row g-3 mb-5">
                @foreach($prochains as $match)
                <div class="col-md-6 col-lg-4">
                    <div class="rounded-4 border p-4 h-100 d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge rounded-pill" style="font-size:.65rem;background:rgba(249,115,22,.12);color:#F97316;">
                                <i class="bi bi-calendar-event me-1"></i>À venir
                            </span>
                            <span class="text-body-secondary" style="font-size:.72rem;">
                                {{ $match->debut_at?->translatedFormat('d M Y · H:i') ?? 'Date à confirmer' }}
                            </span>
                        </div>
                        <h6 class="fw-bold mb-2 text-truncate" style="font-size:.88rem;">{{ $match->titre }}</h6>
                        <div class="d-flex align-items-center justify-content-between py-2 my-auto border-top border-bottom">
                            <span class="fw-semibold small">{{ $club->nom }}</span>
                            <span class="text-body-secondary" style="font-size:.75rem;">VS</span>
                            <span class="fw-semibold small">{{ $match->adversaire_nom ?? 'Adversaire' }}</span>
                        </div>
                        <div class="text-body-secondary mt-2 mb-3" style="font-size:.75rem;">
                            <i class="bi bi-geo-alt me-1"></i>{{ $match->lieu ?? 'Lieu non renseigné' }}
                            &bull; {{ $match->domicile_exterieur === 'exterieur' ? 'Extérieur' : 'Domicile' }}
                        </div>
                        <a href="{{ route('joueur.agenda.show', $match) }}" class="btn btn-sm btn-outline-warning rounded-3 w-100 mt-auto">
                            Voir plus de détails
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mb-5">{{ $prochains->links() }}</div>
        @endif

        {{-- ── Résultats récents ── --}}
        <h2 class="fw-black mb-3" style="font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:.04em;">
            Résultats récents
        </h2>

        @if($resultats->isEmpty())
            <p class="text-body-secondary" style="font-size:.84rem;">Aucun résultat enregistré pour le moment.</p>
        @else
            <div class="row g-3">
                @foreach($resultats as $res)
                @php
                    $victoire = $res->resultat === 'victoire' || ($res->score_nous > $res->score_eux);
                    $defaite  = $res->resultat === 'defaite' || ($res->score_nous < $res->score_eux);
                    $borderColor = $victoire ? 'border-success' : ($defaite ? 'border-danger' : 'border-secondary');
                    $badgeConfig = $victoire
                        ? ['label' => 'Victoire', 'bg' => 'rgba(34,197,94,.12)', 'color' => '#22C55E']
                        : ($defaite ? ['label' => 'Défaite', 'bg' => 'rgba(239,68,68,.12)', 'color' => '#EF4444']
                                    : ['label' => 'Match nul', 'bg' => 'var(--bs-tertiary-bg)', 'color' => 'inherit']);
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="rounded-4 border border-start border-4 {{ $borderColor }} p-4 h-100 d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge rounded-pill" style="font-size:.65rem;background:{{ $badgeConfig['bg'] }};color:{{ $badgeConfig['color'] }};">
                                {{ $badgeConfig['label'] }}
                            </span>
                            <span class="text-body-secondary" style="font-size:.72rem;">
                                {{ $res->debut_at?->translatedFormat('d M Y') }}
                            </span>
                        </div>
                        <h6 class="fw-bold mb-2 text-truncate" style="font-size:.88rem;">{{ $res->titre }}</h6>
                        <div class="d-flex align-items-center justify-content-between py-2 my-auto">
                            <span class="fw-semibold small">{{ $club->nom }}</span>
                            <span class="fw-black" style="font-family:'Bebas Neue',sans-serif;font-size:1.3rem;color:#F97316;">
                                {{ $res->score_nous }} - {{ $res->score_eux }}
                            </span>
                            <span class="fw-semibold small">{{ $res->adversaire_nom ?? 'Adversaire' }}</span>
                        </div>
                        <a href="{{ route('joueur.agenda.show', $res) }}" class="btn btn-sm btn-outline-secondary rounded-3 w-100 mt-3">
                            Voir plus de détails
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $resultats->links() }}</div>
        @endif

    @endif

</div>
@endsection