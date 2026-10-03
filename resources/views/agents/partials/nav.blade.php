@php
    $agentUser  = auth()->user()->agent;
    $planActif  = $agentUser?->planActif();
    $niveauPlan = $agentUser?->niveauPlan() ?? 'gratuit';
    $isStandard = in_array($niveauPlan, ['standard', 'premium']);
    $isPremium  = $niveauPlan === 'premium';

    $urlStandard = fn(string $route) => $isStandard
        ? route($route)
        : route('agent.locked', ['planRequis' => 'standard']);
    $urlPremium = fn(string $route) => $isPremium
        ? route($route)
        : route('agent.locked', ['planRequis' => 'premium']);
    $urlStatistiques = $isPremium
        ? route('agent.statistiques')
        : route('agent.locked', ['planRequis' => 'premium']);
@endphp

<div class="cs-sidebar-section">Espace agent</div>
<a href="{{ route('agent.dashboard') }}" class="{{ request()->routeIs('agent.dashboard') ? 'active' : '' }}"><i class="bi bi-grid-1x2"></i>Tableau de bord</a>
<a href="{{ route('agent.joueurs') }}" class="{{ request()->routeIs('agent.joueurs') ? 'active' : '' }}"><i class="bi bi-people"></i>Joueurs représentés</a>
<a href="{{ route('agent.transferts.index') }}" class="{{ request()->routeIs('agent.transferts.index') ? 'active' : '' }}"><i class="bi bi-arrow-left-right"></i>Transferts</a>
<a href="{{ route('agent.transferts.create') }}" class="{{ request()->routeIs('agent.transferts.create') ? 'active' : '' }}"><i class="bi bi-send"></i>Nouvelle offre</a>
<a href="{{ route('agent.transferts.historique') }}" class="{{ request()->routeIs('agent.transferts.historique') ? 'active' : '' }}"><i class="bi bi-clock-history"></i>Historique</a>
<a href="{{ route('agent.partenaires') }}" class="{{ request()->routeIs('agent.partenaires') ? 'active' : '' }}"><i class="bi bi-building"></i>Clubs partenaires</a>
<a href="{{ route('messages.inbox') }}" class="{{ request()->routeIs('messages.*') ? 'active' : '' }}"><i class="bi bi-chat-dots"></i>Messagerie</a>
<a href="{{ route('agent.profil') }}" class="{{ request()->routeIs('agent.profil*') ? 'active' : '' }}"><i class="bi bi-person-badge"></i>Profil accrédité</a>

<a href="{{ route('agent.commissions') }}" class="agent-sidebar-link {{ request()->routeIs('agent.commissions') ? 'active' : '' }}">
    <i class="bi bi-cash-coin"></i>Commissions
</a>
<a href="{{ $urlStandard('agent.opportunites.index') }}" class="agent-sidebar-link {{ !$isStandard ? 'opacity-75' : '' }} {{ request()->routeIs('agent.opportunites.*') ? 'active' : '' }}">
    <i class="bi bi-briefcase"></i>Opportunités
    @if(!$isStandard)<i class="bi bi-lock ms-auto"></i><span class="badge bg-warning text-dark rounded-pill ms-1" style="font-size:8px;">Standard</span>@endif
</a>

<a href="{{ $urlStandard('agents.index') }}" class="agent-sidebar-link {{ !$isStandard ? 'opacity-75' : '' }} {{ request()->routeIs('agents.*') ? 'active' : '' }}">
    <i class="bi bi-globe2"></i>Annuaire des agents
    @if(!$isStandard)<i class="bi bi-lock ms-auto"></i><span class="badge bg-warning text-dark rounded-pill ms-1" style="font-size:8px;">Standard</span>@endif
</a>

<a href="{{ $urlStatistiques }}" class="agent-sidebar-link {{ !$isPremium ? 'opacity-75' : '' }} {{ request()->routeIs('agent.statistiques') ? 'active' : '' }}">
    <i class="bi bi-bar-chart-line"></i>Statistiques avancées
    @if(!$isPremium)<i class="bi bi-lock ms-auto"></i><span class="badge bg-warning text-dark rounded-pill ms-1" style="font-size:8px;">Premium</span>@endif
</a>

<a href="{{ route('agent.abonnement.index') }}" class="agent-sidebar-link {{ request()->routeIs('agent.abonnement.*') ? 'active' : '' }}">
    <i class="bi bi-gem"></i>Abonnement
    <span class="badge {{ $isPremium ? 'bg-warning text-dark' : ($isStandard ? 'bg-primary' : 'bg-secondary') }} rounded-pill ms-auto" style="font-size:9px;">
        {{ ucfirst($niveauPlan) }}
    </span>
</a>
