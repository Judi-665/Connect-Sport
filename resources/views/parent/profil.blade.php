{{-- resources/views/parent/profil.blade.php --}}
@extends('layouts.app')

@section('title', 'Mon Profil — Espace Parent — Connect Sport')

@section('content')
@include('parent.partials._sidebar')

<div class="ms-0 ms-lg-auto py-4 py-lg-5 px-3 px-md-4"
     style="padding-left: 260px !important; max-width: calc(100vw - 240px);">

    {{-- En-tête --}}
    <div class="rounded-4 p-4 p-md-5 mb-4 shadow-sm position-relative overflow-hidden text-white"
         style="background: linear-gradient(135deg, #0D2E5C 0%, #1A56A0 55%, #0B1E38 100%);">
        <div class="position-absolute rounded-circle"
             style="top:-60px;right:-60px;width:240px;height:240px;
                    background: radial-gradient(circle, rgba(45,212,191,.25) 0%, rgba(45,212,191,0) 70%); pointer-events:none;"></div>

        <div class="d-flex align-items-center gap-4">
            {{-- Avatar cliquable --}}
            <div class="position-relative flex-shrink-0" id="avatar-wrapper" style="cursor:pointer;" onclick="document.getElementById('avatar-input').click()">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}"
                         class="rounded-circle object-fit-cover shadow"
                         style="width:90px;height:90px;border:3px solid rgba(255,255,255,.3);"
                         id="avatar-preview">
                @else
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow"
                         id="avatar-initials"
                         style="width:90px;height:90px;background:linear-gradient(135deg,#0D9488,#2DD4BF);color:#fff;font-size:2rem;border:3px solid rgba(255,255,255,.3);">
                        {{ strtoupper(substr($user->prenom ?? $user->name ?? 'P', 0, 1)) }}
                    </div>
                @endif
                <div class="position-absolute bottom-0 end-0 rounded-circle d-flex align-items-center justify-content-center"
                     style="width:26px;height:26px;background:#2DD4BF;border:2px solid #0D2E5C;">
                    <i class="bi bi-camera-fill" style="font-size:11px;color:#064E3B;"></i>
                </div>
            </div>

            <div>
                <span class="badge fw-bold text-uppercase px-2 py-1 rounded-pill mb-2"
                      style="background:#2DD4BF;color:#064E3B;font-size:.72rem;letter-spacing:1px;">
                    Espace Parent
                </span>
                <h1 class="display-6 fw-bold text-white mb-0">
                    {{ $user->prenom ?? '' }} {{ $user->name }}
                </h1>
                <p class="text-white-50 mb-0 small">{{ $user->email }}</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- Colonne gauche : Informations personnelles --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 p-lg-5">
                    <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                        <span class="rounded-circle d-flex align-items-center justify-content-center"
                              style="width:36px;height:36px;background:rgba(13,148,136,.12);color:#0D9488;">
                            <i class="bi bi-person-fill fs-6"></i>
                        </span>
                        Informations personnelles
                    </h5>

                    <form method="POST" action="{{ route('parent.profil.update') }}" enctype="multipart/form-data" id="profil-form">
                        @csrf @method('PUT')

                        {{-- Input fichier caché pour l'avatar --}}
                        <input type="file" id="avatar-input" name="avatar" accept="image/jpeg,image/png,image/webp" class="d-none">

                        @if($errors->any())
                            <div class="alert alert-danger rounded-3 border-0 small mb-4">
                                <ul class="mb-0 ps-3">
                                    @foreach($errors->all() as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row g-3">
                            {{-- Prénom --}}
                            <div class="col-md-6">
                                <label for="prenom" class="form-label fw-semibold small text-body-secondary text-uppercase" style="letter-spacing:.5px;">Prénom</label>
                                <input type="text" id="prenom" name="prenom"
                                       class="form-control rounded-3 @error('prenom') is-invalid @enderror"
                                       value="{{ old('prenom', $user->prenom) }}"
                                       placeholder="Votre prénom">
                                @error('prenom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Nom --}}
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold small text-body-secondary text-uppercase" style="letter-spacing:.5px;">Nom <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name"
                                       class="form-control rounded-3 @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name) }}"
                                       placeholder="Votre nom de famille" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-12">
                                <label for="email" class="form-label fw-semibold small text-body-secondary text-uppercase" style="letter-spacing:.5px;">Adresse email <span class="text-danger">*</span></label>
                                <input type="email" id="email" name="email"
                                       class="form-control rounded-3 @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}"
                                       placeholder="votre@email.com" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Téléphone --}}
                            <div class="col-md-6">
                                <label for="telephone" class="form-label fw-semibold small text-body-secondary text-uppercase" style="letter-spacing:.5px;">Téléphone</label>
                                <input type="text" id="telephone" name="telephone"
                                       class="form-control rounded-3 @error('telephone') is-invalid @enderror"
                                       value="{{ old('telephone', $user->telephone) }}"
                                       placeholder="+33 6 00 00 00 00">
                                @error('telephone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Lien familial (informatif, stocké dans la liaison) --}}
                            <div class="col-md-6">
                                <label for="lien_type" class="form-label fw-semibold small text-body-secondary text-uppercase" style="letter-spacing:.5px;">Je suis le / la</label>
                                @php
                                    $premierLien = auth()->user()->parentsJoueurs()->actif()->first();
                                    $lienActuel  = $premierLien?->lien ?? 'autre';
                                @endphp
                                <select id="lien_type" name="lien_type" class="form-select rounded-3">
                                    <option value="pere"   {{ $lienActuel === 'pere'   ? 'selected' : '' }}>Pere</option>
                                    <option value="mere"   {{ $lienActuel === 'mere'   ? 'selected' : '' }}>Mere</option>
                                    <option value="tuteur" {{ $lienActuel === 'tuteur' ? 'selected' : '' }}>Tuteur legal</option>
                                    <option value="autre"  {{ $lienActuel === 'autre'  ? 'selected' : '' }}>Autre</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn rounded-pill px-5 fw-semibold shadow-sm"
                                    style="background:#0D9488;color:#fff;">
                                <i class="bi bi-floppy-fill me-2"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Colonne droite : Changer le mot de passe --}}
        <div class="col-lg-5 d-flex flex-column gap-4">

            {{-- Photo de profil (upload) --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <span class="rounded-circle d-flex align-items-center justify-content-center"
                              style="width:36px;height:36px;background:rgba(13,46,92,.1);color:#0D2E5C;">
                            <i class="bi bi-camera2 fs-6"></i>
                        </span>
                        Photo de profil
                    </h5>
                    <p class="text-body-secondary small mb-3">
                        Cliquez sur votre avatar en haut pour changer votre photo. Formats acceptes : JPG, PNG, WebP — max 2 Mo.
                    </p>
                    <button type="button" class="btn btn-outline-secondary rounded-pill w-100"
                            onclick="document.getElementById('avatar-input').click()">
                        <i class="bi bi-upload me-2"></i> Changer la photo
                    </button>
                    <div id="avatar-change-notice" class="alert alert-info rounded-3 border-0 small mt-3 d-none">
                        <i class="bi bi-info-circle me-1"></i> Photo selectionnee. Cliquez sur "Enregistrer" pour confirmer.
                    </div>
                </div>
            </div>

            {{-- Mot de passe --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                        <span class="rounded-circle d-flex align-items-center justify-content-center"
                              style="width:36px;height:36px;background:rgba(220,53,69,.1);color:#dc3545;">
                            <i class="bi bi-lock-fill fs-6"></i>
                        </span>
                        Changer le mot de passe
                    </h5>

                    @if($errors->hasBag('password'))
                        <div class="alert alert-danger rounded-3 border-0 small mb-3">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->getBag('password')->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('parent.password.update') }}">
                        @csrf @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-semibold small text-body-secondary text-uppercase" style="letter-spacing:.5px;">Mot de passe actuel</label>
                            <input type="password" id="current_password" name="current_password"
                                   class="form-control rounded-3 @error('current_password', 'password') is-invalid @enderror"
                                   placeholder="Mot de passe actuel" autocomplete="current-password">
                            @error('current_password', 'password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold small text-body-secondary text-uppercase" style="letter-spacing:.5px;">Nouveau mot de passe</label>
                            <input type="password" id="password" name="password"
                                   class="form-control rounded-3 @error('password', 'password') is-invalid @enderror"
                                   placeholder="Min. 8 caracteres" autocomplete="new-password">
                            @error('password', 'password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold small text-body-secondary text-uppercase" style="letter-spacing:.5px;">Confirmer le mot de passe</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-control rounded-3"
                                   placeholder="Repetez le nouveau mot de passe" autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn btn-danger rounded-pill w-100 fw-semibold">
                            <i class="bi bi-shield-lock-fill me-2"></i> Modifier le mot de passe
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
    // Apercu de l'avatar avant envoi
    const avatarInput = document.getElementById('avatar-input');
    avatarInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            const wrapper = document.getElementById('avatar-wrapper');
            // Remplacer le contenu par un img
            wrapper.innerHTML = `
                <img src="${e.target.result}" alt="Apercu"
                     class="rounded-circle object-fit-cover shadow"
                     style="width:90px;height:90px;border:3px solid rgba(255,255,255,.3);">
                <div class="position-absolute bottom-0 end-0 rounded-circle d-flex align-items-center justify-content-center"
                     style="width:26px;height:26px;background:#2DD4BF;border:2px solid #0D2E5C;">
                    <i class="bi bi-camera-fill" style="font-size:11px;color:#064E3B;"></i>
                </div>
            `;
            // Relier le nouvel input au formulaire
            const form = document.getElementById('profil-form');
            form.appendChild(avatarInput);
        };
        reader.readAsDataURL(file);

        document.getElementById('avatar-change-notice').classList.remove('d-none');
    });
</script>
@endsection
