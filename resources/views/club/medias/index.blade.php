@extends('layouts.app')
@section('title', 'Médias — ' . $club->nom)

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- En-tête --}}
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Médias</h1>
            <p class="text-muted small mb-0">Photos et vidéos publiées par le club</p>
        </div>
        <a href="{{ route('club.medias.store') }}" class="btn btn-warning rounded-pill">
            <i class="bi bi-cloud-upload me-1"></i> Uploader un média
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center py-3">
                <i class="bi bi-images fs-3 mb-1" style="color:#1A56A0;"></i>
                <div class="fw-bold fs-4">{{ $photos->total() }}</div>
                <div class="text-muted small">Photos</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center py-3">
                <i class="bi bi-camera-video fs-3 mb-1" style="color:#F97316;"></i>
                <div class="fw-bold fs-4">{{ $videos->total() }}</div>
                <div class="text-muted small">Vidéos</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center py-3">
                <i class="bi bi-eye fs-3 mb-1" style="color:#10b981;"></i>
                <div class="fw-bold fs-4">
                    {{ $club->medias()->sum('vues') }}
                </div>
                <div class="text-muted small">Vues totales</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center py-3">
                <i class="bi bi-lock fs-3 mb-1" style="color:#8b5cf6;"></i>
                <div class="fw-bold fs-4">
                    {{ $club->medias()->payants()->count() }}
                </div>
                <div class="text-muted small">Contenus premium</div>
            </div>
        </div>
    </div>

    {{-- Onglets --}}
    <ul class="nav nav-tabs mb-4" id="mediaTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold" id="photos-tab"
                    data-bs-toggle="tab" data-bs-target="#photos"
                    type="button" role="tab">
                <i class="bi bi-images me-2"></i>Photos
                <span class="badge bg-primary rounded-pill ms-1">{{ $photos->total() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="videos-tab"
                    data-bs-toggle="tab" data-bs-target="#videos"
                    type="button" role="tab">
                <i class="bi bi-camera-video me-2"></i>Vidéos
                <span class="badge bg-warning text-dark rounded-pill ms-1">{{ $videos->total() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content">

        {{-- ── Photos ── --}}
        <div class="tab-pane fade show active" id="photos" role="tabpanel">
            @if($photos->isEmpty())
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-images fs-1 text-muted d-block mb-3"></i>
                        <p class="text-muted mb-3">Aucune photo publiée.</p>
                        <a href="{{ route('club.medias.store') }}"
                           class="btn btn-warning rounded-pill px-4">
                            <i class="bi bi-cloud-upload me-1"></i> Uploader une photo
                        </a>
                    </div>
                </div>
            @else
                <div class="row g-3" id="galerie-photos">
                    @foreach($photos as $photo)
                        @include('club.medias._carte', ['media' => $photo])
                    @endforeach
                </div>
                @if($photos->hasPages())
                    <div class="mt-4">{{ $photos->links() }}</div>
                @endif
            @endif
        </div>

        {{-- ── Vidéos ── --}}
        <div class="tab-pane fade" id="videos" role="tabpanel">
            @if($videos->isEmpty())
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-camera-video fs-1 text-muted d-block mb-3"></i>
                        <p class="text-muted mb-3">Aucune vidéo publiée.</p>
                        <a href="{{ route('club.medias.store') }}"
                           class="btn btn-warning rounded-pill px-4">
                            <i class="bi bi-cloud-upload me-1"></i> Uploader une vidéo
                        </a>
                    </div>
                </div>
            @else
                <div class="row g-3" id="galerie-videos">
                    @foreach($videos as $video)
                        @include('club.medias._carte', ['media' => $video])
                    @endforeach
                </div>
                @if($videos->hasPages())
                    <div class="mt-4">{{ $videos->appends(['photos_page' => $photos->currentPage()])->links() }}</div>
                @endif
            @endif
        </div>

    </div>
</div>

{{-- Toast container --}}
<div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index:9999;"></div>

{{-- Modal suppression --}}
<div class="modal fade" id="modalSupprimer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Supprimer le média</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer
                    <strong id="modal-media-titre"></strong> ?
                </p>
                <p class="text-muted small mb-0">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill"
                        data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger rounded-pill px-4"
                        id="btn-confirmer-suppression">
                    <span id="spinner-suppr"
                          class="spinner-border spinner-border-sm me-1 d-none"></span>
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal visibilité --}}
<div class="modal fade" id="modalVisibilite" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Changer la visibilité</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">
                    Média : <strong id="modal-visi-titre"></strong>
                </p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary rounded-3 text-start px-3"
                            data-visi="public">
                        <i class="bi bi-globe me-2"></i>
                        <strong>Public</strong>
                        <span class="text-muted small d-block ms-4">Visible par tous les visiteurs</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary rounded-3 text-start px-3"
                            data-visi="club">
                        <i class="bi bi-shield me-2"></i>
                        <strong>Club</strong>
                        <span class="text-muted small d-block ms-4">Visible par les membres du club</span>
                    </button>
                    <button type="button" class="btn btn-outline-warning rounded-3 text-start px-3"
                            data-visi="premium">
                        <i class="bi bi-star me-2"></i>
                        <strong>Premium</strong>
                        <span class="text-muted small d-block ms-4">Contenu payant</span>
                    </button>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill"
                        data-bs-dismiss="modal">Annuler</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // ── Suppression ──
    let mediaASupprimer = null;
    const modalSuppr = new bootstrap.Modal(document.getElementById('modalSupprimer'));

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-action="supprimer"]');
        if (!btn) return;
        mediaASupprimer = btn.dataset.id;
        document.getElementById('modal-media-titre').textContent = btn.dataset.titre;
        modalSuppr.show();
    });

    document.getElementById('btn-confirmer-suppression').addEventListener('click', async function () {
        if (!mediaASupprimer) return;
        const spinner = document.getElementById('spinner-suppr');
        this.disabled = true;
        spinner.classList.remove('d-none');

        try {
            const res = await fetch(`/club/medias/${mediaASupprimer}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            });

            if (!res.ok) throw new Error();

            document.getElementById(`media-${mediaASupprimer}`)?.remove();
            modalSuppr.hide();
            afficherToast('Média supprimé.', 'success');

        } catch {
            afficherToast('Erreur lors de la suppression.', 'danger');
        } finally {
            this.disabled = false;
            spinner.classList.add('d-none');
        }
    });

    // ── Visibilité ──
    let mediaVisibilite = null;
    const modalVisi = new bootstrap.Modal(document.getElementById('modalVisibilite'));

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-action="visibilite"]');
        if (!btn) return;
        mediaVisibilite = btn.dataset.id;
        document.getElementById('modal-visi-titre').textContent = btn.dataset.titre;
        modalVisi.show();
    });

    document.querySelectorAll('[data-visi]').forEach(btn => {
        btn.addEventListener('click', async function () {
            if (!mediaVisibilite) return;

            try {
                const res = await fetch(`/club/medias/${mediaVisibilite}/visibilite`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept':       'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ visibilite: this.dataset.visi }),
                });

                if (!res.ok) throw new Error();

                // Mettre à jour le badge visibilité dans la carte
                const badge = document.querySelector(`#media-${mediaVisibilite} [data-badge-visi]`);
                if (badge) {
                    const labels = { public: 'Public', club: 'Club', premium: 'Premium' };
                    const classes = {
                        public:  'bg-primary',
                        club:    'bg-secondary',
                        premium: 'bg-warning text-dark',
                    };
                    badge.textContent  = labels[this.dataset.visi];
                    badge.className    = `badge ${classes[this.dataset.visi]} rounded-pill`;
                }

                modalVisi.hide();
                afficherToast('Visibilité mise à jour.', 'success');

            } catch {
                afficherToast('Erreur lors de la mise à jour.', 'danger');
            }
        });
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
        new bootstrap.Toast(el, { delay: 4000 }).show();
        el.addEventListener('hidden.bs.toast', () => el.remove());
    }
</script>
@endpush

@endsection