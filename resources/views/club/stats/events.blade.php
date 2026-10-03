@extends('layouts.app')

@section('title', 'Enregistrer des statistiques — Connect Sport')

@section('content')
<div class="container py-5">
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
        <div>
            <a href="{{ route('club.statistiques.index') }}" class="text-warning text-decoration-none small">
                <i class="bi bi-arrow-left me-1"></i>Retour aux statistiques
            </a>
            <h1 class="fw-black mb-1 mt-2" style="font-family:'Bebas Neue',sans-serif;font-size:clamp(28px,4vw,44px);letter-spacing:.04em;">
                Enregistrer des statistiques
            </h1>
            <p class="text-body-secondary mb-0" style="font-size:.84rem;">
                Choisissez un événement de votre club pour renseigner les joueurs concernés.
            </p>
        </div>
    </div>

    @if($evenements->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-calendar-x fs-1 text-body-secondary d-block mb-3"></i>
            <p class="fw-semibold mb-1">Aucun événement enregistré</p>
            <p class="text-body-secondary small">Créez d'abord un événement dans l'agenda de votre club.</p>
            <a href="{{ route('club.agenda.create') }}" class="btn btn-warning fw-bold">
                <i class="bi bi-calendar-plus me-2"></i>Créer un événement
            </a>
        </div>
    @else
        <div class="row g-3">
            @foreach($evenements as $evenement)
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                <span class="badge bg-warning text-dark">{{ ucfirst($evenement->type) }}</span>
                                <span class="text-body-secondary small">
                                    {{ $evenement->debut_at?->format('d/m/Y') }}
                                </span>
                            </div>
                            <h2 class="h6 fw-bold mb-1">{{ $evenement->titre }}</h2>
                            <p class="text-body-secondary small mb-3">
                                {{ $evenement->debut_at?->format('H:i') }}
                                @if($evenement->lieu) · {{ $evenement->lieu }} @endif
                            </p>
                            <div class="mt-auto d-flex align-items-center justify-content-between gap-2">
                                <span class="text-body-secondary small">
                                    {{ $evenement->statistiques_count }} statistique(s)
                                </span>
                                <a href="{{ route('club.agenda.stats.create', $evenement) }}" class="btn btn-sm btn-warning fw-bold">
                                    <i class="bi bi-pencil-square me-1"></i>Renseigner
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
