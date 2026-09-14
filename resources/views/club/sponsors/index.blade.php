@extends('layouts.app')
@section('title', 'Sponsors — ' . $club->nom)

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- En-tête --}}
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Partenaires & Sponsors</h1>
            <p class="text-muted small mb-0">Gérez les partenariats et la visibilité de votre club</p>
        </div>
        <a href="{{ route('club.sponsors.create') }}" class="btn btn-warning rounded-pill">
            <i class="bi bi-plus-lg me-1"></i> Ajouter un sponsor
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Alertes expiration --}}
    @if($stats['expirient'] > 0)
        <div class="alert alert-warning alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>{{ $stats['expirient'] }} partenariat(s)</strong> expirent dans les 30 prochains jours.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-center py-3">
                <i class="bi bi-handshake fs-3 mb-1" style="color:#1A56A0;"></i>
                <div class="fw-bold fs-4">{{ $stats['total'] }}</div>
                <div class="text-muted small">Total sponsors</div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-center py-3">
                <i class="bi bi-patch-check fs-3 mb-1" style="color:#10b981;"></i>
                <div class="fw-bold fs-4">{{ $stats['actifs'] }}</div>
                <div class="text-muted small">Actifs</div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-center py-3">
                <i class="bi bi-clock-history fs-3 mb-1" style="color:#F97316;"></i>
                <div class="fw-bold fs-4">{{ $stats['expirient'] }}</div>
                <div class="text-muted small">Expirent bientôt</div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3 col-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Visibilité</label>
                    <select name="visibilite" class="form-select form-select-sm rounded-3">
                        <option value="">Toutes</option>
                        <option value="logo"    @selected(request('visibilite') === 'logo')>Logo maillot</option>
                        <option value="banniere" @selected(request('visibilite') === 'banniere')>Pancarte/Bannière</option>
                        <option value="tous"    @selected(request('visibilite') === 'tous')>Tous supports</option>
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Statut</label>
                    <select name="statut" class="form-select form-select-sm rounded-3">
                        <option value="">Tous</option>
                        <option value="actif"   @selected(request('statut') === 'actif')>Actif</option>
                        <option value="expire"  @selected(request('statut') === 'expire')>Expiré</option>
                        <option value="bientot" @selected(request('statut') === 'bientot')>Expire bientôt</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1">Recherche</label>
                    <input type="text" name="search" class="form-control form-control-sm rounded-3"
                           placeholder="Nom du sponsor..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-warning btn-sm w-100 rounded-3 fw-semibold">
                        <i class="bi bi-funnel me-1"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Liste sponsors --}}
    @if($sponsors->isEmpty())
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5">
                <i class="bi bi-handshake fs-1 text-muted d-block mb-3"></i>
                <p class="text-muted mb-3">Aucun sponsor enregistré.</p>
                <a href="{{ route('club.sponsors.create') }}" class="btn btn-warning rounded-pill px-4">
                    <i class="bi bi-plus-lg me-1"></i> Ajouter un sponsor
                </a>
            </div>
        </div>
    @else
        <div class="row g-3" id="sponsors-liste">
            @foreach($sponsors as $sponsor)
                @include('club.sponsors._carte', ['sponsor' => $sponsor])
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($sponsors->hasPages())
            <div class="mt-4">
                {{ $sponsors->withQueryString()->links() }}
            </div>
        @endif
    @endif

</div>

{{-- Toast container --}}
<div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index:9999;"></div>

{{-- Modal suppression --}}
<div class="modal fade" id="modalSupprimer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Supprimer le sponsor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer
                    <strong id="modal-sponsor-nom"></strong> ?
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
    let sponsorASupprimer = null;
    const modal = new bootstrap.Modal(document.getElementById('modalSupprimer'));

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-action="supprimer"]');
        if (!btn) return;
        sponsorASupprimer = btn.dataset.id;
        document.getElementById('modal-sponsor-nom').textContent = btn.dataset.nom;
        modal.show();
    });

    document.getElementById('btn-confirmer-suppression').addEventListener('click', async function () {
        if (!sponsorASupprimer) return;
        const spinner = document.getElementById('spinner-suppr');
        this.disabled = true;
        spinner.classList.remove('d-none');

        try {
            const res = await fetch(`/club/sponsors/${sponsorASupprimer}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            });

            if (!res.ok) throw new Error();

            const carte = document.getElementById(`sponsor-${sponsorASupprimer}`);
            if (carte) carte.remove();

            modal.hide();
            afficherToast('Sponsor supprimé avec succès.', 'success');

            // Empty state si plus rien
            const liste = document.getElementById('sponsors-liste');
            if (liste && liste.children.length === 0) {
                liste.outerHTML = `
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-handshake fs-1 text-muted d-block mb-3"></i>
                            <p class="text-muted mb-3">Aucun sponsor enregistré.</p>
                            <a href="{{ route('club.sponsors.create') }}"
                               class="btn btn-warning rounded-pill px-4">
                                <i class="bi bi-plus-lg me-1"></i> Ajouter un sponsor
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