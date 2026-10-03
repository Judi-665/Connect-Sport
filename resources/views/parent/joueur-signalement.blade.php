{{-- resources/views/parent/joueur-signalement.blade.php --}}
@extends('layouts.app')

@section('title', 'Signaler une difficulté / Souhait — Espace Parent')

@section('content')
<div class="container py-4 py-lg-5">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}" class="text-decoration-none">Espace Parent</a></li>
            <li class="breadcrumb-item"><a href="{{ route('parent.joueur.show', $joueur) }}" class="text-decoration-none">{{ $joueur->user->prenom ?? $joueur->user->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Signaler une difficulté / Souhait</li>
        </ol>
    </nav>

    @php $prenom = $joueur->user->prenom ?? $joueur->user->name; @endphp

    <div class="row g-4">

        {{-- Formulaire --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div style="height:5px;background:linear-gradient(90deg, #F59E0B, #EF4444);"></div>
                <div class="card-body p-4 p-md-5">

                    <h4 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                        Nouveau signalement pour {{ $prenom }}
                    </h4>

                    @if($joueur->club)
                        <div class="alert alert-info py-2 px-3 rounded-3 small mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-info-circle-fill"></i>
                            <span>Ce signalement sera transmis directement aux dirigeants du club <strong>{{ $joueur->club->nom }}</strong>.</span>
                        </div>
                    @else
                        <div class="alert alert-warning py-2 px-3 rounded-3 small mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle"></i>
                            <span>{{ $prenom }} n'est affilié(e) à aucun club actuellement.</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('parent.joueur.signalement.store', $joueur) }}">
                        @csrf

                        {{-- Type --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Type de signalement</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="radio" name="type" id="type-difficulte" value="difficulte" class="btn-check" {{ old('type', 'difficulte') === 'difficulte' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-danger w-100 rounded-3 p-3 text-center d-flex flex-column align-items-center gap-1" for="type-difficulte">
                                        <i class="bi bi-exclamation-circle-fill fs-4"></i>
                                        <span class="fw-bold small">Difficulté</span>
                                        <span class="text-body-secondary" style="font-size:11px;">Problème rencontré</span>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" name="type" id="type-voeu" value="voeu" class="btn-check" {{ old('type') === 'voeu' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary w-100 rounded-3 p-3 text-center d-flex flex-column align-items-center gap-1" for="type-voeu">
                                        <i class="bi bi-stars fs-4"></i>
                                        <span class="fw-bold small">Souhait / Vœu</span>
                                        <span class="text-body-secondary" style="font-size:11px;">Demande ou aspiration</span>
                                    </label>
                                </div>
                            </div>
                            @error('type')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        {{-- Catégorie + Priorité --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-7">
                                <label for="categorie" class="form-label fw-semibold small">Catégorie</label>
                                <select name="categorie" id="categorie" class="form-select rounded-3" required>
                                    <option value="">Choisir une catégorie...</option>
                                    <optgroup label="Intégration & Relations">
                                        <option value="integration_equipe" {{ old('categorie') === 'integration_equipe' ? 'selected' : '' }}>Intégration dans l'équipe</option>
                                        <option value="relation_club" {{ old('categorie') === 'relation_club' ? 'selected' : '' }}>Relations avec le club / encadrement</option>
                                    </optgroup>
                                    <optgroup label="Sportif">
                                        <option value="temps_de_jeu" {{ old('categorie') === 'temps_de_jeu' ? 'selected' : '' }}>Temps de jeu</option>
                                        <option value="poste_jeu" {{ old('categorie') === 'poste_jeu' ? 'selected' : '' }}>Poste / Positionnement</option>
                                        <option value="performance" {{ old('categorie') === 'performance' ? 'selected' : '' }}>Progression sportive</option>
                                    </optgroup>
                                    <optgroup label="Santé & Logistique">
                                        <option value="sante" {{ old('categorie') === 'sante' ? 'selected' : '' }}>Santé / Blessure / Fatigue</option>
                                        <option value="logistique" {{ old('categorie') === 'logistique' ? 'selected' : '' }}>Horaires / Transports</option>
                                    </optgroup>
                                    <option value="autre" {{ old('categorie') === 'autre' ? 'selected' : '' }}>Autre sujet</option>
                                </select>
                                @error('categorie')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-5">
                                <label for="priorite" class="form-label fw-semibold small">Priorité</label>
                                <select name="priorite" id="priorite" class="form-select rounded-3" required>
                                    <option value="basse"   {{ old('priorite', 'normale') === 'basse'   ? 'selected' : '' }}>🟢 Basse</option>
                                    <option value="normale" {{ old('priorite', 'normale') === 'normale' ? 'selected' : '' }}>🟡 Normale</option>
                                    <option value="haute"   {{ old('priorite') === 'haute'              ? 'selected' : '' }}>🔴 Haute (urgent)</option>
                                </select>
                                @error('priorite')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Titre --}}
                        <div class="mb-3">
                            <label for="titre" class="form-label fw-semibold small">Titre du signalement</label>
                            <input type="text" name="titre" id="titre" class="form-control rounded-3"
                                   placeholder="Ex : Question sur les horaires d'entraînement"
                                   value="{{ old('titre') }}" maxlength="255" required>
                            @error('titre')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        {{-- Contenu --}}
                        <div class="mb-4">
                            <label for="contenu" class="form-label fw-semibold small">Description détaillée</label>
                            <textarea name="contenu" id="contenu" class="form-control rounded-3" rows="5"
                                      placeholder="Expliquez la situation avec précision..."
                                      maxlength="2000" required>{{ old('contenu') }}</textarea>
                            @error('contenu')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold flex-grow-1" style="background:#0D9488;border-color:#0D9488;">
                                <i class="bi bi-send-fill me-1"></i> Transmettre au club
                            </button>
                            <a href="{{ route('parent.joueur.show', $joueur) }}" class="btn btn-outline-secondary rounded-pill px-4">
                                Annuler
                            </a>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        {{-- Historique des signalements --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-3 d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-clock-history text-muted me-1"></i> Historique récent</span>
                    <span class="badge bg-body-secondary text-body rounded-pill">{{ $signalementsExistants->count() }}</span>
                </h5>

                @if($signalementsExistants->isEmpty())
                    <p class="text-body-secondary small mb-0">Aucun signalement transmis pour le moment.</p>
                @else
                    <div class="d-flex flex-column gap-3">
                        @foreach($signalementsExistants as $sig)
                        <div class="p-3 rounded-3 bg-body-tertiary">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="badge {{ $sig->type === 'difficulte' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary' }} rounded-pill" style="font-size:11px;">
                                    {{ ucfirst($sig->type) }}
                                </span>
                                <span class="text-body-secondary" style="font-size:11px;">{{ $sig->created_at->format('d/m/Y') }}</span>
                            </div>
                            <div class="fw-bold small mb-1">{{ $sig->titre }}</div>
                            <p class="text-body-secondary small mb-0">{{ Str::limit($sig->contenu, 90) }}</p>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection
