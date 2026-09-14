@extends('layouts.app')
@section('title', 'Licences — ' . $club->nom)

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- En-tête --}}
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Gestion des licences</h1>
            <p class="text-muted small mb-0" id="compteur-licences">
                {{ $licences->total() }} licence(s) enregistrée(s)
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('club.licences.export') }}"
               target="_blank"
               class="btn btn-outline-secondary rounded-pill">
                <i class="bi bi-filetype-pdf me-1"></i> Exporter PDF
            </a>
            <a href="{{ route('club.licences.create') }}" class="btn btn-warning rounded-pill">
                <i class="bi bi-plus-lg me-1"></i> Ajouter une licence
            </a>
        </div>
    </div>

    {{-- Alerte licences expirant bientôt --}}
    @if($expirentBientot->isNotEmpty())
        <div class="alert alert-warning alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>{{ $expirentBientot->count() }} licence(s) expirent dans les 30 jours :</strong>
            <ul class="mb-0 mt-2 ps-3">
                @foreach($expirentBientot as $l)
                    <li>
                        <strong>{{ $l->joueur->nomComplet() }}</strong>
                        — expire le {{ $l->date_expiration->format('d/m/Y') }}
                        <span class="badge bg-warning text-dark ms-1">{{ $l->joursRestants() }}j restants</span>
                    </li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Flash success --}}
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
                    <label class="form-label small fw-semibold text-muted mb-1">Catégorie</label>
                    <select name="categorie" class="form-select form-select-sm rounded-3">
                        <option value="">Toutes</option>
                        @foreach(['junior','cadet','senior','veteran'] as $cat)
                            <option value="{{ $cat }}" @selected(request('categorie') === $cat)>
                                {{ ucfirst($cat) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Statut</label>
                    <select name="statut" class="form-select form-select-sm rounded-3">
                        <option value="">Tous</option>
                        <option value="active"   @selected(request('statut') === 'active')>Active</option>
                        <option value="bientot"  @selected(request('statut') === 'bientot')>Expire bientôt</option>
                        <option value="expiree"  @selected(request('statut') === 'expiree')>Expirée</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1">Recherche</label>
                    <input type="text" name="search" class="form-control form-control-sm rounded-3"
                           placeholder="Nom du joueur..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-warning btn-sm w-100 rounded-3 fw-semibold">
                        <i class="bi bi-funnel me-1"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 py-3">Joueur</th>
                        <th>N° licence</th>
                        <th>Catégorie</th>
                        <th>Début</th>
                        <th>Expiration</th>
                        <th>Statut</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody id="licences-tbody">
                    @forelse($licences as $licence)
                        @include('club.licences._ligne', ['licence' => $licence])
                    @empty
                    <tr id="empty-row">
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-file-earmark-pdf fs-1 text-muted d-block mb-2"></i>
                            <p class="text-muted mb-3">Aucune licence enregistrée.</p>
                            <a href="{{ route('club.licences.create') }}" class="btn btn-warning rounded-pill px-4">
                                <i class="bi bi-plus-lg me-1"></i> Ajouter une licence
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($licences->hasPages())
            <div class="card-footer bg-transparent border-top py-3">
                {{ $licences->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal suppression --}}
<div class="modal fade" id="modalSupprimer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Supprimer la licence</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer la licence de
                    <strong id="modal-joueur-nom"></strong> ?
                </p>
                <p class="text-muted small mb-0">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill"
                        data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger rounded-pill px-4"
                        id="btn-confirmer-suppression">
                    <span id="spinner-suppr" class="spinner-border spinner-border-sm me-1 d-none"></span>
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let licenceASupprimer = null;
    const modal = new bootstrap.Modal(document.getElementById('modalSupprimer'));

    // Ouvrir modal suppression
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-action="supprimer"]');
        if (!btn) return;
        licenceASupprimer = btn.dataset.id;
        document.getElementById('modal-joueur-nom').textContent = btn.dataset.nom;
        modal.show();
    });

    // Confirmer suppression
    document.getElementById('btn-confirmer-suppression').addEventListener('click', async function () {
        if (!licenceASupprimer) return;

        const spinner = document.getElementById('spinner-suppr');
        this.disabled = true;
        spinner.classList.remove('d-none');

        try {
            const res = await fetch(`/club/licences/${licenceASupprimer}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            });

            if (!res.ok) throw new Error('Erreur serveur');

            // Supprimer la ligne du tableau
            const ligne = document.getElementById(`ligne-${licenceASupprimer}`);
            if (ligne) ligne.remove();

            // Mettre à jour le compteur
            const compteur = document.getElementById('compteur-licences');
            const match = compteur.textContent.match(/\d+/);
            if (match) {
                const n = parseInt(match[0]) - 1;
                compteur.textContent = `${n} licence(s) enregistrée(s)`;
            }

            // Afficher ligne vide si plus rien
            const tbody = document.getElementById('licences-tbody');
            if (tbody.querySelectorAll('tr:not(#empty-row)').length === 0) {
                tbody.innerHTML = `
                    <tr id="empty-row">
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-file-earmark-pdf fs-1 text-muted d-block mb-2"></i>
                            <p class="text-muted mb-3">Aucune licence enregistrée.</p>
                            <a href="{{ route('club.licences.create') }}" class="btn btn-warning rounded-pill px-4">
                                <i class="bi bi-plus-lg me-1"></i> Ajouter une licence
                            </a>
                        </td>
                    </tr>`;
            }

            modal.hide();
            afficherToast('Licence supprimée avec succès.', 'success');
        } catch (err) {
            afficherToast('Erreur lors de la suppression.', 'danger');
        } finally {
            this.disabled = false;
            spinner.classList.add('d-none');
        }
    });

    // Toast notification
    function afficherToast(message, type = 'success') {
        const id = 'toast-' + Date.now();
        const html = `
            <div id="${id}" class="toast align-items-center text-bg-${type} border-0 rounded-3 shadow"
                 role="alert" style="min-width:280px;">
                <div class="d-flex">
                    <div class="toast-body fw-semibold">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                            data-bs-dismiss="toast"></button>
                </div>
            </div>`;

        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'position-fixed top-0 end-0 p-3';
            container.style.zIndex = 9999;
            document.body.appendChild(container);
        }
        container.insertAdjacentHTML('beforeend', html);
        const toastEl = document.getElementById(id);
        const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
        toast.show();
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    }
</script>
@endpush

@endsection