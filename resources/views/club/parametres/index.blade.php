@extends('layouts.app')
@section('title', 'Paramètres — ' . optional($club)->nom)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            <div class="mb-4">
                <h1 class="h3 fw-bold mb-1">Paramètres</h1>
                <p class="text-muted small mb-0">Gérez votre profil club et votre compte.</p>
            </div>

            {{-- Alertes flash --}}
            @if(session('status') === 'profile-updated')
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>Profil mis à jour avec succès.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('status') === 'password-updated')
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>Mot de passe mis à jour.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Onglets --}}
            <ul class="nav nav-tabs mb-4" id="parametresTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-semibold" id="profil-tab"
                            data-bs-toggle="tab" data-bs-target="#profil"
                            type="button" role="tab">
                        <i class="bi bi-building me-2"></i>Profil du club
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="compte-tab"
                            data-bs-toggle="tab" data-bs-target="#compte"
                            type="button" role="tab">
                        <i class="bi bi-person me-2"></i>Mon compte
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold text-danger" id="danger-tab"
                            data-bs-toggle="tab" data-bs-target="#danger"
                            type="button" role="tab">
                        <i class="bi bi-exclamation-triangle me-2"></i>Zone dangereuse
                    </button>
                </li>
            </ul>

            <div class="tab-content">

                {{-- ── Onglet 1 : Profil du club ── --}}
                <div class="tab-pane fade show active" id="profil" role="tabpanel">

                    {{-- Infos enregistrées --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-transparent border-bottom py-3 px-4">
                            <h6 class="fw-bold mb-0">
                                <i class="bi bi-lock-fill text-muted me-2"></i>
                                Informations enregistrées à l'inscription
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-muted mb-1">Nom du club</label>
                                    <div class="form-control bg-body-secondary border-0 rounded-3 d-flex justify-content-between align-items-center">
                                        <span>{{ optional($club)->nom ?? '—' }}</span>
                                        <span class="badge bg-success-subtle text-success fw-semibold">✓ Enregistré</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-muted mb-1">Sport principal</label>
                                    <div class="form-control bg-body-secondary border-0 rounded-3">
                                        {{ optional(optional($club)->sport)->nom ?? '—' }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-muted mb-1">Abonnement</label>
                                    <div class="form-control bg-body-secondary border-0 rounded-3 d-flex justify-content-between align-items-center">
                                        <span>{{ ucfirst($planActif?->nom ?? $planActif?->slug ?? 'Gratuit') }}</span>
                                        @if($abonnementActif)
                                            <span class="badge bg-success-subtle text-success fw-semibold">✓ Actif</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary fw-semibold">Inactif</span>
                                        @endif
                                    </div>
                                    @if($abonnementActif?->fin_at)
                                        <div class="form-text">
                                            Expire le {{ $abonnementActif->fin_at->format('d/m/Y') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-muted mb-1">Ville</label>
                                    <div class="form-control bg-body-secondary border-0 rounded-3">
                                        {{ optional($club)->ville ?? '—' }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-muted mb-1">Pays</label>
                                    <div class="form-control bg-body-secondary border-0 rounded-3">
                                        {{ optional($club)->pays ?? '—' }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-muted mb-1">Téléphone</label>
                                    <div class="form-control bg-body-secondary border-0 rounded-3">
                                        {{ optional($club)->telephone ?? '—' }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-muted mb-1">Statut</label>
                                    <div class="form-control bg-body-secondary border-0 rounded-3 d-flex justify-content-between align-items-center">
                                        <span>{{ optional($club)->actif ? 'Actif' : 'Inactif' }}</span>
                                        <span class="badge {{ optional($club)->actif ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} fw-semibold">
                                            {{ optional($club)->actif ? '✓ Actif' : '✗ Inactif' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Infos à compléter --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-transparent border-bottom py-3 px-4">
                            <h6 class="fw-bold mb-0">
                                <i class="bi bi-pencil-square text-warning me-2"></i>
                                Informations à compléter
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="{{ route('club.parametres.profil') }}"
                                  enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="description" class="form-label fw-semibold small">
                                            Description du club
                                        </label>
                                        <textarea id="description" name="description"
                                                  class="form-control rounded-3 @error('description') is-invalid @enderror"
                                                  rows="4"
                                                  placeholder="Présentez votre club, son histoire, ses valeurs...">{{ old('description', optional($club)->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="adresse" class="form-label fw-semibold small">
                                            Adresse complète
                                        </label>
                                        <input type="text" id="adresse" name="adresse"
                                               class="form-control rounded-3 @error('adresse') is-invalid @enderror"
                                               placeholder="Ex : Quartier Zogbo, Cotonou"
                                               value="{{ old('adresse', optional($club)->adresse) }}">
                                        @error('adresse')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="stade" class="form-label fw-semibold small">Stade</label>
                                        <input type="text" id="stade" name="stade"
                                               class="form-control rounded-3 @error('stade') is-invalid @enderror"
                                               placeholder="Ex : Stade de l'Amitié"
                                               value="{{ old('stade', optional($club)->stade) }}">
                                        @error('stade')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="niveau" class="form-label fw-semibold small">
                                            Niveau de compétition
                                        </label>
                                        <select id="niveau" name="niveau"
                                                class="form-select rounded-3 @error('niveau') is-invalid @enderror">
                                            <option value="">Sélectionner</option>
                                            @foreach(['National','Régional','Départemental','Loisir'] as $niv)
                                                <option value="{{ $niv }}"
                                                        @selected(old('niveau', optional($club)->niveau) === $niv)>
                                                    {{ $niv }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('niveau')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="site_web" class="form-label fw-semibold small">
                                            Site web
                                        </label>
                                        <input type="url" id="site_web" name="site_web"
                                               class="form-control rounded-3 @error('site_web') is-invalid @enderror"
                                               placeholder="https://monclub.com"
                                               value="{{ old('site_web', optional($club)->site_web) }}">
                                        @error('site_web')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="telephone" class="form-label fw-semibold small">
                                            Téléphone
                                            <span class="text-muted fw-normal">(modifier)</span>
                                        </label>
                                        <input type="text" id="telephone" name="telephone"
                                               class="form-control rounded-3 @error('telephone') is-invalid @enderror"
                                               placeholder="Ex : +229 97000000"
                                               value="{{ old('telephone', optional($club)->telephone) }}">
                                        @error('telephone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Logo --}}
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Logo du club</label>
                                        @if(optional($club)->logo)
                                            <div class="d-flex align-items-center gap-3 mb-2">
                                                <img src="{{ asset('storage/' . $club->logo) }}"
                                                     class="rounded-3 border object-fit-cover"
                                                     style="width:52px;height:52px;"
                                                     alt="Logo actuel">
                                                <span class="small text-muted">
                                                    Logo actuel — uploader pour remplacer
                                                </span>
                                            </div>
                                        @endif
                                        <label for="logo"
                                               class="d-flex flex-column align-items-center justify-content-center gap-1 rounded-3 border border-dashed p-3 text-muted w-100"
                                               role="button">
                                            <i class="bi bi-cloud-upload fs-4"></i>
                                            <span id="logo-name" class="small">Cliquer pour uploader</span>
                                            <span class="text-muted" style="font-size:.72rem;">
                                                PNG, JPG, WEBP — max 2 Mo
                                            </span>
                                            <input type="file" id="logo" name="logo"
                                                   accept="image/*" class="d-none">
                                        </label>
                                        @error('logo')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <button type="submit" class="btn btn-warning rounded-pill px-5 fw-semibold">
                                        <i class="bi bi-check-lg me-1"></i> Enregistrer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

                {{-- ── Onglet 2 : Mon compte ── --}}
                <div class="tab-pane fade" id="compte" role="tabpanel">

                    {{-- Email --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-transparent border-bottom py-3 px-4">
                            <h6 class="fw-bold mb-0">
                                <i class="bi bi-envelope me-2 text-primary"></i>
                                Adresse email
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="{{ route('club.parametres.compte') }}">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="name" class="form-label fw-semibold small">Nom</label>
                                    <input type="text" id="name" name="name"
                                           class="form-control rounded-3 @error('name') is-invalid @enderror"
                                           value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="email" class="form-label fw-semibold small">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" id="email" name="email"
                                           class="form-control rounded-3 @error('email') is-invalid @enderror"
                                           value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-semibold">
                                        <i class="bi bi-check-lg me-1"></i> Mettre à jour
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Mot de passe --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-transparent border-bottom py-3 px-4">
                            <h6 class="fw-bold mb-0">
                                <i class="bi bi-lock me-2 text-warning"></i>
                                Mot de passe
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="{{ route('club.parametres.password') }}">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="current_password" class="form-label fw-semibold small">
                                        Mot de passe actuel <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" id="current_password"
                                           name="current_password"
                                           class="form-control rounded-3 @error('current_password') is-invalid @enderror"
                                           required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold small">
                                        Nouveau mot de passe <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" id="password" name="password"
                                           class="form-control rounded-3 @error('password') is-invalid @enderror"
                                           required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="password_confirmation" class="form-label fw-semibold small">
                                        Confirmer le mot de passe <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" id="password_confirmation"
                                           name="password_confirmation"
                                           class="form-control rounded-3" required>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-warning rounded-pill px-5 fw-semibold">
                                        <i class="bi bi-lock me-1"></i> Changer le mot de passe
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

                {{-- ── Onglet 3 : Zone dangereuse ── --}}
                <div class="tab-pane fade" id="danger" role="tabpanel">
                    <div class="card border-0 shadow-sm rounded-4 border-danger">
                        <div class="card-header bg-danger-subtle border-bottom py-3 px-4">
                            <h6 class="fw-bold mb-0 text-danger">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                Supprimer le compte
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <p class="text-muted small mb-4">
                                La suppression de votre compte est irréversible. Toutes les données
                                associées (club, joueurs, licences, médias, opportunités) seront
                                définitivement supprimées.
                            </p>
                            <button type="button"
                                    class="btn btn-danger rounded-pill px-4"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalSupprimerCompte">
                                <i class="bi bi-trash me-1"></i> Supprimer mon compte
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Modal suppression compte --}}
<div class="modal fade" id="modalSupprimerCompte" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Supprimer le compte
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">
                    Confirmez votre mot de passe pour supprimer définitivement votre compte.
                </p>
                <form method="POST" action="{{ route('club.parametres.destroy') }}"
                      id="form-suppression">
                    @csrf
                    @method('DELETE')
                    <div class="mb-3">
                        <label for="delete_password" class="form-label fw-semibold small">
                            Mot de passe <span class="text-danger">*</span>
                        </label>
                        <input type="password" id="delete_password" name="password"
                               class="form-control rounded-3
                               @error('password', 'userDeletion') is-invalid @enderror"
                               required>
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill"
                        data-bs-dismiss="modal">Annuler</button>
                <button type="submit" form="form-suppression"
                        class="btn btn-danger rounded-pill px-4">
                    <i class="bi bi-trash me-1"></i> Supprimer définitivement
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Prévisualisation logo
    document.getElementById('logo').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        document.getElementById('logo-name').textContent = file.name;
    });

    // Rouvrir le bon onglet après erreur/redirect
    const hash = window.location.hash;
    if (hash) {
        const tab = document.querySelector(`[data-bs-target="${hash}"]`);
        if (tab) new bootstrap.Tab(tab).show();
    }
</script>
@endpush

@endsection