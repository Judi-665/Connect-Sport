@extends('layouts.app')
@section('title', 'Ajouter une licence — ' . $club->nom)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- En-tête --}}
            <div class="mb-4">
                <a href="{{ route('club.licences.index') }}"
                   class="text-muted small text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> Retour aux licences
                </a>
                <h1 class="h3 fw-bold mb-0 mt-1">Ajouter une licence</h1>
                <p class="text-muted small mt-1">Téléchargez le fichier PDF de la licence du joueur.</p>
            </div>

            {{-- Toast zone --}}
            <div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index:9999;"></div>

            {{-- Alerte erreurs serveur --}}
            <div id="form-erreurs" class="alert alert-danger rounded-3 d-none small mb-3"></div>

            {{-- Formulaire --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form id="form-licence" novalidate>
                        @csrf

                        {{-- Joueur --}}
                        <div class="mb-3">
                            <label for="joueur_id" class="form-label fw-semibold small">
                                Joueur <span class="text-danger">*</span>
                            </label>
                            <select id="joueur_id" name="joueur_id"
                                    class="form-select rounded-3" required>
                                <option value="">Sélectionner un joueur</option>
                                @foreach($joueurs as $joueur)
                                    <option value="{{ $joueur->id }}">
                                        {{ $joueur->nomComplet() }}
                                        ({{ ucfirst($joueur->categorie ?? '—') }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Veuillez sélectionner un joueur.</div>
                        </div>

                        {{-- Numéro licence --}}
                        <div class="mb-3">
                            <label for="numero_licence" class="form-label fw-semibold small">
                                Numéro de licence
                            </label>
                            <input type="text" id="numero_licence" name="numero_licence"
                                   class="form-control rounded-3"
                                   placeholder="Ex : FRA-12345">
                        </div>

                        {{-- Catégorie --}}
                        <div class="mb-3">
                            <label for="categorie" class="form-label fw-semibold small">
                                Catégorie <span class="text-danger">*</span>
                            </label>
                            <select id="categorie" name="categorie"
                                    class="form-select rounded-3" required>
                                <option value="">Sélectionner</option>
                                <option value="junior">Junior</option>
                                <option value="cadet">Cadet</option>
                                <option value="senior">Senior</option>
                                <option value="veteran">Vétéran / Loisir</option>
                            </select>
                            <div class="invalid-feedback">Veuillez sélectionner une catégorie.</div>
                        </div>

                        {{-- Dates --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="date_debut" class="form-label fw-semibold small">
                                    Date de début <span class="text-danger">*</span>
                                </label>
                                <input type="date" id="date_debut" name="date_debut"
                                       class="form-control rounded-3"
                                       value="{{ date('Y-m-d') }}" required>
                                <div class="invalid-feedback">Date de début requise.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="date_expiration" class="form-label fw-semibold small">
                                    Date d'expiration <span class="text-danger">*</span>
                                </label>
                                <input type="date" id="date_expiration" name="date_expiration"
                                       class="form-control rounded-3" required>
                                <div class="invalid-feedback">Date d'expiration requise.</div>
                            </div>
                        </div>

                        {{-- Fichier PDF --}}
                        <div class="mb-3">
                            <label for="fichier_pdf" class="form-label fw-semibold small">
                                Fichier PDF <span class="text-danger">*</span>
                            </label>
                            <input type="file" id="fichier_pdf" name="fichier_pdf"
                                   class="form-control rounded-3"
                                   accept=".pdf" required>
                            <div class="form-text">Max. 5 Mo, format PDF uniquement.</div>
                            <div class="invalid-feedback">Veuillez sélectionner un fichier PDF.</div>

                            {{-- Prévisualisation nom fichier --}}
                            <div id="fichier-info" class="d-none mt-2 d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark-pdf text-danger fs-5"></i>
                                <span id="fichier-nom" class="small fw-semibold"></span>
                                <span id="fichier-taille" class="text-muted small"></span>
                            </div>
                        </div>

                        {{-- Note --}}
                        <div class="mb-4">
                            <label for="note" class="form-label fw-semibold small">
                                Note interne <span class="text-muted fw-normal">(optionnel)</span>
                            </label>
                            <textarea id="note" name="note"
                                      class="form-control rounded-3" rows="3"
                                      placeholder="Informations complémentaires..."></textarea>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('club.licences.index') }}"
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
    // Prévisualisation fichier PDF
    document.getElementById('fichier_pdf').addEventListener('change', function () {
        const file = this.files[0];
        const info = document.getElementById('fichier-info');
        if (!file) { info.classList.add('d-none'); return; }
        document.getElementById('fichier-nom').textContent    = file.name;
        document.getElementById('fichier-taille').textContent = `(${(file.size / 1024 / 1024).toFixed(2)} Mo)`;
        info.classList.remove('d-none');
    });

    // Soumission fetch
    document.getElementById('form-licence').addEventListener('submit', async function (e) {
        e.preventDefault();

        const erreurs  = document.getElementById('form-erreurs');
        const spinner  = document.getElementById('spinner-submit');
        const icon     = document.getElementById('icon-submit');
        const btn      = document.getElementById('btn-soumettre');

        erreurs.classList.add('d-none');
        erreurs.innerHTML = '';

        // Validation front
        const joueurId    = document.getElementById('joueur_id').value;
        const categorie   = document.getElementById('categorie').value;
        const dateDebut   = document.getElementById('date_debut').value;
        const dateExp     = document.getElementById('date_expiration').value;
        const fichier     = document.getElementById('fichier_pdf').files[0];

        let valid = true;

        if (!joueurId) {
            document.getElementById('joueur_id').classList.add('is-invalid');
            valid = false;
        } else {
            document.getElementById('joueur_id').classList.remove('is-invalid');
        }

        if (!categorie) {
            document.getElementById('categorie').classList.add('is-invalid');
            valid = false;
        } else {
            document.getElementById('categorie').classList.remove('is-invalid');
        }

        if (!dateDebut) {
            document.getElementById('date_debut').classList.add('is-invalid');
            valid = false;
        } else {
            document.getElementById('date_debut').classList.remove('is-invalid');
        }

        if (!dateExp || dateExp <= dateDebut) {
            document.getElementById('date_expiration').classList.add('is-invalid');
            valid = false;
        } else {
            document.getElementById('date_expiration').classList.remove('is-invalid');
        }

        if (!fichier) {
            document.getElementById('fichier_pdf').classList.add('is-invalid');
            valid = false;
        } else if (fichier.size > 5 * 1024 * 1024) {
            erreurs.innerHTML = 'Le fichier PDF ne doit pas dépasser 5 Mo.';
            erreurs.classList.remove('d-none');
            valid = false;
        } else {
            document.getElementById('fichier_pdf').classList.remove('is-invalid');
        }

        if (!valid) return;

        btn.disabled = true;
        spinner.classList.remove('d-none');
        icon.classList.add('d-none');

        const formData = new FormData(this);

        try {
            const res  = await fetch('{{ route('club.licences.store') }}', {
                method: 'POST',
                body:   formData,
                headers: { 'Accept': 'application/json' },
            });

            const data = await res.json();

            if (!res.ok) {
                // Erreurs de validation Laravel
                if (data.errors) {
                    const msgs = Object.values(data.errors).flat();
                    erreurs.innerHTML = msgs.map(m => `<div>• ${m}</div>`).join('');
                } else {
                    erreurs.innerHTML = data.message ?? 'Une erreur est survenue.';
                }
                erreurs.classList.remove('d-none');
                return;
            }

            // Succès
            afficherToast('Licence ajoutée avec succès !', 'success');
            setTimeout(() => window.location.href = '{{ route('club.licences.index') }}', 1500);

        } catch (err) {
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