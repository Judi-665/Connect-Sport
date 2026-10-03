{{-- resources/views/supporter/decouvrir.blade.php --}}
@extends('layouts.app')

@section('title', 'Découvrir des clubs — Espace Supporter')

@push('styles')
<style>
    .club-discover-card {
        border: 1px solid var(--bs-border-color);
        border-radius: 16px;
        background: var(--bs-body-bg);
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .club-discover-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(13, 46, 92, 0.08);
    }
    [data-bs-theme="dark"] .club-discover-card:hover {
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.4);
    }
    .toast-container-custom {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 1090;
    }
</style>
@endpush

@section('content')
<div class="container py-4 py-lg-5">

    {{-- En-tête --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('supporter.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Découvrir</li>
                </ol>
            </nav>
            <h2 class="fw-bold mb-1">Explorer et suivre des clubs</h2>
            <p class="text-body-secondary small mb-0">
                Abonnez-vous gratuitement à vos clubs préférés pour recevoir leurs actualités, prochains matchs et scores.
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('supporter.clubs') }}" class="btn btn-outline-primary rounded-pill px-4">
                <i class="bi bi-shield-check me-1"></i> Mes clubs suivis
            </a>
            <a href="{{ route('supporter.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Mon fil
            </a>
        </div>
    </div>

    {{-- Filtre & Recherche --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('supporter.decouvrir') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold text-body-secondary">Recherche par nom, ville ou pays</label>
                    <div class="input-group">
                        <span class="input-group-text bg-body-tertiary border-end-0 rounded-start-3">
                            <i class="bi bi-search text-body-secondary"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Ex: Dragons, Cotonou, Bénin..."
                               class="form-control border-start-0 rounded-end-3">
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-body-secondary">Discipline sportive</label>
                    <select name="sport_id" class="form-select rounded-3">
                        <option value="">Tous les sports</option>
                        @foreach($sports as $sp)
                            <option value="{{ $sp->id }}" @selected(request('sport_id') == $sp->id)>
                                {{ $sp->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-3 flex-grow-1 fw-semibold">
                        <i class="bi bi-funnel me-1"></i> Rechercher
                    </button>
                    @if(request('search') || request('sport_id'))
                        <a href="{{ route('supporter.decouvrir') }}" class="btn btn-outline-secondary rounded-3" title="Réinitialiser">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Grille des clubs --}}
    @if($clubs->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center p-5">
            <div class="mx-auto mb-3 rounded-circle bg-secondary bg-opacity-10 text-secondary d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                <i class="bi bi-search fs-2"></i>
            </div>
            <h5 class="fw-bold mb-2">Aucun club trouvé</h5>
            <p class="text-body-secondary small mb-4">Aucun résultat ne correspond à votre filtre.</p>
            <div>
                <a href="{{ route('supporter.decouvrir') }}" class="btn btn-primary rounded-pill px-4">
                    Réinitialiser la recherche
                </a>
            </div>
        </div>
    @else
        <div class="row g-4 mb-4">
            @foreach($clubs as $club)
                @php
                    $isFollowing = in_array($club->id, $followedClubIds);
                @endphp
                <div class="col-md-6 col-lg-4" id="club-card-{{ $club->id }}">
                    <div class="club-discover-card h-100 p-4 d-flex flex-column">

                        {{-- En-tête --}}
                        <div class="d-flex align-items-center gap-3 mb-3">
                            @if($club->logo)
                                <img src="{{ Storage::url($club->logo) }}" alt="{{ $club->nom }}" class="rounded-3 object-fit-cover shadow-sm flex-shrink-0" width="56" height="56">
                            @else
                                <div class="rounded-3 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 56px; height: 56px; font-size: 1.25rem;">
                                    {{ strtoupper(substr($club->nom, 0, 2)) }}
                                </div>
                            @endif

                            <div class="overflow-hidden">
                                <h5 class="fw-bold text-truncate mb-1">{{ $club->nom }}</h5>
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill small">
                                        {{ $club->sport?->nom ?? 'Sport' }}
                                    </span>
                                    @if($club->ville)
                                        <span class="badge bg-secondary bg-opacity-10 text-body-secondary rounded-pill small">
                                            <i class="bi bi-geo-alt"></i> {{ $club->ville }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <p class="text-body-secondary small mb-3 line-clamp-3" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $club->description ?? 'Club sportif inscrit sur la plateforme Connect Sport.' }}
                        </p>

                        {{-- Compteur supporters --}}
                        <div class="d-flex align-items-center justify-content-between py-2 border-top text-body-secondary small mb-3">
                            <span><i class="bi bi-people me-1"></i> Supporters inscrits</span>
                            <strong class="text-primary">{{ $club->supporters_count ?? 0 }}</strong>
                        </div>

                        {{-- Actions : Suivre ou Déjà suivi --}}
                        <div class="mt-auto pt-2 d-flex gap-2">
                            @if($isFollowing)
                                <button type="button" class="btn btn-sm btn-success rounded-pill flex-grow-1" disabled>
                                    <i class="bi bi-check-lg me-1"></i> Déjà abonné
                                </button>
                            @else
                                <button type="button"
                                        class="btn btn-sm btn-primary rounded-pill flex-grow-1 follow-btn"
                                        data-club-id="{{ $club->id }}"
                                        data-club-nom="{{ $club->nom }}"
                                        data-url="{{ route('supporter.club.suivre', $club) }}">
                                    <i class="bi bi-plus-lg me-1"></i> S'abonner (gratuit)
                                </button>
                            @endif

                            <a href="{{ route('clubs.show', $club->slug) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3" title="Voir la page publique">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center">
            {{ $clubs->links() }}
        </div>
    @endif

</div>

{{-- Toast container --}}
<div class="toast-container-custom">
    <div id="ajaxToast" class="toast align-items-center text-white bg-dark border-0 shadow-lg rounded-3" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2">
                <i class="bi bi-info-circle-fill text-warning" id="toastIcon"></i>
                <span id="toastMessage">Action effectuée</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const toastEl = document.getElementById('ajaxToast');
    const bsToast = toastEl ? new bootstrap.Toast(toastEl, { delay: 3500 }) : null;

    function showToast(message, isSuccess = true) {
        if (!toastEl) return;
        const msgSpan = document.getElementById('toastMessage');
        const iconEl = document.getElementById('toastIcon');
        msgSpan.textContent = message;
        iconEl.className = isSuccess ? 'bi bi-check-circle-fill text-success fs-5' : 'bi bi-exclamation-triangle-fill text-danger fs-5';
        bsToast.show();
    }

    document.querySelectorAll('.follow-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const url = this.getAttribute('data-url');
            const self = this;

            self.disabled = true;
            self.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Abonnement...';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({})
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    self.className = 'btn btn-sm btn-success rounded-pill flex-grow-1';
                    self.innerHTML = '<i class="bi bi-check-lg me-1"></i> Déjà abonné';
                    showToast(data.message, true);
                } else {
                    self.disabled = false;
                    self.innerHTML = '<i class="bi bi-plus-lg me-1"></i> S\'abonner (gratuit)';
                    showToast(data.message || 'Erreur lors de l\'abonnement.', false);
                }
            })
            .catch(err => {
                self.disabled = false;
                self.innerHTML = '<i class="bi bi-plus-lg me-1"></i> S\'abonner (gratuit)';
                console.error(err);
                showToast('Erreur de connexion au serveur.', false);
            });
        });
    });
});
</script>
@endpush
@endsection
