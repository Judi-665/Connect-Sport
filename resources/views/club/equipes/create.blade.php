{{-- resources/views/club/equipes/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Nouvelle équipe — ' . $club->nom)

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('club.dashboard') }}" class="text-warning text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('club.equipes.index') }}" class="text-warning text-decoration-none">Équipes</a>
            </li>
            <li class="breadcrumb-item active">Nouvelle équipe</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-bottom pt-4 pb-3 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:42px;height:42px;background:rgba(249,115,22,.12);">
                            <i class="bi bi-people-fill text-warning fs-5"></i>
                        </div>
                        <div>
                            <h2 class="h5 fw-bold mb-0">Créer une équipe</h2>
                            <p class="text-muted small mb-0">{{ $club->nom }}</p>
                        </div>
                    </div>
                </div>

                <div class="card-body px-4 py-4">
                    <form method="POST" action="{{ route('club.equipes.store') }}">
                        @csrf

                        {{-- Nom --}}
                        <div class="mb-4">
                            <label for="nom" class="form-label fw-semibold">
                                Nom de l'équipe <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   id="nom" name="nom"
                                   class="form-control rounded-3 @error('nom') is-invalid @enderror"
                                   value="{{ old('nom') }}"
                                   placeholder="Ex : Équipe A Seniors, U17 Masculins…"
                                   required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Genre + Catégorie --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="genre" class="form-label fw-semibold">
                                    Genre <span class="text-danger">*</span>
                                </label>
                                <select id="genre" name="genre"
                                        class="form-select rounded-3 @error('genre') is-invalid @enderror"
                                        required>
                                    <option value="" disabled @selected(!old('genre'))>Choisir…</option>
                                    <option value="masculin" @selected(old('genre') === 'masculin')>Masculin</option>
                                    <option value="feminin"  @selected(old('genre') === 'feminin')>Féminin</option>
                                    <option value="mixte"    @selected(old('genre') === 'mixte')>Mixte</option>
                                </select>
                                @error('genre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="categorie" class="form-label fw-semibold">
                                    Catégorie <span class="text-danger">*</span>
                                </label>
                                <select id="categorie" name="categorie"
                                        class="form-select rounded-3 @error('categorie') is-invalid @enderror"
                                        required>
                                    <option value="" disabled @selected(!old('categorie'))>Choisir…</option>
                                    <option value="junior"   @selected(old('categorie') === 'junior')>Junior</option>
                                    <option value="cadet"    @selected(old('categorie') === 'cadet')>Cadet</option>
                                    <option value="senior"   @selected(old('categorie') === 'senior')>Senior</option>
                                    <option value="veteran"  @selected(old('categorie') === 'veteran')>Vétéran / Loisir</option>
                                    <option value="autre"    @selected(old('categorie') === 'autre')>Autre</option>
                                </select>
                                @error('categorie')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">
                                Description
                                <span class="text-muted fw-normal">(optionnel)</span>
                            </label>
                            <textarea id="description" name="description"
                                      class="form-control rounded-3 @error('description') is-invalid @enderror"
                                      rows="4"
                                      placeholder="Entraîneur, objectifs de la saison, particularités…">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Maximum 1000 caractères.</div>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex justify-content-end gap-2 pt-2">
                            <a href="{{ route('club.equipes.index') }}"
                               class="btn btn-outline-secondary rounded-pill px-4">
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-warning rounded-pill px-5 fw-semibold">
                                <i class="bi bi-plus-lg me-1"></i> Créer l'équipe
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection