{{-- resources/views/club/equipes/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier — ' . $equipe->nom)

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
            <li class="breadcrumb-item active">Modifier — {{ $equipe->nom }}</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">

            {{-- Alertes --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible rounded-3 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-bottom pt-4 pb-3 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:42px;height:42px;background:rgba(249,115,22,.12);">
                            <i class="bi bi-pencil-fill text-warning fs-5"></i>
                        </div>
                        <div>
                            <h2 class="h5 fw-bold mb-0">Modifier l'équipe</h2>
                            <p class="text-muted small mb-0">{{ $equipe->nom }}</p>
                        </div>
                        {{-- Statut actif --}}
                        <div class="ms-auto">
                            @if($equipe->actif)
                                <span class="badge bg-success rounded-pill">Active</span>
                            @else
                                <span class="badge bg-secondary rounded-pill">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body px-4 py-4">
                    <form method="POST" action="{{ route('club.equipes.update', $equipe) }}">
                        @csrf @method('PUT')

                        {{-- Nom --}}
                        <div class="mb-4">
                            <label for="nom" class="form-label fw-semibold">
                                Nom de l'équipe <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   id="nom" name="nom"
                                   class="form-control rounded-3 @error('nom') is-invalid @enderror"
                                   value="{{ old('nom', $equipe->nom) }}"
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
                                    <option value="masculin" @selected(old('genre', $equipe->genre) === 'masculin')>Masculin</option>
                                    <option value="feminin"  @selected(old('genre', $equipe->genre) === 'feminin')>Féminin</option>
                                    <option value="mixte"    @selected(old('genre', $equipe->genre) === 'mixte')>Mixte</option>
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
                                    <option value="junior"  @selected(old('categorie', $equipe->categorie) === 'junior')>Junior</option>
                                    <option value="cadet"   @selected(old('categorie', $equipe->categorie) === 'cadet')>Cadet</option>
                                    <option value="senior"  @selected(old('categorie', $equipe->categorie) === 'senior')>Senior</option>
                                    <option value="veteran" @selected(old('categorie', $equipe->categorie) === 'veteran')>Vétéran / Loisir</option>
                                    <option value="autre"   @selected(old('categorie', $equipe->categorie) === 'autre')>Autre</option>
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
                                      rows="4">{{ old('description', $equipe->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Infos équipe en lecture seule --}}
                        <div class="alert alert-light border rounded-3 mb-4 py-2 px-3">
                            <div class="d-flex flex-wrap gap-3 small text-muted">
                                <span><i class="bi bi-people-fill me-1 text-warning"></i>{{ $equipe->joueurs->count() }} joueur(s)</span>
                                <span><i class="bi bi-calendar3 me-1 text-warning"></i>Créée le {{ $equipe->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex justify-content-between align-items-center pt-2">
                            {{-- Désactiver (danger, à gauche) --}}
                            @if($equipe->actif)
                                <button type="button"
                                        class="btn btn-outline-danger rounded-pill btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#deactivateModal">
                                    <i class="bi bi-slash-circle me-1"></i> Désactiver
                                </button>
                            @else
                                <div></div>
                            @endif

                            <div class="d-flex gap-2">
                                <a href="{{ route('club.equipes.index') }}"
                                   class="btn btn-outline-secondary rounded-pill px-4">
                                    Annuler
                                </a>
                                <button type="submit" class="btn btn-warning rounded-pill px-5 fw-semibold">
                                    <i class="bi bi-check-lg me-1"></i> Mettre à jour
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Modal désactivation --}}
@if($equipe->actif)
<div class="modal fade" id="deactivateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Désactiver l'équipe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir désactiver <strong>{{ $equipe->nom }}</strong> ?</p>
                @if($equipe->joueurs->count() > 0)
                    <div class="alert alert-warning rounded-3 py-2 small mb-2">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Cette équipe contient <strong>{{ $equipe->joueurs->count() }} joueur(s)</strong>.
                        Ils ne seront pas supprimés mais resteront sans équipe.
                    </div>
                @endif
                <p class="text-muted small mb-0">Cette action ne supprime pas les joueurs.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('club.equipes.destroy', $equipe) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Désactiver</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endsection