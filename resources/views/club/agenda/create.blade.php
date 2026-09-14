@extends('layouts.app')
@section('title', 'Créer un événement — ' . $club->nom)

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('club.dashboard') }}" class="text-warning text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('club.agenda.index') }}" class="text-warning text-decoration-none">Agenda</a>
            </li>
            <li class="breadcrumb-item active">Créer un événement</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-bottom pt-4 pb-3 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:42px;height:42px;background:rgba(249,115,22,.12);">
                            <i class="bi bi-calendar-plus-fill text-warning fs-5"></i>
                        </div>
                        <div>
                            <h2 class="h5 fw-bold mb-0">Créer un événement</h2>
                            <p class="text-muted small mb-0">{{ $club->nom }}</p>
                        </div>
                    </div>
                </div>

                <div class="card-body px-4 py-4">
                    <form id="form-agenda" method="POST" action="{{ route('club.agenda.store') }}" novalidate>
                        @csrf

                        {{-- Titre --}}
                        <div class="mb-4">
                            <label for="titre" class="form-label fw-semibold">
                                Titre de l'événement <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   id="titre" name="titre"
                                   class="form-control rounded-3 @error('titre') is-invalid @enderror"
                                   value="{{ old('titre') }}"
                                   placeholder="Ex : Matchs de préparation, Entraînement intensif…"
                                   required
                                   maxlength="150">
                            @error('titre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Maximum 150 caractères.</div>
                        </div>

                        {{-- Type + Visibilité --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="type" class="form-label fw-semibold">
                                    Type d'événement <span class="text-danger">*</span>
                                </label>
                                <select id="type" name="type"
                                        class="form-select rounded-3 @error('type') is-invalid @enderror"
                                        required>
                                    <option value="" disabled @selected(!old('type'))>Choisir…</option>
                                    <option value="match" @selected(old('type') === 'match')>
                                        <i class="bi bi-trophy"></i> Match
                                    </option>
                                    <option value="entrainement" @selected(old('type') === 'entrainement')>
                                        <i class="bi bi-people"></i> Entraînement
                                    </option>
                                    <option value="evenement" @selected(old('type') === 'evenement')>
                                        <i class="bi bi-calendar-event"></i> Événement
                                    </option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="visibilite" class="form-label fw-semibold">
                                    Visibilité <span class="text-danger">*</span>
                                </label>
                                <select id="visibilite" name="visibilite"
                                        class="form-select rounded-3 @error('visibilite') is-invalid @enderror"
                                        required>
                                    <option value="" disabled @selected(!old('visibilite'))>Choisir…</option>
                                    <option value="public" @selected(old('visibilite') === 'public')>
                                        Public
                                    </option>
                                    <option value="club" @selected(old('visibilite') === 'club')>
                                        Club uniquement
                                    </option>
                                    <option value="equipe" @selected(old('visibilite') === 'equipe')>
                                        Équipe uniquement
                                    </option>
                                </select>
                                @error('visibilite')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Date et heure de début et fin --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="debut_at" class="form-label fw-semibold">
                                    Date et heure de début <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local"
                                       id="debut_at" name="debut_at"
                                       class="form-control rounded-3 @error('debut_at') is-invalid @enderror"
                                       value="{{ old('debut_at') }}"
                                       required>
                                @error('debut_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="fin_at" class="form-label fw-semibold">
                                    Date et heure de fin
                                    <span class="text-muted fw-normal">(optionnel)</span>
                                </label>
                                <input type="datetime-local"
                                       id="fin_at" name="fin_at"
                                       class="form-control rounded-3 @error('fin_at') is-invalid @enderror"
                                       value="{{ old('fin_at') }}">
                                @error('fin_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Doit être après la date de début.</div>
                            </div>
                        </div>

                        {{-- Lieu --}}
                        <div class="mb-4">
                            <label for="lieu" class="form-label fw-semibold">
                                Lieu
                                <span class="text-muted fw-normal">(optionnel)</span>
                            </label>
                            <input type="text"
                                   id="lieu" name="lieu"
                                   class="form-control rounded-3 @error('lieu') is-invalid @enderror"
                                   value="{{ old('lieu') }}"
                                   placeholder="Ex : Stade municipal, Gymnase…"
                                   maxlength="150">
                            @error('lieu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Maximum 150 caractères.</div>
                        </div>

                        {{-- Section Match (affichée seulement si type = match) --}}
                        <div id="section-match" class="border-top pt-4 mb-4 d-none">
                            <h5 class="h6 fw-bold mb-3">
                                <i class="bi bi-trophy me-2" style="color:#F97316;"></i>Infos du match
                            </h5>

                            {{-- Adversaire + Domicile/Extérieur --}}
                            <div class="row g-3 mb-4">
                                <div class="col-md-8">
                                    <label for="adversaire_nom" class="form-label fw-semibold">
                                        Adversaire
                                        <span class="text-muted fw-normal">(optionnel)</span>
                                    </label>
                                    <input type="text"
                                           id="adversaire_nom" name="adversaire_nom"
                                           class="form-control rounded-3 @error('adversaire_nom') is-invalid @enderror"
                                           value="{{ old('adversaire_nom') }}"
                                           placeholder="Nom du club adverse…"
                                           maxlength="120">
                                    @error('adversaire_nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Maximum 120 caractères.</div>
                                </div>

                                <div class="col-md-4">
                                    <label for="domicile_exterieur" class="form-label fw-semibold">
                                        Localisation
                                        <span class="text-muted fw-normal">(optionnel)</span>
                                    </label>
                                    <select id="domicile_exterieur" name="domicile_exterieur"
                                            class="form-select rounded-3 @error('domicile_exterieur') is-invalid @enderror">
                                        <option value="">Non défini</option>
                                        <option value="domicile" @selected(old('domicile_exterieur') === 'domicile')>
                                            Domicile
                                        </option>
                                        <option value="exterieur" @selected(old('domicile_exterieur') === 'exterieur')>
                                            Extérieur
                                        </option>
                                        <option value="neutre" @selected(old('domicile_exterieur') === 'neutre')>
                                            Terrain neutre
                                        </option>
                                    </select>
                                    @error('domicile_exterieur')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Description / Notes --}}
                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">
                                Description / Notes
                                <span class="text-muted fw-normal">(optionnel)</span>
                            </label>
                            <textarea id="description" name="description"
                                      class="form-control rounded-3 @error('description') is-invalid @enderror"
                                      rows="4"
                                      placeholder="Informations supplémentaires, consignes, objectifs…">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Vous pourrez éditer cet événement après sa création pour ajouter résultat et détails.</div>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('club.agenda.index') }}"
                               class="btn btn-outline-secondary rounded-pill px-4">
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-warning rounded-pill px-5 fw-semibold">
                                <i class="bi bi-plus-lg me-1"></i> Créer l'événement
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
    {{-- Affichage/masquage de la section match selon le type --}}
    document.getElementById('type').addEventListener('change', function() {
        const sectionMatch = document.getElementById('section-match');
        if (this.value === 'match') {
            sectionMatch.classList.remove('d-none');
        } else {
            sectionMatch.classList.add('d-none');
        }
    });

    {{-- Initialiser l'affichage si la page se recharge avec une valeur --}}
    if (document.getElementById('type').value === 'match') {
        document.getElementById('section-match').classList.remove('d-none');
    }

    {{-- Validation et soumission du formulaire --}}
    document.getElementById('form-agenda').addEventListener('submit', function(e) {
        e.preventDefault();

        const btn = this.querySelector('button[type="submit"]');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Création...';

        const formData = new FormData(this);

        {{-- Convertir datetime-local en format ISO pour le serveur --}}
        const debutInput = document.getElementById('debut_at').value;
        const finInput = document.getElementById('fin_at').value;

        if (debutInput) {
            formData.set('debut_at', debutInput.replace('T', ' '));
        }
        if (finInput) {
            formData.set('fin_at', finInput.replace('T', ' '));
        }

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.message) {
                afficherToast(data.message, 'success');
                setTimeout(() => {
                    window.location.href = '{{ route('club.agenda.index') }}';
                }, 1500);
            }
        })
        .catch(err => {
            afficherToast('Une erreur est survenue. Veuillez réessayer.', 'danger');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    });

    {{-- Fonction d'affichage de toast --}}
    function afficherToast(message, type = 'success') {
        const id   = 'toast-' + Date.now();
        const bgClass = type === 'success' ? 'text-bg-success' : 'text-bg-danger';
        const html = `
            <div id="${id}" class="toast align-items-center ${bgClass} border-0 rounded-3 shadow"
                 role="alert" style="min-width:280px;">
                <div class="d-flex">
                    <div class="toast-body fw-semibold">
                        <i class="bi ${type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                            data-bs-dismiss="toast"></button>
                </div>
            </div>`;

        if (!document.getElementById('toast-container')) {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }

        document.getElementById('toast-container').insertAdjacentHTML('beforeend', html);
        const el = document.getElementById(id);
        new bootstrap.Toast(el, { delay: 3000 }).show();
        el.addEventListener('hidden.bs.toast', () => el.remove());
    }
</script>
@endpush

@endsection
