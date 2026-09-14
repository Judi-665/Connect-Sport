@extends('layouts.app')
@section('title', 'Agenda — ' . $club->nom)

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- En-tête --}}
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Agenda</h1>
            <p class="text-muted small mb-0">Calendrier des matchs, entraînements et événements</p>
        </div>
        <a href="{{ route('club.agenda.create') }}" class="btn btn-warning rounded-pill">
            <i class="bi bi-plus-lg me-1"></i> Créer un événement
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filtres --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3 col-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Type</label>
                    <select name="type" class="form-select form-select-sm rounded-3">
                        <option value="">Tous</option>
                        <option value="match"        @selected(request('type') === 'match')>Match</option>
                        <option value="entrainement" @selected(request('type') === 'entrainement')>Entraînement</option>
                        <option value="evenement"    @selected(request('type') === 'evenement')>Événement</option>
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Visibilité</label>
                    <select name="visibilite" class="form-select form-select-sm rounded-3">
                        <option value="">Toutes</option>
                        <option value="public" @selected(request('visibilite') === 'public')>Public</option>
                        <option value="club"   @selected(request('visibilite') === 'club')>Club</option>
                        <option value="equipe" @selected(request('visibilite') === 'equipe')>Équipe</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1">Recherche</label>
                    <input type="text" name="search" class="form-control form-control-sm rounded-3"
                           placeholder="Titre, lieu, adversaire..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-warning btn-sm w-100 rounded-3 fw-semibold">
                        <i class="bi bi-funnel me-1"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Stats rapides --}}
    <div class="row g-3 mb-4">
        @php
            $totalAvenir     = $aVenir->count();
            $matchsAvenir    = $aVenir->where('type', 'match')->count();
            $entrainements   = $aVenir->where('type', 'entrainement')->count();
            $totalPasses     = $passes->count();
        @endphp
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center py-3">
                <i class="bi bi-calendar-check fs-3 mb-1" style="color:#1A56A0;"></i>
                <div class="fw-bold fs-4">{{ $totalAvenir }}</div>
                <div class="text-muted small">À venir</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center py-3">
                <i class="bi bi-trophy fs-3 mb-1" style="color:#F97316;"></i>
                <div class="fw-bold fs-4">{{ $matchsAvenir }}</div>
                <div class="text-muted small">Matchs à venir</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center py-3">
                <i class="bi bi-people fs-3 mb-1" style="color:#10b981;"></i>
                <div class="fw-bold fs-4">{{ $entrainements }}</div>
                <div class="text-muted small">Entraînements</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center py-3">
                <i class="bi bi-clock-history fs-3 mb-1" style="color:#8b5cf6;"></i>
                <div class="fw-bold fs-4">{{ $totalPasses }}</div>
                <div class="text-muted small">Passés</div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- Événements à venir --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-transparent border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                    <h2 class="h6 fw-bold mb-0">
                        <i class="bi bi-calendar-event me-2" style="color:#1A56A0;"></i>
                        Événements à venir
                    </h2>
                    <span class="badge bg-primary rounded-pill">{{ $totalAvenir }}</span>
                </div>

                <div id="liste-avenir">
                    @forelse($aVenir as $ev)
                        @include('club.agenda._ligne', ['evenement' => $ev])
                    @empty
                        <div class="text-center py-5" id="empty-avenir">
                            <i class="bi bi-calendar-x fs-1 text-muted d-block mb-2"></i>
                            <p class="text-muted mb-3">Aucun événement à venir.</p>
                            <a href="{{ route('club.agenda.create') }}"
                               class="btn btn-warning rounded-pill px-4">
                                <i class="bi bi-plus-lg me-1"></i> Créer un événement
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Événements passés --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-transparent border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                    <h2 class="h6 fw-bold mb-0">
                        <i class="bi bi-clock-history me-2 text-muted"></i>
                        Événements passés
                    </h2>
                    <span class="badge bg-secondary rounded-pill">{{ $totalPasses }}</span>
                </div>

                <div>
                    @forelse($passes as $ev)
                    @php
                        $typeIcon = match($ev->type) {
                            'match'        => 'bi-trophy',
                            'entrainement' => 'bi-people',
                            default        => 'bi-calendar-event',
                        };
                        $typeBadge = match($ev->type) {
                            'match'        => 'bg-warning text-dark',
                            'entrainement' => 'bg-success',
                            default        => 'bg-info',
                        };
                    @endphp
                    <div class="d-flex align-items-start gap-3 px-4 py-3 border-bottom">
                        {{-- Date --}}
                        <div class="text-center flex-shrink-0"
                             style="width:42px;">
                            <div class="fw-bold fs-5 lh-1">{{ $ev->debut_at->format('d') }}</div>
                            <div class="text-muted" style="font-size:10px;text-transform:uppercase;">
                                {{ $ev->debut_at->format('M') }}
                            </div>
                        </div>

                        {{-- Infos --}}
                        <div class="flex-grow-1 min-width-0">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge {{ $typeBadge }} rounded-pill"
                                      style="font-size:10px;">
                                    <i class="bi {{ $typeIcon }} me-1"></i>
                                    {{ ucfirst($ev->type) }}
                                </span>
                                @if($ev->type === 'match' && $ev->resultat)
                                    <span class="badge rounded-pill {{ $ev->resultat === 'victoire' ? 'bg-success' : ($ev->resultat === 'defaite' ? 'bg-danger' : 'bg-secondary') }}">
                                        {{ ucfirst($ev->resultat) }}
                                        @if(!is_null($ev->score_nous) && !is_null($ev->score_eux))
                                            {{ $ev->score_nous }} - {{ $ev->score_eux }}
                                        @endif
                                    </span>
                                @endif
                            </div>
                            <div class="fw-semibold small">{{ $ev->titre }}</div>
                            @if($ev->lieu)
                                <div class="text-muted" style="font-size:11px;">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $ev->lieu }}
                                </div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex gap-1 flex-shrink-0">
                            <a href="{{ route('club.agenda.show', $ev) }}"
                               class="btn btn-sm btn-outline-secondary rounded-2">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('club.agenda.edit', $ev) }}"
                               class="btn btn-sm btn-outline-warning rounded-2">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger rounded-2"
                                    data-action="supprimer"
                                    data-id="{{ $ev->id }}"
                                    data-titre="{{ $ev->titre }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <p class="text-muted small mb-0">Aucun événement passé.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Toast container --}}
