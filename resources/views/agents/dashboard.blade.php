@extends('layouts.agent')

@section('title', 'Dashboard agent')
@section('page-title', 'Tableau de bord agent')

@section('sidebar-nav')
    @include('agents.partials.nav')
@endsection

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="text-uppercase small fw-bold" style="color:var(--cs-orange);">Espace agent</div>
            <h1 class="h3 mb-1">Bonjour {{ $agent->user->prenom ?? $agent->user->name }}</h1>
            <p class="text-secondary mb-0">Suivez vos joueurs, mandats et négociations.</p>
        </div>
        <a href="{{ route('agent.profil') }}" class="btn-cs btn-cs-outline"><i class="bi bi-person-badge"></i>Profil accrédité</a>
    </div>

    @if(!$agent->verifie)
        <div class="alert alert-warning"><i class="bi bi-hourglass-split me-2"></i>Votre profil est en attente de validation. Il sera visible après accréditation.</div>
    @endif

    @if($mandatsExpirantBientot->isNotEmpty())
        <div class="alert alert-danger">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <strong>{{ $mandatsExpirantBientot->count() }} mandat(s) arrivent à échéance sous 30 jours</strong>
            </div>
            <div class="d-flex flex-column gap-1">
                @foreach($mandatsExpirantBientot as $j)
                <div class="d-flex justify-content-between" style="font-size:.86rem;">
                    <span>{{ $j->nomComplet() }}</span>
                    <span class="fw-semibold">{{ \Carbon\Carbon::parse($j->pivot->fin_mandat)->format('d/m/Y') }}</span>
                </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="row g-3 mb-4">
        @foreach([
            ['joueurs', 'Joueurs actifs', 'bi-people', '#DBEAFE', '#1D4ED8'],
            ['mandats', 'Mandats suivis', 'bi-file-earmark-text', '#D1FAE5', '#065F46'],
            ['transferts', 'Transferts', 'bi-arrow-left-right', '#FEF3C7', '#92400E'],
            ['en_cours', 'Négociations en cours', 'bi-chat-square-text', '#E0E7FF', '#3730A3'],
        ] as [$key, $label, $icon, $background, $color])
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="cs-kpi h-100"><div class="cs-kpi-icon" style="background:{{ $background }};color:{{ $color }}"><i class="bi {{ $icon }}"></i></div><div class="cs-kpi-val">{{ $stats[$key] }}</div><div class="cs-kpi-lbl">{{ $label }}</div></div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-7">
            <div class="cs-card">
                <div class="cs-card-header"><div class="cs-card-title"><i class="bi bi-people"></i>Joueurs représentés</div><a href="{{ route('agent.joueurs') }}" class="btn-cs btn-cs-ghost">Voir tout</a></div>
                <div class="table-responsive"><table class="cs-table"><thead><tr><th>Joueur</th><th>Début du mandat</th><th>Fin du mandat</th><th>Commission</th></tr></thead><tbody>
                    @forelse($joueurs as $joueur)
                        <tr>
                            <td>{{ $joueur->nomComplet() }}</td>
                            <td>{{ $joueur->pivot->debut_mandat ? \Carbon\Carbon::parse($joueur->pivot->debut_mandat)->format('d/m/Y') : 'Non défini' }}</td>
                            <td>{{ $joueur->pivot->fin_mandat ? \Carbon\Carbon::parse($joueur->pivot->fin_mandat)->format('d/m/Y') : 'Non défini' }}</td>
                            <td>{{ $joueur->pivot->commission_pourcentage ?? 0 }} %</td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><div class="cs-empty"><i class="bi bi-people"></i><p>Aucun joueur actif représenté.</p></div></td></tr>
                    @endforelse
                </tbody></table></div>
            </div>
        </div>
        <div class="col-12 col-xl-5">
            <div class="cs-card">
                <div class="cs-card-header"><div class="cs-card-title"><i class="bi bi-arrow-left-right"></i>Derniers transferts</div><a href="{{ route('agent.transferts.index') }}" class="btn-cs btn-cs-ghost">Voir tout</a></div>
                @forelse($transferts as $transfert)
                    <div class="px-3 py-3 border-bottom"><div class="d-flex justify-content-between gap-2"><strong>{{ $transfert->joueur?->nomComplet() ?? 'Joueur' }}</strong><span class="cs-status status-{{ $transfert->statut }}">{{ str_replace('_', ' ', ucfirst($transfert->statut)) }}</span></div><small class="text-secondary">{{ $transfert->clubSource?->nom ?? 'Club source' }} → {{ $transfert->clubDestinataire?->nom ?? 'Club destinataire' }}</small></div>
                @empty
                    <div class="cs-empty"><i class="bi bi-arrow-left-right"></i><p>Aucun transfert suivi.</p></div>
                @endforelse
            </div>
        </div>
    </div>
@endsection