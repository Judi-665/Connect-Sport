@extends('layouts.agent')

@section('title', 'Choisir un plan')
@section('page-title', 'Choisir un plan')

@section('sidebar-nav')
    @include('agents.partials.nav')
@endsection

@section('content')
<div class="mb-4">
    <h1 class="h3 mb-1">Choisir un plan</h1>
    <p class="text-secondary mb-0">Mensuel ou annuel — changez à tout moment.</p>
</div>

@php
    $groupes = $plans->groupBy(fn($p) => \Illuminate\Support\Str::before($p->slug, '-'));
@endphp

<div class="row g-3">
    @foreach($groupes as $niveau => $variantes)
    <div class="col-12 col-md-4">
        <div class="cs-card h-100 {{ $planActif && \Illuminate\Support\Str::before($planActif->slug, '-') === $niveau ? 'border-primary' : '' }}">
            <div class="cs-card-header"><div class="cs-card-title text-capitalize">{{ $niveau }}</div></div>
            <div class="p-4">
                @foreach($variantes as $plan)
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <div>
                        <strong>{{ $plan->prixFormate() }}</strong>
                        <span class="text-secondary" style="font-size:.8rem;">{{ $plan->frequenceLabel() }}</span>
                    </div>
                    <button type="button" class="btn-cs {{ $planActif?->id === $plan->id ? 'btn-cs-secondary' : 'btn-cs-primary' }} btn-sm btn-souscrire" data-plan-id="{{ $plan->id }}" {{ $planActif?->id === $plan->id ? 'disabled' : '' }}>
                        {{ $planActif?->id === $plan->id ? 'Plan actuel' : ($plan->prix > 0 ? 'Payer et choisir' : 'Activer gratuitement') }}
                    </button>
                </div>
                @endforeach

                @php
                    $plan = $variantes->first();
                    $statistiques = (bool) $plan->stats_avancees;
                    $fonctionnalites = [
                        [true, $plan->max_joueurs_representes === null ? 'Joueurs représentés illimités' : $plan->max_joueurs_representes . ' joueurs représentés max'],
                        [true, 'Commissions, transferts, offres et historique'],
                        [$niveau !== 'gratuit', 'Opportunités'],
                        [(bool) $plan->mise_en_avant, 'Mise en avant annuaire'],
                        [$statistiques, 'Statistiques avancées'],
                    ];
                @endphp
                <ul class="list-unstyled mt-3 mb-0" style="font-size:.82rem;">
                    @foreach($fonctionnalites as [$incluse, $libelle])
                        <li class="mb-1 {{ !$incluse ? 'text-secondary' : '' }}"><i class="bi {{ $incluse ? 'bi-check-circle text-success' : 'bi-lock text-secondary' }} me-2"></i>{{ $libelle }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endforeach
</div>

@push('scripts')
<script>
document.querySelectorAll('.btn-souscrire').forEach(btn => {
    btn.addEventListener('click', async function () {
        const planId = this.dataset.planId;
        this.disabled = true;
        this.textContent = 'Chargement...';

        try {
            const res = await fetch('{{ route('agent.abonnement.souscrire') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ plan_id: planId }),
            });
            const data = await res.json();

            if (!res.ok) {
                alert(data.message || 'Erreur.');
                this.disabled = false;
                this.textContent = 'Choisir';
                return;
            }

            window.location.href = data.payment_url || data.redirect;
        } catch (e) {
            alert('Erreur réseau.');
            this.disabled = false;
            this.textContent = 'Choisir';
        }
    });
});
</script>
@endpush
@endsection