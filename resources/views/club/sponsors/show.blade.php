@extends('layouts.app')
@section('title', 'Sponsor — ' . $sponsor->nom)

@section('content')
@php
    $expire = $sponsor->estExpire();
    $jours  = $sponsor->joursRestants();
    $badge  = $expire
        ? ['class' => 'bg-danger',               'label' => 'Expiré']
        : ($jours <= 30
            ? ['class' => 'bg-warning text-dark', 'label' => 'Expire bientôt']
            : ['class' => 'bg-success',           'label' => 'Actif']);
    $visiBadge = match($sponsor->type_visibilite) {
        'logo'     => ['class' => 'bg-primary',   'label' => 'Logo maillot',      'icon' => 'bi-shield'],
        'banniere' => ['class' => 'bg-info',      'label' => 'Pancarte/Bannière', 'icon' => 'bi-megaphone'],
        default    => ['class' => 'bg-secondary', 'label' => 'Tous supports',     'icon' => 'bi-stars'],
    };
@endphp

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- En-tête --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <a href="{{ route('club.sponsors.index') }}"
                       class="text-muted small text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i> Retour aux sponsors
                    </a>
                    <h1 class="h3 fw-bold mb-0 mt-1">Détail du sponsor</h1>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('club.sponsors.edit', $sponsor) }}"
                       class="btn btn-warning rounded-pill btn-sm">
                        <i class="bi bi-pencil me-1"></i> Modifier
                    </a>
                    <button type="button"
                            class="btn btn-outline-danger rounded-pill btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#modalSupprimer">
                        <i class="bi bi-trash me-1"></i> Supprimer
                    </button>
                </div>
            </div>

            <div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index:9999;"></div>

            {{-- Carte principale --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">

                    {{-- Logo + Nom + Badges --}}
                    <div class="d-flex align-items-start gap-4 mb-4 pb-3 border-bottom">
                        @if($sponsor->logo)
                            <img src="{{ asset('storage/' . $sponsor->logo) }}"
                                 class="rounded-3 border object-fit-cover flex-shrink-0"
                                 style="width:72px;height:72px;" alt="{{ $sponsor->nom }}">
                        @else
                            <div class="rounded-3 bg-light d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:72px;height:72px;">
                                <i class="bi bi-building fs-2 text-muted"></i>
                            </div>
                        @endif
                        <div class="flex-grow-1">
                            <h2 class="h4 fw-bold mb-2">{{ $sponsor->nom }}</h2>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge {{ $badge['class'] }} rounded-pill">
                                    {{ $badge['label'] }}
                                </span>
                                <span class="badge {{ $visiBadge['class'] }} rounded-pill">
                                    <i class="bi {{ $visiBadge['icon'] }} me-1"></i>
                                    {{ $visiBadge['label'] }}
                                </span>
                                @if($sponsor->site_web)
                                    <a href="{{ $sponsor->site_web }}" target="_blank"
                                       class="badge bg-light text-primary rounded-pill text-decoration-none">
                                        <i class="bi bi-link-45deg me-1"></i>
                                        Visiter le site
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    @if($sponsor->description)
                        <div class="mb-4 pb-3 border-bottom">
                            <p class="text-muted small fw-semibold mb-2">Présentation</p>
                            <p class="mb-0 small">{{ $sponsor->description }}</p>
                        </div>
                    @endif

                    {{-- Infos contact --}}
                    <div class="mb-4 pb-3 border-bottom">
                        <p class="text-muted small fw-semibold mb-3">Contact</p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="small text-muted mb-1">Email</div>
                                <a href="mailto:{{ $sponsor->email_contact }}"
                                   class="fw-semibold small text-decoration-none">
                                    <i class="bi bi-envelope me-1"></i>
                                    {{ $sponsor->email_contact }}
                                </a>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted mb-1">Téléphone</div>
                                <a href="tel:{{ $sponsor->telephone_contact }}"
                                   class="fw-semibold small text-decoration-none">
                                    <i class="bi bi-telephone me-1"></i>
                                    {{ $sponsor->telephone_contact }}
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Infos partenariat --}}
                    <div>
                        <p class="text-muted small fw-semibold mb-3">Partenariat</p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="small text-muted mb-1">Début</div>
                                <div class="fw-semibold small">
                                    <i class="bi bi-calendar me-1"></i>
                                    {{ $sponsor->debut_partenariat->format('d/m/Y') }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted mb-1">Fin</div>
                                <div class="fw-semibold small">
                                    <i class="bi bi-calendar-x me-1"></i>
                                    {{ $sponsor->fin_partenariat->format('d/m/Y') }}
                                    @if(!$expire && $jours <= 30)
                                        <span class="badge bg-warning text-dark ms-1 rounded-pill">
                                            {{ $jours }}j restants
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted mb-1">Montant du contrat</div>
                                <div class="fw-semibold small">
                                    <i class="bi bi-cash me-1"></i>
                                    {{ $sponsor->montantAffiche() }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted mb-1">Visibilité</div>
                                <div class="fw-semibold small">
                                    <i class="bi {{ $visiBadge['icon'] }} me-1"></i>
                                    {{ $visiBadge['label'] }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Actions bas --}}
            <div class="d-flex justify-content-between">
                <a href="{{ route('club.sponsors.index') }}"
                   class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Retour
                </a>
                <a href="{{ route('club.sponsors.edit', $sponsor) }}"
                   class="btn btn-warning rounded-pill px-4">
                    <i class="bi bi-pencil me-1"></i> Modifier
                </a>
            </div>

        </div>
    </div>
</div>

{{-- Modal suppression --}}
<div class="modal fade" id="modalSupprimer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Supprimer le sponsor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer
                    <strong>{{ $sponsor->nom }}</strong> ?
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

@push('scripts')
<script>
    document.getElementById('btn-confirmer-suppression').addEventListener('click', async function () {
        const spinner = document.getElementById('spinner-suppr');
        this.disabled = true;
        spinner.classList.remove('d-none');

        try {
            const res = await fetch('{{ route('club.sponsors.destroy', $sponsor) }}', {
                method:  'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept':       'application/json',
                },
            });

            if (!res.ok) throw new Error();

            afficherToast('Sponsor supprimé.', 'success');
            setTimeout(() => window.location.href = '{{ route('club.sponsors.index') }}', 1500);

        } catch {
            afficherToast('Erreur lors de la suppression.', 'danger');
            this.disabled = false;
            spinner.classList.add('d-none');
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