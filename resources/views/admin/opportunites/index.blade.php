{{-- resources/views/admin/opportunites/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des Opportunités — Administration')

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
                    <h1 class="fw-bold mb-1" style="font-size:24px;color:var(--text);">Gestion des Opportunités</h1>
                    <p class="text-muted small mb-0">Détection, mise en avant et modération des annonces de recrutement</p>
                </div>
            </div>

            @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
            @endif

            {{-- Filtres --}}
            <div class="cs-card p-3 mb-4">
                <form method="GET" action="{{ route('admin.opportunites') }}" class="row g-2 align-items-end">
                    <div class="col-md-3 col-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Type</label>
                        <select name="type" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                            <option value="">Tous les types</option>
                            <option value="recrutement" @selected(request('type') === 'recrutement')>Recrutement</option>
                            <option value="essai" @selected(request('type') === 'essai')>Essai / Détection</option>
                            <option value="stage" @selected(request('type') === 'stage')>Stage</option>
                        </select>
                    </div>

                    <div class="col-md-3 col-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Conformité (Active)</label>
                        <select name="active" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                            <option value="">Tous statuts</option>
                            <option value="1" @selected(request('active') === '1')>Active / Conforme</option>
                            <option value="0" @selected(request('active') === '0')>Désactivée (Non conforme)</option>
                        </select>
                    </div>

                    <div class="col-md-3 col-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Mise en avant</label>
                        <select name="mise_en_avant" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                            <option value="">Toutes</option>
                            <option value="1" @selected(request('mise_en_avant') === '1')>En vedette (Mise en avant)</option>
                            <option value="0" @selected(request('mise_en_avant') === '0')>Standard</option>
                        </select>
                    </div>

                    <div class="col-md-3 col-6 d-flex gap-2">
                        <button type="submit" class="btn btn-warning btn-sm rounded-3 fw-semibold w-100">Filtrer</button>
                        <a href="{{ route('admin.opportunites') }}" class="btn btn-light btn-sm rounded-3 border">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table Opportunités --}}
            <div class="cs-card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Titre de l'opportunité</th>
                                <th>Club Émetteur</th>
                                <th>Type & Catégorie</th>
                                <th>Date Limite</th>
                                <th>Mise en avant</th>
                                <th>Statut Conforme</th>
                                <th class="text-end pe-4">Modération</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($opportunites as $opp)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-semibold" style="color:var(--text);">{{ $opp->titre }}</div>
                                    <span class="small text-muted">{{ Str::limit($opp->description, 60) }}</span>
                                </td>
                                <td>{{ $opp->club?->nom ?? 'Club' }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ ucfirst($opp->type) }}</span>
                                    <span class="small text-muted ms-1">{{ $opp->categorie_cible ?? 'Tous' }}</span>
                                </td>
                                <td>
                                    <span class="small text-muted">{{ $opp->date_limite ? $opp->date_limite->format('d/m/Y') : 'Ouverte' }}</span>
                                </td>
                                <td>
                                    @if($opp->mise_en_avant)
                                        <span class="badge bg-warning text-dark rounded-pill">En Vedette</span>
                                    @else
                                        <span class="badge bg-light text-muted border rounded-pill">Standard</span>
                                    @endif
                                </td>
                                <td>
                                    @if($opp->active)
                                        <span class="badge bg-success rounded-pill">Active</span>
                                    @else
                                        <span class="badge bg-danger rounded-pill">Désactivée</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        {{-- Toggle Mise en avant --}}
                                        <form method="POST" action="{{ route('admin.opportunites.feature', $opp) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-{{ $opp->mise_en_avant ? 'secondary' : 'warning' }} rounded-circle"
                                                    title="{{ $opp->mise_en_avant ? 'Retirer la mise en avant' : 'Mettre en avant' }}">
                                                <i class="bi {{ $opp->mise_en_avant ? 'bi-star-fill text-warning' : 'bi-star' }}"></i>
                                            </button>
                                        </form>

                                        {{-- Toggle Active / Non conforme --}}
                                        <form method="POST" action="{{ route('admin.opportunites.toggle', $opp) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-{{ $opp->active ? 'danger' : 'success' }} rounded-circle"
                                                    title="{{ $opp->active ? 'Désactiver (Non conforme)' : 'Réactiver' }}">
                                                <i class="bi {{ $opp->active ? 'bi-slash-circle' : 'bi-check-circle' }}"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="p-4 text-center text-muted">Aucune opportunité enregistrée.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top">
                    {{ $opportunites->withQueryString()->links() }}
                </div>
            </div>

        </div>
    </main>
</div>
@endsection
