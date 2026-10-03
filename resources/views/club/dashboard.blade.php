{{-- resources/views/club/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard — ' . ($club->nom ?? 'Mon Club'))

@php
    $planAbonnement = $abonnementActif?->subscriptionPlan;
    $planSlug = $planAbonnement?->slug ?? $abonnementActif?->plan ?? 'gratuit';
    $planNom = $planAbonnement?->nom ?? ucfirst($planSlug);
    $estPremium = $planSlug === 'premium';
@endphp

@push('styles')
<style>
/* ── Layout général ── */
.cs-dash {
    display: flex;
    min-height: 100vh;
    background: var(--bg2);
    padding-top: 64px; /* compense navbar fixe */
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

/* ── Zone contenu ── */
.cs-dash__content {
    flex: 1;
    padding: 24px;
}

/* ── Titre de page ── */
.cs-page-title {
    font-size: 22px;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 4px;
}
.cs-page-subtitle {
    font-size: 13px;
    color: var(--text3);
    margin-bottom: 24px;
}

/* ── Cards stats ── */
.cs-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
    font-size: 32px;
    font-weight: 800;
    color: var(--text);
    line-height: 1;
}
.cs-stat-card__sub {
    display: block;
    font-size: 12px;
    color: var(--text3);
    margin-top: 4px;
}
.cs-stat-card__sub--warn { color: #F97316; }
.cs-stat-card__icon-wrap {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.cs-stat-card__icon-wrap svg { width: 22px; height: 22px; }
.cs-stat-card__footer {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 20px;
    font-size: 12px; font-weight: 500;
    text-decoration: none;
    border-top: 1px solid var(--border);
    transition: background .15s;
}
.cs-stat-card__footer:hover { background: var(--bg2); }

/* Couleurs par carte */
.cs-stat-card--joueurs .cs-stat-card__icon-wrap { background: rgba(26,86,160,.1); color: #1A56A0; }
.cs-stat-card--joueurs .cs-stat-card__footer     { color: #1A56A0; }
.cs-stat-card--equipes .cs-stat-card__icon-wrap  { background: rgba(249,115,22,.1); color: #F97316; }
.cs-stat-card--equipes .cs-stat-card__footer     { color: #F97316; }
.cs-stat-card--licences .cs-stat-card__icon-wrap { background: rgba(16,185,129,.1); color: #10b981; }
.cs-stat-card--licences .cs-stat-card__footer    { color: #10b981; }
.cs-stat-card--agenda .cs-stat-card__icon-wrap   { background: rgba(139,92,246,.1); color: #8b5cf6; }
.cs-stat-card--agenda .cs-stat-card__footer      { color: #8b5cf6; }

/* ── Cards génériques ── */
.cs-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
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
.cs-card__title svg { color: #F97316; flex-shrink: 0; }
.cs-card__action {
    font-size: 12px; font-weight: 600; color: #F97316;
    text-decoration: none; transition: opacity .15s;
}
.cs-card__action:hover { opacity: .75; }
.cs-card__action--add {
    display: flex; align-items: center; gap: 4px;
    padding: 5px 12px; border-radius: 20px;
    background: rgba(249,115,22,.1); color: #F97316;
    font-size: 12px; font-weight: 600; text-decoration: none;
}
.cs-card__body { padding: 20px; }
.cs-card__body--flush { padding: 0; }
.cs-card__footer {
    padding: 12px 20px;
    border-top: 1px solid var(--border);
    text-align: center;
}
.cs-card__footer-link {
    font-size: 12px; font-weight: 600; color: #F97316;
    text-decoration: none;
}

/* ── Joueurs récents ── */
.cs-joueur-row {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 20px;
    border-bottom: 1px solid var(--border);
    transition: background .15s;
}
.cs-joueur-row:last-child { border-bottom: none; }
.cs-joueur-row:hover { background: var(--bg2); }
.cs-joueur-row__avatar {
    width: 36px; height: 36px; border-radius: 10px;
    background: linear-gradient(135deg, #1A56A0, #5B9BD5);
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700; color: #fff; flex-shrink: 0;
}
.cs-joueur-row__info { flex: 1; min-width: 0; }
.cs-joueur-row__name {
    display: block; font-size: 13px; font-weight: 600; color: var(--text);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.cs-joueur-row__meta { font-size: 11px; color: var(--text3); margin-top: 1px; }
.cs-joueur-row__dispo {
    font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 20px;
    flex-shrink: 0;
}
.cs-joueur-row__dispo--libre { background: rgba(16,185,129,.1); color: #10b981; }
.cs-joueur-row__dispo--contrat { background: rgba(26,86,160,.1); color: #1A56A0; }

/* ── Événements ── */
.cs-event-row {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 20px;
    border-bottom: 1px solid var(--border);
    transition: background .15s;
}
.cs-event-row:last-child { border-bottom: none; }
.cs-event-row:hover { background: var(--bg2); }
.cs-event-row__date {
    display: flex; flex-direction: column; align-items: center;
    width: 40px; flex-shrink: 0;
    background: var(--bg2); border-radius: 10px; padding: 6px 4px;
}
.cs-event-row__day { font-size: 18px; font-weight: 800; color: var(--text); line-height: 1; }
.cs-event-row__month { font-size: 9px; font-weight: 600; color: var(--text3); text-transform: uppercase; letter-spacing: 1px; }
.cs-event-row__body { flex: 1; min-width: 0; }
.cs-event-row__title { display: block; font-size: 13px; font-weight: 600; color: var(--text); }
.cs-event-row__meta { display: flex; align-items: center; gap: 8px; margin-top: 3px; flex-wrap: wrap; }
.cs-event-row__lieu { display: flex; align-items: center; gap: 3px; font-size: 11px; color: var(--text3); }
.cs-event-row__heure { font-size: 11px; color: var(--text3); }
.cs-event-row__edit {
    width: 28px; height: 28px; border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    color: var(--text3); background: transparent;
    text-decoration: none; transition: all .15s; flex-shrink: 0;
}
.cs-event-row__edit:hover { background: var(--bg2); color: #F97316; }

/* ── Transferts ── */
.cs-transfert-row {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 20px;
    border-bottom: 1px solid var(--border);
    transition: background .15s;
}
.cs-transfert-row:last-child { border-bottom: none; }
.cs-transfert-row:hover { background: var(--bg2); }
.cs-transfert-row__avatar {
    width: 34px; height: 34px; border-radius: 8px;
    background: rgba(249,115,22,.15);
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700; color: #F97316; flex-shrink: 0;
}
.cs-transfert-row__joueur { display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0; }
.cs-transfert-row__name { display: block; font-size: 13px; font-weight: 600; color: var(--text); }
.cs-transfert-row__poste { display: block; font-size: 11px; color: var(--text3); }
.cs-transfert-row__direction { flex-shrink: 0; }
.cs-transfert-row__dir {
    display: flex; align-items: center; gap: 4px;
    font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 20px;
}
.cs-transfert-row__dir--sortant  { background: rgba(239,68,68,.1);   color: #ef4444; }
.cs-transfert-row__dir--arrivant { background: rgba(16,185,129,.1);  color: #10b981; }
.cs-transfert-row__club { font-size: 12px; color: var(--text3); flex: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.cs-transfert-row__statut { flex-shrink: 0; }
.cs-transfert-row__link {
    width: 28px; height: 28px; border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    color: var(--text3); text-decoration: none; transition: all .15s; flex-shrink: 0;
}
.cs-transfert-row__link:hover { background: var(--bg2); color: #F97316; }

/* ── Tags / badges ── */
.cs-tag {
    display: inline-flex; align-items: center;
    font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 20px;
}
.cs-tag--xs { font-size: 10px; padding: 2px 7px; }
.cs-tag--warning  { background: rgba(245,158,11,.12); color: #d97706; }
.cs-tag--primary  { background: rgba(26,86,160,.1);   color: #1A56A0; }
.cs-tag--success  { background: rgba(16,185,129,.1);  color: #10b981; }
.cs-tag--danger   { background: rgba(239,68,68,.1);   color: #ef4444; }
.cs-tag--info     { background: rgba(6,182,212,.1);   color: #0891b2; }
.cs-tag--neutral  { background: var(--bg2);           color: var(--text3); }

/* ── Empty state ── */
.cs-empty-state {
    display: flex; flex-direction: column; align-items: center;
    padding: 32px 20px; gap: 10px; color: var(--text3); text-align: center;
}
.cs-empty-state p { font-size: 13px; margin: 0; }
.cs-btn--sm {
    padding: 6px 16px; border-radius: 20px; font-size: 12px;
    font-weight: 600; text-decoration: none; display: inline-flex;
    align-items: center; gap: 6px;
}
.cs-btn--primary { background: #F97316; color: #fff; }
.cs-btn--primary:hover { background: #ea6a0b; color: #fff; }

/* ── Colonne droite ── */
.cs-side-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 20px;
    margin-bottom: 16px;
    transition: background .25s, border-color .25s;
}
.cs-side-card__title {
    font-size: 13px; font-weight: 700; color: var(--text);
    margin-bottom: 16px;
    display: flex; align-items: center; gap: 8px;
}
.cs-side-card__title i { color: #F97316; }

/* Responsive */
@media (max-width: 768px) {
    .cs-dash__main { margin-left: 0; }
    .cs-dash__content { padding: 16px; }
}
</style>
@endpush

@section('content')


        {{-- Contenu --}}
        <div class="cs-dash__content">

            {{-- Titre --}}
           <h1 class="cs-page-title">
                @php
                    $heure = (int) date('H');
                    $salutation = match(true) {
                        $heure >= 5 && $heure < 12 => 'Bonjour',
                        $heure >= 12 && $heure < 18 => 'Bon après-midi',
                        default => 'Bonsoir'
                    };
                @endphp
                {{ $salutation }}, {{ $club->nom }}
            </h1>
            <p class="cs-page-subtitle">Voici un aperçu de votre club — {{ now()->translatedFormat('l d F Y') }}</p>

            {{-- Stats --}}
            @include('club.partials._stats_cards')

            <div class="row g-4 mt-1">

                {{-- Colonne gauche --}}
                <div class="col-lg-8">

                    {{-- Joueurs récents --}}
                    @include('club.partials._joueurs_recents')

                    {{-- Agenda + Transferts --}}
                    <div class="row g-4 mt-0">
                        <div class="col-md-6">
                            @include('club.partials._agendas_prochains')
                        </div>
                        <div class="col-md-6">
                            @include('club.partials._transferts_encours')
                        </div>
                    </div>
                </div>

                {{-- Colonne droite --}}
                <div class="col-lg-4">

                    {{-- Abonnement --}}
                    <div class="cs-side-card">
                        <div class="cs-side-card__title">
                            <i class="bi bi-gem"></i> Mon abonnement
                            <span class="ms-auto cs-tag {{ $estPremium ? 'cs-tag--warning' : 'cs-tag--neutral' }}">
                                {{ $planNom }}
                            </span>
                        </div>
                        <div class="progress mb-3" style="height:6px;border-radius:10px;">
                            <div class="progress-bar" role="progressbar"
                                 style="width:{{ $estPremium ? '100' : ($planSlug === 'standard' ? '65' : '30') }}%;background:#F97316;border-radius:10px;">
                            </div>
                        </div>
                        @if(!$estPremium)
                            <a href="{{ route('club.abonnement.index') }}" class="btn btn-warning btn-sm w-100 fw-semibold rounded-pill">
                                <i class="bi bi-arrow-up-circle me-1"></i> Passer à Premium
                            </a>
                        @else
                            <div class="text-success small text-center">
                                <i class="bi bi-check-circle-fill me-1"></i> Abonnement Premium actif
                                @if($abonnementActif?->fin_at)
                                    <span class="d-block text-muted mt-1">Expire le {{ $abonnementActif->fin_at->format('d/m/Y') }}</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Actions rapides --}}
                    <div class="cs-side-card">
                        <div class="cs-side-card__title"><i class="bi bi-lightning-charge"></i> Actions rapides</div>
                        <div class="d-grid gap-2">
                            <a href="{{ route('club.agenda.create') }}" class="btn btn-outline-warning btn-sm rounded-pill">
                                <i class="bi bi-calendar-plus me-1"></i> Créer un événement
                            </a>
                            <a href="{{ route('club.licences.create') }}" class="btn btn-outline-warning btn-sm rounded-pill">
                                <i class="bi bi-file-earmark-plus me-1"></i> Ajouter une licence
                            </a>
                            <a href="{{ route('club.opportunites.create') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                                <i class="bi bi-megaphone me-1"></i> Publier une opportunité
                            </a>
                            <a href="{{ route('club.profil.edit') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                                <i class="bi bi-pencil me-1"></i> Modifier le profil
                            </a>
                        </div>
                    </div>

                    {{-- Sponsors actifs --}}
                    <div class="cs-side-card">
                        <div class="cs-side-card__title"><i class="bi bi-trophy"></i> Sponsors actifs</div>
                        @forelse($sponsorsActifs ?? [] as $sponsor)
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:38px;height:38px;background:var(--bg2);">
                                    <i class="bi bi-building text-secondary"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold small" style="color:var(--text);">{{ $sponsor->nom }}</div>
                                    <div class="text-muted small">Depuis {{ $sponsor->created_at->format('Y') }}</div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center small mb-0" style="color:var(--text3);">Aucun sponsor actif</p>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>

@endsection