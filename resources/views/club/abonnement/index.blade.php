@extends('layouts.app')

@section('title', 'Choisir un abonnement — ConnectSport')

@section('content')
<div class="container py-4">

    <div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index:9999;"></div>

    {{-- Fil d'Ariane --}}
    <div class="mb-4">
        <a href="{{ route('club.abonnement.index') }}" class="text-muted small text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Retour à mon abonnement
        </a>
        <h1 class="h3 fw-bold mb-0 mt-1">Choisir un abonnement</h1>
        <p class="text-muted small mt-1">Sélectionnez le plan adapté à votre club.</p>
    </div>

    {{-- 3 Cartes plans --}}
    <div class="row g-4 justify-content-center">

        @foreach($plans as $plan)
        @php
            $estActuel    = $planActif && $planActif->id === $plan->id;
            $estInferieur = $planActif && (
                ($planActif->slug === 'premium') ||
                ($planActif->slug === 'standard' && $plan->slug === 'gratuit')
            );

            $couleur = match($plan->slug) {
                'premium'  => '#F97316',   // orange
                'standard' => '#1A56A0',   // bleu
                default    => '#6c757d',   // gris
            };
            $icon = match($plan->slug) {
                'premium'  => 'bi-trophy-fill',
                'standard' => 'bi-star-fill',
                default    => 'bi-person-fill',
            };
            $features = [
                'multi_equipes'         => 'Gestion multi-équipes',
                'gestion_licences'      => 'Gestion des licences',
                'agenda'                => 'Agenda & compétitions',
                'stats_avancees'        => 'Statistiques avancées',
                'stockage_etendu'       => 'Stockage photo/vidéo',
                'mise_en_avant'         => 'Mise en avant profil',
                'outils_marketing'      => 'Outils marketing',
                'galerie_media'         => 'Galerie médias',
                'notifications_ciblees' => 'Notifications ciblées',
            ];
        @endphp

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 {{ $plan->slug === 'premium' ? 'border-warning' : '' }}"
                 style="{{ $estActuel ? 'border: 2px solid '.$couleur.' !important;' : '' }}">

                {{-- Badge --}}
                @if($estActuel)
                    <div class="text-center mt-3">
                        <span class="badge rounded-pill px-3 py-1"
                              style="background-color:{{ $couleur }}; font-size:.7rem;">
                            <i class="bi bi-check-circle me-1"></i> Plan actuel
                        </span>
                    </div>
                @elseif($plan->slug === 'premium')
                    <div class="text-center mt-3">
                        <span class="badge rounded-pill px-3 py-1 bg-warning text-dark"
                              style="font-size:.7rem;">
                            <i class="bi bi-star-fill me-1"></i> Recommandé
                        </span>
                    </div>
                @else
                    <div style="height:32px;"></div>
                @endif

                <div class="card-body p-4 d-flex flex-column">

                    {{-- Icône + nom --}}
                    <div class="text-center mb-3">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                             style="width:56px;height:56px;background-color:{{ $couleur }}1a;">
                            <i class="bi {{ $icon }} fs-4" style="color:{{ $couleur }};"></i>
                        </div>
                        <h5 class="fw-bold mb-0">{{ $plan->nom }}</h5>
                    </div>

                    {{-- Prix --}}
                    <div class="text-center mb-4">
                        @if($plan->prix === 0)
                            <span class="display-6 fw-bold">Gratuit</span>
                        @else
                            <span class="display-6 fw-bold">
                                {{ number_format($plan->prix, 0, ',', ' ') }}
                            </span>
                            <span class="text-muted"> FCFA{{ $plan->frequenceLabel() }}</span>
                        @endif
                    </div>

                    {{-- Fonctionnalités --}}
                    <ul class="list-unstyled flex-grow-1 mb-4">
                        @foreach($features as $key => $label)
                            <li class="d-flex align-items-center mb-2 small">
                                @if($plan->$key)
                                    <i class="bi bi-check-circle-fill me-2" style="color:{{ $couleur }};"></i>
                                    <span>{{ $label }}</span>
                                @else
                                    <i class="bi bi-x-circle me-2 text-muted"></i>
                                    <span class="text-muted">{{ $label }}</span>
                                @endif
                            </li>
                        @endforeach
                        {{-- Équipes --}}
                        <li class="d-flex align-items-center mb-2 small">
                            <i class="bi bi-people-fill me-2" style="color:{{ $couleur }};"></i>
                            <span>
                                {{ $plan->max_equipes === -1 ? 'Équipes illimitées' : $plan->max_equipes . ' équipe(s) max' }}
                            </span>
                        </li>
                    </ul>

                    {{-- Bouton --}}
                    @if($estActuel)
                        <button class="btn rounded-pill fw-semibold w-100" disabled
                                style="background-color:{{ $couleur }}22; color:{{ $couleur }}; border:1px solid {{ $couleur }};">
                            <i class="bi bi-check-lg me-1"></i> Plan actuel
                        </button>
                    @elseif($estInferieur)
                        <button class="btn btn-outline-secondary rounded-pill fw-semibold w-100" disabled>
                            <i class="bi bi-lock me-1"></i> Downgrade non disponible
                        </button>
                    @else
                        <button class="btn rounded-pill fw-semibold w-100 btn-choisir"
                                data-plan-id="{{ $plan->id }}"
                                data-plan-nom="{{ $plan->nom }}"
                                data-plan-prix="{{ $plan->prixFormate() }}"
                                style="background-color:{{ $couleur }}; color:white;">
                            <span class="spinner-border spinner-border-sm me-1 d-none"></span>
                            <i class="bi bi-arrow-up-circle me-1"></i>
                            {{ $plan->prix === 0 ? 'Commencer' : 'Souscrire' }}
                        </button>
                    @endif

                </div>
            </div>
        </div>
        @endforeach

    </div>
</div>

{{-- Modal confirmation --}}
<div class="modal fade" id="modalConfirmer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Confirmer l'abonnement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Vous allez souscrire au plan <strong id="modal-plan-nom"></strong>.</p>
                <p class="text-muted small mb-0">Montant : <strong id="modal-plan-prix"></strong></p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill"
                        data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-warning rounded-pill px-4 fw-semibold"
                        id="btn-confirmer">
                    <span id="spinner-confirmer"
                          class="spinner-border spinner-border-sm me-1 d-none"></span>
                    Confirmer
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let planSelectionne = null;
    const modal = new bootstrap.Modal(document.getElementById('modalConfirmer'));

    // Clic sur un bouton souscrire
    document.querySelectorAll('.btn-choisir').forEach(btn => {
        btn.addEventListener('click', function () {
            planSelectionne = this.dataset.planId;
            document.getElementById('modal-plan-nom').textContent  = this.dataset.planNom;
            document.getElementById('modal-plan-prix').textContent = this.dataset.planPrix;
            modal.show();
        });
    });

    // Confirmer la souscription
    document.getElementById('btn-confirmer').addEventListener('click', async function () {
        if (!planSelectionne) return;

        const spinner = document.getElementById('spinner-confirmer');
        this.disabled = true;
        spinner.classList.remove('d-none');

        try {
            const res = await fetch('{{ route('club.abonnement.souscrire') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ plan_id: planSelectionne }),
            });

            const data = await res.json();

            modal.hide();

            if (data.payment_url) {
                // Plan payant → redirection vers FedaPay
                window.location.href = data.payment_url;
            } else if (data.redirect) {
                // Plan gratuit → succès direct
                afficherToast('Abonnement activé avec succès !', 'success');
                setTimeout(() => window.location.href = data.redirect, 1500);
            } else {
                afficherToast(data.message ?? 'Erreur.', 'danger');
                this.disabled = false;
                spinner.classList.add('d-none');
            }
        } catch {
            afficherToast('Erreur réseau. Veuillez réessayer.', 'danger');
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