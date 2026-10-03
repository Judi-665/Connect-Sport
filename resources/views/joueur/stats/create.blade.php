@extends('layouts.app')

@section('title', 'Saisir les statistiques — Connect Sport')

@section('content')
<div class="container py-5">

    <div class="d-flex align-items-center gap-2 mb-2">
        <div style="width:22px;height:2px;background:#F97316;border-radius:2px;"></div>
        <span class="fw-semibold text-uppercase text-warning" style="font-size:.65rem;letter-spacing:.14em;">
            Statistiques
        </span>
    </div>
    <h1 class="fw-black mb-4" style="font-family:'Bebas Neue',sans-serif;font-size:clamp(26px,4vw,40px);letter-spacing:.04em;">
        Saisir les stats — {{ $evenement->titre ?? 'Événement' }}
    </h1>

    <p class="text-body-secondary mb-4" style="font-size:.84rem;">
        {{ $evenement->date_debut?->format('d/m/Y') }} — un formulaire par joueur convoqué.
    </p>

    <div class="d-flex flex-column gap-3">
        @foreach($joueurs as $joueur)
        <div class="rounded-4 border p-3 p-md-4">
            <form action="{{ route('club.agenda.stats.store', $evenement) }}" method="POST">
                @csrf
                <input type="hidden" name="joueur_id" value="{{ $joueur->id }}">

                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle fw-black d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:40px;height:40px;background:rgba(249,115,22,.12);color:#F97316;
                                font-family:'Bebas Neue',sans-serif;font-size:15px;">
                        {{ strtoupper(substr($joueur->user->name, 0, 2)) }}
                    </div>
                    <h5 class="fw-bold mb-0" style="font-size:.9rem;">{{ $joueur->user->name }}</h5>
                </div>

                <div class="row g-2">
                    <div class="col-6 col-md-3">
                        <label class="form-label mb-1" style="font-size:.72rem;">Matchs joués</label>
                        <input type="number" name="matchs_joues" class="form-control form-control-sm rounded-3" min="0" value="1" required>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label mb-1" style="font-size:.72rem;">Minutes jouées</label>
                        <input type="number" name="minutes_jouees" class="form-control form-control-sm rounded-3" min="0">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label mb-1" style="font-size:.72rem;">Buts</label>
                        <input type="number" name="buts" class="form-control form-control-sm rounded-3" min="0" value="0">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label mb-1" style="font-size:.72rem;">Passes déc.</label>
                        <input type="number" name="passes_decisives" class="form-control form-control-sm rounded-3" min="0" value="0">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label mb-1" style="font-size:.72rem;">Note /10</label>
                        <input type="number" step="0.1" name="note_moyenne" class="form-control form-control-sm rounded-3" min="0" max="10">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label mb-1" style="font-size:.72rem;">Titulaire</label>
                        <select name="matchs_titulaire" class="form-select form-select-sm rounded-3">
                            <option value="1">Oui</option>
                            <option value="0" selected>Non</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label mb-1" style="font-size:.72rem;">Cartons jaunes</label>
                        <input type="number" name="cartons_jaunes" class="form-control form-control-sm rounded-3" min="0" value="0">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label mb-1" style="font-size:.72rem;">Cartons rouges</label>
                        <input type="number" name="cartons_rouges" class="form-control form-control-sm rounded-3" min="0" value="0">
                    </div>
                </div>

                <button type="submit" class="btn btn-warning btn-sm rounded-3 fw-semibold mt-3">
                    <i class="bi bi-check2 me-1"></i>Enregistrer
                </button>
            </form>
        </div>
        @endforeach
    </div>

</div>
@endsection