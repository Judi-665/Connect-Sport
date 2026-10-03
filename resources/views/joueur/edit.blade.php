@extends('layouts.app')

@section('title', 'Modifier mon profil joueur')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 px-4 pt-4">
                    <p class="text-uppercase small fw-bold text-warning mb-1">Espace joueur</p>
                    <h1 class="h3 mb-1">Modifier mon profil</h1>
                    <p class="text-muted mb-0">Mettez à jour vos informations sportives.</p>
                </div>
                <div class="card-body px-4 py-4">
                    <form method="POST" action="{{ route('joueur.profil.update') }}" enctype="multipart/form-data" class="row g-3">
                        @csrf
                        @method('PUT')
                        <div class="col-md-6"><label for="poste" class="form-label">Poste</label><input id="poste" name="poste" value="{{ old('poste', $joueur->poste) }}" class="form-control @error('poste') is-invalid @enderror">@error('poste')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-md-6"><label for="categorie" class="form-label">Catégorie <span class="text-danger">*</span></label><select id="categorie" name="categorie" class="form-select" required>@foreach(['junior' => 'Junior', 'cadet' => 'Cadet', 'senior' => 'Senior', 'veteran' => 'Vétéran'] as $value => $label)<option value="{{ $value }}" @selected(old('categorie', $joueur->categorie) === $value)>{{ $label }}</option>@endforeach</select></div>
                        <div class="col-md-6"><label for="date_naissance" class="form-label">Date de naissance</label><input type="date" id="date_naissance" name="date_naissance" value="{{ old('date_naissance', optional($joueur->date_naissance)->format('Y-m-d')) }}" class="form-control"></div>
                        <div class="col-md-6"><label for="nationalite" class="form-label">Nationalité</label><input id="nationalite" name="nationalite" value="{{ old('nationalite', $joueur->nationalite) }}" class="form-control"></div>
                        <div class="col-md-6"><label for="telephone" class="form-label">Téléphone</label><input id="telephone" name="telephone" value="{{ old('telephone', $joueur->telephone) }}" class="form-control"></div>
                        <div class="col-md-6"><label for="ville" class="form-label">Ville</label><input id="ville" name="ville" value="{{ old('ville', $joueur->ville) }}" class="form-control"></div>
                        <div class="col-12 small text-muted">L’affiliation à un club ou à une équipe est gérée par le processus de candidature et ne peut pas être modifiée ici.</div>
                        <div class="col-12"><label for="bio" class="form-label">Présentation</label><textarea id="bio" name="bio" rows="4" class="form-control">{{ old('bio', $joueur->bio) }}</textarea></div>
                        <div class="col-md-6"><label for="avatar" class="form-label">Photo</label><input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp" class="form-control"></div>
                        <div class="col-md-6 d-flex align-items-end"><div class="form-check mb-2"><input type="checkbox" id="visible_recruteur" name="visible_recruteur" value="1" class="form-check-input" @checked(old('visible_recruteur', $joueur->visible_recruteur))><label for="visible_recruteur" class="form-check-label">Visible par les recruteurs</label></div></div>
                        <div class="col-12 d-flex justify-content-end"><button type="submit" class="btn btn-warning px-4 fw-semibold"><i class="bi bi-check2 me-1"></i>Enregistrer</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
