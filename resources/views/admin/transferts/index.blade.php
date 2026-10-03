{{-- resources/views/admin/transferts/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Supervision des Transferts — Administration')

@push('styles')
<style>
.cs-dash { display: flex; min-height: 100vh; background: var(--bg2); padding-top: 64px; }
.cs-dash__main { flex: 1; margin-left: 240px; display: flex; flex-direction: column; min-width: 0; }
.cs-dash__content { flex: 1; padding: 24px; }
.cs-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.cs-tag { display: inline-flex; align-items: center; font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 20px; }
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
                    <h1 class="fw-bold mb-1" style="font-size:24px;color:var(--text);">Supervision des Transferts</h1>
                    <p class="text-muted small mb-0">Suivi des négociations, arbitrage de litiges et validation forcée</p>
                </div>
            </div>

            @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
            @endif

            {{-- Filtres --}}
            <div class="cs-card p-3 mb-4">
                <form method="GET" action="{{ route('admin.transferts') }}" class="row g-2 align-items-end">
                    <div class="col-md-4 col-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Statut</label>
                        <select name="statut" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                            <option value="">Tous les statuts</option>
                            <option value="en_attente" @selected(request('statut') === 'en_attente')>En attente</option>
                            <option value="en_negociation" @selected(request('statut') === 'en_negociation')>En négociation</option>
                            <option value="accepte" @selected(request('statut') === 'accepte')>Accepté / Finalisé</option>
                            <option value="refuse" @selected(request('statut') === 'refuse')>Refusé / Annulé</option>
                        </select>
                    </div>

                    <div class="col-md-4 col-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Type de transfert</label>
                        <select name="type" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                            <option value="">Tous les types</option>
                            <option value="definitif" @selected(request('type') === 'definitif')>Définitif</option>
                            <option value="pret" @selected(request('type') === 'pret')>Prêt</option>
                            <option value="libre" @selected(request('type') === 'libre')>Joueur libre</option>
                        </select>
                    </div>

                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-danger btn-sm rounded-3 fw-semibold w-100" style="background:#ef4444;border-color:#ef4444;">
                            Filtrer
                        </button>
                        <a href="{{ route('admin.transferts') }}" class="btn btn-light btn-sm rounded-3 border">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table Transferts --}}
            <div class="cs-card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Joueur</th>
                                <th>Club Départ</th>
                                <th>Club Arrivée</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th class="text-end pe-4">Actions & Arbitrage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transferts as $t)
                            <tr>
                                <td class="ps-4 fw-semibold" style="color:var(--text);">
                                    <a href="{{ route('admin.transferts.show', $t) }}" class="text-decoration-none" style="color:var(--text);">
                                        {{ $t->joueur?->nomComplet() ?? 'Joueur' }}
                                    </a>
                                </td>
                                <td>{{ $t->clubSource?->nom ?? 'Libre (Sans club)' }}</td>
                                <td>{{ $t->clubDestinataire?->nom ?? 'Non défini' }}</td>
                                <td>
                                    <strong>{{ $t->montant ? number_format($t->montant, 0, ',', ' ') . ' ' . ($t->devise ?? 'FCFA') : 'Gratuit / Non communiqué' }}</strong>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($t->statut) {
                                            'accepte'        => 'bg-success',
                                            'en_attente'     => 'bg-warning text-dark',
                                            'en_negociation' => 'bg-info text-dark',
                                            'refuse'         => 'bg-danger',
                                            default          => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} rounded-pill px-2 py-1">
                                        {{ ucfirst(str_replace('_', ' ', $t->statut)) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="small text-muted">{{ $t->created_at->format('d/m/Y') }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <a href="{{ route('admin.transferts.show', $t) }}" class="btn btn-sm btn-light rounded-circle" title="Détails & Arbitrage">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if($t->statut !== 'accepte')
                                        {{-- Valider de force --}}
                                        <form method="POST" action="{{ route('admin.transferts.valider', $t) }}" class="d-inline" onsubmit="return confirm('Valider de force ce transfert et mettre à jour le club du joueur ?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-success rounded-circle" title="Valider de force le transfert">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>
                                        @endif

                                        @if($t->statut !== 'refuse')
                                        {{-- Rejeter de force --}}
                                        <form method="POST" action="{{ route('admin.transferts.rejeter', $t) }}" class="d-inline" onsubmit="return confirm('Rejeter / Annuler de force ce transfert en cas de litige ?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" title="Rejeter / Annuler de force">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="p-4 text-center text-muted">Aucun transfert trouvé.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top">
                    {{ $transferts->withQueryString()->links() }}
                </div>
            </div>

        </div>
    </main>
</div>
@endsection
