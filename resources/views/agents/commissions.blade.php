@extends('layouts.agent')

@section('title', 'Mes commissions')
@section('page-title', 'Commissions')

@section('sidebar-nav')
    @include('agents.partials.nav')
@endsection

@section('content')
    <div class="mb-4">
        <div class="text-uppercase small fw-bold" style="color:var(--cs-orange);">Espace agent</div>
        <h1 class="h3 mb-1">Mes commissions</h1>
        <p class="text-secondary mb-0">Calculées sur les transferts finalisés, selon le taux fixé dans chaque mandat.</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="cs-kpi h-100">
                <div class="cs-kpi-icon" style="background:#D1FAE5;color:#065F46;"><i class="bi bi-cash-coin"></i></div>
                <div class="cs-kpi-val">{{ number_format($totalCommissions, 0, ',', ' ') }}</div>
                <div class="cs-kpi-lbl">Total commissions (toutes devises confondues)</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="cs-kpi h-100">
                <div class="cs-kpi-icon" style="background:#FEF3C7;color:#92400E;"><i class="bi bi-arrow-left-right"></i></div>
                <div class="cs-kpi-val">{{ $transferts->count() }}</div>
                <div class="cs-kpi-lbl">Transferts finalisés</div>
            </div>
        </div>
    </div>

    <div class="cs-card">
        <div class="cs-card-header">
            <div class="cs-card-title"><i class="bi bi-cash-coin"></i>Détail par transfert</div>
        </div>
        <div class="table-responsive">
            <table class="cs-table">
                <thead>
                    <tr>
                        <th>Joueur</th>
                        <th>Transfert</th>
                        <th>Montant</th>
                        <th>Taux</th>
                        <th>Commission</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transferts as $t)
                    <tr>
                        <td>{{ $t->joueur?->nomComplet() ?? '—' }}</td>
                        <td>{{ $t->clubSource?->nom }} → {{ $t->clubDestinataire?->nom }}</td>
                        <td>{{ $t->montantAffiche() }}</td>
                        <td>{{ $t->commission_taux }} %</td>
                        <td class="fw-semibold">{{ number_format($t->commission_montant, 0, ',', ' ') }} {{ $t->devise }}</td>
                        <td>{{ $t->finalise_at?->format('d/m/Y') ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6"><div class="cs-empty"><i class="bi bi-cash-coin"></i><p>Aucune commission pour le moment.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection