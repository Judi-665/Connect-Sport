@extends('layouts.app')

@section('title', 'Créer mon profil joueur')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 px-4 pt-4">
                    <p class="text-uppercase small fw-bold text-warning mb-1">Espace joueur</p>
                    <h1 class="h3 mb-1">Créer mon profil joueur</h1>
                    <p class="text-muted mb-0">Complétez vos informations pour être visible par les clubs et les agents.</p>
                </div>
                <div class="card-body px-4 py-4">
                    <form method="POST" action="{{ route('joueur.profil.store') }}" enctype="multipart/form-data" class="row g-3">
                        @csrf

                        <div class="col-md-6">
                            <label for="poste" class="form-label">Poste</label>
                            <input id="poste" name="poste" value="{{ old('poste') }}" class="form-control @error('poste') is-invalid @enderror" placeholder="Ex. Attaquant">
                            @error('poste')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="categorie" class="form-label">Catégorie <span class="text-danger">*</span></label>
                            <select id="categorie" name="categorie" class="form-select @error('categorie') is-invalid @enderror" required>
                                <option value="">Choisir une catégorie</option>
                                @foreach(['junior' => 'Junior', 'cadet' => 'Cadet', 'senior' => 'Senior', 'veteran' => 'Vétéran'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('categorie') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('categorie')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="date_naissance" class="form-label">Date de naissance</label>
                            <input type="date" id="date_naissance" name="date_naissance" value="{{ old('date_naissance') }}" max="{{ date('Y-m-d', strtotime('-1 day')) }}" class="form-control @error('date_naissance') is-invalid @enderror">
                            @error('date_naissance')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="nationalite" class="form-label">Nationalité</label>
                            <input id="nationalite" name="nationalite" value="{{ old('nationalite') }}" class="form-control @error('nationalite') is-invalid @enderror">
                            @error('nationalite')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input id="telephone" name="telephone" value="{{ old('telephone', auth()->user()->telephone) }}" class="form-control @error('telephone') is-invalid @enderror">
                            @error('telephone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="ville" class="form-label">Ville</label>
                            <input id="ville" name="ville" value="{{ old('ville') }}" class="form-control @error('ville') is-invalid @enderror">
                            @error('ville')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="club_id" class="form-label">Club actuel</label>
                            <select id="club_id" name="club_id" class="form-select @error('club_id') is-invalid @enderror">
                                <option value="">Sans club</option>
                                @foreach($clubs as $club)
                                    <option value="{{ $club->id }}" @selected(old('club_id') == $club->id)>{{ $club->nom }}{{ $club->ville ? ' - ' . $club->ville : '' }}</option>
                                @endforeach
                            </select>
                            @error('club_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="equipe_id" class="form-label">Équipe</label>
                            <select id="equipe_id" name="equipe_id" class="form-select @error('equipe_id') is-invalid @enderror">
                                <option value="">Aucune équipe</option>
                                @foreach($equipes as $equipe)
                                    <option value="{{ $equipe->id }}" @selected(old('equipe_id') == $equipe->id)>{{ $equipe->nom }}</option>
                                @endforeach
                            </select>
                            @error('equipe_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="bio" class="form-label">Présentation</label>
                            <textarea id="bio" name="bio" rows="4" class="form-control @error('bio') is-invalid @enderror" placeholder="Parcours, qualités et objectifs sportifs...">{{ old('bio') }}</textarea>
                            @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="avatar" class="form-label">Photo</label>
                            <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp" class="form-control @error('avatar') is-invalid @enderror">
                            <div class="form-text">JPEG, PNG ou WebP, 2 Mo maximum.</div>
                            @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning px-4 fw-semibold"><i class="bi bi-check2 me-1"></i>Créer mon profil</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
