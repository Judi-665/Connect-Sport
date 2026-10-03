{{-- resources/views/agents/edit.blade.php --}}
@extends('layouts.agent')

@section('title', 'Mon Profil Agent — Connect Sport')
@section('page-title', 'Mon Profil Agent')

@section('content')

@if(session('success'))
<div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
    @foreach($errors->all() as $e)
        <div><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $e }}</div>
    @endforeach
</div>
@endif

<div class="row g-4">
    {{-- Colonne gauche : Carte récapitulative & Photo --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 text-center p-4 mb-4" style="background:var(--card-bg);border:1px solid var(--border) !important;">
            <div class="position-relative mx-auto mb-3" style="width: 110px; height: 110px;">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar Agent" id="avatarPreview"
                         class="rounded-circle object-fit-cover w-100 h-100 shadow-sm border border-3 border-warning">
                @else
                    <div id="avatarFallback" class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm w-100 h-100"
                         style="background:#8b5cf6;font-size:36px;">
                        {{ strtoupper(substr($user->prenom ?? $user->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <h4 class="fw-bold mb-1" style="color:var(--text);">{{ $user->prenom }} {{ $user->name }}</h4>
            <p class="text-muted small mb-2">{{ $user->email }}</p>
            <div class="mb-3">
                <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-semibold">
                    <i class="bi bi-briefcase-fill me-1"></i> {{ $agent->agence }}
                </span>
            </div>

            <div class="d-flex justify-content-center gap-2 mb-3">
                @if($agent->verifie)
                    <span class="badge bg-success rounded-pill px-3 py-1">
                        <i class="bi bi-patch-check-fill me-1"></i> Vérifié
                    </span>
                @else
                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1">
                        <i class="bi bi-hourglass-split me-1"></i> En attente de vérification
                    </span>
                @endif
            </div>

            <hr class="my-3">

            <div class="text-start small">
                <div class="mb-2">
                    <span class="text-muted">N° Accréditation :</span>
                    <strong class="d-block text-truncate"><code>{{ $agent->numero_accreditation }}</code></strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Joueurs gérés :</span>
                    <strong>{{ $agent->joueurs?->count() ?? 0 }} joueurs</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- Colonne droite : Formulaire d'édition --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4" style="background:var(--card-bg);border:1px solid var(--border) !important;">
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                <h5 class="fw-bold mb-0" style="color:var(--text);">
                    <i class="bi bi-pencil-square me-2 text-warning"></i>Modifier mes informations
                </h5>
            </div>

            <form method="POST" action="{{ route('agent.profil.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Photo de profil / Avatar --}}
                <div class="mb-4 p-3 rounded-3" style="background:var(--bg2);border:1px dashed var(--border);">
                    <label class="form-label small fw-semibold text-muted text-uppercase mb-1">
                        <i class="bi bi-camera me-1"></i>Photo de profil / Logo Agence
                    </label>
                    <input type="file" name="avatar" class="form-control form-control-sm rounded-3 @error('avatar') is-invalid @enderror"
                           accept="image/jpeg,image/png,image/jpg,image/webp" onchange="previewImage(this)">
                    <div class="form-text small text-muted">Formats acceptés : JPG, PNG, WEBP (Max : 2 Mo).</div>
                    @error('avatar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Nom et Prénom --}}
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold text-muted text-uppercase">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3 @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold text-muted text-uppercase">Prénom</label>
                        <input type="text" name="prenom" class="form-control rounded-3 @error('prenom') is-invalid @enderror"
                               value="{{ old('prenom', $user->prenom) }}">
                        @error('prenom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Email & Téléphone --}}
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold text-muted text-uppercase">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control rounded-3 @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold text-muted text-uppercase">Téléphone <span class="text-danger">*</span></label>
                        <input type="text" name="telephone" class="form-control rounded-3 @error('telephone') is-invalid @enderror"
                               value="{{ old('telephone', $agent->telephone ?? $user->telephone) }}" required>
                        @error('telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Agence --}}
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted text-uppercase">Nom de l'Agence <span class="text-danger">*</span></label>
                    <input type="text" name="agence" class="form-control rounded-3 @error('agence') is-invalid @enderror"
                           value="{{ old('agence', $agent->agence) }}" required>
                    @error('agence')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Ville et Pays --}}
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold text-muted text-uppercase">Ville <span class="text-danger">*</span></label>
                        <input type="text" name="ville" class="form-control rounded-3 @error('ville') is-invalid @enderror"
                               value="{{ old('ville', $agent->ville) }}" required>
                        @error('ville')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold text-muted text-uppercase">Pays <span class="text-danger">*</span></label>
                        <input type="text" name="pays" class="form-control rounded-3 @error('pays') is-invalid @enderror"
                               value="{{ old('pays', $agent->pays) }}" required>
                        @error('pays')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Site web --}}
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted text-uppercase">Site Web</label>
                    <input type="url" name="site_web" class="form-control rounded-3 @error('site_web') is-invalid @enderror"
                           placeholder="https://mon-agence.com" value="{{ old('site_web', $agent->site_web) }}">
                    @error('site_web')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Biographie professionnelle --}}
                <div class="mb-4">
                    <label class="form-label small fw-semibold text-muted text-uppercase">Biographie professionnelle & Présentation</label>
                    <textarea name="bio" class="form-control rounded-3 @error('bio') is-invalid @enderror"
                              rows="4" placeholder="Présentez votre parcours, vos réseaux et vos services aux joueurs...">{{ old('bio', $agent->bio) }}</textarea>
                    @error('bio')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-semibold">
                        <i class="bi bi-check2 me-1"></i>Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            let img = document.getElementById('avatarPreview');
            if (!img) {
                const fallback = document.getElementById('avatarFallback');
                if (fallback) {
                    img = document.createElement('img');
                    img.id = 'avatarPreview';
                    img.className = 'rounded-circle object-fit-cover w-100 h-100 shadow-sm border border-3 border-warning';
                    fallback.parentNode.replaceChild(img, fallback);
                }
            }
            if (img) {
                img.src = e.target.result;
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush

@endsection
