{{-- resources/views/admin/profil/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Mon Profil Administrateur — Connect Sport')

@push('styles')
<style>
.cs-dash { display: flex; min-height: 100vh; background: var(--bg2); padding-top: 64px; }
.cs-dash__main { flex: 1; margin-left: 240px; display: flex; flex-direction: column; min-width: 0; }
.cs-dash__content { flex: 1; padding: 24px; }
.cs-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; margin-bottom: 24px; }
.cs-card__header { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
.cs-card__body { padding: 20px; }
@media (max-width: 768px) { .cs-dash__main { margin-left: 0; } .cs-dash__content { padding: 16px; } }
</style>
@endpush

@section('content')
<div class="cs-dash">
    @include('admin.partials._sidebar')

    <main class="cs-dash__main">
        <div class="cs-dash__content">

            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Mon Profil</li>
                </ol>
            </nav>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <h1 class="fw-bold mb-1" style="font-size:24px;color:var(--text);">Paramètres du Compte Administrateur</h1>
                    <p class="text-muted small mb-0">Mettez à jour vos coordonnées personnelles et votre mot de passe d'accès</p>
                </div>
            </div>

            <div class="row g-4">
                {{-- Colonne gauche : Informations du profil --}}
                <div class="col-lg-6">
                    <div class="cs-card h-100">
                        <div class="cs-card__header">
                            <h5 class="fw-bold mb-0" style="color:var(--text);">
                                <i class="bi bi-person-lines-fill me-2 text-primary"></i>Informations personnelles
                            </h5>
                        </div>
                        <div class="cs-card__body">

                            @if(session('success_profil'))
                            <div class="alert alert-success border-0 rounded-3 mb-4 d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill"></i> {{ session('success_profil') }}
                            </div>
                            @endif

                            @if($errors->has('name') || $errors->has('prenom') || $errors->has('email') || $errors->has('telephone'))
                            <div class="alert alert-danger border-0 rounded-3 mb-4">
                                @foreach($errors->only(['name', 'prenom', 'email', 'telephone']) as $err)
                                    <div><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $err[0] }}</div>
                                @endforeach
                            </div>
                            @endif

                            <form method="POST" action="{{ route('admin.profile.update') }}">
                                @csrf
                                @method('PUT')

                                <div class="row g-3 mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label small fw-semibold text-muted text-uppercase">Nom <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control rounded-3" value="{{ old('name', $user->name) }}" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label small fw-semibold text-muted text-uppercase">Prénom</label>
                                        <input type="text" name="prenom" class="form-control rounded-3" value="{{ old('prenom', $user->prenom) }}">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold text-muted text-uppercase">Adresse email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control rounded-3" value="{{ old('email', $user->email) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold text-muted text-uppercase">Numéro de téléphone</label>
                                    <input type="text" name="telephone" class="form-control rounded-3" value="{{ old('telephone', $user->telephone) }}" placeholder="+229 01 02 03 04">
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-semibold text-muted text-uppercase">Rôle plateforme</label>
                                    <input type="text" class="form-control rounded-3 bg-light" value="Administrateur (Super Admin)" disabled>
                                </div>

                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold text-white w-100" style="background:#1A56A0;border-color:#1A56A0;">
                                    <i class="bi bi-check-lg me-1"></i> Enregistrer les modifications
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Colonne droite : Sécurité & Changement de mot de passe --}}
                <div class="col-lg-6">
                    <div class="cs-card h-100">
                        <div class="cs-card__header">
                            <h5 class="fw-bold mb-0" style="color:var(--text);">
                                <i class="bi bi-shield-lock-fill me-2 text-warning"></i>Sécurité & Mot de passe
                            </h5>
                        </div>
                        <div class="cs-card__body">

                            @if(session('success_password'))
                            <div class="alert alert-success border-0 rounded-3 mb-4 d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill"></i> {{ session('success_password') }}
                            </div>
                            @endif

                            @if($errors->has('current_password') || $errors->has('password'))
                            <div class="alert alert-danger border-0 rounded-3 mb-4">
                                @foreach($errors->only(['current_password', 'password']) as $err)
                                    <div><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $err[0] }}</div>
                                @endforeach
                            </div>
                            @endif

                            <form method="POST" action="{{ route('admin.password.update') }}">
                                @csrf
                                @method('PUT')

                                {{-- Mot de passe actuel --}}
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold text-muted text-uppercase">Mot de passe actuel <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" name="current_password" id="curPassword" class="form-control rounded-start-3" placeholder="••••••••" required>
                                        <button type="button" class="btn btn-outline-secondary rounded-end-3" onclick="togglePass('curPassword', 'eyeCur')">
                                            <i class="bi bi-eye" id="eyeCur"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Nouveau mot de passe --}}
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold text-muted text-uppercase">Nouveau mot de passe <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="newPassword" class="form-control rounded-start-3" placeholder="••••••••" required>
                                        <button type="button" class="btn btn-outline-secondary rounded-end-3" onclick="togglePass('newPassword', 'eyeNew')">
                                            <i class="bi bi-eye" id="eyeNew"></i>
                                        </button>
                                    </div>
                                    <div class="form-text small text-muted">Le mot de passe doit comporter au moins 8 caractères.</div>
                                </div>

                                {{-- Confirmation nouveau mot de passe --}}
                                <div class="mb-4">
                                    <label class="form-label small fw-semibold text-muted text-uppercase">Confirmer le nouveau mot de passe <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" name="password_confirmation" id="newPasswordConf" class="form-control rounded-start-3" placeholder="••••••••" required>
                                        <button type="button" class="btn btn-outline-secondary rounded-end-3" onclick="togglePass('newPasswordConf', 'eyeConf')">
                                            <i class="bi bi-eye" id="eyeConf"></i>
                                        </button>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-semibold w-100">
                                    <i class="bi bi-key-fill me-1"></i> Mettre à jour le mot de passe
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

@push('scripts')
<script>
function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>
@endpush

@endsection
