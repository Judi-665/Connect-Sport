@extends('layouts.app')
@section('title', 'Publier une opportunité')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="mb-4">
                <a href="{{ route('club.opportunites.index') }}"
                   class="text-muted small text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> Retour aux opportunités
                </a>
                <h1 class="h3 fw-bold mb-0 mt-1">Publier une opportunité</h1>
                <p class="text-muted small mt-1">Recrutement, sélection, bourse ou stage.</p>
            </div>

            <div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index:9999;"></div>
            <div id="form-erreurs" class="alert alert-danger rounded-3 d-none small mb-3"></div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form id="form-opportunite" novalidate>
                        @csrf

                        {{-- Infos générales --}}
                        <p class="text-uppercase fw-bold text-muted mb-3"
                           style="font-size:.7rem;letter-spacing:2px;">
                            Informations générales
                        </p>

                        <div class="mb-3">
                            <label for="titre" class="form-label fw-semibold small">
                                Titre <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="titre" name="titre"
                                   class="form-control rounded-3"
                                   placeholder="Ex : Recherche attaquant senior" required>
                            <div class="invalid-feedback">Ce champ est obligatoire.</div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="type" class="form-label fw-semibold small">
                                    Type <span class="text-danger">*</span>
                                </label>
                                <select id="type" name="type"
                                        class="form-select rounded-3" required>
                                    <option value="">Sélectionner</option>
                                    <option value="recrutement">Recrutement</option>
                                    <option value="selection">Sélection / Détection</option>
                                    <option value="bourse">Bourse / Soutien</option>
                                    <option value="stage">Stage / Formation</option>
                                </select>
                                <div class="invalid-feedback">Veuillez sélectionner un type.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="sport_cible" class="form-label fw-semibold small">
                                    Sport <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="sport_cible" name="sport_cible"
                                       class="form-control rounded-3"
                                       placeholder="Ex : Football, Handball..." required>
                                <div class="invalid-feedback">Ce champ est obligatoire.</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold small">
                                Description <span class="text-danger">*</span>
                            </label>
                            <textarea id="description" name="description"
                                      class="form-control rounded-3" rows="4"
                                      placeholder="Décrivez l'opportunité, les critères, les avantages..."
                                      required></textarea>
                            <div class="invalid-feedback">Ce champ est obligatoire.</div>
                        </div>

                        <hr class="mb-4">

                        {{-- Cible --}}
                        <p class="text-uppercase fw-bold text-muted mb-3"
                           style="font-size:.7rem;letter-spacing:2px;">
                            Profil recherché
                        </p>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="categorie_cible" class="form-label fw-semibold small">
                                    Catégorie <span class="text-danger">*</span>
                                </label>
                                <select id="categorie_cible" name="categorie_cible"
                                        class="form-select rounded-3" required>
                                    <option value="">Sélectionner</option>
                                    <option value="junior">Junior</option>
                                    <option value="cadet">Cadet</option>
                                    <option value="senior">Senior</option>
                                    <option value="veteran">Vétéran</option>
                                    <option value="tous">Tous</option>
                                </select>
                                <div class="invalid-feedback">Veuillez sélectionner une catégorie.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="poste_cible" class="form-label fw-semibold small">
                                    Poste recherché
                                </label>
                                <input type="text" id="poste_cible" name="poste_cible"
                                       class="form-control rounded-3"
                                       placeholder="Ex : Gardien, Ailier...">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="places_disponibles" class="form-label fw-semibold small">
                                    Places disponibles
                                </label>
                                <input type="number" id="places_disponibles"
                                       name="places_disponibles"
                                       class="form-control rounded-3"
                                       placeholder="Ex : 3" min="1">
                            </div>
                            <div class="col-md-6">
                                <label for="date_limite" class="form-label fw-semibold small">
                                    Date limite <span class="text-danger">*</span>
                                </label>
                                <input type="date" id="date_limite" name="date_limite"
                                       class="form-control rounded-3"
                                       min="{{ date('Y-m-d') }}" required>
                                <div class="invalid-feedback">Date limite requise.</div>
                            </div>
                        </div>

                        <hr class="mb-4">

                        {{-- Localisation --}}
                        <p class="text-uppercase fw-bold text-muted mb-3"
                           style="font-size:.7rem;letter-spacing:2px;">
                            Localisation
                        </p>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="lieu" class="form-label fw-semibold small">
                                    Lieu <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="lieu" name="lieu"
                                       class="form-control rounded-3"
                                       placeholder="Ex : Cotonou" required>
                                <div class="invalid-feedback">Ce champ est obligatoire.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="pays" class="form-label fw-semibold small">
                                    Pays <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="pays" name="pays"
                                       class="form-control rounded-3"
                                       placeholder="Ex : Bénin" required>
                                <div class="invalid-feedback">Ce champ est obligatoire.</div>
                            </div>
                        </div>

                        <hr class="mb-4">

                        {{-- Budget --}}
                        <p class="text-uppercase fw-bold text-muted mb-3"
                           style="font-size:.7rem;letter-spacing:2px;">
                            Budget & Options
                        </p>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="budget" class="form-label fw-semibold small">
                                    Budget proposé
                                </label>
                                <input type="number" id="budget" name="budget"
                                       class="form-control rounded-3"
                                       placeholder="0" min="0" step="0.01">
                            </div>
                            <div class="col-md-6">
                                <label for="devise" class="form-label fw-semibold small">
                                    Devise <span class="text-danger">*</span>
                                </label>
                                <select id="devise" name="devise"
                                        class="form-select rounded-3" required>
                                    <option value="XOF" selected>XOF (FCFA)</option>
                                    <option value="EUR">EUR (€)</option>
                                    <option value="USD">USD ($)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       id="mise_en_avant" name="mise_en_avant" value="1">
                                <label class="form-check-label small fw-semibold"
                                       for="mise_en_avant">
                                    <i class="bi bi-star-fill text-warning me-1"></i>
                                    Mettre en avant cette opportunité
                                </label>
                                <div class="form-text">
                                    Apparaît en priorité dans les résultats de recherche.
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('club.opportunites.index') }}"
                               class="btn btn-outline-secondary rounded-pill px-4">
                                Annuler
                            </a>
                            <button type="submit" id="btn-soumettre"
                                    class="btn btn-warning rounded-pill px-5 fw-semibold">
                                <span id="spinner-submit"
                                      class="spinner-border spinner-border-sm me-1 d-none"></span>
                                <i class="bi bi-megaphone me-1" id="icon-submit"></i>
                                Publier
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
    document.getElementById('form-opportunite').addEventListener('submit', async function (e) {
        e.preventDefault();

        const erreurs = document.getElementById('form-erreurs');
        const spinner = document.getElementById('spinner-submit');
        const icon    = document.getElementById('icon-submit');
        const btn     = document.getElementById('btn-soumettre');

        erreurs.classList.add('d-none');
        erreurs.innerHTML = '';

        const champs = ['titre', 'type', 'sport_cible', 'description',
                        'categorie_cible', 'date_limite', 'lieu', 'pays', 'devise'];
        let valid = true;

        champs.forEach(id => {
            const el = document.getElementById(id);
            if (!el.value.trim()) {
                el.classList.add('is-invalid');
                valid = false;
            } else {
                el.classList.remove('is-invalid');
            }
        });

        if (!valid) return;

        btn.disabled = true;
        spinner.classList.remove('d-none');
        icon.classList.add('d-none');

        try {
            const res  = await fetch('{{ route('club.opportunites.store') }}', {
                method:  'POST',
                body:    new FormData(this),
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

            afficherToast('Opportunité publiée avec succès !', 'success');
            setTimeout(() => window.location.href = '{{ route('club.opportunites.index') }}', 1500);

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