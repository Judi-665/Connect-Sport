@extends('layouts.app')
@section('title', 'Modifier — ' . $sponsor->nom)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="mb-4">
                <a href="{{ route('club.sponsors.show', $sponsor) }}"
                   class="text-muted small text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> Retour au sponsor
                </a>
                <h1 class="h3 fw-bold mb-0 mt-1">Modifier le sponsor</h1>
                <p class="text-muted small mt-1">{{ $sponsor->nom }}</p>
            </div>

            <div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index:9999;"></div>
            <div id="form-erreurs" class="alert alert-danger rounded-3 d-none small mb-3"></div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form id="form-sponsor" novalidate>
                        @csrf
                        @method('PUT')

                        {{-- Informations générales --}}
                        <p class="text-uppercase fw-bold text-muted mb-3"
                           style="font-size:.7rem;letter-spacing:2px;">
                            Informations générales
                        </p>

                        <div class="mb-3">
                            <label for="nom" class="form-label fw-semibold small">
                                Nom du sponsor <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="nom" name="nom"
                                   class="form-control rounded-3"
                                   value="{{ old('nom', $sponsor->nom) }}" required>
                            <div class="invalid-feedback">Ce champ est obligatoire.</div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="email_contact" class="form-label fw-semibold small">
                                    Email contact <span class="text-danger">*</span>
                                </label>
                                <input type="email" id="email_contact" name="email_contact"
                                       class="form-control rounded-3"
                                       value="{{ old('email_contact', $sponsor->email_contact) }}"
                                       required>
                                <div class="invalid-feedback">Email invalide.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="telephone_contact" class="form-label fw-semibold small">
                                    Téléphone <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="telephone_contact" name="telephone_contact"
                                       class="form-control rounded-3"
                                       value="{{ old('telephone_contact', $sponsor->telephone_contact) }}"
                                       required>
                                <div class="invalid-feedback">Ce champ est obligatoire.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="site_web" class="form-label fw-semibold small">Site web</label>
                            <input type="url" id="site_web" name="site_web"
                                   class="form-control rounded-3"
                                   value="{{ old('site_web', $sponsor->site_web) }}"
                                   placeholder="https://sponsor.com">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold small">
                                Présentation du sponsor
                            </label>
                            <textarea id="description" name="description"
                                      class="form-control rounded-3" rows="3">{{ old('description', $sponsor->description) }}</textarea>
                        </div>

                        {{-- Logo actuel --}}
                        <div class="mb-4">
                            <label for="logo" class="form-label fw-semibold small">Logo</label>
                            @if($sponsor->logo)
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <img src="{{ asset('storage/' . $sponsor->logo) }}"
                                         class="rounded-3 border object-fit-cover"
                                         style="width:48px;height:48px;" alt="Logo actuel">
                                    <span class="small text-muted">Logo actuel — uploader pour remplacer</span>
                                </div>
                            @endif
                            <input type="file" id="logo" name="logo"
                                   class="form-control rounded-3"
                                   accept=".jpg,.png,.svg">
                            <div class="form-text">JPG, PNG ou SVG — max 5 Mo.</div>
                            <div id="logo-info" class="d-none mt-2 d-flex align-items-center gap-2">
                                <i class="bi bi-image text-primary fs-5"></i>
                                <span id="logo-nom" class="small fw-semibold"></span>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Partenariat --}}
                        <p class="text-uppercase fw-bold text-muted mb-3"
                           style="font-size:.7rem;letter-spacing:2px;">
                            Conditions du partenariat
                        </p>

                        <div class="mb-3">
                            <label for="type_visibilite" class="form-label fw-semibold small">
                                Type de visibilité <span class="text-danger">*</span>
                            </label>
                            <select id="type_visibilite" name="type_visibilite"
                                    class="form-select rounded-3" required>
                                <option value="">Sélectionner</option>
                                <option value="logo"     @selected(old('type_visibilite', $sponsor->type_visibilite) === 'logo')>Logo sur maillot</option>
                                <option value="banniere" @selected(old('type_visibilite', $sponsor->type_visibilite) === 'banniere')>Pancarte / Bannière</option>
                                <option value="tous"     @selected(old('type_visibilite', $sponsor->type_visibilite) === 'tous')>Tous les supports</option>
                            </select>
                            <div class="invalid-feedback">Veuillez sélectionner un type.</div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="montant_contrat" class="form-label fw-semibold small">
                                    Montant du contrat
                                </label>
                                <input type="number" id="montant_contrat" name="montant_contrat"
                                       class="form-control rounded-3"
                                       value="{{ old('montant_contrat', $sponsor->montant_contrat) }}"
                                       min="0" step="0.01">
                            </div>
                            <div class="col-md-6">
                                <label for="devise" class="form-label fw-semibold small">
                                    Devise <span class="text-danger">*</span>
                                </label>
                                <select id="devise" name="devise"
                                        class="form-select rounded-3" required>
                                    <option value="XOF" @selected(old('devise', $sponsor->devise) === 'XOF')>XOF (FCFA)</option>
                                    <option value="EUR" @selected(old('devise', $sponsor->devise) === 'EUR')>EUR (€)</option>
                                    <option value="USD" @selected(old('devise', $sponsor->devise) === 'USD')>USD ($)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       id="montant_confidentiel" name="montant_confidentiel"
                                       value="1"
                                       {{ old('montant_confidentiel', $sponsor->montant_confidentiel) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="montant_confidentiel">
                                    Montant confidentiel
                                </label>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="debut_partenariat" class="form-label fw-semibold small">
                                    Début <span class="text-danger">*</span>
                                </label>
                                <input type="date" id="debut_partenariat" name="debut_partenariat"
                                       class="form-control rounded-3"
                                       value="{{ old('debut_partenariat', $sponsor->debut_partenariat->format('Y-m-d')) }}"
                                       required>
                                <div class="invalid-feedback">Date de début requise.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="fin_partenariat" class="form-label fw-semibold small">
                                    Fin <span class="text-danger">*</span>
                                </label>
                                <input type="date" id="fin_partenariat" name="fin_partenariat"
                                       class="form-control rounded-3"
                                       value="{{ old('fin_partenariat', $sponsor->fin_partenariat->format('Y-m-d')) }}"
                                       required>
                                <div class="invalid-feedback">Date de fin requise.</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       id="actif" name="actif" value="1"
                                       {{ old('actif', $sponsor->actif) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="actif">
                                    Partenariat actif
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('club.sponsors.show', $sponsor) }}"
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
    document.getElementById('logo').addEventListener('change', function () {
        const file = this.files[0];
        const info = document.getElementById('logo-info');
        if (!file) { info.classList.add('d-none'); return; }
        document.getElementById('logo-nom').textContent = file.name;
        info.classList.remove('d-none');
    });

    document.getElementById('form-sponsor').addEventListener('submit', async function (e) {
        e.preventDefault();

        const erreurs = document.getElementById('form-erreurs');
        const spinner = document.getElementById('spinner-submit');
        const icon    = document.getElementById('icon-submit');
        const btn     = document.getElementById('btn-soumettre');

        erreurs.classList.add('d-none');
        erreurs.innerHTML = '';

        const champs = ['nom', 'email_contact', 'telephone_contact',
                        'type_visibilite', 'devise',
                        'debut_partenariat', 'fin_partenariat'];
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

        const debut = document.getElementById('debut_partenariat').value;
        const fin   = document.getElementById('fin_partenariat').value;
        if (fin && debut && fin <= debut) {
            document.getElementById('fin_partenariat').classList.add('is-invalid');
            erreurs.innerHTML = 'La date de fin doit être après la date de début.';
            erreurs.classList.remove('d-none');
            valid = false;
        }

        if (!valid) return;

        btn.disabled = true;
        spinner.classList.remove('d-none');
        icon.classList.add('d-none');

        const formData = new FormData(this);
        formData.append('_method', 'PUT');

        try {
            const res  = await fetch('{{ route('club.sponsors.update', $sponsor) }}', {
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

            afficherToast('Sponsor mis à jour !', 'success');
            setTimeout(() => window.location.href = '{{ route('club.sponsors.show', $sponsor) }}', 1500);

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