@extends('layouts.app')
@section('title', 'Modifier le transfert')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="mb-4">
                <a href="{{ route('club.transferts.show', $transfert) }}"
                   class="text-muted small text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> Retour au transfert
                </a>
                <h1 class="h3 fw-bold mb-0 mt-1">Modifier le transfert</h1>
                <p class="text-muted small mt-1">
                    {{ $transfert->joueur->nomComplet() }}
                    — {{ $transfert->clubSource->nom }} →
                    {{ $transfert->clubDestinataire->nom }}
                </p>
            </div>

            <div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index:9999;"></div>
            <div id="form-erreurs" class="alert alert-danger rounded-3 d-none small mb-3"></div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form id="form-transfert" novalidate>
                        @csrf
                        @method('PUT')

                        {{-- Infos en lecture seule --}}
                        <p class="text-uppercase fw-bold text-muted mb-3"
                           style="font-size:.7rem;letter-spacing:2px;">
                            Informations du transfert
                        </p>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Joueur</label>
                                <div class="form-control bg-body-secondary border-0 rounded-3">
                                    {{ $transfert->joueur->nomComplet() }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Type</label>
                                <div class="form-control bg-body-secondary border-0 rounded-3">
                                    {{ ucfirst($transfert->type) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Club source</label>
                                <div class="form-control bg-body-secondary border-0 rounded-3">
                                    {{ $transfert->clubSource->nom }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Club destinataire</label>
                                <div class="form-control bg-body-secondary border-0 rounded-3">
                                    {{ $transfert->clubDestinataire->nom }}
                                </div>
                            </div>
                        </div>

                        <hr class="mb-4">

                        {{-- Notes modifiables --}}
                        <p class="text-uppercase fw-bold text-muted mb-3"
                           style="font-size:.7rem;letter-spacing:2px;">
                            Notes & Observations
                        </p>

                        <div class="mb-3">
                            <label for="note_joueur" class="form-label fw-semibold small">
                                Note joueur
                            </label>
                            <textarea id="note_joueur" name="note_joueur"
                                      class="form-control rounded-3" rows="3"
                                      placeholder="Observations concernant le joueur...">{{ old('note_joueur', $transfert->note_joueur) }}</textarea>
                        </div>

                        @if($transfert->club_source_id === $club->id)
                            <div class="mb-3">
                                <label for="note_club_source" class="form-label fw-semibold small">
                                    Note club source
                                </label>
                                <textarea id="note_club_source" name="note_club_source"
                                          class="form-control rounded-3" rows="3"
                                          placeholder="Observations du club source...">{{ old('note_club_source', $transfert->note_club_source) }}</textarea>
                            </div>
                        @endif

                        @if($transfert->club_destinataire_id === $club->id)
                            <div class="mb-3">
                                <label for="note_club_destinataire" class="form-label fw-semibold small">
                                    Note club destinataire
                                </label>
                                <textarea id="note_club_destinataire" name="note_club_destinataire"
                                          class="form-control rounded-3" rows="3"
                                          placeholder="Observations du club destinataire...">{{ old('note_club_destinataire', $transfert->note_club_destinataire) }}</textarea>
                            </div>
                        @endif

                        <hr class="mb-4">

                        {{-- Statut --}}
                        <p class="text-uppercase fw-bold text-muted mb-3"
                           style="font-size:.7rem;letter-spacing:2px;">
                            Statut du transfert
                        </p>

                        <div class="mb-4">
                            <label for="statut" class="form-label fw-semibold small">
                                Statut <span class="text-danger">*</span>
                            </label>
                            <select id="statut" name="statut"
                                    class="form-select rounded-3" required>
                                <option value="en_attente"    @selected(old('statut', $transfert->statut) === 'en_attente')>En attente</option>
                                <option value="en_negociation" @selected(old('statut', $transfert->statut) === 'en_negociation')>En négociation</option>
                                <option value="accepte"       @selected(old('statut', $transfert->statut) === 'accepte')>Accepté</option>
                                <option value="refuse"        @selected(old('statut', $transfert->statut) === 'refuse')>Refusé</option>
                                <option value="annule"        @selected(old('statut', $transfert->statut) === 'annule')>Annulé</option>
                            </select>
                            <div class="invalid-feedback">Veuillez sélectionner un statut.</div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('club.transferts.show', $transfert) }}"
                               class="btn btn-outline-secondary rounded-pill px-4">
                                Annuler
                            </a>
                            <button type="submit" id="btn-soumettre"
                                    class="btn btn-warning rounded-pill px-5 fw-semibold">
                                <span id="spinner-submit"
                                      class="spinner-border spinner-border-sm me-1 d-none"></span>
                                <i class="bi bi-check-lg me-1" id="icon-submit"></i>
                                Enregistrer
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('form-transfert').addEventListener('submit', async function (e) {
        e.preventDefault();

        const erreurs = document.getElementById('form-erreurs');
        const spinner = document.getElementById('spinner-submit');
        const icon    = document.getElementById('icon-submit');
        const btn     = document.getElementById('btn-soumettre');

        erreurs.classList.add('d-none');
        erreurs.innerHTML = '';

        const statut = document.getElementById('statut').value;
        if (!statut) {
            document.getElementById('statut').classList.add('is-invalid');
            return;
        }
        document.getElementById('statut').classList.remove('is-invalid');

        btn.disabled = true;
        spinner.classList.remove('d-none');
        icon.classList.add('d-none');

        const formData = new FormData(this);
        formData.append('_method', 'PUT');

        try {
            const res  = await fetch('{{ route('club.transferts.statut', $transfert) }}', {
                method:  'POST',
                body:    formData,
                headers: { 'Accept': 'application/json' },
            });

            const data = await res.json();

            if (!res.ok) {
                if (data.errors) {
                    erreurs.innerHTML = Object.values(data.errors).flat()
                        .map(m => `<div>• ${m}</div>`).join('');
                } else {
                    erreurs.innerHTML = data.message ?? 'Une erreur est survenue.';
                }
                erreurs.classList.remove('d-none');
                return;
            }

            afficherToast('Transfert mis à jour !', 'success');
            setTimeout(() => window.location.href = '{{ route('club.transferts.show', $transfert) }}', 1500);

        } catch {
            erreurs.innerHTML = 'Erreur réseau. Veuillez réessayer.';
            erreurs.classList.remove('d-none');
        } finally {
            btn.disabled = false;
            spinner.classList.add('d-none');
            icon.classList.remove('d-none');
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
        new bootstrap.Toast(el, { delay: 3000 }).show();
        el.addEventListener('hidden.bs.toast', () => el.remove());
    }
</script>
@endpush

@endsection