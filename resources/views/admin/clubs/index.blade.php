{{-- resources/views/admin/clubs/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des Clubs — Administration')

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
                    <h1 class="fw-bold mb-1" style="font-size:24px;color:var(--text);">Gestion des Clubs</h1>
                    <p class="text-muted small mb-0">Supervision de tous les clubs enregistrés sur Connect Sport</p>
                </div>
            </div>

            @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
            @endif

            {{-- Filtres & Recherche --}}
            <div class="cs-card p-3 mb-4">
                <form method="GET" action="{{ route('admin.clubs') }}" class="row g-2 align-items-end">
                    <div class="col-md-3 col-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Statut</label>
                        <select name="statut" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                            <option value="">Tous les statuts</option>
                            <option value="actif" @selected(request('statut') === 'actif')>Actif</option>
                            <option value="inactif" @selected(request('statut') === 'inactif')>Inactif</option>
                        </select>
                    </div>

                    <div class="col-md-6 col-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Recherche</label>
                        <input type="text" name="search" class="form-control form-control-sm rounded-3"
                               placeholder="Nom du club, ville..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-warning btn-sm rounded-3 fw-semibold w-100">Filtrer</button>
                        <a href="{{ route('admin.clubs') }}" class="btn btn-light btn-sm rounded-3 border">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Tableau des Clubs --}}
            <div class="cs-card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Club</th>
                                <th>Localisation</th>
                                <th>Joueurs / Équipes</th>
                                <th>Statut</th>
                                <th>Date création</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clubs as $club)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-2 d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                             style="width:36px;height:36px;background:#F97316;font-size:12px;">
                                            {{ strtoupper(substr($club->nom, 0, 2)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.clubs.show', $club) }}" class="fw-semibold text-decoration-none" style="color:var(--text);">
                                                {{ $club->nom }}
                                            </a>
                                            <div class="text-muted small">{{ $club->user->email ?? 'Compte non relié' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="small">{{ $club->ville ?? '-' }}, {{ $club->pays ?? 'Bénin' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border rounded-pill">
                                        {{ $club->joueurs?->count() ?? 0 }} joueurs
                                    </span>
                                    <span class="badge bg-light text-dark border rounded-pill ms-1">
                                        {{ $club->equipes?->count() ?? 0 }} équipes
                                    </span>
                                </td>
                                <td>
                                    @if($club->actif)
                                        <span class="badge bg-success rounded-pill px-2 py-1">Actif</span>
                                    @else
                                        <span class="badge bg-danger rounded-pill px-2 py-1">Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="small text-muted">{{ $club->created_at->format('d/m/Y') }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <a href="{{ route('admin.clubs.show', $club) }}" class="btn btn-sm btn-light rounded-circle" title="Voir la fiche complète">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <form method="POST" action="{{ route('admin.clubs.toggle', $club) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-{{ $club->actif ? 'warning' : 'success' }} rounded-circle"
                                                    title="{{ $club->actif ? 'Désactiver le club' : 'Activer le club' }}">
                                                <i class="bi {{ $club->actif ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-4 text-center text-muted">
                                    Aucun club trouvé.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top">
                    {{ $clubs->withQueryString()->links() }}
                </div>
            </div>

        </div>
    </main>
</div>
@endsection
