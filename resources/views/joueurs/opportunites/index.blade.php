@extends('layouts.app')

@section('title', 'Opportunités — Connect Sport')

@section('content')
<div class="container py-5">

    <div class="d-flex align-items-center gap-2 mb-2">
        <div style="width:22px;height:2px;background:#F97316;border-radius:2px;"></div>
        <span class="fw-semibold text-uppercase text-warning" style="font-size:.65rem;letter-spacing:.14em;">
            Opportunités
        </span>
    </div>
    <h1 class="fw-black mb-4" style="font-family:'Bebas Neue',sans-serif;font-size:clamp(28px,4vw,44px);letter-spacing:.04em;">
        Opportunités disponibles
    </h1>

    @if(session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning rounded-3">{{ session('warning') }}</div>
    @endif

    @if($opportunites->isEmpty())
        <div class="text-center py-5">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:64px;height:64px;background:var(--bs-tertiary-bg);">
                <i class="bi bi-briefcase text-body-tertiary fs-3"></i>
            </div>
            <p class="fw-semibold mb-0">Aucune opportunité disponible pour le moment.</p>
        </div>
    @else
        <div class="row g-4">
            @foreach($opportunites as $opp)
            @php $statutCandidature = $candidatures[$opp->id] ?? null; @endphp
            <div class="col-md-6 col-lg-4">
                <div class="border rounded-4 h-100 p-4 d-flex flex-column">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge rounded-pill" style="font-size:.68rem;background:rgba(249,115,22,.12);color:#F97316;">
                            {{ ucfirst($opp->type) }}
                        </span>
                        @if($opp->mise_en_avant)
                            <i class="bi bi-star-fill text-warning"></i>
                        @endif
                    </div>

                    <h5 class="fw-bold mb-1" style="font-size:.95rem;">{{ $opp->titre }}</h5>
                    <div class="text-body-secondary mb-3" style="font-size:.78rem;">
                        <i class="bi bi-building me-1"></i>{{ $opp->club->nom }}
                        &bull; <i class="bi bi-geo-alt me-1"></i>{{ $opp->lieu }}, {{ $opp->pays }}
                    </div>

                    <p class="text-body-secondary flex-grow-1 mb-3" style="font-size:.82rem;line-height:1.5;">
                        {{ Str::limit($opp->description, 100) }}
                    </p>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @if($opp->poste_cible)
                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill" style="font-size:.68rem;">
                            {{ $opp->poste_cible }}
                        </span>
                        @endif
                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill" style="font-size:.68rem;">
                            <i class="bi bi-calendar3 me-1"></i>{{ $opp->date_limite?->format('d/m/Y') }}
                        </span>
                    </div>

                    @if($statutCandidature)
                        @php
                            $badgeConfig = [
                                'en_attente' => ['label' => 'Candidature en attente', 'bg' => 'rgba(249,115,22,.12)', 'color' => '#F97316'],
                                'acceptee'   => ['label' => 'Acceptée', 'bg' => 'rgba(34,197,94,.12)', 'color' => '#22C55E'],
                                'refusee'    => ['label' => 'Refusée', 'bg' => 'rgba(239,68,68,.12)', 'color' => '#EF4444'],
                            ][$statutCandidature];
                        @endphp
                        <span class="badge rounded-pill text-center py-2" style="background:{{ $badgeConfig['bg'] }};color:{{ $badgeConfig['color'] }};">
                            {{ $badgeConfig['label'] }}
                        </span>
                    @else
                        <form action="{{ route('joueur.opportunites.candidater', $opp) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm w-100 fw-semibold rounded-3">
                                Postuler
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $opportunites->links() }}</div>
    @endif

</div>
@endsection