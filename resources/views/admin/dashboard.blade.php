{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Administration — Connect Sport')

@push('styles')
<style>
/* Layout général calqué sur le club */
.cs-dash {
    display: flex;
    min-height: 100vh;
    background: var(--bg2);
    padding-top: 64px;
    transition: background .25s ease;
}
.cs-dash__main {
    flex: 1;
    margin-left: 240px;
    display: flex;
    flex-direction: column;
    min-width: 0;
    transition: margin-left .25s ease;
}
body.sidebar-collapsed .cs-dash__main { margin-left: 64px; }

/* Zone contenu */
.cs-dash__content {
    flex: 1;
    padding: 24px;
}

/* Titre de page */
.cs-page-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 4px;
}
.cs-page-subtitle {
    font-size: 13px;
    color: var(--text3);
    margin-bottom: 24px;
}

/* Banner boss / admin */
.cs-admin-banner {
    background: linear-gradient(135deg, #0D2E5C 0%, #1A56A0 100%);
    border-radius: 16px;
    padding: 24px 28px;
    color: #fff;
    margin-bottom: 24px;
    box-shadow: var(--shadow);
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
}
.cs-admin-banner::after {
    content: '';
    position: absolute;
    right: -40px;
    bottom: -40px;
    width: 180px;
    height: 180px;
    background: rgba(255,255,255,0.05);
    border-radius: 50%;
    pointer-events: none;
}

/* Alertes prioritaires */
.cs-alert-card {
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 14px;
    border: 1px solid transparent;
    transition: transform .15s ease;
}
.cs-alert-card:hover {
    transform: translateX(4px);
}
.cs-alert-card--warning {
    background: rgba(245,158,11,.1);
    border-color: rgba(245,158,11,.25);
    color: #d97706;
}
.cs-alert-card--danger {
    background: rgba(239,68,68,.1);
    border-color: rgba(239,68,68,.25);
    color: #ef4444;
}
.cs-alert-card--info {
    background: rgba(26,86,160,.1);
    border-color: rgba(26,86,160,.25);
    color: #1A56A0;
}

/* Grille de stats */
.cs-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}
.cs-stat-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s, background .25s;
}
.cs-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}
.cs-stat-card__body {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px;
}
.cs-stat-card__label {
    display: block;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--text3);
    margin-bottom: 6px;
}
.cs-stat-card__value {
    display: block;
    font-size: 30px;
    font-weight: 800;
    color: var(--text);
    line-height: 1;
}
.cs-stat-card__sub {
    display: block;
    font-size: 12px;
    color: var(--text3);
    margin-top: 5px;
}
.cs-stat-card__icon-wrap {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.cs-stat-card__icon-wrap svg, .cs-stat-card__icon-wrap i {
    font-size: 22px;
}
.cs-stat-card__footer {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 20px;
    font-size: 12px; font-weight: 500;
    text-decoration: none;
    border-top: 1px solid var(--border);
    transition: background .15s;
}
.cs-stat-card__footer:hover { background: var(--bg2); }

/* Thèmes de cartes */
.cs-stat-card--users .cs-stat-card__icon-wrap { background: rgba(26,86,160,.1); color: #1A56A0; }
.cs-stat-card--users .cs-stat-card__footer     { color: #1A56A0; }

.cs-stat-card--clubs .cs-stat-card__icon-wrap { background: rgba(249,115,22,.1); color: #F97316; }
.cs-stat-card--clubs .cs-stat-card__footer     { color: #F97316; }

.cs-stat-card--joueurs .cs-stat-card__icon-wrap { background: rgba(16,185,129,.1); color: #10b981; }
.cs-stat-card--joueurs .cs-stat-card__footer    { color: #10b981; }

.cs-stat-card--agents .cs-stat-card__icon-wrap { background: rgba(139,92,246,.1); color: #8b5cf6; }
.cs-stat-card--agents .cs-stat-card__footer    { color: #8b5cf6; }

.cs-stat-card--transferts .cs-stat-card__icon-wrap { background: rgba(239,68,68,.1); color: #ef4444; }
.cs-stat-card--transferts .cs-stat-card__footer    { color: #ef4444; }

.cs-stat-card--fedapay .cs-stat-card__icon-wrap { background: rgba(13,148,136,.1); color: #0d9488; }
.cs-stat-card--fedapay .cs-stat-card__footer    { color: #0d9488; }

/* Cards génériques & listes */
.cs-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 24px;
    transition: background .25s, border-color .25s;
}
.cs-card__header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
}
.cs-card__title {
    display: flex; align-items: center; gap: 8px;
    font-size: 14px; font-weight: 700; color: var(--text);
    margin: 0;
}
.cs-card__title i { color: #1A56A0; }
.cs-card__action {
    font-size: 12px; font-weight: 600; color: #1A56A0;
    text-decoration: none;
}
.cs-card__body { padding: 20px; }
.cs-card__body--flush { padding: 0; }

/* List items */
.cs-list-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 20px;
    border-bottom: 1px solid var(--border);
    transition: background .15s;
}
.cs-list-row:last-child { border-bottom: none; }
.cs-list-row:hover { background: var(--bg2); }

.cs-tag {
    display: inline-flex; align-items: center;
    font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 20px;
}
.cs-tag--warning { background: rgba(245,158,11,.12); color: #d97706; }
.cs-tag--success { background: rgba(16,185,129,.12); color: #10b981; }
.cs-tag--danger  { background: rgba(239,68,68,.12);  color: #ef4444; }
.cs-tag--primary { background: rgba(26,86,160,.12);  color: #1A56A0; }

/* Side card */
.cs-side-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 20px;
    margin-bottom: 20px;
}
.cs-side-card__title {
    font-size: 13px; font-weight: 700; color: var(--text);
    margin-bottom: 16px;
    display: flex; align-items: center; justify-content: space-between;
}
.cs-side-card__title i { color: #1A56A0; }

/* Responsive */
@media (max-width: 768px) {
    .cs-dash__main { margin-left: 0; }
    .cs-dash__content { padding: 16px; }
}
</style>
@endpush

@section('content')

<div class="cs-dash">

    {{-- Sidebar Admin dédiée --}}
    @include('admin.partials._sidebar')

    <main class="cs-dash__main">
        <div class="cs-dash__content">

            {{-- Bannière Titre Super Admin --}}
            <div class="cs-admin-banner">
                <div>
                    <span class="badge rounded-pill px-3 py-1 mb-2"
                          style="background:rgba(255,255,255,0.2);color:#fff;font-size:11px;letter-spacing:1px;">
                        <i class="bi bi-shield-lock-fill me-1"></i> ESPACE SUPERVISEUR
                    </span>
                    <h1 class="fw-black mb-1" style="font-family:'Bebas Neue',sans-serif;font-size:32px;letter-spacing:1px;color:#fff;">
                        Vue d'ensemble de la Plateforme
                    </h1>
                    <p class="mb-0 text-white-50" style="font-size:13px;">
                        Supervision globale, gestion des acteurs, suivi des abonnements et des flux financiers — {{ now()->translatedFormat('l d F Y') }}
                    </p>
                </div>
                <div class="d-none d-md-flex align-items-center gap-3">
                    <div class="text-end">
                        <div class="fw-bold" style="font-size:20px;color:#fff;">{{ number_format($transactionsFedaPay['total_7j'], 0, ',', ' ') }} FCFA</div>
                        <div class="text-white-50" style="font-size:11px;">Encaissé sur 7 jours</div>
                    </div>
                </div>
            </div>

            {{-- ═══ ALERTES VISUELLES ═══ --}}
            @if($agentsEnAttente > 0 || $licencesExpirantBientotCount > 0 || $abonnementsExpirantBientotCount > 0)
            <div class="mb-4">
                <h6 class="fw-bold text-uppercase mb-3" style="font-size:11px;letter-spacing:1.5px;color:var(--text3);">
                    Alertes & Actions Requises
                </h6>

                <div class="row g-3">
                    {{-- Alerte Agents en attente --}}
                    @if($agentsEnAttente > 0)
                    <div class="col-md-4">
                        <div class="cs-alert-card cs-alert-card--warning">
                            <i class="bi bi-exclamation-triangle-fill flex-shrink-0" style="font-size:22px;"></i>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-bold" style="font-size:13px;">{{ $agentsEnAttente }} Agent(s) en attente</div>
                                <div style="font-size:11px;color:var(--text2);">Vérification de dossier requise</div>
                            </div>
                            <a href="{{ route('admin.agents.index', ['statut' => 'en_attente']) }}"
                               class="btn btn-sm btn-warning rounded-pill px-3 fw-semibold text-nowrap" style="font-size:11px;">
                                Examiner
                            </a>
                        </div>
                    </div>
                    @endif

                    {{-- Alerte Licences expirant bientôt --}}
                    @if($licencesExpirantBientotCount > 0)
                    <div class="col-md-4">
                        <div class="cs-alert-card cs-alert-card--danger">
                            <i class="bi bi-clock-history flex-shrink-0" style="font-size:22px;"></i>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-bold" style="font-size:13px;">{{ $licencesExpirantBientotCount }} Licence(s) expirant bientôt</div>
                                <div style="font-size:11px;color:var(--text2);">Échéance à moins de 30 jours</div>
                            </div>
                            <span class="badge bg-danger rounded-pill px-2 py-1" style="font-size:10px;">
                                Sous 30j
                            </span>
                        </div>
                    </div>
                    @endif

                    {{-- Alerte Abonnements expirant bientôt --}}
                    @if($abonnementsExpirantBientotCount > 0)
                    <div class="col-md-4">
                        <div class="cs-alert-card cs-alert-card--info">
                            <i class="bi bi-credit-card-2-front flex-shrink-0" style="font-size:22px;"></i>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-bold" style="font-size:13px;">{{ $abonnementsExpirantBientotCount }} Abonnement(s) à renouveler</div>
                                <div style="font-size:11px;color:var(--text2);">Échéance à moins de 15 jours</div>
                            </div>
                            <a href="{{ route('admin.paiements') }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold text-nowrap" style="font-size:11px;background:#1A56A0;border-color:#1A56A0;">
                                Détails
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- ═══ COMPTEURS GLOBAUX ═══ --}}
            <h6 class="fw-bold text-uppercase mb-3" style="font-size:11px;letter-spacing:1.5px;color:var(--text3);">
                Indicateurs Globaux & Activité Récente (7j)
            </h6>

            <div class="cs-stats-grid">

                {{-- Utilisateurs --}}
                <div class="cs-stat-card cs-stat-card--users">
                    <div class="cs-stat-card__body">
                        <div class="cs-stat-card__info">
                            <span class="cs-stat-card__label">Utilisateurs</span>
                            <span class="cs-stat-card__value">{{ number_format($stats['users'], 0, ',', ' ') }}</span>
                            <span class="cs-stat-card__sub">
                                <i class="bi bi-arrow-up-short text-success"></i> +{{ $activite['users_7j'] }} en 7 jours
                            </span>
                        </div>
                        <div class="cs-stat-card__icon-wrap">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.users') }}" class="cs-stat-card__footer">
                        Gérer les utilisateurs
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>

                {{-- Clubs --}}
                <div class="cs-stat-card cs-stat-card--clubs">
                    <div class="cs-stat-card__body">
                        <div class="cs-stat-card__info">
                            <span class="cs-stat-card__label">Clubs inscrits</span>
                            <span class="cs-stat-card__value">{{ number_format($stats['clubs'], 0, ',', ' ') }}</span>
                            <span class="cs-stat-card__sub">
                                <i class="bi bi-arrow-up-short text-success"></i> +{{ $activite['clubs_7j'] }} en 7 jours
                            </span>
                        </div>
                        <div class="cs-stat-card__icon-wrap">
                            <i class="bi bi-shield-fill"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.clubs') }}" class="cs-stat-card__footer">
                        Gérer les clubs
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>

                {{-- Joueurs --}}
                <div class="cs-stat-card cs-stat-card--joueurs">
                    <div class="cs-stat-card__body">
                        <div class="cs-stat-card__info">
                            <span class="cs-stat-card__label">Joueurs inscrits</span>
                            <span class="cs-stat-card__value">{{ number_format($stats['joueurs'], 0, ',', ' ') }}</span>
                            <span class="cs-stat-card__sub">
                                <i class="bi bi-arrow-up-short text-success"></i> +{{ $activite['joueurs_7j'] }} en 7 jours
                            </span>
                        </div>
                        <div class="cs-stat-card__icon-wrap">
                            <i class="bi bi-dribbble"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.joueurs') }}" class="cs-stat-card__footer">
                        Explorer les joueurs
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>

                {{-- Agents --}}
                <div class="cs-stat-card cs-stat-card--agents">
                    <div class="cs-stat-card__body">
                        <div class="cs-stat-card__info">
                            <span class="cs-stat-card__label">Agents sportifs</span>
                            <span class="cs-stat-card__value">{{ number_format($stats['agents'], 0, ',', ' ') }}</span>
                            <span class="cs-stat-card__sub">
                                @if($agentsEnAttente > 0)
                                    <span class="text-warning fw-semibold">{{ $agentsEnAttente }} en attente</span>
                                @else
                                    <span class="text-success">Tous vérifiés</span>
                                @endif
                            </span>
                        </div>
                        <div class="cs-stat-card__icon-wrap">
                            <i class="bi bi-briefcase-fill"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.agents.index') }}" class="cs-stat-card__footer">
                        Vérifier les agents
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>

                {{-- Transferts --}}
                <div class="cs-stat-card cs-stat-card--transferts">
                    <div class="cs-stat-card__body">
                        <div class="cs-stat-card__info">
                            <span class="cs-stat-card__label">Transferts</span>
                            <span class="cs-stat-card__value">{{ number_format($stats['transferts'], 0, ',', ' ') }}</span>
                            <span class="cs-stat-card__sub">
                                <i class="bi bi-arrow-up-short text-success"></i> +{{ $activite['transferts_7j'] }} en 7 jours
                            </span>
                        </div>
                        <div class="cs-stat-card__icon-wrap">
                            <i class="bi bi-arrow-left-right"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.transferts') }}" class="cs-stat-card__footer">
                        Historique transferts
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>

                {{-- FedaPay / Revenus --}}
                <div class="cs-stat-card cs-stat-card--fedapay">
                    <div class="cs-stat-card__body">
                        <div class="cs-stat-card__info">
                            <span class="cs-stat-card__label">FedaPay (7j)</span>
                            <span class="cs-stat-card__value" style="font-size:24px;">
                                {{ number_format($transactionsFedaPay['total_7j'], 0, ',', ' ') }} <small style="font-size:12px;">FCFA</small>
                            </span>
                            <span class="cs-stat-card__sub">
                                Total: {{ number_format($transactionsFedaPay['total_global'], 0, ',', ' ') }} FCFA
                            </span>
                        </div>
                        <div class="cs-stat-card__icon-wrap">
                            <i class="bi bi-wallet2"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.paiements') }}" class="cs-stat-card__footer">
                        Suivi des transactions
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>

            </div>

            {{-- ═══ GRILLES PRINCIPALES ═══ --}}
            <div class="row g-4">

                {{-- Colonne gauche (8 colonnes) --}}
                <div class="col-lg-8">

                    {{-- Section 1 : Agents en attente de vérification --}}
                    <div class="cs-card">
                        <div class="cs-card__header">
                            <h3 class="cs-card__title">
                                <i class="bi bi-shield-exclamation"></i>
                                Agents en attente de vérification
                                @if($agentsEnAttente > 0)
                                    <span class="badge bg-warning text-dark rounded-pill ms-1" style="font-size:10px;">{{ $agentsEnAttente }}</span>
                                @endif
                            </h3>
                            <a href="{{ route('admin.agents.index', ['statut' => 'en_attente']) }}" class="cs-card__action">
                                Voir tous
                            </a>
                        </div>
                        <div class="cs-card__body cs-card__body--flush">
                            @forelse($agentsEnAttenteList as $agent)
                            <div class="cs-list-row">
                                <div class="rounded-3 d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                                     style="width:38px;height:38px;background:#8b5cf6;font-size:13px;">
                                    {{ strtoupper(substr($agent->nomComplet(), 0, 2)) }}
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-semibold text-truncate small" style="color:var(--text);">
                                            {{ $agent->nomComplet() }}
                                        </span>
                                        <span class="cs-tag cs-tag--warning">En attente</span>
                                    </div>
                                    <div class="text-muted" style="font-size:11px;">
                                        Agence: {{ $agent->agence ?? 'Indépendant' }} · Accréditation: {{ $agent->numero_accreditation ?? 'Non renseignée' }}
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <form method="POST" action="{{ route('admin.agents.verifier', $agent) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1" style="font-size:11px;">
                                            <i class="bi bi-check-lg me-1"></i>Valider
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.agents.show', $agent) }}" class="btn btn-sm btn-light rounded-circle" title="Consulter">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>
                            @empty
                            <div class="p-4 text-center text-muted" style="font-size:13px;">
                                <i class="bi bi-check-circle text-success me-1"></i> Aucun agent en attente de validation.
                            </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Section 2 : Transferts récents & Licences en alerte --}}
                    <div class="row g-4">
                        {{-- Transferts --}}
                        <div class="col-md-6">
                            <div class="cs-card mb-0 h-100">
                                <div class="cs-card__header">
                                    <h3 class="cs-card__title">
                                        <i class="bi bi-arrow-left-right"></i>
                                        Derniers transferts
                                    </h3>
                                    <a href="{{ route('admin.transferts') }}" class="cs-card__action">Voir</a>
                                </div>
                                <div class="cs-card__body cs-card__body--flush">
                                    @forelse($derniersTransferts as $transfert)
                                    <div class="cs-list-row">
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="fw-semibold text-truncate small" style="color:var(--text);">
                                                {{ $transfert->joueur?->nomComplet() ?? 'Joueur' }}
                                            </div>
                                            <div class="text-muted" style="font-size:11px;">
                                                {{ $transfert->clubSource?->nom ?? 'Libre' }} → {{ $transfert->clubDestinataire?->nom ?? 'Club' }}
                                            </div>
                                        </div>
                                        <span class="cs-tag {{ $transfert->statut === 'accepte' ? 'cs-tag--success' : ($transfert->statut === 'en_cours' ? 'cs-tag--warning' : 'cs-tag--primary') }}">
                                            {{ ucfirst(str_replace('_', ' ', $transfert->statut ?? 'en attente')) }}
                                        </span>
                                    </div>
                                    @empty
                                    <div class="p-3 text-center text-muted" style="font-size:12px;">
                                        Aucun transfert récent.
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        {{-- Licences expirant bientôt --}}
                        <div class="col-md-6">
                            <div class="cs-card mb-0 h-100">
                                <div class="cs-card__header">
                                    <h3 class="cs-card__title">
                                        <i class="bi bi-clock-history"></i>
                                        Licences expirant sous 30j
                                    </h3>
                                    <span class="badge bg-danger rounded-pill" style="font-size:10px;">
                                        {{ $licencesExpirantBientotCount }}
                                    </span>
                                </div>
                                <div class="cs-card__body cs-card__body--flush">
                                    @forelse($licencesExpirantBientot as $licence)
                                    <div class="cs-list-row">
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="fw-semibold text-truncate small" style="color:var(--text);">
                                                {{ $licence->joueur?->nomComplet() ?? 'Joueur' }}
                                            </div>
                                            <div class="text-muted" style="font-size:11px;">
                                                {{ $licence->club?->nom ?? 'Club' }} · N° {{ $licence->numero_licence }}
                                            </div>
                                        </div>
                                        <span class="badge bg-light text-danger border border-danger-subtle rounded-pill" style="font-size:10px;">
                                            Exp. {{ $licence->date_expiration ? $licence->date_expiration->format('d/m/Y') : '-' }}
                                        </span>
                                    </div>
                                    @empty
                                    <div class="p-3 text-center text-muted" style="font-size:12px;">
                                        <i class="bi bi-check-circle text-success me-1"></i> Aucune licence proche de l'expiration.
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Colonne droite (4 colonnes) --}}
                <div class="col-lg-4">

                    {{-- 1. Abonnements actifs par type d'acteur --}}
                    <div class="cs-side-card">
                        <div class="cs-side-card__title">
                            <span><i class="bi bi-pie-chart-fill me-1"></i> Abonnements Actifs</span>
                            <span class="badge bg-success rounded-pill" style="font-size:10px;">{{ $abonnementsActifs['total'] }} au total</span>
                        </div>

                        <div class="d-flex flex-column gap-3">
                            {{-- Clubs --}}
                            <div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="fw-semibold" style="color:var(--text);">Clubs</span>
                                    <span class="text-muted">{{ $abonnementsActifs['clubs'] }} actifs</span>
                                </div>
                                <div class="progress" style="height:6px;border-radius:10px;background:var(--bg2);">
                                    <div class="progress-bar bg-warning" role="progressbar"
                                         style="width:{{ $abonnementsActifs['total'] > 0 ? ($abonnementsActifs['clubs'] / $abonnementsActifs['total']) * 100 : 0 }}%;">
                                    </div>
                                </div>
                            </div>

                            {{-- Joueurs --}}
                            <div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="fw-semibold" style="color:var(--text);">Joueurs</span>
                                    <span class="text-muted">{{ $abonnementsActifs['joueurs'] }} actifs</span>
                                </div>
                                <div class="progress" style="height:6px;border-radius:10px;background:var(--bg2);">
                                    <div class="progress-bar bg-success" role="progressbar"
                                         style="width:{{ $abonnementsActifs['total'] > 0 ? ($abonnementsActifs['joueurs'] / $abonnementsActifs['total']) * 100 : 0 }}%;">
                                    </div>
                                </div>
                            </div>

                            {{-- Agents --}}
                            <div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="fw-semibold" style="color:var(--text);">Agents</span>
                                    <span class="text-muted">{{ $abonnementsActifs['agents'] }} actifs</span>
                                </div>
                                <div class="progress" style="height:6px;border-radius:10px;background:var(--bg2);">
                                    <div class="progress-bar bg-purple" role="progressbar"
                                         style="background:#8b5cf6;width:{{ $abonnementsActifs['total'] > 0 ? ($abonnementsActifs['agents'] / $abonnementsActifs['total']) * 100 : 0 }}%;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top text-center">
                            <a href="{{ route('admin.paiements') }}" class="btn btn-sm btn-outline-primary rounded-pill w-100 fw-semibold" style="font-size:11px;">
                                <i class="bi bi-wallet2 me-1"></i> Consulter tous les paiements
                            </a>
                        </div>
                    </div>

                    {{-- 2. Abonnements expirant bientôt --}}
                    <div class="cs-side-card">
                        <div class="cs-side-card__title">
                            <span><i class="bi bi-hourglass-split me-1"></i> Abonnements à renouveler (15j)</span>
                            <span class="badge bg-warning text-dark rounded-pill" style="font-size:10px;">{{ $abonnementsExpirantBientotCount }}</span>
                        </div>

                        @forelse($abonnementsExpirantBientot as $ab)
                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 mb-2" style="background:var(--bg2);border:1px solid var(--border);">
                            <div class="min-w-0 flex-grow-1">
                                <div class="fw-semibold text-truncate small" style="color:var(--text);">
                                    @if($ab->club)
                                        {{ $ab->club->nom }} (Club)
                                    @elseif($ab->joueur)
                                        {{ $ab->joueur->nomComplet() }} (Joueur)
                                    @elseif($ab->agent)
                                        {{ $ab->agent->nomComplet() }} (Agent)
                                    @else
                                        Acteur #{{ $ab->id }}
                                    @endif
                                </div>
                                <div class="text-muted" style="font-size:10px;">
                                    Plan: {{ ucfirst($ab->plan ?? 'Standard') }} · Fin le {{ $ab->fin_at ? $ab->fin_at->format('d/m/Y') : '-' }}
                                </div>
                            </div>
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill" style="font-size:9px;">
                                Bientôt
                            </span>
                        </div>
                        @empty
                        <p class="text-center small text-muted mb-0">Aucun abonnement en fin de cycle immédiate.</p>
                        @endforelse
                    </div>

                    {{-- 3. Derniers utilisateurs inscrits --}}
                    <div class="cs-side-card">
                        <div class="cs-side-card__title">
                            <span><i class="bi bi-person-plus me-1"></i> Dernières Inscriptions</span>
                            <a href="{{ route('admin.users') }}" class="text-decoration-none small" style="color:#1A56A0;">Gérer</a>
                        </div>

                        @foreach($derniersUsers as $u)
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                 style="width:32px;height:32px;background:#1A56A0;font-size:11px;">
                                {{ strtoupper(substr($u->name, 0, 2)) }}
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-semibold text-truncate small" style="color:var(--text);">{{ $u->name }}</div>
                                <div class="text-muted" style="font-size:10px;">
                                    <span class="badge bg-light text-dark border rounded-pill">{{ ucfirst($u->role) }}</span>
                                    · {{ $u->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                </div>

            </div>

        </div>
    </main>

</div>

@endsection
