{{-- resources/views/club/equipes/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Équipes — ' . $club->nom)

@push('styles')
<style>
.cs-dash__content { padding: 24px; }

/* Header */
.cs-page-header {
    display: flex; align-items: flex-start; justify-content: space-between;
    flex-wrap: wrap; gap: 16px; margin-bottom: 24px;
}
.cs-page-title    { font-size: 22px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.cs-page-subtitle { font-size: 13px; color: var(--text3); margin: 0; }

/* Compteurs */
.cs-counters { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 24px; }
.cs-counter {
    background: var(--card-bg); border: 1px solid var(--border);
    border-radius: 12px; padding: 14px 20px; min-width: 120px;
    transition: background .25s, border-color .25s;
}
.cs-counter__val   { display: block; font-size: 28px; font-weight: 800; color: var(--text); line-height: 1; }
.cs-counter__label { display: block; font-size: 11px; font-weight: 600; letter-spacing: .8px; text-transform: uppercase; color: var(--text3); margin-top: 4px; }
.cs-counter--accent .cs-counter__val { color: #F97316; }

/* Alertes */
.cs-alert {
    padding: 10px 14px; border-radius: 10px; font-size: 13px;
    display: flex; align-items: center; gap: 8px; margin-bottom: 20px;
}
.cs-alert--success { background: rgba(16,185,129,.1); color: #10b981; border: 1px solid rgba(16,185,129,.2); }
.cs-alert--danger  { background: rgba(239,68,68,.1);  color: #ef4444; border: 1px solid rgba(239,68,68,.2); }

/* Grid équipes */
.cs-equipes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

/* Card équipe */
.cs-equipe-card {
    background: var(--card-bg); border: 1px solid var(--border);
    border-radius: 16px; overflow: hidden;
    transition: transform .2s, box-shadow .2s, background .25s, border-color .25s;
    display: flex; flex-direction: column;
}
.cs-equipe-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.08); }

.cs-equipe-card__top {
    padding: 20px 20px 16px;
    border-bottom: 1px solid var(--border);
}
.cs-equipe-card__header {
    display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 10px;
}
.cs-equipe-card__icon {
    width: 46px; height: 46px; border-radius: 12px; flex-shrink: 0;
    background: rgba(249,115,22,.12);
    display: flex; align-items: center; justify-content: center; color: #F97316;
}
.cs-equipe-card__name {
    font-size: 16px; font-weight: 700; color: var(--text);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    max-width: 180px;
}
.cs-equipe-card__badges { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
.cs-equipe-card__desc {
    font-size: 12px; color: var(--text3); line-height: 1.5;
    margin-top: 10px;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}

.cs-equipe-card__stats {
    display: flex; gap: 0; border-bottom: 1px solid var(--border);
}
.cs-equipe-card__stat {
    flex: 1; padding: 12px 10px; text-align: center;
    border-right: 1px solid var(--border);
}
.cs-equipe-card__stat:last-child { border-right: none; }
.cs-equipe-card__stat-val { display: block; font-size: 18px; font-weight: 800; color: var(--text); }
.cs-equipe-card__stat-lbl { display: block; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: .6px; color: var(--text3); margin-top: 2px; }

.cs-equipe-card__footer {
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 16px; margin-top: auto;
}
.cs-equipe-card__status { font-size: 12px; color: var(--text3); display: flex; align-items: center; gap: 5px; }
.cs-equipe-card__status-dot {
    width: 7px; height: 7px; border-radius: 50%;
}
.cs-equipe-card__status-dot--active   { background: #10b981; }
.cs-equipe-card__status-dot--inactive { background: var(--text3); }
.cs-equipe-card__actions { display: flex; gap: 6px; }

/* Boutons */
.cs-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 18px; border-radius: 20px; font-size: 13px; font-weight: 600;
    text-decoration: none; border: none; cursor: pointer; transition: all .15s;
}
.cs-btn--primary { background: #F97316; color: #fff; }
.cs-btn--primary:hover { background: #ea6a0b; color: #fff; }
.cs-btn--sm { padding: 6px 14px; font-size: 12px; }
.cs-btn--icon {
    width: 30px; height: 30px; padding: 0; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    color: var(--text3); background: transparent;
    border: 1px solid var(--border); cursor: pointer; transition: all .15s;
    text-decoration: none;
}
.cs-btn--icon:hover { border-color: #F97316; color: #F97316; background: rgba(249,115,22,.08); }
.cs-btn--icon--danger:hover { border-color: #ef4444; color: #ef4444; background: rgba(239,68,68,.08); }

/* Tags */
.cs-tag {
    display: inline-flex; align-items: center;
    font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 20px; white-space: nowrap;
}
.cs-tag--primary  { background: rgba(26,86,160,.1);   color: #1A56A0; }
.cs-tag--danger   { background: rgba(239,68,68,.1);   color: #ef4444; }
.cs-tag--neutral  { background: var(--bg2); color: var(--text3); border: 1px solid var(--border); }
.cs-tag--orange   { background: rgba(249,115,22,.12); color: #F97316; }
.cs-tag--info     { background: rgba(6,182,212,.1);   color: #0891b2; }
.cs-tag--success  { background: rgba(16,185,129,.1);  color: #10b981; }

/* Empty */
.cs-empty {
    background: var(--card-bg); border: 1px solid var(--border);
    border-radius: 16px; padding: 60px 20px;
    display: flex; flex-direction: column; align-items: center;
    gap: 12px; color: var(--text3); text-align: center;
    transition: background .25s;
}
.cs-empty svg { opacity: .3; }
.cs-empty h3 { font-size: 16px; font-weight: 600; color: var(--text); margin: 0; }
.cs-empty p  { font-size: 13px; margin: 0; }

/* Modal */
.cs-modal-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,.5);
    z-index: 1050; display: none; align-items: center; justify-content: center; padding: 20px;
}
.cs-modal-overlay.active { display: flex; }
.cs-modal {
    background: var(--card-bg); border: 1px solid var(--border);
    border-radius: 16px; width: 100%; max-width: 420px;
    transition: background .25s;
}
.cs-modal__header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px; border-bottom: 1px solid var(--border);
}
.cs-modal__title { font-size: 15px; font-weight: 700; color: var(--text); margin: 0; }
.cs-modal__close {
    width: 28px; height: 28px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    color: var(--text3); background: transparent; border: none; cursor: pointer; transition: all .15s;
}
.cs-modal__close:hover { background: var(--bg2); }
.cs-modal__body { padding: 20px; font-size: 13px; color: var(--text3); line-height: 1.6; }
.cs-modal__body strong { color: var(--text); }
.cs-modal__footer { padding: 14px 20px; border-top: 1px solid var(--border); display: flex; gap: 10px; justify-content: flex-end; }

@media (max-width: 768px) {
    .cs-dash__content { padding: 16px; }
    .cs-equipes-grid  { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div class="cs-dash__content">

    {{-- Header --}}
    <div class="cs-page-header">
        <div>
            <h1 class="cs-page-title">Gestion des équipes</h1>
            <p class="cs-page-subtitle">{{ $equipes->count() }} équipe(s) active(s) · {{ $club->nom }}</p>
        </div>
        <a href="{{ route('club.equipes.create') }}" class="cs-btn cs-btn--primary">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            Nouvelle équipe
        </a>
    </div>

    {{-- Alertes --}}
    @if(session('success'))
        <div class="cs-alert cs-alert--success">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="cs-alert cs-alert--danger">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Compteurs --}}
    <div class="cs-counters">
        <div class="cs-counter cs-counter--accent">
            <span class="cs-counter__val">{{ $equipes->count() }}</span>
            <span class="cs-counter__label">Total équipes</span>
        </div>
        <div class="cs-counter">
            <span class="cs-counter__val">{{ $equipes->where('genre', 'masculin')->count() }}</span>
            <span class="cs-counter__label">Masculines</span>
        </div>
        <div class="cs-counter">
            <span class="cs-counter__val">{{ $equipes->where('genre', 'feminin')->count() }}</span>
            <span class="cs-counter__label">Féminines</span>
        </div>
        <div class="cs-counter">
            <span class="cs-counter__val">{{ $equipes->where('genre', 'mixte')->count() }}</span>
            <span class="cs-counter__label">Mixtes</span>
        </div>
        <div class="cs-counter">
            <span class="cs-counter__val">{{ $club->joueurs()->count() }}</span>
            <span class="cs-counter__label">Joueurs total</span>
        </div>
    </div>

    {{-- Grid équipes --}}
    @if($equipes->isEmpty())
        <div class="cs-empty">
            <svg width="52" height="52" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            <h3>Aucune équipe active</h3>
            <p>Créez votre première équipe pour commencer à gérer vos joueurs.</p>
            <a href="{{ route('club.equipes.create') }}" class="cs-btn cs-btn--primary" style="margin-top:4px;">
                Créer une équipe
            </a>
        </div>
    @else
        <div class="cs-equipes-grid">
            @foreach($equipes as $equipe)
            <div class="cs-equipe-card">

                {{-- Top --}}
                <div class="cs-equipe-card__top">
                    <div class="cs-equipe-card__header">
                        <div class="cs-equipe-card__icon">
                            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div class="cs-equipe-card__name">{{ $equipe->nom }}</div>
                        </div>
                    </div>

                    <div class="cs-equipe-card__badges">
                        {{-- Genre --}}
                        @php
                            $genreClass = match($equipe->genre) {
                                'masculin' => 'cs-tag--primary',
                                'feminin'  => 'cs-tag--danger',
                                default    => 'cs-tag--neutral',
                            };
                        @endphp
                        <span class="cs-tag {{ $genreClass }}">{{ ucfirst($equipe->genre) }}</span>

                        {{-- Catégorie --}}
                        <span class="cs-tag cs-tag--orange">{{ ucfirst($equipe->categorie) }}</span>
                    </div>

                    @if($equipe->description)
                        <p class="cs-equipe-card__desc">{{ $equipe->description }}</p>
                    @endif
                </div>

                {{-- Stats effectif --}}
                <div class="cs-equipe-card__stats">
                    <div class="cs-equipe-card__stat">
                        <span class="cs-equipe-card__stat-val">{{ $equipe->joueurs->count() }}</span>
                        <span class="cs-equipe-card__stat-lbl">Joueurs</span>
                    </div>
                    <div class="cs-equipe-card__stat">
                        <span class="cs-equipe-card__stat-val">{{ $equipe->joueurs->where('actif', true)->count() }}</span>
                        <span class="cs-equipe-card__stat-lbl">Actifs</span>
                    </div>
                    <div class="cs-equipe-card__stat">
                        @php
                            $avecLicence = $equipe->joueurs->filter(fn($j) => $j->licenceActive)->count();
                        @endphp
                        <span class="cs-equipe-card__stat-val">{{ $avecLicence }}</span>
                        <span class="cs-equipe-card__stat-lbl">Licenciés</span>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="cs-equipe-card__footer">
                    <div class="cs-equipe-card__status">
                        <span class="cs-equipe-card__status-dot cs-equipe-card__status-dot--active"></span>
                        Active
                    </div>
                    <div class="cs-equipe-card__actions">
                        {{-- Modifier --}}
                        <a href="{{ route('club.equipes.edit', $equipe) }}"
                           class="cs-btn--icon" title="Modifier">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </a>
                        {{-- Désactiver --}}
                        <button type="button"
                                class="cs-btn--icon cs-btn--icon--danger"
                                title="Désactiver"
                                onclick="openDeleteModal({{ $equipe->id }}, '{{ addslashes($equipe->nom) }}', {{ $equipe->joueurs->count() }})">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                <path d="M10 11v6M14 11v6"/>
                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

</div>

{{-- Modal désactivation --}}
<div class="cs-modal-overlay" id="deleteModal">
    <div class="cs-modal">
        <div class="cs-modal__header">
            <h4 class="cs-modal__title">Désactiver l'équipe</h4>
            <button class="cs-modal__close" onclick="closeDeleteModal()">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="cs-modal__body">
            <p>Êtes-vous sûr de vouloir désactiver l'équipe <strong id="deleteEquipeNom"></strong> ?</p>
            <p id="deleteEquipeWarn" style="display:none;color:#d97706;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:4px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Cette équipe contient des joueurs. Ils ne seront pas supprimés mais resteront sans équipe.
            </p>
            <p style="margin-top:8px;">Cette action ne supprime pas les joueurs.</p>
        </div>
        <div class="cs-modal__footer">
            <button type="button" class="cs-btn cs-btn--sm" style="background:transparent;border:1px solid var(--border);color:var(--text3);border-radius:20px;" onclick="closeDeleteModal()">Annuler</button>
            <form id="deleteForm" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="cs-btn cs-btn--sm" style="background:#ef4444;color:#fff;border-radius:20px;">Désactiver</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openDeleteModal(id, nom, nbJoueurs) {
    document.getElementById('deleteEquipeNom').textContent = nom;
    document.getElementById('deleteForm').action = `/club/equipes/${id}`;
    document.getElementById('deleteEquipeWarn').style.display = nbJoueurs > 0 ? 'block' : 'none';
    document.getElementById('deleteModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
    document.body.style.overflow = '';
}
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endpush
@endsection