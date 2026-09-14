@extends('layouts.app')
@section('title', 'Opportunités — ' . $club->nom)

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- En-tête --}}
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Opportunités</h1>
            <p class="text-muted small mb-0">Gérez vos offres de recrutement et opportunités publiées</p>
        </div>
        <a href="{{ route('club.opportunites.create') }}" class="btn btn-warning rounded-pill">
            <i class="bi bi-plus-lg me-1"></i> Publier une opportunité
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stats --}}
    @php
        $total      = $opportunites->total();
        $actives    = $club->opportunites()->where('active', true)->count();
        $expirees   = $club->opportunites()->where('date_limite', '<', now())->count();
        $enAvant    = $club->opportunites()->where('mise_en_avant', true)->count();
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center py-3">
                <i class="bi bi-megaphone fs-3 mb-1" style="color:#1A56A0;"></i>
                <div class="fw-bold fs-4">{{ $total }}</div>
                <div class="text-muted small">Total</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center py-3">
                <i class="bi bi-patch-check fs-3 mb-1" style="color:#10b981;"></i>
                <div class="fw-bold fs-4">{{ $actives }}</div>
                <div class="text-muted small">Actives</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center py-3">
                <i class="bi bi-clock-history fs-3 mb-1" style="color:#dc3545;"></i>
                <div class="fw-bold fs-4">{{ $expirees }}</div>
                <div class="text-muted small">Expirées</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center py-3">
                <i class="bi bi-star fs-3 mb-1" style="color:#F97316;"></i>
                <div class="fw-bold fs-4">{{ $enAvant }}</div>
                <div class="text-muted small">Mises en avant</div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3 col-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Type</label>
                    <select name="type" class="form-select form-select-sm rounded-3">
                        <option value="">Tous</option>
                        <option value="recrutement" @selected(request('type') === 'recrutement')>Recrutement</option>
                        <option value="selection"   @selected(request('type') === 'selection')>Sélection / Détection</option>
                        <option value="bourse"      @selected(request('type') === 'bourse')>Bourse / Soutien</option>
                        <option value="stage"       @selected(request('type') === 'stage')>Stage / Formation</option>
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Statut</label>
                    <select name="statut" class="form-select form-select-sm rounded-3">
                        <option value="">Tous</option>
                        <option value="active"  @selected(request('statut') === 'active')>Active</option>
                        <option value="expiree" @selected(request('statut') === 'expiree')>Expirée</option>
                        <option value="avant"   @selected(request('statut') === 'avant')>Mise en avant</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1">Recherche</label>
                    <input type="text" name="search" class="form-control form-control-sm rounded-3"
                           placeholder="Titre, poste, sport..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-warning btn-sm w-100 rounded-3 fw-semibold">
                        <i class="bi bi-funnel me-1"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Liste --}}
    @if($opportunites->isEmpty())
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5">
                <i class="bi bi-megaphone fs-1 text-muted d-block mb-3"></i>
                <p class="text-muted mb-3">Aucune opportunité publiée.</p>
                <a href="{{ route('club.opportunites.create') }}"
                   class="btn btn-warning rounded-pill px-4">
                    <i class="bi bi-plus-lg me-1"></i> Publier une opportunité
                </a>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3">Opportunité</th>
                            <th>Type</th>
                            <th>Lieu</th>
                            <th>Date limite</th>
                            <th>Places</th>
                            <th>Statut</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="opportunites-tbody">
                        @foreach($opportunites as $opp)
                            @include('club.opportunites._ligne', ['opportunite' => $opp])
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($opportunites->hasPages())
                <div class="card-footer bg-transparent border-top py-3">
                    {{ $opportunites->withQueryString()->links() }}
                </div>
            @endif
        </div>
    @endif

</div>

{{-- Toast container --}}
<div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index:9999;"></div>

{{-- Modal suppression --}}
<div class="modal fade" id="modalSupprimer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Supprimer l'opportunité</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer
                    <strong id="modal-opp-titre"></strong> ?
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
    let oppASupprimer = null;
    const modal = new bootstrap.Modal(document.getElementById('modalSupprimer'));

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-action="supprimer"]');
        if (!btn) return;
        oppASupprimer = btn.dataset.id;
        document.getElementById('modal-opp-titre').textContent = btn.dataset.titre;
        modal.show();
    });

    document.getElementById('btn-confirmer-suppression').addEventListener('click', async function () {
        if (!oppASupprimer) return;
        const spinner = document.getElementById('spinner-suppr');
        this.disabled = true;
        spinner.classList.remove('d-none');

        try {
            const res = await fetch(`/club/opportunites/${oppASupprimer}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            });

            if (!res.ok) throw new Error();

            document.getElementById(`opp-${oppASupprimer}`)?.remove();
            modal.hide();
            afficherToast('Opportunité supprimée.', 'success');

            const tbody = document.getElementById('opportunites-tbody');
            if (tbody && tbody.querySelectorAll('tr').length === 0) {
                tbody.closest('.card').outerHTML = `
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-megaphone fs-1 text-muted d-block mb-3"></i>
                            <p class="text-muted mb-3">Aucune opportunité publiée.</p>
                            <a href="{{ route('club.opportunites.create') }}"
                               class="btn btn-warning rounded-pill px-4">
                                <i class="bi bi-plus-lg me-1"></i> Publier une opportunité
                            </a>
                        </div>
                    </div>`;
            }

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