@extends('layouts.app')

@section('title', 'Saisir les statistiques — Connect Sport')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-9">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div style="width:22px;height:2px;background:#F97316;border-radius:2px;"></div>
                <span class="fw-semibold text-uppercase text-warning" style="font-size:.65rem;letter-spacing:.14em;">
                    Saisie officielle du club
                </span>
            </div>
            <h1 class="fw-black mb-1" style="font-family:'Bebas Neue',sans-serif;font-size:clamp(28px,4vw,44px);letter-spacing:.04em;">
                Statistiques du joueur
            </h1>
            <p class="text-body-secondary mb-4" style="font-size:.84rem;">
                {{ $evenement->titre }} · {{ $evenement->debut_at?->format('d/m/Y à H:i') }}
            </p>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('club.agenda.stats.store', $evenement) }}" class="card border-0 shadow-sm">
                @csrf
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label for="joueur_id" class="form-label fw-semibold">Joueur</label>
                        <select id="joueur_id" name="joueur_id" class="form-select" required>
                            <option value="">Sélectionner un joueur</option>
                            @foreach($joueurs as $joueur)
                                <option value="{{ $joueur->id }}" @selected(old('joueur_id') == $joueur->id)>
                                    {{ $joueur->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="matchs_joues" class="form-label">Matchs joués</label>
                            <input id="matchs_joues" name="matchs_joues" type="number" min="0" class="form-control" value="{{ old('matchs_joues', 1) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="matchs_titulaire" class="form-label">Titulaire</label>
                            <input id="matchs_titulaire" name="matchs_titulaire" type="number" min="0" class="form-control" value="{{ old('matchs_titulaire', 0) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="matchs_remplacant" class="form-label">Remplaçant</label>
                            <input id="matchs_remplacant" name="matchs_remplacant" type="number" min="0" class="form-control" value="{{ old('matchs_remplacant', 0) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="minutes_jouees" class="form-label">Minutes jouées</label>
                            <input id="minutes_jouees" name="minutes_jouees" type="number" min="0" class="form-control" value="{{ old('minutes_jouees', 0) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="buts" class="form-label">Buts</label>
                            <input id="buts" name="buts" type="number" min="0" class="form-control" value="{{ old('buts', 0) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="passes_decisives" class="form-label">Passes décisives</label>
                            <input id="passes_decisives" name="passes_decisives" type="number" min="0" class="form-control" value="{{ old('passes_decisives', 0) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="cartons_jaunes" class="form-label">Cartons jaunes</label>
                            <input id="cartons_jaunes" name="cartons_jaunes" type="number" min="0" class="form-control" value="{{ old('cartons_jaunes', 0) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="cartons_rouges" class="form-label">Cartons rouges</label>
                            <input id="cartons_rouges" name="cartons_rouges" type="number" min="0" class="form-control" value="{{ old('cartons_rouges', 0) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="note_moyenne" class="form-label">Note / 10</label>
                            <input id="note_moyenne" name="note_moyenne" type="number" min="0" max="10" step="0.01" class="form-control" value="{{ old('note_moyenne') }}">
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 px-4 pb-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('club.agenda.show', $evenement) }}" class="btn btn-outline-secondary">Annuler</a>
                    <button type="submit" class="btn btn-warning fw-bold">
                        <i class="bi bi-shield-check me-2"></i>Enregistrer officiellement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
