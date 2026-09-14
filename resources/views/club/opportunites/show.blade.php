@extends('layouts.app')
@section('title', $opportunite->titre)

@section('content')
@php
    $expire = $opportunite->estExpiree();
    $typeBadge = match($opportunite->type) {
        'recrutement' => ['class' => 'bg-primary',           'label' => 'Recrutement',        'icon' => 'bi-person-plus'],
        'selection'   => ['class' => 'bg-info',              'label' => 'Sélection/Détection', 'icon' => 'bi-trophy'],
        'bourse'      => ['class' => 'bg-success',           'label' => 'Bourse/Soutien',      'icon' => 'bi-award'],
        'stage'       => ['class' => 'bg-warning text-dark', 'label' => 'Stage/Formation',     'icon' => 'bi-book'],
        default       => ['class' => 'bg-secondary',         'label' => ucfirst($opportunite->type), 'icon' => 'bi-megaphone'],
    };
    $statutBadge = $expire
        ? ['class' => 'bg-danger',  'label' => 'Expirée']
        : ($opportunite->active
            ? ['class' => 'bg-success', 'label' => 'Active']
            : ['class' => 'bg-secondary', 'label' => 'Inactive']);
@endphp

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- En-tête --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <a href="{{ route('club.opportunites.index') }}"
                       class="text-muted small text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i> Retour aux opportunités
                    </a>
                    <h1 class="h3 fw-bold mb-0 mt-1">Détail de l'opportunité</h1>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('club.opportunites.edit', $opportunite) }}"
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

                    {{-- Titre + badges --}}
                    <div class="mb-4 pb-3 border-bottom">
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <span class="badge {{ $typeBadge['class'] }} rounded-pill">
                                <i class="bi {{ $typeBadge['icon'] }} me-1"></i>
                                {{ $typeBadge['label'] }}
                            </span>
                            <span class="badge {{ $statutBadge['class'] }} rounded-pill">
                                {{ $statutBadge['label'] }}
                            </span>
                            @if($opportunite->mise_en_avant)
                                <span class="badge bg-warning text-dark rounded-pill">
                                    <i class="bi bi-star-fill me-1"></i> Mise en avant
                                </span>
                            @endif
                        </div>
                        <h2 class="h4 fw-bold mb-1">{{ $opportunite->titre }}</h2>
                        <div class="text-muted small">
                            <i class="bi bi-eye me-1"></i>{{ $opportunite->vues }} vue(s)
                            · Publiée le {{ $opportunite->created_at->format('d/m/Y') }}
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="mb-4 pb-3 border-bottom">
                        <p class="text-muted small fw-semibold mb-2">Description</p>
                        <p class="mb-0 small">{{ $opportunite->description }}</p>
                    </div>

                    {{-- Profil recherché --}}
                    <div class="mb-4 pb-3 border-bottom">
                        <p class="text-muted small fw-semibold mb-3">Profil recherché</p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="small text-muted mb-1">Sport</div>
                                <div class="fw-semibold small">
                                    <i class="bi bi-dribbble me-1"></i>
                                    {{ $opportunite->sport_cible }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted mb-1">Catégorie</div>
                                <div class="fw-semibold small">
                                    <i class="bi bi-people me-1"></i>
                                    {{ ucfirst($opportunite->categorie_cible) }}
                                </div>
                            </div>
                            @if($opportunite->poste_cible)
                                <div class="col-md-6">
                                    <div class="small text-muted mb-1">Poste</div>
                                    <div class="fw-semibold small">
                                        <i class="bi bi-person-badge me-1"></i>
                                        {{ $opportunite->poste_cible }}
                                    </div>
                                </div>
                            @endif
                            @if($opportunite->places_disponibles)
                                <div class="col-md-6">
                                    <div class="small text-muted mb-1">Places disponibles</div>
                                    <div class="fw-semibold small">
                                        <i class="bi bi-123 me-1"></i>
                                        {{ $opportunite->places_disponibles }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Localisation & Dates --}}
                    <div class="mb-4 pb-3 border-bottom">
                        <p class="text-muted small fw-semibold mb-3">Localisation & Dates</p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="small text-muted mb-1">Lieu</div>
                                <div class="fw-semibold small">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    {{ $opportunite->lieu }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted mb-1">Pays</div>
                                <div class="fw-semibold small">
                                    <i class="bi bi-globe me-1"></i>
                                    {{ $opportunite->pays }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted mb-1">Date limite</div>
                                <div class="fw-semibold small {{ $expire ? 'text-danger' : '' }}">
                                    <i class="bi bi-calendar-x me-1"></i>
                                    {{ $opportunite->date_limite?->format('d/m/Y') ?? '—' }}
                                    @if($expire)
                                        <span class="badge bg-danger rounded-pill ms-1">Expirée</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Budget --}}
                    <div>
                        <p class="text-muted small fw-semibold mb-3">Budget</p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="small text-muted mb-1">Montant proposé</div>
                                <div class="fw-semibold small">
                                    <i class="bi bi-cash me-1"></i>
                                    {{ $opportunite->budgetAffiche() }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Actions bas --}}
            <div class="d-flex justify-content-between">
                <a href="{{ route('club.opportunites.index') }}"
                   class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Retour
                </a>
                <a href="{{ route('club.opportunites.edit', $opportunite) }}"
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
                <h5 class="modal-title fw-bold">Supprimer l'opportunité</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer
                    <strong>{{ $opportunite->titre }}</strong> ?
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
            const res = await fetch('{{ route('club.opportunites.destroy', $opportunite) }}', {
                method:  'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept':       'application/json',
                },
            });

            if (!res.ok) throw new Error();

            afficherToast('Opportunité supprimée.', 'success');
            setTimeout(() => window.location.href = '{{ route('club.opportunites.index') }}', 1500);

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