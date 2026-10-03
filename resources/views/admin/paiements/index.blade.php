{{-- resources/views/admin/paiements/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Abonnements & Transactions FedaPay — Administration')

@push('styles')
<style>
.cs-dash { display: flex; min-height: 100vh; background: var(--bg2); padding-top: 64px; }
.cs-dash__main { flex: 1; margin-left: 240px; display: flex; flex-direction: column; min-width: 0; }
.cs-dash__content { flex: 1; padding: 24px; }
.cs-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; margin-bottom: 20px; }
.cs-stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
.cs-stat-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 14px; padding: 18px 20px; }
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
                    <h1 class="fw-bold mb-1" style="font-size:24px;color:var(--text);">Abonnements & Transactions FedaPay</h1>
                    <p class="text-muted small mb-0">Historique financier, plans d'abonnements des clubs, joueurs et agents</p>
                </div>
            </div>

            {{-- 4 Mini stats de paiements --}}
            <div class="cs-stats-grid">
                <div class="cs-stat-card">
                    <span class="text-muted small text-uppercase d-block mb-1">Total Encaissé</span>
                    <h3 class="fw-bold mb-0 text-success">{{ number_format($sommairePaiements['total_encaissé'], 0, ',', ' ') }} <small style="font-size:13px;">FCFA</small></h3>
                </div>
                <div class="cs-stat-card">
                    <span class="text-muted small text-uppercase d-block mb-1">Abonnements Actifs</span>
                    <h3 class="fw-bold mb-0 text-primary">{{ $sommairePaiements['nb_actifs'] }}</h3>
                </div>
                <div class="cs-stat-card">
                    <span class="text-muted small text-uppercase d-block mb-1">En Attente FedaPay</span>
                    <h3 class="fw-bold mb-0 text-warning">{{ $sommairePaiements['nb_en_attente'] }}</h3>
                </div>
                <div class="cs-stat-card">
                    <span class="text-muted small text-uppercase d-block mb-1">Expirés</span>
                    <h3 class="fw-bold mb-0 text-secondary">{{ $sommairePaiements['nb_expires'] }}</h3>
                </div>
            </div>

            {{-- Filtres --}}
            <div class="cs-card p-3 mb-4">
                <form method="GET" action="{{ route('admin.paiements') }}" class="row g-2 align-items-end">
                    <div class="col-md-3 col-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Type d'acteur</label>
                        <select name="type_acteur" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                            <option value="">Tous les acteurs</option>
                            <option value="club" @selected(request('type_acteur') === 'club')>Clubs</option>
                            <option value="joueur" @selected(request('type_acteur') === 'joueur')>Joueurs</option>
                            <option value="agent" @selected(request('type_acteur') === 'agent')>Agents</option>
                        </select>
                    </div>

                    <div class="col-md-3 col-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Statut</label>
                        <select name="statut" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                            <option value="">Tous les statuts</option>
                            <option value="actif" @selected(request('statut') === 'actif')>Actif</option>
                            <option value="en_attente" @selected(request('statut') === 'en_attente')>En attente</option>
                            <option value="expire" @selected(request('statut') === 'expire')>Expiré</option>
                            <option value="annule" @selected(request('statut') === 'annule')>Annulé</option>
                        </select>
                    </div>

                    <div class="col-md-3 col-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Plan</label>
                        <select name="plan" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                            <option value="">Tous les plans</option>
                            <option value="standard" @selected(request('plan') === 'standard')>Standard</option>
                            <option value="premium" @selected(request('plan') === 'premium')>Premium</option>
                        </select>
                    </div>

                    <div class="col-md-3 col-6 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm rounded-3 fw-semibold w-100" style="background:#1A56A0;border-color:#1A56A0;">Filtrer</button>
                        <a href="{{ route('admin.paiements') }}" class="btn btn-light btn-sm rounded-3 border">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table Abonnements --}}
            <div class="cs-card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Souscripteur (Acteur)</th>
                                <th>Plan & Montant</th>
                                <th>Réf / ID FedaPay</th>
                                <th>Période de Validité</th>
                                <th>Statut Transaction</th>
                                <th>Souscrit le</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($abonnements as $ab)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-semibold" style="color:var(--text);">
                                        @if($ab->club)
                                            <i class="bi bi-shield text-warning me-1"></i> {{ $ab->club->nom }} (Club)
                                        @elseif($ab->joueur)
                                            <i class="bi bi-dribbble text-success me-1"></i> {{ $ab->joueur->nomComplet() }} (Joueur)
                                        @elseif($ab->agent)
                                            <i class="bi bi-briefcase text-purple me-1"></i> {{ $ab->agent->nomComplet() }} (Agent)
                                        @else
                                            Acteur #{{ $ab->id }}
                                        @endif
                                    </div>
                                    <span class="small text-muted">{{ $ab->mode_paiement ?? 'FedaPay Mobile Money / Carte' }}</span>
                                </td>
                                <td>
                                    <strong>{{ ucfirst($ab->plan ?? 'Standard') }}</strong><br>
                                    <span class="text-success fw-bold small">{{ number_format($ab->montant, 0, ',', ' ') }} {{ $ab->devise ?? 'FCFA' }}</span>
                                </td>
                                <td>
                                    <code>{{ $ab->fedapay_transaction_id ?? $ab->fedapay_reference ?? 'N/A' }}</code>
                                </td>
                                <td>
                                    <span class="small">
                                        Du {{ $ab->debut_at ? $ab->debut_at->format('d/m/Y') : '-' }}<br>
                                        Au {{ $ab->fin_at ? $ab->fin_at->format('d/m/Y') : 'Illimité' }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($ab->statut) {
                                            'actif'      => 'bg-success',
                                            'en_attente' => 'bg-warning text-dark',
                                            'expire'     => 'bg-secondary',
                                            'annule'     => 'bg-danger',
                                            default      => 'bg-light text-dark',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} rounded-pill px-2 py-1">
                                        {{ ucfirst($ab->statut) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="small text-muted">{{ $ab->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="p-4 text-center text-muted">Aucune transaction ou abonnement enregistré.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top">
                    {{ $abonnements->withQueryString()->links() }}
                </div>
            </div>

        </div>
    </main>
</div>
@endsection
