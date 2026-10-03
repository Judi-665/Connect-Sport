{{-- resources/views/admin/agents/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détails Agent — ' . ($agent->agence ?? $agent->nomComplet()))

@push('styles')
<style>
.cs-dash { display: flex; min-height: 100vh; background: var(--bg2); padding-top: 64px; }
.cs-dash__main { flex: 1; margin-left: 240px; display: flex; flex-direction: column; min-width: 0; }
.cs-dash__content { flex: 1; padding: 24px; }
.cs-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; margin-bottom: 24px; }
.cs-card__header { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
.cs-card__body { padding: 20px; }
.cs-card__body--flush { padding: 0; }
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
                    <li class="breadcrumb-item"><a href="{{ route('admin.agents.index') }}" class="text-decoration-none">Agents</a></li>
                    <li class="breadcrumb-item active">{{ $agent->nomComplet() }}</li>
                </ol>
            </nav>

            @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
            @endif

            {{-- Fiche en-tête Agent --}}
            <div class="cs-card">
                <div class="cs-card__header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white fw-bold"
                             style="width:52px;height:52px;background:#8b5cf6;font-size:18px;">
                            {{ strtoupper(substr($agent->nomComplet(), 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0" style="color:var(--text);">{{ $agent->nomComplet() }}</h3>
                            <span class="text-muted small">Agence : <strong>{{ $agent->agence ?? 'Indépendant' }}</strong> · Inscrit le {{ $agent->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if($agent->verifie)
                            <span class="badge bg-success rounded-pill px-3 py-2">
                                <i class="bi bi-patch-check-fill me-1"></i>Vérifié
                            </span>
                            <form method="POST" action="{{ route('admin.agents.rejeter', $agent) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                    Retirer vérification
                                </button>
                            </form>
                        @else
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                <i class="bi bi-hourglass-split me-1"></i>En attente de vérification
                            </span>
                            <form method="POST" action="{{ route('admin.agents.verifier', $agent) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold">
                                    Approuver & Vérifier
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.agents.toggle', $agent) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-outline-{{ $agent->actif ? 'danger' : 'success' }} rounded-pill px-3">
                                {{ $agent->actif ? 'Bloquer' : 'Débloquer' }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="cs-card__body">
                    <div class="row g-4">
                        <div class="col-md-3 col-6">
                            <span class="text-muted small text-uppercase d-block">N° Accréditation</span>
                            <code>{{ $agent->numero_accreditation ?? 'Non renseigné' }}</code>
                        </div>
                        <div class="col-md-3 col-6">
                            <span class="text-muted small text-uppercase d-block">Contact Email & Tel</span>
                            <strong>{{ $agent->user->email ?? '-' }}</strong><br>
                            <span class="small text-muted">{{ $agent->telephone ?? $agent->user->telephone ?? 'Non renseigné' }}</span>
                        </div>
                        <div class="col-md-3 col-6">
                            <span class="text-muted small text-uppercase d-block">Localisation</span>
                            <strong>{{ $agent->ville ?? '-' }}, {{ $agent->pays ?? 'Bénin' }}</strong>
                        </div>
                        <div class="col-md-3 col-6">
                            <span class="text-muted small text-uppercase d-block">Commissions estimées</span>
                            <strong class="text-success">{{ number_format($commissionsAgent ?? 0, 0, ',', ' ') }} FCFA</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Joueurs sous contrat de représentation & Transferts --}}
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="cs-card">
                        <div class="cs-card__header">
                            <h6 class="fw-bold mb-0"><i class="bi bi-people me-2 text-purple"></i>Joueurs Représentés ({{ $agent->joueurs?->count() ?? 0 }})</h6>
                        </div>
                        <div class="cs-card__body cs-card__body--flush">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle small">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">Joueur</th>
                                            <th>Club</th>
                                            <th class="text-end pe-3">Fiche</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($agent->joueurs ?? [] as $j)
                                        <tr>
                                            <td class="ps-3 fw-semibold">{{ $j->nomComplet() }}</td>
                                            <td>{{ $j->club?->nom ?? 'Sans club' }}</td>
                                            <td class="text-end pe-3">
                                                <a href="{{ route('admin.joueurs.show', $j) }}" class="btn btn-xs btn-light rounded-circle">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="3" class="p-3 text-center text-muted">Aucun joueur actuellement sous contrat avec cet agent.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="cs-card">
                        <div class="cs-card__header">
                            <h6 class="fw-bold mb-0"><i class="bi bi-arrow-left-right me-2 text-danger"></i>Transferts gérés ({{ $agent->transferts?->count() ?? 0 }})</h6>
                        </div>
                        <div class="cs-card__body cs-card__body--flush">
                            @forelse($agent->transferts ?? [] as $t)
                            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="fw-semibold small" style="color:var(--text);">{{ $t->joueur?->nomComplet() ?? 'Joueur' }}</div>
                                    <div class="text-muted small">Montant : {{ number_format($t->montant ?? 0, 0, ',', ' ') }} {{ $t->devise ?? 'FCFA' }}</div>
                                </div>
                                <span class="badge {{ $t->statut === 'accepte' ? 'bg-success' : 'bg-warning' }} rounded-pill">
                                    {{ ucfirst($t->statut) }}
                                </span>
                            </div>
                            @empty
                            <div class="p-3 text-center text-muted small">Aucun transfert négocié par cet agent.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>
@endsection