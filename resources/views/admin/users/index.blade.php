{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des Utilisateurs — Administration')

@push('styles')
<style>
.cs-dash {
    display: flex;
    min-height: 100vh;
    background: var(--bg2);
    padding-top: 64px;
}
.cs-dash__main {
    flex: 1;
    margin-left: 240px;
    display: flex;
    flex-direction: column;
    min-width: 0;
}
.cs-dash__content { flex: 1; padding: 24px; }
.cs-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
}
.cs-tag {
    display: inline-flex; align-items: center;
    font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 20px;
}
.cs-tag--club      { background: rgba(249,115,22,.12); color: #F97316; }
.cs-tag--joueur    { background: rgba(16,185,129,.12); color: #10b981; }
.cs-tag--agent     { background: rgba(139,92,246,.12); color: #8b5cf6; }
.cs-tag--supporter { background: rgba(236,72,153,.12); color: #ec4899; }
.cs-tag--parent    { background: rgba(13,148,136,.12); color: #0d9488; }
.cs-tag--admin     { background: rgba(26,86,160,.12);  color: #1A56A0; }
@media (max-width: 768px) {
    .cs-dash__main { margin-left: 0; }
    .cs-dash__content { padding: 16px; }
}
</style>
@endpush

@section('content')
<div class="cs-dash">
    @include('admin.partials._sidebar')

    <main class="cs-dash__main">
        <div class="cs-dash__content">

            {{-- Titre et résumé rapide --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <h1 class="fw-bold mb-1" style="font-size:24px;color:var(--text);">Gestion des Utilisateurs</h1>
                    <p class="text-muted small mb-0">Tous les comptes inscrits sur la plateforme ({{ $counts['total'] }} au total)</p>
                </div>
            </div>

            {{-- Messages Flash --}}
            @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
            @endif
            @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                @foreach($errors->all() as $e)
                    <div><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $e }}</div>
                @endforeach
            </div>
            @endif

            {{-- Filtres & Recherche --}}
            <div class="cs-card p-3 mb-4">
                <form method="GET" action="{{ route('admin.users') }}" class="row g-2 align-items-end">
                    <div class="col-md-3 col-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Rôle</label>
                        <select name="role" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                            <option value="">Tous les rôles ({{ $counts['total'] }})</option>
                            <option value="club" @selected(request('role') === 'club')>Club ({{ $counts['clubs'] }})</option>
                            <option value="joueur" @selected(request('role') === 'joueur')>Joueur ({{ $counts['joueurs'] }})</option>
                            <option value="agent" @selected(request('role') === 'agent')>Agent ({{ $counts['agents'] }})</option>
                            <option value="parent" @selected(request('role') === 'parent')>Parent ({{ $counts['parents'] }})</option>
                            <option value="supporter" @selected(request('role') === 'supporter')>Supporter ({{ $counts['supporters'] }})</option>
                            <option value="admin" @selected(request('role') === 'admin')>Admin ({{ $counts['admins'] }})</option>
                        </select>
                    </div>

                    <div class="col-md-3 col-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Statut du compte</label>
                        <select name="statut" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                            <option value="">Tous les statuts</option>
                            <option value="actif" @selected(request('statut') === 'actif')>Actif uniquement</option>
                            <option value="bloque" @selected(request('statut') === 'bloque')>Bloqué ({{ $counts['bloques'] }})</option>
                        </select>
                    </div>

                    <div class="col-md-4 col-8">
                        <label class="form-label small fw-semibold text-muted mb-1">Rechercher</label>
                        <input type="text" name="search" class="form-control form-control-sm rounded-3"
                               placeholder="Nom, prénom, email, téléphone..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2 col-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm rounded-3 fw-semibold w-100" style="background:#1A56A0;border-color:#1A56A0;">
                            Filtrer
                        </button>
                        <a href="{{ route('admin.users') }}" class="btn btn-light btn-sm rounded-3 border">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Tableau des Utilisateurs --}}
            <div class="cs-card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Utilisateur</th>
                                <th>Rôle</th>
                                <th>Contact</th>
                                <th>Statut</th>
                                <th>Inscrit le</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $u)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                             style="width:36px;height:36px;background:#1A56A0;font-size:12px;">
                                            {{ strtoupper(substr($u->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.users.show', $u) }}" class="fw-semibold text-decoration-none" style="color:var(--text);">
                                                {{ $u->prenom ? $u->prenom . ' ' : '' }}{{ $u->name }}
                                            </a>
                                            <div class="text-muted small">{{ $u->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="cs-tag cs-tag--{{ $u->role }}">
                                        {{ ucfirst($u->role) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="small text-muted">{{ $u->telephone ?? 'Non renseigné' }}</span>
                                </td>
                                <td>
                                    @if(isset($u->actif) && !$u->actif)
                                        <span class="badge bg-danger rounded-pill px-2 py-1">Bloqué</span>
                                    @else
                                        <span class="badge bg-success rounded-pill px-2 py-1">Actif</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="small text-muted">{{ $u->created_at->format('d/m/Y') }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        {{-- Voir profil détaillé --}}
                                        <a href="{{ route('admin.users.show', $u) }}" class="btn btn-sm btn-light rounded-circle" title="Voir profil">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        {{-- Éditer --}}
                                        <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-light rounded-circle" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        {{-- Bloquer / Débloquer --}}
                                        @if($u->id !== auth()->id())
                                            @if(isset($u->actif) && !$u->actif)
                                                <form method="POST" action="{{ route('admin.users.unblock', $u) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-success rounded-circle" title="Débloquer l'accès">
                                                        <i class="bi bi-unlock"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.users.block', $u) }}" class="d-inline" onsubmit="return confirm('Bloquer cet utilisateur empêchera sa connexion. Confirmer ?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-warning rounded-circle" title="Bloquer l'accès">
                                                        <i class="bi bi-lock"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- Supprimer --}}
                                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="d-inline" onsubmit="return confirm('Attention : Êtes-vous sûr de vouloir supprimer définitivement cet utilisateur ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-4 text-center text-muted">
                                    Aucun utilisateur ne correspond à ces critères.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top">
                    {{ $users->withQueryString()->links() }}
                </div>
            </div>

        </div>
    </main>
</div>
@endsection