<div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index:9999;"></div>

{{-- Modal suppression --}}
<div class="modal fade" id="modalSupprimer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Supprimer l'événement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer
                    <strong id="modal-ev-titre"></strong> ?
                </p>
                <p class="text-muted small mb-0">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill"
                        data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger rounded-pill px-4"
                        id="btn-confirmer-suppression">
                    <span id="spinner-suppr"
                          class="spinner-border spinner-border-sm me-1 d-none"></span>
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let evASupprimer = null;
    const modal = new bootstrap.Modal(document.getElementById('modalSupprimer'));

    // Ouvrir modal
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-action="supprimer"]');
        if (!btn) return;
        evASupprimer = btn.dataset.id;
        document.getElementById('modal-ev-titre').textContent = btn.dataset.titre;
        modal.show();
    });

    // Confirmer suppression
    document.getElementById('btn-confirmer-suppression').addEventListener('click', async function () {
        if (!evASupprimer) return;
        const spinner = document.getElementById('spinner-suppr');
        this.disabled = true;
        spinner.classList.remove('d-none');

        try {
            const res = await fetch(`/club/agenda/${evASupprimer}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            });

            if (!res.ok) throw new Error();

            // Supprimer la ligne
            const ligne = document.getElementById(`ev-${evASupprimer}`);
            if (ligne) ligne.remove();

            // Empty state si plus rien
            const liste = document.getElementById('liste-avenir');
            if (liste && liste.querySelectorAll('[id^="ev-"]').length === 0) {
                liste.innerHTML = `
                    <div class="text-center py-5" id="empty-avenir">
                        <i class="bi bi-calendar-x fs-1 text-muted d-block mb-2"></i>
                        <p class="text-muted mb-3">Aucun événement à venir.</p>
                        <a href="{{ route('club.agenda.create') }}"
                           class="btn btn-warning rounded-pill px-4">
                            <i class="bi bi-plus-lg me-1"></i> Créer un événement
                        </a>
                    </div>`;
            }

            modal.hide();
            afficherToast('Événement supprimé.', 'success');

        } catch {
            afficherToast('Erreur lors de la suppression.', 'danger');
        } finally {
            this.disabled = false;
            spinner.classList.add('d-none');
        }
    });

    function afficherToast(message, type = 'success') {
        const id   = 'toast-' + Date.now();
        const html = `
            <div id="${id}" class="toast align-items-center text-bg-${type} border-0 rounded-3 shadow"
                 role="alert" style="min-width:280px;">
                <div class="d-flex">
                    <div class="toast-body fw-semibold">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                            data-bs-dismiss="toast"></button>
                </div>
            </div>`;
        document.getElementById('toast-container').insertAdjacentHTML('beforeend', html);
        const el = document.getElementById(id);
        new bootstrap.Toast(el, { delay: 4000 }).show();
        el.addEventListener('hidden.bs.toast', () => el.remove());
    }
</script>
@endpush

@endsection