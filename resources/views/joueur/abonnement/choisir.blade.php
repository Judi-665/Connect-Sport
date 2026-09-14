@extends('layouts.app')

@section('title', 'Choisir un abonnement')

@section('content')
<div class="container py-4">
    <div class="text-center mb-4">
        <p class="text-uppercase small fw-bold" style="color:#F97316;">Espace joueur</p>
        <h1 class="h3">Choisissez votre formule</h1>
        <p class="text-muted">Débloquez plus de visibilité et d'outils pour votre carrière.</p>
    </div>

    <div class="row g-4 justify-content-center">
        @foreach($plans as $plan)
            @php
                $estActif = $planActif && $planActif->slug === $plan->slug;
                $isPremium = $plan->slug === 'premium';
            @endphp
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 {{ $isPremium ? 'border-top border-4' : '' }}"
                     style="{{ $isPremium ? 'border-color:#F97316 !important;' : '' }}">
                    <div class="card-body p-4 text-center d-flex flex-column">
                        <p class="text-uppercase small fw-bold mb-1" style="color:{{ $isPremium ? '#F97316' : '#1A56A0' }};">
                            {{ $plan->nom }}
                        </p>
                        <h2 class="h4 mb-0">
                            {{ $plan->prix > 0 ? number_format($plan->prix, 0, '.', ' ') . ' FCFA' : 'Gratuit' }}
                            @if($plan->prix > 0)<span class="fs-6 text-muted">/ mois</span>@endif
                        </h2>

                        <ul class="list-unstyled text-start small mt-3 mb-4 flex-grow-1">
                            @if($plan->slug === 'gratuit')
                                <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Profil joueur basique</li>
                                <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Stats limitées</li>
                                <li class="mb-2 text-muted"><i class="bi bi-x-lg me-2"></i>Vidéos de formation</li>
                                <li class="mb-2 text-muted"><i class="bi bi-x-lg me-2"></i>Mise en avant recruteurs</li>
                            @elseif($plan->slug === 'standard')
                                <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Profil enrichi + badge Standard</li>
                                <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Stats détaillées</li>
                                <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Formations sélectionnées incluses</li>
                                <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Recommandations IA de base</li>
                            @else
                                <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Profil premium + badge</li>
                                <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Stats avancées + suivi</li>
                                <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Accès illimité aux formations</li>
                                <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Mise en avant prioritaire recruteurs</li>
                                <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Plan de développement IA complet</li>
                            @endif
                        </ul>

                        <button type="button"
                            class="btn {{ $isPremium ? 'text-white' : 'btn-outline-primary' }} rounded-pill btn-souscrire"
                            {{ $isPremium ? 'style=background:#F97316;' : '' }}
                            data-plan-id="{{ $plan->id }}"
                            {{ $estActif ? 'disabled' : '' }}>
                            {{ $estActif ? 'Formule actuelle' : 'Choisir' }}
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.btn-souscrire').forEach(btn => {
    btn.addEventListener('click', function () {
        const planId = this.dataset.planId;
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Redirection...';

        fetch('{{ route("joueur.abonnement.souscrire") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ plan_id: planId }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.payment_url) {
                window.location.href = data.payment_url;
            } else if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                alert(data.message || 'Une erreur est survenue.');
                this.disabled = false;
                this.textContent = 'Choisir';
            }
        })
        .catch(() => {
            alert('Erreur réseau. Réessayez.');
            this.disabled = false;
        });
    });
});
</script>
@endpush
@endsection