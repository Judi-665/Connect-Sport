{{-- resources/views/club/joueurs/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Joueurs — ' . $club->nom)

@push('styles')
<style>
.cs-dash__content { padding: 24px; }

.cs-page-header {
    display: flex; align-items: flex-start;
    justify-content: space-between; flex-wrap: wrap;
    gap: 16px; margin-bottom: 24px;
}
.cs-page-title   { font-size: 22px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.cs-page-subtitle { font-size: 13px; color: var(--text3); margin: 0; }

/* Compteurs */
.cs-counters { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 24px; }
.cs-counter {
    background: var(--card-bg); border: 1px solid var(--border);
    border-radius: 12px; padding: 14px 20px; min-width: 130px;
    transition: background .25s, border-color .25s;
}
.cs-counter__val  { display: block; font-size: 28px; font-weight: 800; color: var(--text); line-height: 1; }
.cs-counter__label { display: block; font-size: 11px; font-weight: 600; letter-spacing: .8px; text-transform: uppercase; color: var(--text3); margin-top: 4px; }
.cs-counter--accent .cs-counter__val { color: #F97316; }

/* Filtres */
.cs-filters {
    background: var(--card-bg); border: 1px solid var(--border);
    border-radius: 14px; padding: 16px 20px; margin-bottom: 20px;
    display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;
    transition: background .25s, border-color .25s;
}
.cs-filter-group { display: flex; flex-direction: column; gap: 5px; min-width: 130px; }
.cs-filter-group--grow { flex: 1; min-width: 180px; }
.cs-filter-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .7px; color: var(--text3); }
.cs-filter-input,
.cs-filter-select {
    background: var(--bg2); border: 1px solid var(--border);
    border-radius: 8px; padding: 8px 12px; font-size: 13px;
    color: var(--text); outline: none; width: 100%;
    transition: border-color .15s, background .25s;
}
.cs-filter-input::placeholder { color: var(--text3); }
.cs-filter-input:focus, .cs-filter-select:focus { border-color: #F97316; }
.cs-filter-select option { background: var(--card-bg); color: var(--text); }
.cs-filter-actions { display: flex; gap: 8px; align-items: flex-end; }

/* Boutons */
.cs-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 18px; border-radius: 20px;
    font-size: 13px; font-weight: 600;
    text-decoration: none; border: none; cursor: pointer; transition: all .15s;
}
.cs-btn--primary { background: #F97316; color: #fff; }
.cs-btn--primary:hover { background: #ea6a0b; color: #fff; }
.cs-btn--outline { background: transparent; border: 1px solid var(--border); color: var(--text3); }
.cs-btn--outline:hover { border-color: #F97316; color: #F97316; }

/* Table */
.cs-table-wrap {
    background: var(--card-bg); border: 1px solid var(--border);
    border-radius: 14px; overflow: hidden;
    transition: background .25s, border-color .25s;
}
.cs-table { width: 100%; border-collapse: collapse; }
.cs-table thead th {
    padding: 12px 16px; font-size: 11px; font-weight: 600;
    letter-spacing: .8px; text-transform: uppercase; color: var(--text3);
    border-bottom: 1px solid var(--border); white-space: nowrap;
    background: var(--bg2); text-align: left;
}
.cs-table tbody tr { border-bottom: 1px solid var(--border); transition: background .15s; }
.cs-table tbody tr:last-child { border-bottom: none; }
.cs-table tbody tr:hover { background: var(--bg2); }
.cs-table td { padding: 13px 16px; vertical-align: middle; font-size: 13px; color: var(--text); }

/* Joueur cell */
.cs-player-cell { display: flex; align-items: center; gap: 12px; }
.cs-player-avatar {
    width: 38px; height: 38px; border-radius: 10px;
    background: linear-gradient(135deg, #1A56A0, #5B9BD5);
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700; color: #fff; flex-shrink: 0; text-transform: uppercase;
}
.cs-player-name { display: block; font-size: 13px; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px; }
.cs-player-meta { font-size: 11px; color: var(--text3); margin-top: 1px; }

/* Tags */
.cs-tag {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 20px; white-space: nowrap;
}
.cs-tag--success  { background: rgba(16,185,129,.1);  color: #10b981; }
.cs-tag--warning  { background: rgba(245,158,11,.12); color: #d97706; }
.cs-tag--primary  { background: rgba(26,86,160,.1);   color: #1A56A0; }
.cs-tag--danger   { background: rgba(239,68,68,.1);   color: #ef4444; }
.cs-tag--neutral  { background: var(--bg2);           color: var(--text3); border: 1px solid var(--border); }
.cs-tag--orange   { background: rgba(249,115,22,.12); color: #F97316; }
.cs-tag--info     { background: rgba(6,182,212,.1);   color: #0891b2; }

/* Stats inline */
.cs-inline-stats { display: flex; gap: 12px; }
.cs-inline-stat  { text-align: center; }
.cs-inline-stat__val { display: block; font-size: 14px; font-weight: 700; color: var(--text); }
.cs-inline-stat__lbl { display: block; font-size: 10px; color: var(--text3); }

/* Stats pending badge */
.cs-stats-pending { font-size: 10px; color: var(--text3); margin-top: 3px; }

/* Actions */
.cs-row-actions { display: flex; gap: 6px; justify-content: flex-end; }
.cs-action-btn {
    width: 30px; height: 30px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    color: var(--text3); text-decoration: none;
    transition: all .15s; border: none; background: transparent; cursor: pointer;
}
.cs-action-btn:hover { background: var(--bg2); color: #F97316; }

/* Empty */
.cs-empty { padding: 60px 20px; display: flex; flex-direction: column; align-items: center; gap: 12px; color: var(--text3); text-align: center; }
.cs-empty svg { opacity: .3; }
.cs-empty p { font-size: 14px; margin: 0; }

/* Pagination */
.cs-pagination { padding: 16px 20px; border-top: 1px solid var(--border); }

@media (max-width: 1024px) {
    .cs-table thead th:nth-child(6),
    .cs-table td:nth-child(6) { display: none; }
}
@media (max-width: 768px) {
    .cs-table thead th:nth-child(n+4),
    .cs-table td:nth-child(n+4) { display: none; }
    .cs-dash__content { padding: 16px; }
}
</style>
@endpush

@section('content')
<div class="cs-dash__content">

    {{-- Header --}}
    <div class="cs-page-header">
        <div>
            <h1 class="cs-page-title">Gestion des joueurs</h1>
            <p class="cs-page-subtitle">{{ $compteurs['total'] }} joueur(s) · Saison {{ $compteurs['saison'] }}</p>
        </div>
    </div>

    {{-- Compteurs --}}
    <div class="cs-counters">
        <div class="cs-counter cs-counter--accent">
            <span class="cs-counter__val">{{ $compteurs['total'] }}</span>
            <span class="cs-counter__label">Total joueurs</span>
        </div>
        <div class="cs-counter">
            <span class="cs-counter__val">{{ $compteurs['actifs'] }}</span>
            <span class="cs-counter__label">Actifs</span>
        </div>
        <div class="cs-counter">
            <span class="cs-counter__val">{{ $equipes->count() }}</span>
            <span class="cs-counter__label">Équipes</span>
        </div>
        <div class="cs-counter">
            <span class="cs-counter__val">{{ $compteurs['visibles_recruteur'] }}</span>
            <span class="cs-counter__label">Visibles recruteurs</span>
        </div>
        <div class="cs-counter">
            <span class="cs-counter__val">{{ $compteurs['sans_club'] }}</span>
            <span class="cs-counter__label">Sans club</span>
        </div>
    </div>

    {{-- Filtres --}}
    <form method="GET" action="{{ route('club.joueurs.index') }}">
        <div class="cs-filters">

            <div class="cs-filter-group cs-filter-group--grow">
                <label class="cs-filter-label">Recherche</label>
                <input type="text" name="search" class="cs-filter-input"
                       placeholder="Nom du joueur..." value="{{ request('search') }}">
            </div>

            <div class="cs-filter-group">
                <label class="cs-filter-label">Catégorie</label>
                <select name="categorie" class="cs-filter-select">
                    <option value="">Toutes</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" @selected(request('categorie') === $cat)>{{ ucfirst($cat) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="cs-filter-group">
                <label class="cs-filter-label">Poste</label>
                <select name="poste" class="cs-filter-select">
                    <option value="">Tous</option>
                    @foreach($postes as $poste)
                        <option value="{{ $poste }}" @selected(request('poste') === $poste)>{{ ucfirst($poste) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="cs-filter-group">
                <label class="cs-filter-label">Équipe</label>
                <select name="equipe_id" class="cs-filter-select">
                    <option value="">Toutes</option>
                    @foreach($equipes as $equipe)
                        <option value="{{ $equipe->id }}" @selected(request('equipe_id') == $equipe->id)>{{ $equipe->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div class="cs-filter-group">
                <label class="cs-filter-label">Statut</label>
                <select name="statut" class="cs-filter-select">
                    <option value="">Tous</option>
                    <option value="actif"            @selected(request('statut') === 'actif')>Actif</option>
                    <option value="inactif"          @selected(request('statut') === 'inactif')>Inactif</option>
                    <option value="sans_club"        @selected(request('statut') === 'sans_club')>Sans club</option>
                    <option value="visible_recruteur" @selected(request('statut') === 'visible_recruteur')>Visible recruteur</option>
                </select>
            </div>

            <div class="cs-filter-actions">
                <button type="submit" class="cs-btn cs-btn--primary">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    Filtrer
                </button>
                @if(request()->hasAny(['search','categorie','poste','equipe_id','statut']))
                    <a href="{{ route('club.joueurs.index') }}" class="cs-btn cs-btn--outline">Réinitialiser</a>
                @endif
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="cs-table-wrap">
        @if($joueurs->isEmpty())
            <div class="cs-empty">
                <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <p>Aucun joueur trouvé.</p>
                @if(request()->hasAny(['search','categorie','poste','equipe_id','statut']))
                    <a href="{{ route('club.joueurs.index') }}" class="cs-btn cs-btn--outline" style="margin-top:4px;">Effacer les filtres</a>
                @endif
            </div>
        @else
            <table class="cs-table">
                <thead>
                    <tr>
                        <th>Joueur</th>
                        <th>Poste</th>
                        <th>Catégorie</th>
                        <th>Équipe</th>
                        <th>Licence</th>
                        <th>Stats saison (toutes)</th>
                        <th>Visibilité</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($joueurs as $joueur)
                    <tr>
                        {{-- Joueur --}}
                        <td>
                            <div class="cs-player-cell">
                                <div class="cs-player-avatar">
                                    {{ mb_substr($joueur->user->prenom ?? '?', 0, 1) }}{{ mb_substr($joueur->user->name ?? '', 0, 1) }}
                                </div>
                                <div>
                                    <span class="cs-player-name">{{ $joueur->nomComplet() }}</span>
                                    <span class="cs-player-meta">
                                        {{ $joueur->age() ? $joueur->age().' ans' : '—' }}
                                        @if($joueur->nationalite) · {{ $joueur->nationalite }} @endif
                                    </span>
                                </div>
                            </div>
                        </td>

                        {{-- Poste --}}
                        <td>
                            @if($joueur->poste)
                                <span class="cs-tag cs-tag--primary">{{ ucfirst($joueur->poste) }}</span>
                            @else <span style="color:var(--text3);">—</span>
                            @endif
                        </td>

                        {{-- Catégorie --}}
                        <td>
                            @if($joueur->categorie)
                                <span class="cs-tag cs-tag--orange">{{ ucfirst($joueur->categorie) }}</span>
                            @else <span style="color:var(--text3);">—</span>
                            @endif
                        </td>

                        {{-- Équipe --}}
                        <td>{{ $joueur->equipe?->nom ?? '—' }}</td>

                        {{-- Licence --}}
                        <td>
                            @if($joueur->licenceActive)
                                <span class="cs-tag cs-tag--success">Active</span>
                            @else
                                <span class="cs-tag cs-tag--danger">Absente</span>
                            @endif
                        </td>

                        {{-- Stats saison — club voit toutes (validées + en attente) --}}
                        <td>
                            @php
                                $toutesStats  = $joueur->statistiques; // déjà chargées (saisonEnCours + valides via eager load)
                                $statsValides = $toutesStats->where('valide', true);
                                $statsPending = $toutesStats->where('valide', false);
                                $matchs  = $statsValides->sum('matchs_joues');
                                $buts    = $statsValides->sum('buts');
                                $passes  = $statsValides->sum('passes_decisives');
                            @endphp
                            @if($matchs > 0)
                                <div class="cs-inline-stats">
                                    <div class="cs-inline-stat">
                                        <span class="cs-inline-stat__val">{{ $matchs }}</span>
                                        <span class="cs-inline-stat__lbl">Matchs</span>
                                    </div>
                                    <div class="cs-inline-stat">
                                        <span class="cs-inline-stat__val">{{ $buts }}</span>
                                        <span class="cs-inline-stat__lbl">Buts</span>
                                    </div>
                                    <div class="cs-inline-stat">
                                        <span class="cs-inline-stat__val">{{ $passes }}</span>
                                        <span class="cs-inline-stat__lbl">Passes</span>
                                    </div>
                                </div>
                                @if($statsPending->count() > 0)
                                    <div class="cs-stats-pending">
                                        <span class="cs-tag cs-tag--warning" style="padding:2px 6px;font-size:10px;">
                                            {{ $statsPending->count() }} en attente
                                        </span>
                                    </div>
                                @endif
                            @else
                                <span style="font-size:12px;color:var(--text3);">
                                    Aucune stat
                                    @if($statsPending->count() > 0)
                                        <span class="cs-tag cs-tag--warning" style="padding:2px 6px;font-size:10px;margin-left:4px;">{{ $statsPending->count() }} en attente</span>
                                    @endif
                                </span>
                            @endif
                        </td>

                        {{-- Visibilité recruteur (CDC 4.3) --}}
                        <td>
                            @if($joueur->visible_recruteur)
                                <span class="cs-tag cs-tag--info">
                                    <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Visible
                                </span>
                            @else
                                <span class="cs-tag cs-tag--neutral">Restreint</span>
                            @endif
                            @if($joueur->sans_club)
                                <span class="cs-tag cs-tag--warning" style="margin-top:3px;display:inline-flex;">Sans club</span>
                            @endif
                        </td>

                        {{-- Statut actif --}}
                        <td>
                            @if($joueur->actif)
                                <span class="cs-tag cs-tag--success">Actif</span>
                            @else
                                <span class="cs-tag cs-tag--neutral">Inactif</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="cs-row-actions">
                                <a href="{{ route('club.joueurs.show', $joueur) }}"
                                   class="cs-action-btn" title="Voir le profil">
                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($joueurs->hasPages())
                <div class="cs-pagination">{{ $joueurs->links() }}</div>
            @endif
        @endif
    </div>

</div>
@endsection