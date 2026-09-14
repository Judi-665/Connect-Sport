<div class="cs-sidebar-section">Espace agent</div>
<a href="{{ route('agent.dashboard') }}" class="{{ request()->routeIs('agent.dashboard') ? 'active' : '' }}"><i class="bi bi-grid-1x2"></i>Tableau de bord</a>
<a href="{{ route('agent.joueurs') }}" class="{{ request()->routeIs('agent.joueurs') ? 'active' : '' }}"><i class="bi bi-people"></i>Joueurs représentés</a>
<a href="{{ route('agent.transferts') }}" class="{{ request()->routeIs('agent.transferts') && !request()->routeIs('agent.transferts.*') ? 'active' : '' }}"><i class="bi bi-arrow-left-right"></i>Transferts</a>
<a href="{{ route('agent.transferts.create') }}" class="{{ request()->routeIs('agent.transferts.create') ? 'active' : '' }}"><i class="bi bi-send"></i>Nouvelle offre</a>
<a href="{{ route('agent.transferts.historique') }}" class="{{ request()->routeIs('agent.transferts.historique') ? 'active' : '' }}"><i class="bi bi-clock-history"></i>Historique</a>
<a href="{{ route('agent.partenaires') }}" class="{{ request()->routeIs('agent.partenaires') ? 'active' : '' }}"><i class="bi bi-building"></i>Clubs partenaires</a>
<a href="{{ route('messages.inbox') }}"><i class="bi bi-chat-dots"></i>Messagerie</a>
<a href="{{ route('agent.profil') }}" class="{{ request()->routeIs('agent.profil*') ? 'active' : '' }}"><i class="bi bi-person-badge"></i>Profil accrédité</a>
