{{-- resources/views/club/joueurs/show.blade.php --}}
@extends('layouts.app')

@section('title', $joueur->nomComplet() . ' — ' . $club->nom)

@push('styles')
<style>
.cs-dash__content { padding: 24px; }

/* Breadcrumb */
.cs-breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--text3); margin-bottom: 20px; }
.cs-breadcrumb a { color: var(--text3); text-decoration: none; transition: color .15s; }
.cs-breadcrumb a:hover { color: #F97316; }
.cs-breadcrumb span { color: var(--text); font-weight: 600; }

/* Hero profil */
.cs-hero {
    background: var(--card-bg); border: 1px solid var(--border);
    border-radius: 16px; padding: 24px; margin-bottom: 24px;
    display: flex; align-items: flex-start; gap: 20px; flex-wrap: wrap;
    transition: background .25s, border-color .25s;
}
.cs-hero__avatar {
    width: 72px; height: 72px; border-radius: 16px; flex-shrink: 0;
    background: linear-gradient(135deg, #1A56A0, #5B9BD5);
    display: flex; align-items: center; justify-content: center;
    font-size: 26px; font-weight: 800; color: #fff; text-transform: uppercase;
}
.cs-hero__info { flex: 1; min-width: 0; }
.cs-hero__name { font-size: 22px; font-weight: 800; color: var(--text); margin-bottom: 6px; }
.cs-hero__badges { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 8px; }
.cs-hero__meta { font-size: 12px; color: var(--text3); display: flex; flex-wrap: wrap; gap: 14px; }
.cs-hero__meta-item { display: flex; align-items: center; gap: 5px; }

/* Grid layout */
.cs-show-grid { display: grid; grid-template-columns: 300px 1fr; gap: 20px; }
@media (max-width: 1024px) { .cs-show-grid { grid-template-columns: 1fr; } }

/* Cards */
.cs-card {
    background: var(--card-bg); border: 1px solid var(--border);
    border-radius: 14px; overflow: hidden; margin-bottom: 20px;
    transition: background .25s, border-color .25s;
}
.cs-card__header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 18px; border-bottom: 1px solid var(--border);
}
.cs-card__title {
    display: flex; align-items: center; gap: 8px;
    font-size: 13px; font-weight: 700; color: var(--text); margin: 0;
}
.cs-card__title svg { color: #F97316; flex-shrink: 0; }
.cs-card__body { padding: 18px; }
.cs-card__body--flush { padding: 0; }

/* Info table */
.cs-info-table { width: 100%; border-collapse: collapse; }
.cs-info-table tr { border-bottom: 1px solid var(--border); }
.cs-info-table tr:last-child { border-bottom: none; }
.cs-info-table th {
    padding: 9px 0; font-size: 11px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .6px;
    color: var(--text3); width: 45%; vertical-align: top;
}
.cs-info-table td { padding: 9px 0; font-size: 13px; color: var(--text); }

/* Stats agrégées */
.cs-agg-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
.cs-agg-item {
    background: var(--bg2); border-radius: 10px; padding: 14px 10px;
    text-align: center; border: 1px solid var(--border);
}
.cs-agg-item__val { display: block; font-size: 24px; font-weight: 800; color: var(--text); line-height: 1; }
.cs-agg-item__lbl { display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: .6px; color: var(--text3); margin-top: 4px; }
.cs-agg-item--accent .cs-agg-item__val { color: #F97316; }
.cs-agg-item--success .cs-agg-item__val { color: #10b981; }
.cs-agg-item--info .cs-agg-item__val    { color: #0891b2; }
.cs-agg-item--warning .cs-agg-item__val { color: #d97706; }
.cs-agg-item--danger .cs-agg-item__val  { color: #ef4444; }

/* Tags */
.cs-tag {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 20px; white-space: nowrap;
}
.cs-tag--success  { background: rgba(16,185,129,.1);  color: #10b981; }
.cs-tag--warning  { background: rgba(245,158,11,.12); color: #d97706; }
.cs-tag--primary  { background: rgba(26,86,160,.1);   color: #1A56A0; }
.cs-tag--danger   { background: rgba(239,68,68,.1);   color: #ef4444; }
.cs-tag--neutral  { background: var(--bg2); color: var(--text3); border: 1px solid var(--border); }
.cs-tag--orange   { background: rgba(249,115,22,.12); color: #F97316; }
.cs-tag--info     { background: rgba(6,182,212,.1);   color: #0891b2; }

/* Formulaire stats */
.cs-form-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 12px; }
.cs-form-group { display: flex; flex-direction: column; gap: 5px; }
.cs-form-group--full { grid-column: 1 / -1; }
.cs-form-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .7px; color: var(--text3); }
.cs-form-label span { color: #ef4444; }
.cs-form-input, .cs-form-select {
    background: var(--bg2); border: 1px solid var(--border);
    border-radius: 8px; padding: 8px 12px; font-size: 13px; color: var(--text);
    outline: none; width: 100%; transition: border-color .15s, background .25s;
}
.cs-form-input:focus, .cs-form-select:focus { border-color: #F97316; }
.cs-form-select option { background: var(--card-bg); color: var(--text); }
.cs-form-input.is-invalid, .cs-form-select.is-invalid { border-color: #ef4444; }
.cs-error { font-size: 11px; color: #ef4444; margin-top: 3px; }

/* Boutons */
.cs-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 18px; border-radius: 20px; font-size: 13px; font-weight: 600;
    text-decoration: none; border: none; cursor: pointer; transition: all .15s;
}
.cs-btn--primary  { background: #F97316; color: #fff; }
.cs-btn--primary:hover { background: #ea6a0b; color: #fff; }
.cs-btn--success  { background: #10b981; color: #fff; }
.cs-btn--success:hover { background: #059669; color: #fff; }
.cs-btn--outline  { background: transparent; border: 1px solid var(--border); color: var(--text3); }
.cs-btn--outline:hover { border-color: #F97316; color: #F97316; }
.cs-btn--sm { padding: 5px 12px; font-size: 12px; }
.cs-btn--icon { padding: 5px; border-radius: 8px; width: 28px; height: 28px; justify-content: center; }

/* Table historique */
.cs-table { width: 100%; border-collapse: collapse; }
.cs-table thead th {
    padding: 10px 14px; font-size: 11px; font-weight: 600;
    letter-spacing: .8px; text-transform: uppercase; color: var(--text3);
    border-bottom: 1px solid var(--border); background: var(--bg2); text-align: left;
    white-space: nowrap;
}
.cs-table tbody tr { border-bottom: 1px solid var(--border); transition: background .15s; }
.cs-table tbody tr:last-child { border-bottom: none; }
.cs-table tbody tr:hover { background: var(--bg2); }
.cs-table td { padding: 11px 14px; vertical-align: middle; font-size: 13px; color: var(--text); }
.cs-table td.cs-td-center { text-align: center; }

/* Modal */
.cs-modal-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,.5);
    z-index: 1050; display: none; align-items: center; justify-content: center;
    padding: 20px;
}
.cs-modal-overlay.active { display: flex; }
.cs-modal {
    background: var(--card-bg); border: 1px solid var(--border);
    border-radius: 16px; width: 100%; max-width: 640px;
    max-height: 90vh; overflow-y: auto;
    transition: background .25s;
}
.cs-modal__header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px; border-bottom: 1px solid var(--border);
}
.cs-modal__title { font-size: 15px; font-weight: 700; color: var(--text); margin: 0; }
.cs-modal__close {
    width: 28px; height: 28px; border-radius: 8px; display: flex;
    align-items: center; justify-content: center;
    color: var(--text3); background: transparent; border: none; cursor: pointer;
    transition: all .15s;
}
.cs-modal__close:hover { background: var(--bg2); color: var(--text); }
.cs-modal__body { padding: 20px; }
.cs-modal__footer { padding: 14px 20px; border-top: 1px solid var(--border); display: flex; gap: 10px; justify-content: flex-end; }

/* Saison tabs */
.cs-saison-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
.cs-saison-tab {
    padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 600;
    border: 1px solid var(--border); color: var(--text3); background: transparent;
    cursor: pointer; text-decoration: none; transition: all .15s;
}
.cs-saison-tab:hover, .cs-saison-tab.active { background: #F97316; color: #fff; border-color: #F97316; }

/* Alert */
.cs-alert {
    padding: 10px 14px; border-radius: 10px; font-size: 13px;
    display: flex; align-items: center; gap: 8px; margin-bottom: 16px;
}
.cs-alert--success { background: rgba(16,185,129,.1); color: #10b981; border: 1px solid rgba(16,185,129,.2); }
.cs-alert--danger  { background: rgba(239,68,68,.1);  color: #ef4444; border: 1px solid rgba(239,68,68,.2); }

/* Empty */
.cs-empty-sm { padding: 30px 20px; text-align: center; color: var(--text3); font-size: 13px; }
</style>
@endpush

@section('content')
<div class="cs-dash__content">

    {{-- Breadcrumb --}}
    <div class="cs-breadcrumb">
        <a href="{{ route('club.dashboard') }}">Dashboard</a>
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
        <a href="{{ route('club.joueurs.index') }}">Joueurs</a>
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
        <span>{{ $joueur->nomComplet() }}</span>
    </div>

    {{-- Alertes session --}}
    @if(session('success'))
        <div class="cs-alert cs-alert--success">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="cs-alert cs-alert--danger">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Hero --}}
    <div class="cs-hero">
        <div class="cs-hero__avatar">
            {{ mb_substr($joueur->user->prenom ?? '?', 0, 1) }}{{ mb_substr($joueur->user->name ?? '', 0, 1) }}
        </div>
        <div class="cs-hero__info">
            <div class="cs-hero__name">{{ $joueur->nomComplet() }}</div>
            <div class="cs-hero__badges">
                @if($joueur->poste)
                    <span class="cs-tag cs-tag--primary">{{ ucfirst($joueur->poste) }}</span>
                @endif
                @if($joueur->categorie)
                    <span class="cs-tag cs-tag--orange">{{ ucfirst($joueur->categorie) }}</span>
                @endif
                @if($joueur->actif)
                    <span class="cs-tag cs-tag--success">Actif</span>
                @else
                    <span class="cs-tag cs-tag--neutral">Inactif</span>
                @endif
                @if($joueur->visible_recruteur)
                    <span class="cs-tag cs-tag--info">Visible recruteurs</span>
                @endif
                @if($joueur->sans_club)
                    <span class="cs-tag cs-tag--warning">Sans club</span>
                @endif
            </div>
            <div class="cs-hero__meta">
                @if($joueur->age())
                    <span class="cs-hero__meta-item">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        {{ $joueur->age() }} ans
                    </span>
                @endif
                @if($joueur->nationalite)
                    <span class="cs-hero__meta-item">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        {{ $joueur->nationalite }}
                    </span>
                @endif
                <span class="cs-hero__meta-item">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Équipe : {{ $joueur->equipe?->nom ?? 'Non affecté' }}
                </span>
                @if($joueur->user->email)
                    <span class="cs-hero__meta-item">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        {{ $joueur->user->email }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="cs-show-grid">

        {{-- ═══ COLONNE GAUCHE ═══ --}}
        <div>

            {{-- Infos personnelles --}}
            <div class="cs-card">
                <div class="cs-card__header">
                    <h3 class="cs-card__title">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Informations personnelles
                    </h3>
                </div>
                <div class="cs-card__body">
                    <table class="cs-info-table">
                        <tr>
                            <th>Date naissance</th>
                            <td>{{ $joueur->date_naissance?->format('d/m/Y') ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Nationalité</th>
                            <td>{{ $joueur->nationalite ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Téléphone</th>
                            <td>{{ $joueur->telephone ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Ville</th>
                            <td>{{ $joueur->ville ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Poste</th>
                            <td>{{ $joueur->poste ? ucfirst($joueur->poste) : '—' }}</td>
                        </tr>
                        <tr>
                            <th>Catégorie</th>
                            <td>{{ $joueur->categorie ? ucfirst($joueur->categorie) : '—' }}</td>
                        </tr>
                        <tr>
                            <th>Agent actif</th>
                            <td>
                                @if($joueur->agents->count())
                                    {{ $joueur->agents->first()->user->name ?? '—' }}
                                @else — @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Visible recruteur</th>
                            <td>
                                @if($joueur->visible_recruteur)
                                    <span class="cs-tag cs-tag--info">Oui</span>
                                @else
                                    <span class="cs-tag cs-tag--neutral">Non</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Licence --}}
            <div class="cs-card">
                <div class="cs-card__header">
                    <h3 class="cs-card__title">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                        Licence
                    </h3>
                    <a href="{{ route('club.licences.create') }}" class="cs-btn cs-btn--sm cs-btn--outline">+ Ajouter</a>
                </div>
                <div class="cs-card__body">
                    @if($joueur->licenceActive)
                        <div class="cs-alert cs-alert--success" style="margin-bottom:0;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            Licence active
                            @if($joueur->licenceActive->date_fin)
                                · expire le {{ $joueur->licenceActive->date_fin->format('d/m/Y') }}
                            @endif
                        </div>
                    @else
                        <div class="cs-alert cs-alert--danger" style="margin-bottom:0;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            Aucune licence active
                        </div>
                    @endif

                    @if($joueur->licences->count() > 1)
                        <div style="margin-top:12px;font-size:12px;color:var(--text3);">
                            {{ $joueur->licences->count() - 1 }} licence(s) précédente(s)
                        </div>
                    @endif
                </div>
            </div>

            {{-- Bio --}}
            @if($joueur->bio)
            <div class="cs-card">
                <div class="cs-card__header">
                    <h3 class="cs-card__title">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Biographie
                    </h3>
                </div>
                <div class="cs-card__body">
                    <p style="font-size:13px;color:var(--text3);line-height:1.6;margin:0;">{{ $joueur->bio }}</p>
                </div>
            </div>
            @endif

        </div>

        {{-- ═══ COLONNE DROITE ═══ --}}
        <div>

            {{-- Stats agrégées saison en cours --}}
            <div class="cs-card">
                <div class="cs-card__header">
                    <h3 class="cs-card__title">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                        Statistiques — saison {{ $saisonEnCours }}
                    </h3>
                </div>
                <div class="cs-card__body">
                    <div class="cs-agg-grid">
                        <div class="cs-agg-item cs-agg-item--accent">
                            <span class="cs-agg-item__val">{{ $aggregats['matchs_joues'] }}</span>
                            <span class="cs-agg-item__lbl">Matchs</span>
                        </div>
                        <div class="cs-agg-item cs-agg-item--success">
                            <span class="cs-agg-item__val">{{ $aggregats['buts'] }}</span>
                            <span class="cs-agg-item__lbl">Buts</span>
                        </div>
                        <div class="cs-agg-item cs-agg-item--info">
                            <span class="cs-agg-item__val">{{ $aggregats['passes_decisives'] }}</span>
                            <span class="cs-agg-item__lbl">Passes</span>
                        </div>
                        <div class="cs-agg-item">
                            <span class="cs-agg-item__val">{{ $aggregats['minutes_jouees'] }}</span>
                            <span class="cs-agg-item__lbl">Minutes</span>
                        </div>
                        <div class="cs-agg-item cs-agg-item--warning">
                            <span class="cs-agg-item__val">{{ $aggregats['cartons_jaunes'] }}</span>
                            <span class="cs-agg-item__lbl">Jaunes</span>
                        </div>
                        <div class="cs-agg-item cs-agg-item--danger">
                            <span class="cs-agg-item__val">{{ $aggregats['cartons_rouges'] }}</span>
                            <span class="cs-agg-item__lbl">Rouges</span>
                        </div>
                        @if($aggregats['note_moyenne'])
                        <div class="cs-agg-item" style="grid-column: 1 / -1;">
                            <span class="cs-agg-item__val" style="color:#8b5cf6;">{{ $aggregats['note_moyenne'] }}/10</span>
                            <span class="cs-agg-item__lbl">Note moyenne</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Formulaire ajout stat --}}
            <div class="cs-card">
                <div class="cs-card__header">
                    <h3 class="cs-card__title">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                        Enregistrer une performance
                    </h3>
                </div>
                <div class="cs-card__body">
                    <form action="{{ route('club.joueurs.stats.store', $joueur) }}" method="POST">
                        @csrf
                        <div class="cs-form-grid">

                            {{-- Saison --}}
                            <div class="cs-form-group">
                                <label class="cs-form-label">Saison <span>*</span></label>
                                <input type="text" name="saison"
                                       class="cs-form-input @error('saison') is-invalid @enderror"
                                       value="{{ old('saison', $saisonEnCours) }}"
                                       placeholder="2025-2026" required>
                                @error('saison')<div class="cs-error">{{ $message }}</div>@enderror
                            </div>

                            {{-- Événement lié --}}
                            <div class="cs-form-group">
                                <label class="cs-form-label">Événement lié</label>
                                <select name="evenement_id" class="cs-form-select">
                                    <option value="">Aucun</option>
                                    @foreach($evenements as $evt)
                                        <option value="{{ $evt->id }}" @selected(old('evenement_id') == $evt->id)>
                                            {{ $evt->titre }} ({{ $evt->debut_at?->format('d/m/Y') ?? '—' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Matchs joués --}}
                            <div class="cs-form-group">
                                <label class="cs-form-label">Matchs joués <span>*</span></label>
                                <input type="number" name="matchs_joues"
                                       class="cs-form-input @error('matchs_joues') is-invalid @enderror"
                                       value="{{ old('matchs_joues', 1) }}" min="0" required>
                                @error('matchs_joues')<div class="cs-error">{{ $message }}</div>@enderror
                            </div>

                            {{-- Titulaire --}}
                            <div class="cs-form-group">
                                <label class="cs-form-label">Titulaire</label>
                                <input type="number" name="matchs_titulaire"
                                       class="cs-form-input"
                                       value="{{ old('matchs_titulaire', 0) }}" min="0">
                            </div>

                            {{-- Remplaçant --}}
                            <div class="cs-form-group">
                                <label class="cs-form-label">Remplaçant</label>
                                <input type="number" name="matchs_remplacant"
                                       class="cs-form-input"
                                       value="{{ old('matchs_remplacant', 0) }}" min="0">
                            </div>

                            {{-- Minutes --}}
                            <div class="cs-form-group">
                                <label class="cs-form-label">Minutes jouées</label>
                                <input type="number" name="minutes_jouees"
                                       class="cs-form-input"
                                       value="{{ old('minutes_jouees', 0) }}" min="0">
                            </div>

                            {{-- Buts --}}
                            <div class="cs-form-group">
                                <label class="cs-form-label">Buts</label>
                                <input type="number" name="buts"
                                       class="cs-form-input"
                                       value="{{ old('buts', 0) }}" min="0">
                            </div>

                            {{-- Passes --}}
                            <div class="cs-form-group">
                                <label class="cs-form-label">Passes déc.</label>
                                <input type="number" name="passes_decisives"
                                       class="cs-form-input"
                                       value="{{ old('passes_decisives', 0) }}" min="0">
                            </div>

                            {{-- Cartons jaunes --}}
                            <div class="cs-form-group">
                                <label class="cs-form-label">C. Jaunes</label>
                                <input type="number" name="cartons_jaunes"
                                       class="cs-form-input"
                                       value="{{ old('cartons_jaunes', 0) }}" min="0">
                            </div>

                            {{-- Cartons rouges --}}
                            <div class="cs-form-group">
                                <label class="cs-form-label">C. Rouges</label>
                                <input type="number" name="cartons_rouges"
                                       class="cs-form-input"
                                       value="{{ old('cartons_rouges', 0) }}" min="0">
                            </div>

                            {{-- Note --}}
                            <div class="cs-form-group">
                                <label class="cs-form-label">Note /10</label>
                                <input type="number" name="note_moyenne"
                                       class="cs-form-input"
                                       value="{{ old('note_moyenne') }}" min="0" max="10" step="0.1">
                            </div>

                            {{-- Visibilité (CDC 4.3) --}}
                            <div class="cs-form-group">
                                <label class="cs-form-label">Visibilité <span>*</span></label>
                                <select name="visibilite" class="cs-form-select @error('visibilite') is-invalid @enderror" required>
                                    <option value="restreinte" @selected(old('visibilite','restreinte') === 'restreinte')>Restreinte</option>
                                    <option value="publique"   @selected(old('visibilite') === 'publique')>Publique</option>
                                </select>
                                @error('visibilite')<div class="cs-error">{{ $message }}</div>@enderror
                            </div>

                            {{-- Valider directement --}}
                            <div class="cs-form-group" style="justify-content: flex-end; padding-top: 20px;">
                                <label class="cs-form-label" style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                                    <input type="checkbox" name="valide" value="1" @checked(old('valide'))
                                           style="width:15px;height:15px;accent-color:#F97316;">
                                    Valider maintenant
                                </label>
                            </div>

                        </div>

                        <div style="margin-top:16px;">
                            <button type="submit" class="cs-btn cs-btn--primary">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                Enregistrer la performance
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Historique des stats --}}
            <div class="cs-card">
                <div class="cs-card__header">
                    <h3 class="cs-card__title">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Historique des performances
                    </h3>
                </div>

                {{-- Onglets saisons --}}
                @if($saisons->count())
                <div style="padding:12px 18px;border-bottom:1px solid var(--border);">
                    <div class="cs-saison-tabs">
                        @foreach($saisons as $s)
                            <a href="{{ route('club.joueurs.show', $joueur) }}?saison={{ $s }}"
                               class="cs-saison-tab {{ (request('saison', $saisonEnCours) === $s) ? 'active' : '' }}">
                                {{ $s }}
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="cs-card__body--flush">
                    @php
                        $saisonFiltree = request('saison', $saisonEnCours);
                        $statsFiltrees = $joueur->statistiques->where('saison', $saisonFiltree)->sortByDesc('created_at');
                    @endphp

                    @if($statsFiltrees->isEmpty())
                        <div class="cs-empty-sm">Aucune statistique pour cette saison.</div>
                    @else
                        <div style="overflow-x:auto;">
                            <table class="cs-table">
                                <thead>
                                    <tr>
                                        <th>Événement</th>
                                        <th class="cs-td-center">M</th>
                                        <th class="cs-td-center">Tit</th>
                                        <th class="cs-td-center">Rem</th>
                                        <th class="cs-td-center">Min</th>
                                        <th class="cs-td-center">Buts</th>
                                        <th class="cs-td-center">Passes</th>
                                        <th class="cs-td-center">🟡</th>
                                        <th class="cs-td-center">🔴</th>
                                        <th class="cs-td-center">Note</th>
                                        <th>Visibilité</th>
                                        <th>Statut</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($statsFiltrees as $stat)
                                    <tr>
                                        <td style="max-width:140px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                            {{ $stat->evenement?->titre ?? 'Sans événement' }}
                                            <div style="font-size:10px;color:var(--text3);">
                                                {{ $stat->created_at->format('d/m/Y') }}
                                            </div>
                                        </td>
                                        <td class="cs-td-center">{{ $stat->matchs_joues }}</td>
                                        <td class="cs-td-center">{{ $stat->matchs_titulaire }}</td>
                                        <td class="cs-td-center">{{ $stat->matchs_remplacant }}</td>
                                        <td class="cs-td-center">{{ $stat->minutes_jouees }}</td>
                                        <td class="cs-td-center"><strong>{{ $stat->buts }}</strong></td>
                                        <td class="cs-td-center">{{ $stat->passes_decisives }}</td>
                                        <td class="cs-td-center">{{ $stat->cartons_jaunes }}</td>
                                        <td class="cs-td-center">{{ $stat->cartons_rouges }}</td>
                                        <td class="cs-td-center">{{ $stat->note_moyenne ?? '—' }}</td>
                                        <td>
                                            @if($stat->visibilite === 'publique')
                                                <span class="cs-tag cs-tag--info" style="font-size:10px;padding:2px 7px;">Publique</span>
                                            @else
                                                <span class="cs-tag cs-tag--neutral" style="font-size:10px;padding:2px 7px;">Restreinte</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($stat->valide)
                                                <span class="cs-tag cs-tag--success" style="font-size:10px;padding:2px 7px;">Validée</span>
                                            @else
                                                <span class="cs-tag cs-tag--warning" style="font-size:10px;padding:2px 7px;">En attente</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div style="display:flex;gap:4px;">
                                                {{-- Modifier --}}
                                                <button type="button"
                                                        class="cs-btn cs-btn--sm cs-btn--outline cs-btn--icon"
                                                        title="Modifier"
                                                        onclick="openEditModal({{ $stat->id }}, {{ json_encode($stat) }})">
                                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                </button>
                                                {{-- Valider rapidement si pas encore validée --}}
                                                @if(!$stat->valide)
                                                <form action="{{ route('club.joueurs.stats.update', $stat) }}" method="POST" style="display:inline;">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="saison"            value="{{ $stat->saison }}">
                                                    <input type="hidden" name="matchs_joues"      value="{{ $stat->matchs_joues }}">
                                                    <input type="hidden" name="matchs_titulaire"  value="{{ $stat->matchs_titulaire }}">
                                                    <input type="hidden" name="matchs_remplacant" value="{{ $stat->matchs_remplacant }}">
                                                    <input type="hidden" name="minutes_jouees"    value="{{ $stat->minutes_jouees }}">
                                                    <input type="hidden" name="buts"              value="{{ $stat->buts }}">
                                                    <input type="hidden" name="passes_decisives"  value="{{ $stat->passes_decisives }}">
                                                    <input type="hidden" name="cartons_jaunes"    value="{{ $stat->cartons_jaunes }}">
                                                    <input type="hidden" name="cartons_rouges"    value="{{ $stat->cartons_rouges }}">
                                                    <input type="hidden" name="note_moyenne"      value="{{ $stat->note_moyenne }}">
                                                    <input type="hidden" name="visibilite"        value="{{ $stat->visibilite }}">
                                                    <input type="hidden" name="valide"            value="1">
                                                    <button type="submit"
                                                            class="cs-btn cs-btn--sm cs-btn--success cs-btn--icon"
                                                            title="Valider">
                                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ═══ MODAL ÉDITION STAT ═══ --}}
<div class="cs-modal-overlay" id="editModal">
    <div class="cs-modal">
        <div class="cs-modal__header">
            <h4 class="cs-modal__title">Modifier la performance</h4>
            <button class="cs-modal__close" onclick="closeEditModal()">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="editForm" method="POST">
            @csrf @method('PUT')
            <div class="cs-modal__body">
                <div class="cs-form-grid">
                    <div class="cs-form-group">
                        <label class="cs-form-label">Saison <span>*</span></label>
                        <input type="text" name="saison" id="edit_saison" class="cs-form-input" required>
                    </div>
                    <div class="cs-form-group">
                        <label class="cs-form-label">Matchs joués <span>*</span></label>
                        <input type="number" name="matchs_joues" id="edit_matchs_joues" class="cs-form-input" min="0" required>
                    </div>
                    <div class="cs-form-group">
                        <label class="cs-form-label">Titulaire</label>
                        <input type="number" name="matchs_titulaire" id="edit_matchs_titulaire" class="cs-form-input" min="0">
                    </div>
                    <div class="cs-form-group">
                        <label class="cs-form-label">Remplaçant</label>
                        <input type="number" name="matchs_remplacant" id="edit_matchs_remplacant" class="cs-form-input" min="0">
                    </div>
                    <div class="cs-form-group">
                        <label class="cs-form-label">Minutes</label>
                        <input type="number" name="minutes_jouees" id="edit_minutes_jouees" class="cs-form-input" min="0">
                    </div>
                    <div class="cs-form-group">
                        <label class="cs-form-label">Buts</label>
                        <input type="number" name="buts" id="edit_buts" class="cs-form-input" min="0">
                    </div>
                    <div class="cs-form-group">
                        <label class="cs-form-label">Passes déc.</label>
                        <input type="number" name="passes_decisives" id="edit_passes_decisives" class="cs-form-input" min="0">
                    </div>
                    <div class="cs-form-group">
                        <label class="cs-form-label">C. Jaunes</label>
                        <input type="number" name="cartons_jaunes" id="edit_cartons_jaunes" class="cs-form-input" min="0">
                    </div>
                    <div class="cs-form-group">
                        <label class="cs-form-label">C. Rouges</label>
                        <input type="number" name="cartons_rouges" id="edit_cartons_rouges" class="cs-form-input" min="0">
                    </div>
                    <div class="cs-form-group">
                        <label class="cs-form-label">Note /10</label>
                        <input type="number" name="note_moyenne" id="edit_note_moyenne" class="cs-form-input" min="0" max="10" step="0.1">
                    </div>
                    <div class="cs-form-group">
                        <label class="cs-form-label">Visibilité <span>*</span></label>
                        <select name="visibilite" id="edit_visibilite" class="cs-form-select" required>
                            <option value="restreinte">Restreinte</option>
                            <option value="publique">Publique</option>
                        </select>
                    </div>
                    <div class="cs-form-group" style="justify-content:flex-end;padding-top:20px;">
                        <label class="cs-form-label" style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                            <input type="checkbox" name="valide" id="edit_valide" value="1"
                                   style="width:15px;height:15px;accent-color:#F97316;">
                            Validée
                        </label>
                    </div>
                </div>
            </div>
            <div class="cs-modal__footer">
                <button type="button" class="cs-btn cs-btn--outline" onclick="closeEditModal()">Annuler</button>
                <button type="submit" class="cs-btn cs-btn--primary">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openEditModal(statId, stat) {
    const baseUrl = "{{ url('club/joueurs/stats') }}/";
    document.getElementById('editForm').action = baseUrl + statId;

    document.getElementById('edit_saison').value            = stat.saison            ?? '';
    document.getElementById('edit_matchs_joues').value      = stat.matchs_joues      ?? 0;
    document.getElementById('edit_matchs_titulaire').value  = stat.matchs_titulaire  ?? 0;
    document.getElementById('edit_matchs_remplacant').value = stat.matchs_remplacant ?? 0;
    document.getElementById('edit_minutes_jouees').value    = stat.minutes_jouees    ?? 0;
    document.getElementById('edit_buts').value              = stat.buts              ?? 0;
    document.getElementById('edit_passes_decisives').value  = stat.passes_decisives  ?? 0;
    document.getElementById('edit_cartons_jaunes').value    = stat.cartons_jaunes    ?? 0;
    document.getElementById('edit_cartons_rouges').value    = stat.cartons_rouges    ?? 0;
    document.getElementById('edit_note_moyenne').value      = stat.note_moyenne      ?? '';
    document.getElementById('edit_visibilite').value        = stat.visibilite        ?? 'restreinte';
    document.getElementById('edit_valide').checked          = stat.valide == 1;

    document.getElementById('editModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
    document.body.style.overflow = '';
}

// Fermer en cliquant sur l'overlay
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>
@endpush
@endsection