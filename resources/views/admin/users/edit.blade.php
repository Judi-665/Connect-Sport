{{-- resources/views/admin/users/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier l\'utilisateur — ' . $user->name)

@push('styles')
<style>
.cs-dash { display: flex; min-height: 100vh; background: var(--bg2); padding-top: 64px; }
.cs-dash__main { flex: 1; margin-left: 240px; display: flex; flex-direction: column; min-width: 0; }
.cs-dash__content { flex: 1; padding: 24px; }
.cs-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
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
                    <li class="breadcrumb-item"><a href="{{ route('admin.users') }}" class="text-decoration-none">Utilisateurs</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.show', $user) }}" class="text-decoration-none">{{ $user->name }}</a></li>
                    <li class="breadcrumb-item active">Édition</li>
                </ol>
            </nav>

            <div class="cs-card p-4 mx-auto" style="max-width:720px;">
                <h4 class="fw-bold mb-1" style="color:var(--text);">Modifier l'utilisateur</h4>
                <p class="text-muted small mb-4">Modifiez les informations de base et le rôle du compte</p>

                @if($errors->any())
                <div class="alert alert-danger border-0 rounded-3 mb-4">
                    @foreach($errors->all() as $e)
                        <div><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $e }}</div>
                    @endforeach
                </div>
                @endif

                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold text-muted text-uppercase">Nom *</label>
                            <input type="text" name="name" class="form-control rounded-3" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold text-muted text-uppercase">Prénom</label>
                            <input type="text" name="prenom" class="form-control rounded-3" value="{{ old('prenom', $user->prenom) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted text-uppercase">Email *</label>
                        <input type="email" name="email" class="form-control rounded-3" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted text-uppercase">Téléphone</label>
                        <input type="text" name="telephone" class="form-control rounded-3" value="{{ old('telephone', $user->telephone) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted text-uppercase">Rôle de l'utilisateur *</label>
                        <select name="role" class="form-select rounded-3" required>
                            <option value="club" @selected(old('role', $user->role) === 'club')>Club</option>
                            <option value="joueur" @selected(old('role', $user->role) === 'joueur')>Joueur</option>
                            <option value="agent" @selected(old('role', $user->role) === 'agent')>Agent</option>
                            <option value="parent" @selected(old('role', $user->role) === 'parent')>Parent</option>
                            <option value="supporter" @selected(old('role', $user->role) === 'supporter')>Supporter</option>
                            <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                        </select>
                        <div class="form-text small text-muted">
                            Règle système : Le changement de rôle conserve l'historique et les données existantes pour archivage et intégrité.
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input type="checkbox" name="actif" value="1" class="form-check-input" id="userActif"
                               {{ old('actif', $user->actif ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="userActif">
                            Compte actif (décocher pour bloquer la connexion)
                        </label>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-light rounded-pill px-4">
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold text-white" style="background:#1A56A0;border-color:#1A56A0;">
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </main>
</div>
@endsection
