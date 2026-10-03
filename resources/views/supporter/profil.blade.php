{{-- resources/views/supporter/profil.blade.php --}}
@extends('layouts.app')

@section('title', 'Mon Profil Supporter — Connect Sport')

@section('content')
<div class="container py-4 py-lg-5">

    <div class="row g-4 justify-content-center">
        {{-- Colonne récapitulative --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 mb-4">
                <div class="position-relative mx-auto mb-3" style="width: 96px; height: 96px;">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="rounded-circle object-fit-cover w-100 h-100 shadow-sm border border-2 border-primary">
                    @else
                        <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center fw-bold fs-2 shadow-sm w-100 h-100">
                            {{ strtoupper(substr($user->prenom ?? $user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <h4 class="fw-bold mb-1">{{ $user->prenom ?? '' }} {{ $user->name }}</h4>
                <p class="text-body-secondary small mb-3">{{ $user->email }}</p>

                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-danger rounded-pill px-3 py-2">
                        <i class="bi bi-star-fill me-1"></i> Supporter Officiel
                    </span>
                    @if($supporter->pays)
                        <span class="badge bg-secondary bg-opacity-10 text-body-secondary rounded-pill px-3 py-2">
                            <i class="bi bi-flag me-1"></i> {{ $supporter->pays }}
                        </span>
                    @endif
                </div>

                <hr class="my-3">

                <div class="row g-2 text-center">
                    <div class="col-6">
                        <div class="p-2 rounded-3 bg-body-tertiary">
                            <div class="fs-4 fw-bold text-primary">{{ $clubsCount }}</div>
                            <div class="text-body-secondary small">Clubs suivis</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 rounded-3 bg-body-tertiary">
                            <div class="fs-4 fw-bold text-success">Gratuit</div>
                            <div class="text-body-secondary small">Statut abonné</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('supporter.dashboard') }}" class="btn btn-outline-primary rounded-pill w-100">
                        <i class="bi bi-speedometer2 me-1"></i> Accéder au Dashboard
                    </a>
                </div>
            </div>
        </div>

        {{-- Formulaire d'édition --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <h4 class="fw-bold mb-1">Modifier mon profil</h4>
                    <p class="text-body-secondary small mb-4">Mettez à jour vos coordonnées personnelles et votre biographie.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 mb-4">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('supporter.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-body-secondary">Nom *</label>
                                <input type="text" name="name" class="form-control rounded-3" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-body-secondary">Prénom</label>
                                <input type="text" name="prenom" class="form-control rounded-3" value="{{ old('prenom', $user->prenom) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-body-secondary">Adresse Email</label>
                                <input type="email" class="form-control rounded-3 bg-body-secondary" value="{{ $user->email }}" disabled>
                                <div class="form-text" style="font-size: 0.75rem;">L'adresse email ne peut pas être modifiée ici.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-body-secondary">Numéro de téléphone</label>
                                <input type="text" name="telephone" class="form-control rounded-3" value="{{ old('telephone', $user->telephone) }}" placeholder="+229 ...">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-body-secondary">Ville</label>
                                <input type="text" name="ville" class="form-control rounded-3" value="{{ old('ville', $supporter->ville) }}" placeholder="Ex: Cotonou, Porto-Novo...">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-body-secondary">Pays *</label>
                                <input type="text" name="pays" class="form-control rounded-3" value="{{ old('pays', $supporter->pays ?? 'Bénin') }}" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold text-body-secondary">Bio / Présentation</label>
                                <textarea name="bio" rows="4" class="form-control rounded-3" placeholder="Parlez-nous de vos passions sportives, vos clubs de cœur...">{{ old('bio', $supporter->bio) }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('supporter.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">Annuler</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                                <i class="bi bi-check2 me-1"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
