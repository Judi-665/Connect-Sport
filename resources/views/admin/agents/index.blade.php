{{-- resources/views/admin/agents/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des Agents — Administration')

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
                    <h1 class="fw-bold mb-1" style="font-size:24px;color:var(--text);">Gestion des Agents</h1>
                    <p class="text-muted small mb-0">Vérification, accréditations, blocage et commissions</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if($enAttente > 0)
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                            <i class="bi bi-hourglass-split me-1"></i>{{ $enAttente }} en attente de vérification
                        </span>
                    @endif
                    <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3 py-2">
                        Commissions estimées : {{ number_format($commissionsGlobales ?? 0, 0, ',', ' ') }} FCFA
                    </span>
                </div>
            </div>

            @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
            @endif

            {{-- Filtres --}}
            <div class="cs-card p-3 mb-4">
                <form method="GET" action="{{ route('admin.agents.index') }}" class="row g-2 align-items-end">
                    <div class="col-md-3 col-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Statut</label>
                        <select name="statut" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                            <option value="">Tous les agents</option>
                            <option value="en_attente" @selected(request('statut') === 'en_attente')>En attente ({{ $enAttente }})</option>
                            <option value="verifie" @selected(request('statut') === 'verifie')>Vérifiés</option>
                            <option value="bloque" @selected(request('statut') === 'bloque')>Bloqués</option>
                        </select>
                    </div>

                    <div class="col-md-6 col-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Recherche</label>
                        <input type="text" name="search" class="form-control form-control-sm rounded-3"
                               placeholder="Agence, N° accréditation, nom agent..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-warning btn-sm rounded-3 fw-semibold w-100">Filtrer</button>
                        <a href="{{ route('admin.agents.index') }}" class="btn btn-light btn-sm rounded-3 border">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table des Agents --}}
            <div class="cs-card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Agent & Contact</th>
                                <th>Agence</th>
                                <th>N° accréditation</th>
                                <th>Statut Vérification</th>
                                <th>Accès Compte</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($agents as $agent)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                             style="width:36px;height:36px;background:#8b5cf6;font-size:12px;">
                                            {{ strtoupper(substr($agent->nomComplet(), 0, 2)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.agents.show', $agent) }}" class="fw-semibold text-decoration-none" style="color:var(--text);">
                                                {{ $agent->nomComplet() }}
                                            </a>
                                            <div class="text-muted small">{{ $agent->user->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $agent->agence ?? 'Indépendant' }}</td>
                                <td><code>{{ $agent->numero_accreditation ?? 'Non renseigné' }}</code></td>
                                <td>
                                    @if($agent->verifie)
                                        <span class="badge bg-success rounded-pill px-2 py-1">Vérifié</span>
                                    @else
                                        <span class="badge bg-warning text-dark rounded-pill px-2 py-1">En attente</span>
                                    @endif
                                </td>
                                <td>
                                    @if($agent->actif)
                                        <span class="badge bg-light text-success border border-success-subtle rounded-pill">Autorisé</span>
                                    @else
                                        <span class="badge bg-light text-danger border border-danger-subtle rounded-pill">Bloqué</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <a href="{{ route('admin.agents.show', $agent) }}" class="btn btn-sm btn-light rounded-circle" title="Voir la fiche">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        {{-- Vérifier / Rejeter --}}
                                        @if(!$agent->verifie)
                                            <form method="POST" action="{{ route('admin.agents.verifier', $agent) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-success rounded-circle" title="Vérifier et approuver l'agent">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.agents.rejeter', $agent) }}" class="d-inline" onsubmit="return confirm('Retirer la vérification de cet agent ?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-secondary rounded-circle" title="Annuler la vérification">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Bloquer / Débloquer indépendant --}}
                                        <form method="POST" action="{{ route('admin.agents.toggle', $agent) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-{{ $agent->actif ? 'warning' : 'success' }} rounded-circle"
                                                    title="{{ $agent->actif ? 'Bloquer l\'agent' : 'Débloquer l\'agent' }}">
                                                <i class="bi {{ $agent->actif ? 'bi-lock' : 'bi-unlock' }}"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="p-4 text-center text-muted">Aucun agent trouvé.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top">
                    {{ $agents->withQueryString()->links() }}
                </div>
            </div>

        </div>
    </main>
</div>
@endsection