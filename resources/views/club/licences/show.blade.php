@extends('layouts.app')
@section('title', 'Licence — ' . $licence->joueur->nomComplet())

@section('content')
@php
    $expiree       = $licence->estExpiree();
    $joursRestants = $licence->joursRestants();
    $badge = $expiree
        ? ['class' => 'bg-danger',               'label' => 'Expirée']
        : ($joursRestants <= 30
            ? ['class' => 'bg-warning text-dark', 'label' => 'Expire bientôt']
            : ['class' => 'bg-success',           'label' => 'Active']);
@endphp

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- En-tête --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <a href="{{ route('club.licences.index') }}"
                       class="text-muted small text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i> Retour aux licences
                    </a>
                    <h1 class="h3 fw-bold mb-0 mt-1">Détail de la licence</h1>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('club.licences.download', $licence) }}"
                       class="btn btn-outline-info rounded-pill btn-sm">
                        <i class="bi bi-download me-1"></i> Télécharger PDF
                    </a>
                    @if($expiree || $joursRestants <= 60)
                        <button type="button"
                                class="btn btn-warning rounded-pill btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalRenouveler">
                            <i class="bi bi-arrow-repeat me-1"></i> Renouveler
                        </button>
                    @endif
                </div>
            </div>

            {{-- Toast zone --}}
            <div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index:9999;"></div>

            {{-- Carte principale --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">

                    {{-- Joueur --}}
                    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                             style="width:52px;height:52px;font-size:16px;">
                            {{ strtoupper(substr($licence->joueur->user->prenom ?? '?', 0, 1)) }}{{ strtoupper(substr($licence->joueur->user->name ?? '', 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold fs-5">{{ $licence->joueur->nomComplet() }}</div>
                            <div class="text-muted small">
                                {{ $licence->joueur->poste ?? '—' }}
                                · {{ ucfirst($licence->joueur->categorie ?? '—') }}
                            </div>
                        </div>
                        <span class="badge {{ $badge['class'] }} rounded-pill ms-auto fs-6"
                              id="statut-badge">
                            {{ $badge['label'] }}
                        </span>
                    </div>

                    {{-- Infos licence --}}
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="text-muted small fw-semibold mb-1">N° de licence</p>
                            <p class="fw-semibold mb-0">{{ $licence->numero_licence ?? '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small fw-semibold mb-1">Catégorie</p>
                            <p class="fw-semibold mb-0">{{ ucfirst($licence->categorie) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small fw-semibold mb-1">Date de début</p>
                            <p class="fw-semibold mb-0" id="date-debut">
                                {{ $licence->date_debut->format('d/m/Y') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small fw-semibold mb-1">Date d'expiration</p>
                            <p class="fw-semibold mb-0" id="date-expiration">
                                {{ $licence->date_expiration->format('d/m/Y') }}
                                @if(!$expiree && $joursRestants <= 30)
                                    <span class="badge bg-warning text-dark ms-1 rounded-pill"
                                          id="jours-badge">
                                        {{ $joursRestants }}j restants
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small fw-semibold mb-1">Statut actif</p>
                            <p class="fw-semibold mb-0">
                                {{ $licence->active ? 'Oui' : 'Non' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small fw-semibold mb-1">Club</p>
                            <p class="fw-semibold mb-0">{{ $licence->club->nom ?? '—' }}</p>
                        </div>
                    </div>

                    {{-- Note interne --}}
                    @if($licence->note)
                        <div class="mt-4 pt-3 border-top">
                            <p class="text-muted small fw-semibold mb-2">Note interne</p>
                            <div class="bg-light rounded-3 p-3 small">
                                {!! nl2br(e($licence->note)) !!}
                            </div>
                        </div>
                    @endif

                    {{-- Fichier PDF --}}
                    <div class="mt-4 pt-3 border-top">
                        <p class="text-muted small fw-semibold mb-2">Fichier PDF</p>
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-file-earmark-pdf text-danger fs-2"></i>
                            <div>
                                <div class="fw-semibold small">Licence officielle</div>
                                <a href="{{ route('club.licences.download', $licence) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-outline-danger rounded-pill mt-1">
                                    <i class="bi bi-eye me-1"></i> Ouvrir
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Actions bas de page --}}
            <div class="d-flex justify-content-between">
                <a href="{{ route('club.licences.index') }}"
                   class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Retour
                </a>
                <button type="button"
                        class="btn btn-outline-danger rounded-pill"
                        data-bs-toggle="modal"
                        data-bs-target="#modalSupprimer">
                    <i class="bi bi-trash me-1"></i> Supprimer
                </button>
            </div>

        </div>
    </div>
</div>

{{-- Modal renouvellement --}}
<div class="modal fade" id="modalRenouveler" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-arrow-repeat text-warning me-2"></i>
                    Renouveler la licence
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">
                    Joueur : <strong>{{ $licence->joueur->nomComplet() }}</strong>
                </p>
                <div id="renouveler-erreurs" class="alert alert-danger rounded-3 d-none small"></div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Nouvelle date de début <span class="text-danger">*</span></label>
                        <input type="date" id="new-date-debut" class="form-control rounded-3"
                               value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Nouvelle date d'expiration <span class="text-danger">*</span></label>
                        <input type="date" id="new-date-expiration" class="form-control rounded-3"
                               value="{{ now()->addYear()->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">
                            Nouveau fichier PDF <span class="text-danger">*</span>
                        </label>
                        <input type="file" id="new-fichier-pdf" class="form-control rounded-3"
                               accept=".pdf" required>
                        <div class="form-text">Max. 5 Mo, format PDF uniquement.</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill"
                        data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-warning rounded-pill px-4"
                        id="btn-confirmer-renouvellement">
                    <span id="spinner-renouv" class="spinner-border spinner-border-sm me-1 d-none"></span>
                    Renouveler
                </button>
            </div>
        </div>
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
                    <strong>{{ $licence->joueur->nomComplet() }}</strong> ?
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
    // ── Renouvellement ──
    document.getElementById('btn-confirmer-renouvellement').addEventListener('click', async function () {
        const dateDebut     = document.getElementById('new-date-debut').value;
        const dateExp       = document.getElementById('new-date-expiration').value;
        const fichier       = document.getElementById('new-fichier-pdf').files[0];
        const erreurs       = document.getElementById('renouveler-erreurs');
        const spinner       = document.getElementById('spinner-renouv');

        erreurs.classList.add('d-none');
        erreurs.textContent = '';

        // Validation front
        if (!dateDebut || !dateExp || !fichier) {
            erreurs.textContent = 'Tous les champs sont obligatoires.';
            erreurs.classList.remove('d-none');
            return;
        }
        if (dateExp <= dateDebut) {
            erreurs.textContent = 'La date d\'expiration doit être après la date de début.';
            erreurs.classList.remove('d-none');
            return;
        }
        if (fichier.size > 5 * 1024 * 1024) {
            erreurs.textContent = 'Le fichier ne doit pas dépasser 5 Mo.';
            erreurs.classList.remove('d-none');
            return;
        }

        this.disabled = true;
        spinner.classList.remove('d-none');

        const formData = new FormData();
        formData.append('_token',          '{{ csrf_token() }}');
        formData.append('date_debut',      dateDebut);
        formData.append('date_expiration', dateExp);
        formData.append('fichier_pdf',     fichier);

        try {
            const res = await fetch('{{ route('club.licences.renouveler', $licence) }}', {
                method: 'POST',
                body:   formData,
            });

            const data = await res.json();

            if (!res.ok) {
                erreurs.textContent = data.message ?? 'Une erreur est survenue.';
                erreurs.classList.remove('d-none');
                return;
            }

            // Mettre à jour les infos affichées sans rechargement
            document.getElementById('date-debut').textContent      = data.date_debut;
            document.getElementById('date-expiration').textContent = data.date_expiration;

            const statutBadge = document.getElementById('statut-badge');
            statutBadge.textContent  = data.statut_label;
            statutBadge.className    = `badge ${data.statut_class} rounded-pill ms-auto fs-6`;

            const jours = document.getElementById('jours-badge');
            if (jours) jours.remove();

            bootstrap.Modal.getInstance(document.getElementById('modalRenouveler')).hide();
            afficherToast('Licence renouvelée avec succès.', 'success');

            // Masquer bouton renouveler si plus nécessaire
            const btnRenouv = document.querySelector('[data-bs-target="#modalRenouveler"]');
            if (btnRenouv) btnRenouv.remove();

        } catch (err) {
            erreurs.textContent = 'Erreur réseau. Veuillez réessayer.';
            erreurs.classList.remove('d-none');
        } finally {
            this.disabled = false;
            spinner.classList.add('d-none');
        }
    });

    // ── Suppression ──
    document.getElementById('btn-confirmer-suppression').addEventListener('click', async function () {
        const spinner = document.getElementById('spinner-suppr');
        this.disabled = true;
        spinner.classList.remove('d-none');

        try {
            const res = await fetch('{{ route('club.licences.destroy', $licence) }}', {
                method:  'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept':       'application/json',
                },
            });

            if (!res.ok) throw new Error();

            afficherToast('Licence supprimée.', 'success');
            setTimeout(() => window.location.href = '{{ route('club.licences.index') }}', 1500);

        } catch {
            afficherToast('Erreur lors de la suppression.', 'danger');
            this.disabled = false;
            spinner.classList.add('d-none');
        }
    });

    // ── Toast ──
    function afficherToast(message, type = 'success') {
        const id  = 'toast-' + Date.now();
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