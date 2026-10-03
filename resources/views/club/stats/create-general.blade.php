@extends('layouts.app')

@section('title', 'Enregistrer les statistiques — Connect Sport')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-9">
            <a href="{{ route('club.statistiques.index') }}" class="text-warning text-decoration-none small">
                <i class="bi bi-arrow-left me-1"></i>Retour aux statistiques
            </a>
            <div class="d-flex align-items-center gap-2 mt-3 mb-2">
                <div style="width:22px;height:2px;background:#F97316;border-radius:2px;"></div>
                <span class="fw-semibold text-uppercase text-warning" style="font-size:.65rem;letter-spacing:.14em;">
                    Saisie officielle du club
                </span>
            </div>
            <h1 class="fw-black mb-1" style="font-family:'Bebas Neue',sans-serif;font-size:clamp(28px,4vw,44px);letter-spacing:.04em;">
                Statistiques générales du joueur
            </h1>
            <p class="text-body-secondary mb-4" style="font-size:.84rem;">
                {{ $club->sport?->nom ?? 'Sport du club' }} · Ces informations sont renseignées par votre club pour la saison choisie.
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

            <form method="POST" action="{{ route('club.statistiques.enregistrer.store') }}" class="card border-0 shadow-sm">
                @csrf
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-7">
                            <label for="joueur_id" class="form-label fw-semibold">Joueur du club</label>
                            <select id="joueur_id" name="joueur_id" class="form-select" required>
                                <option value="">Sélectionner un joueur</option>
                                @foreach($joueurs as $joueur)
                                    <option value="{{ $joueur->id }}" @selected(old('joueur_id') == $joueur->id)>
                                        {{ $joueur->nomComplet() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="annee_debut" class="form-label fw-semibold">Année de début</label>
                            <select id="annee_debut" name="annee_debut" class="form-select" required>
                                @foreach($annees as $annee)
                                    <option value="{{ $annee }}" @selected(old('annee_debut', $anneeEnCours) == $annee)>
                                        {{ $annee }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="annee_fin" class="form-label fw-semibold">Année de fin</label>
                            <select id="annee_fin" name="annee_fin" class="form-select" required>
                                @foreach($annees as $annee)
                                    <option value="{{ $annee }}" @selected(old('annee_fin', $anneeEnCours + 1) == $annee)>
                                        {{ $annee }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Exemple : 2023 - 2024.</div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="matchs_joues" class="form-label">Matchs joués</label>
                            <input id="matchs_joues" name="matchs_joues" type="number" min="0" class="form-control" value="{{ old('matchs_joues', 0) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="matchs_titulaire" class="form-label">Matchs titulaire</label>
                            <input id="matchs_titulaire" name="matchs_titulaire" type="number" min="0" class="form-control" value="{{ old('matchs_titulaire', 0) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="matchs_remplacant" class="form-label">Matchs remplaçant</label>
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
                            <label for="note_moyenne" class="form-label">Note moyenne / 10</label>
                            <input id="note_moyenne" name="note_moyenne" type="number" min="0" max="10" step="0.01" class="form-control" value="{{ old('note_moyenne') }}">
                        </div>
                    </div>

                    @php
                        $sportSlug = \Illuminate\Support\Str::slug($club->sport?->slug ?? $club->sport?->nom ?? '');
                        $champsSport = match (true) {
                            str_contains($sportSlug, 'basket') => [
                                'rebonds' => 'Rebonds',
                                'interceptions' => 'Interceptions',
                                'contres' => 'Contres',
                                'points' => 'Points',
                            ],
                            str_contains($sportSlug, 'hand') => [
                                'arrets' => 'Arrêts du gardien',
                                'penaltys_arretes' => 'Penaltys arrêtés',
                                'exclusions' => 'Exclusions',
                            ],
                            str_contains($sportSlug, 'foot') => [
                                'tirs_cadres' => 'Tirs cadrés',
                                'duels_gagnes' => 'Duels gagnés',
                                'interceptions' => 'Interceptions',
                            ],
                            default => [
                                'points' => 'Points',
                                'passes' => 'Passes décisives',
                            ],
                        };
                    @endphp

                    <div class="mt-4 pt-4 border-top">
                        <h2 class="h6 fw-bold mb-1">Statistiques spécifiques</h2>
                        <p class="text-body-secondary small mb-3">Champs adaptés au sport du club.</p>
                        <div class="row g-3">
                            @foreach($champsSport as $cle => $libelle)
                                <div class="col-md-4">
                                    <label for="stats_{{ $cle }}" class="form-label">{{ $libelle }}</label>
                                    <input id="stats_{{ $cle }}" name="stats_complementaires[{{ $cle }}]" type="number" min="0" class="form-control" value="{{ old('stats_complementaires.' . $cle, 0) }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 px-4 pb-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('club.statistiques.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    <button type="submit" class="btn btn-warning fw-bold">
                        <i class="bi bi-shield-check me-2"></i>Enregistrer les statistiques
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
