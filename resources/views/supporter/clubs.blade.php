{{-- resources/views/supporter/clubs.blade.php --}}
@extends('layouts.app')

@section('title', 'Mes clubs suivis — Espace Supporter')

@push('styles')
<style>
    .club-card-manage {
        border: 1px solid var(--bs-border-color);
        border-radius: 16px;
        background: var(--bs-body-bg);
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .club-card-manage:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(13, 46, 92, 0.08);
    }
    [data-bs-theme="dark"] .club-card-manage:hover {
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
                    <li class="breadcrumb-item active" aria-current="page">Mes clubs</li>
                </ol>
            </nav>
            <h2 class="fw-bold mb-1">Clubs que vous suivez</h2>
            <p class="text-body-secondary small mb-0">
                Gérez vos {{ $clubs->total() }} clubs suivis et réglez vos préférences d'alertes notifications.
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('supporter.decouvrir') }}" class="btn btn-warning rounded-pill px-4 fw-semibold shadow-sm">
                <i class="bi bi-search me-1"></i> Découvrir d'autres clubs
            </a>
            <a href="{{ route('supporter.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Mon fil
            </a>
        </div>
    </div>

    {{-- Filtre & Recherche --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('supporter.clubs') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold text-body-secondary">Recherche par nom ou ville</label>
                    <div class="input-group">
                        <span class="input-group-text bg-body-tertiary border-end-0 rounded-start-3">
                            <i class="bi bi-search text-body-secondary"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Ex: Cotonou, AS Dragons..."
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
                        <i class="bi bi-funnel me-1"></i> Filtrer
                    </button>
                    @if(request('search') || request('sport_id'))
                        <a href="{{ route('supporter.clubs') }}" class="btn btn-outline-secondary rounded-3" title="Réinitialiser">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Liste des clubs --}}
    @if($clubs->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center p-5">
            <div class="mx-auto mb-3 rounded-circle bg-secondary bg-opacity-10 text-secondary d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                <i class="bi bi-shield-x fs-2"></i>
            </div>
            <h5 class="fw-bold mb-2">Aucun club ne correspond à votre recherche</h5>
            <p class="text-body-secondary small mb-4">Essayez d'ajuster vos critères ou explorez l'annuaire complet.</p>
            <div>
                <a href="{{ route('supporter.decouvrir') }}" class="btn btn-primary rounded-pill px-4">
                    Parcourir l'annuaire des clubs
                </a>
            </div>
        </div>
    @else
        <div class="row g-4 mb-4">
            @foreach($clubs as $club)
                <div class="col-md-6 col-lg-4" id="club-card-{{ $club->id }}">
                    <div class="club-card-manage h-100 p-4 d-flex flex-column">

                        {{-- En-tête carte --}}
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

                        {{-- Détails supporter --}}
                        <div class="p-3 rounded-3 bg-body-tertiary mb-3 text-body-secondary small">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Abonné depuis :</span>
                                <strong>{{ $club->pivot->created_at ? $club->pivot->created_at->format('d/m/Y') : 'Récemment' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Formule :</span>
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill fw-semibold">Gratuit</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Supporters :</span>
                                <span>{{ $club->supporters_count ?? 1 }} abonnés</span>
                            </div>
                        </div>

                        {{-- Actions & Alertes --}}
                        <div class="mt-auto pt-3 border-top d-flex flex-column gap-2">
                            {{-- Bouton Toggle notifications --}}
                            @php $notifActives = (bool) $club->pivot->notifications_actives; @endphp
                            <button type="button"
                                    class="btn btn-sm {{ $notifActives ? 'btn-outline-success' : 'btn-outline-secondary' }} rounded-pill d-flex align-items-center justify-content-center gap-2 notif-toggle-btn"
                                    data-club-id="{{ $club->id }}"
                                    data-url="{{ route('supporter.club.notifications.toggle', $club) }}"
                                    data-active="{{ $notifActives ? 'true' : 'false' }}">
                                <i class="bi {{ $notifActives ? 'bi-bell-fill text-success' : 'bi-bell-slash text-muted' }}"></i>
                                <span class="notif-label">{{ $notifActives ? 'Notifications activées' : 'Notifications coupées' }}</span>
                            </button>

                            <div class="d-flex gap-2">
                                <a href="{{ route('clubs.show', $club->slug) }}" class="btn btn-sm btn-outline-primary rounded-pill flex-grow-1">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Fiche club
                                </a>

                                {{-- Se désabonner --}}
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger rounded-pill px-3 unfollow-btn"
                                        data-club-id="{{ $club->id }}"
                                        data-club-nom="{{ $club->nom }}"
                                        data-url="{{ route('supporter.club.quitter', $club) }}"
                                        title="Se désabonner de ce club">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
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

{{-- Modal de confirmation de désabonnement --}}
<div class="modal fade" id="unfollowConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="mx-auto mb-3 rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="bi bi-exclamation-triangle fs-3"></i>
                </div>
                <h5 class="fw-bold mb-2">Se désabonner ?</h5>
                <p class="text-body-secondary small mb-4">
                    Vous ne recevrez plus les actualités de <strong id="unfollowModalClubName">ce club</strong>.
                </p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light rounded-pill flex-grow-1" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger rounded-pill flex-grow-1" id="confirmUnfollowBtn">Confirmer</button>
                </div>
            </div>
        </div>
    </div>
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

    // Toggle Notifications
    document.querySelectorAll('.notif-toggle-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const url = this.getAttribute('data-url');
            const self = this;
            self.disabled = true;

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
                self.disabled = false;
                if (data.success) {
                    const isNowActive = data.notifications_actives;
                    self.setAttribute('data-active', isNowActive ? 'true' : 'false');
                    const icon = self.querySelector('i');
                    const label = self.querySelector('.notif-label');

                    if (isNowActive) {
                        self.classList.remove('btn-outline-secondary');
                        self.classList.add('btn-outline-success');
                        icon.className = 'bi bi-bell-fill text-success';
                        label.textContent = 'Notifications activées';
                    } else {
                        self.classList.remove('btn-outline-success');
                        self.classList.add('btn-outline-secondary');
                        icon.className = 'bi bi-bell-slash text-muted';
                        label.textContent = 'Notifications coupées';
                    }
                    showToast(data.message, true);
                } else {
                    showToast(data.message || 'Une erreur est survenue.', false);
                }
            })
            .catch(err => {
                self.disabled = false;
                console.error(err);
                showToast('Erreur lors du changement d\'état des notifications.', false);
            });
        });
    });

    // Désabonnement
    let pendingUnfollow = null;
    const unfollowModal = new bootstrap.Modal(document.getElementById('unfollowConfirmModal'));

    document.querySelectorAll('.unfollow-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            pendingUnfollow = {
                clubId: this.getAttribute('data-club-id'),
                clubNom: this.getAttribute('data-club-nom'),
                url: this.getAttribute('data-url')
            };
            document.getElementById('unfollowModalClubName').textContent = pendingUnfollow.clubNom;
            unfollowModal.show();
        });
    });

    document.getElementById('confirmUnfollowBtn').addEventListener('click', function () {
        if (!pendingUnfollow) return;

        const btnConfirm = this;
        btnConfirm.disabled = true;
        btnConfirm.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> En cours...';

        fetch(pendingUnfollow.url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({})
        })
        .then(res => res.json())
        .then(data => {
            btnConfirm.disabled = false;
            btnConfirm.innerHTML = 'Confirmer';
            unfollowModal.hide();

            if (data.success) {
                showToast(data.message, true);
                const card = document.getElementById('club-card-' + pendingUnfollow.clubId);
                if (card) {
                    card.style.transition = 'opacity .4s ease, transform .4s ease';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        card.remove();
                        window.location.reload();
                    }, 400);
                } else {
                    setTimeout(() => window.location.reload(), 1000);
                }
            } else {
                showToast(data.message || 'Erreur lors du désabonnement.', false);
            }
        })
        .catch(err => {
            btnConfirm.disabled = false;
            btnConfirm.innerHTML = 'Confirmer';
            unfollowModal.hide();
            console.error(err);
            showToast('Erreur réseau lors du désabonnement.', false);
        });
    });
});
</script>
@endpush
@endsection
