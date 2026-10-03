{{-- resources/views/admin/joueurs/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des Joueurs — Administration')

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

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <h1 class="fw-bold mb-1" style="font-size:24px;color:var(--text);">Gestion des Joueurs</h1>
                    <p class="text-muted small mb-0">Recherche, filtres sans-club, visibilité recruteur et modération</p>
                </div>
            </div>

            @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
            @endif

            {{-- Filtres & Recherche avancée --}}
            <div class="cs-card p-3 mb-4">
                <form method="GET" action="{{ route('admin.joueurs') }}" class="row g-2 align-items-end">
                    <div class="col-md-3 col-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Sans Club</label>
                        <select name="sans_club" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                            <option value="">Tous les joueurs</option>
                            <option value="1" @selected(request('sans_club') === '1')>Joueurs Libres (Sans Club)</option>
                            <option value="0" @selected(request('sans_club') === '0')>En Club</option>
                        </select>
                    </div>

                    <div class="col-md-3 col-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Visibilité Recruteur</label>
                        <select name="visible_recruteur" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                            <option value="">Toutes visibilités</option>
                            <option value="1" @selected(request('visible_recruteur') === '1')>Visible par les recruteurs</option>
                            <option value="0" @selected(request('visible_recruteur') === '0')>Masqué / Non visible</option>
                        </select>
                    </div>

                    <div class="col-md-4 col-8">
                        <label class="form-label small fw-semibold text-muted mb-1">Recherche</label>
                        <input type="text" name="search" class="form-control form-control-sm rounded-3"
                               placeholder="Nom, prénom, email joueur..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2 col-4 d-flex gap-2">
                        <button type="submit" class="btn btn-success btn-sm rounded-3 fw-semibold w-100" style="background:#10b981;border-color:#10b981;">
                            Filtrer
                        </button>
                        <a href="{{ route('admin.joueurs') }}" class="btn btn-light btn-sm rounded-3 border">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table Joueurs --}}
            <div class="cs-card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Joueur</th>
                                <th>Club Actuel</th>
                                <th>Poste / Catégorie</th>
                                <th>Visibilité Recruteur</th>
                                <th>Statut Compte</th>
                                <th class="text-end pe-4">Modération & Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($joueurs as $joueur)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                             style="width:36px;height:36px;background:#10b981;font-size:12px;">
                                            {{ strtoupper(substr($joueur->nomComplet(), 0, 2)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.joueurs.show', $joueur) }}" class="fw-semibold text-decoration-none" style="color:var(--text);">
                                                {{ $joueur->nomComplet() }}
                                            </a>
                                            <div class="text-muted small">{{ $joueur->user->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($joueur->club)
                                        <span class="badge bg-light text-dark border">{{ $joueur->club->nom }}</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Sans club (Libre)</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="small">{{ $joueur->poste ?? '-' }} · {{ $joueur->categorie ?? '-' }}</span>
                                </td>
                                <td>
                                    @if($joueur->visible_recruteur)
                                        <span class="badge bg-success rounded-pill px-2 py-1">Visible</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill px-2 py-1">Masqué</span>
                                    @endif
                                </td>
                                <td>
                                    @if($joueur->actif)
                                        <span class="badge bg-light text-success border border-success-subtle rounded-pill">Actif</span>
                                    @else
                                        <span class="badge bg-light text-danger border border-danger-subtle rounded-pill">Désactivé / Signalé</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <a href="{{ route('admin.joueurs.show', $joueur) }}" class="btn btn-sm btn-light rounded-circle" title="Voir profil complet">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        {{-- Basculer visibilité recruteur --}}
                                        <form method="POST" action="{{ route('admin.joueurs.visibilite', $joueur) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-{{ $joueur->visible_recruteur ? 'secondary' : 'info' }} rounded-circle"
                                                    title="{{ $joueur->visible_recruteur ? 'Retirer la visibilité recruteur' : 'Rendre visible aux recruteurs' }}">
                                                <i class="bi {{ $joueur->visible_recruteur ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                                            </button>
                                        </form>

                                        {{-- Désactiver profil signalé / Activer --}}
                                        <form method="POST" action="{{ route('admin.joueurs.toggle', $joueur) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-{{ $joueur->actif ? 'danger' : 'success' }} rounded-circle"
                                                    title="{{ $joueur->actif ? 'Désactiver le profil joueur' : 'Réactiver le profil' }}">
                                                <i class="bi {{ $joueur->actif ? 'bi-slash-circle' : 'bi-check-circle' }}"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="p-4 text-center text-muted">Aucun joueur trouvé.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top">
                    {{ $joueurs->withQueryString()->links() }}
                </div>
            </div>

        </div>
    </main>
</div>
@endsection
