@extends('layouts.app')
@section('title', 'Uploader un média')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="mb-4">
                <a href="{{ route('club.medias.index') }}"
                   class="text-muted small text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i> Retour aux médias
                </a>
                <h1 class="h3 fw-bold mb-0 mt-1">Uploader un média</h1>
                <p class="text-muted small mt-1">Photo ou vidéo pour valoriser votre club et vos joueurs.</p>
            </div>

            <div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index:9999;"></div>
            <div id="form-erreurs" class="alert alert-danger rounded-3 d-none small mb-3"></div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form id="form-media" novalidate>
                        @csrf

                        {{-- Type --}}
                        <p class="text-uppercase fw-bold text-muted mb-3"
                           style="font-size:.7rem;letter-spacing:2px;">
                            Type de média
                        </p>

                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="type"
                                       id="type-photo" value="photo" checked>
                                <label class="btn btn-outline-primary w-100 rounded-3 py-3"
                                       for="type-photo">
                                    <i class="bi bi-image d-block fs-3 mb-1"></i>
                                    <span class="fw-semibold">Photo</span>
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="type"
                                       id="type-video" value="video">
                                <label class="btn btn-outline-warning w-100 rounded-3 py-3"
                                       for="type-video">
                                    <i class="bi bi-camera-video d-block fs-3 mb-1"></i>
                                    <span class="fw-semibold">Vidéo</span>
                                </label>
                            </div>
                        </div>

                        <hr class="mb-4">

                        {{-- Fichier --}}
                        <p class="text-uppercase fw-bold text-muted mb-3"
                           style="font-size:.7rem;letter-spacing:2px;">
                            Fichier
                        </p>

                        <div class="mb-3">
                            <label for="fichier" class="form-label fw-semibold small">
                                Fichier <span class="text-danger">*</span>
                            </label>
                            <input type="file" id="fichier" name="fichier"
                                   class="form-control rounded-3" accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp" required>
                            <div class="form-text" id="fichier-hint">
                                Photos : JPG, PNG, WEBP — max 10 Mo.
                                Vidéos : MP4, WEBM, MOV — max 100 Mo.
                            </div>
                            <div class="invalid-feedback">Veuillez sélectionner un fichier.</div>

                            {{-- Prévisualisation --}}
                            <div id="preview-wrap" class="d-none mt-3 text-center">
                                <img id="preview-img"
                                     class="rounded-3 border d-none"
                                     style="max-height:200px;max-width:100%;object-fit:contain;"
                                     alt="Aperçu">
                                <div id="preview-video" class="d-none">
                                    <i class="bi bi-camera-video text-warning fs-1 d-block mb-1"></i>
                                    <span id="preview-nom" class="small fw-semibold"></span>
                                    <span id="preview-taille" class="text-muted small ms-2"></span>
                                </div>
                            </div>
                        </div>

                        <hr class="mb-4">

                        {{-- Infos --}}
                        <p class="text-uppercase fw-bold text-muted mb-3"
                           style="font-size:.7rem;letter-spacing:2px;">
                            Informations
                        </p>

                        <div class="mb-3">
                            <label for="titre" class="form-label fw-semibold small">Titre</label>
                            <input type="text" id="titre" name="titre"
                                   class="form-control rounded-3"
                                   placeholder="Ex : But de la finale 2024">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold small">
                                Description
                            </label>
                            <textarea id="description" name="description"
                                      class="form-control rounded-3" rows="2"
                                      placeholder="Contexte, événement associé..."></textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="joueur_id" class="form-label fw-semibold small">
                                    Joueur associé
                                </label>
                                <select id="joueur_id" name="joueur_id"
                                        class="form-select rounded-3">
                                    <option value="">Aucun</option>
                                    @foreach($joueurs as $joueur)
                                        <option value="{{ $joueur->id }}">
                                            {{ $joueur->user->prenom ?? '' }}
                                            {{ $joueur->user->name ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="evenement_id" class="form-label fw-semibold small">
                                    Événement associé
                                </label>
                                <select id="evenement_id" name="evenement_id"
                                        class="form-select rounded-3">
                                    <option value="">Aucun</option>
                                    @foreach($evenements as $ev)
                                        <option value="{{ $ev->id }}">
                                            {{ $ev->titre }}
                                            ({{ $ev->debut_at->format('d/m/Y') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr class="mb-4">

                        {{-- Visibilité & Prix --}}
                        <p class="text-uppercase fw-bold text-muted mb-3"
                           style="font-size:.7rem;letter-spacing:2px;">
                            Visibilité & Accès
                        </p>

                        <div class="mb-3">
                            <label for="visibilite" class="form-label fw-semibold small">
                                Visibilité <span class="text-danger">*</span>
                            </label>
                            <select id="visibilite" name="visibilite"
                                    class="form-select rounded-3" required>
                                <option value="public">Public — visible par tous</option>
                                <option value="club">Club — membres uniquement</option>
                                <option value="premium">Premium — contenu payant</option>
                            </select>
                            <div class="invalid-feedback">Veuillez sélectionner la visibilité.</div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       id="payant" name="payant" value="1">
                                <label class="form-check-label small fw-semibold"
                                       for="payant">
                                    Contenu payant (séquence d'entraînement, formation...)
                                </label>
                            </div>
                        </div>

                        <div id="prix-wrap" class="mb-4 d-none">
                            <label for="prix" class="form-label fw-semibold small">
                                Prix (XOF) <span class="text-danger">*</span>
                            </label>
                            <input type="number" id="prix" name="prix"
                                   class="form-control rounded-3"
                                   placeholder="Ex : 2500" min="0" step="0.01">
                            <div class="invalid-feedback">Veuillez saisir un prix.</div>
                        </div>

                        {{-- Barre de progression --}}
                        <div id="progress-wrap" class="mb-4 d-none">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Upload en cours...</span>
                                <span id="progress-pct" class="fw-semibold">0%</span>
                            </div>
                            <div class="progress rounded-3" style="height:8px;">
                                <div id="progress-bar"
                                     class="progress-bar bg-warning progress-bar-striped progress-bar-animated"
                                     role="progressbar" style="width:0%"></div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('club.medias.index') }}"
                               class="btn btn-outline-secondary rounded-pill px-4">
                                Annuler
                            </a>
                            <button type="submit" id="btn-soumettre"
                                    class="btn btn-warning rounded-pill px-5 fw-semibold">
                                <span id="spinner-submit"
                                      class="spinner-border spinner-border-sm me-1 d-none"></span>
                                <i class="bi bi-cloud-upload me-1" id="icon-submit"></i>
                                Uploader
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
    // ── Afficher/masquer prix si payant ──
    document.getElementById('payant').addEventListener('change', function () {
        document.getElementById('prix-wrap').classList.toggle('d-none', !this.checked);
        if (!this.checked) document.getElementById('prix').value = '';
    });

    // ── Prévisualisation fichier ──
    const inputFichier = document.getElementById('fichier');
    const hintFichier = document.getElementById('fichier-hint');

    function actualiserTypesAcceptes() {
        const type = document.querySelector('input[name="type"]:checked').value;
        if (type === 'photo') {
            inputFichier.accept = 'image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp';
            hintFichier.textContent = 'Photos : JPG, PNG, WEBP — max 10 Mo. Vidéos : MP4, WEBM, MOV — max 100 Mo.';
        } else {
            inputFichier.accept = 'video/mp4,video/webm,video/quicktime,.mp4,.webm,.mov';
            hintFichier.textContent = 'Vidéos : MP4, WEBM, MOV — max 100 Mo. MP4 (H.264) est recommandé pour une lecture compatible avec la plupart des appareils.';
        }
    }

    document.querySelectorAll('input[name="type"]').forEach(radio => {
        radio.addEventListener('change', () => {
            inputFichier.value = '';
            document.getElementById('preview-wrap').classList.add('d-none');
            actualiserTypesAcceptes();
        });
    });

    inputFichier.addEventListener('change', function () {
        const file = this.files[0];
        const wrap = document.getElementById('preview-wrap');
        const img  = document.getElementById('preview-img');
        const vid  = document.getElementById('preview-video');

        if (!file) { wrap.classList.add('d-none'); return; }

        wrap.classList.remove('d-none');

        if (file.type.startsWith('image/')) {
            img.classList.remove('d-none');
            vid.classList.add('d-none');
            const reader = new FileReader();
            reader.onload = e => img.src = e.target.result;
            reader.readAsDataURL(file);
        } else {
            img.classList.add('d-none');
            vid.classList.remove('d-none');
            document.getElementById('preview-nom').textContent    = file.name;
            document.getElementById('preview-taille').textContent =
                `(${(file.size / 1024 / 1024).toFixed(2)} Mo)`;
        }
    });

    // ── Soumission avec progression XHR ──
    document.getElementById('form-media').addEventListener('submit', function (e) {
        e.preventDefault();

        const erreurs = document.getElementById('form-erreurs');
        const spinner = document.getElementById('spinner-submit');
        const icon    = document.getElementById('icon-submit');
        const btn     = document.getElementById('btn-soumettre');

        erreurs.classList.add('d-none');
        erreurs.innerHTML = '';

        // Validation front
        const fichier    = document.getElementById('fichier').files[0];
        const visibilite = document.getElementById('visibilite').value;
        const payant     = document.getElementById('payant').checked;
        const prix       = document.getElementById('prix').value;
        let valid        = true;

        if (!fichier) {
            document.getElementById('fichier').classList.add('is-invalid');
            valid = false;
        } else {
            document.getElementById('fichier').classList.remove('is-invalid');
        }

        if (!visibilite) {
            document.getElementById('visibilite').classList.add('is-invalid');
            valid = false;
        } else {
            document.getElementById('visibilite').classList.remove('is-invalid');
        }

        if (payant && !prix) {
            document.getElementById('prix').classList.add('is-invalid');
            valid = false;
        } else {
            document.getElementById('prix').classList.remove('is-invalid');
        }

        const maxMo = document.querySelector('input[name="type"]:checked').value === 'photo'
            ? 10 : 100;
        if (fichier && fichier.size > maxMo * 1024 * 1024) {
            erreurs.innerHTML = `Le fichier ne doit pas dépasser ${maxMo} Mo.`;
            erreurs.classList.remove('d-none');
            valid = false;
        }

        if (!valid) return;

        btn.disabled = true;
        spinner.classList.remove('d-none');
        icon.classList.add('d-none');

        // XHR pour la barre de progression
        const xhr      = new XMLHttpRequest();
        const formData = new FormData(this);
        const progWrap = document.getElementById('progress-wrap');
        const progBar  = document.getElementById('progress-bar');
        const progPct  = document.getElementById('progress-pct');

        progWrap.classList.remove('d-none');

        xhr.upload.addEventListener('progress', function (e) {
            if (!e.lengthComputable) return;
            const pct = Math.round((e.loaded / e.total) * 100);
            progBar.style.width = pct + '%';
            progPct.textContent = pct + '%';
        });

        xhr.addEventListener('load', function () {
            if (xhr.status === 201) {
                afficherToast('Média uploadé avec succès !', 'success');
                setTimeout(() => window.location.href = '{{ route('club.medias.index') }}', 1500);
            } else {
                try {
                    const data = JSON.parse(xhr.responseText);
                    if (data.errors) {
                        erreurs.innerHTML = Object.values(data.errors).flat()
                            .map(m => `<div>• ${m}</div>`).join('');
                    } else {
                        erreurs.innerHTML = data.message ?? 'Une erreur est survenue.';
                    }
                } catch {
                    erreurs.innerHTML = 'Une erreur est survenue.';
                }
                erreurs.classList.remove('d-none');
                progWrap.classList.add('d-none');
                btn.disabled = false;
                spinner.classList.add('d-none');
                icon.classList.remove('d-none');
            }
        });

        xhr.addEventListener('error', function () {
            erreurs.innerHTML = 'Erreur réseau. Veuillez réessayer.';
            erreurs.classList.remove('d-none');
            progWrap.classList.add('d-none');
            btn.disabled = false;
            spinner.classList.add('d-none');
            icon.classList.remove('d-none');
        });

        xhr.open('POST', '{{ route('club.medias.store') }}');
        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.send(formData);
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