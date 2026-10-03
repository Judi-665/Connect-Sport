<aside id="agentSidebar" class="d-flex flex-column position-fixed top-0 start-0 h-100 bg-dark text-white"
       style="width:240px;z-index:100;overflow:hidden;">
    <div class="d-flex align-items-center gap-2 px-3 py-3 border-bottom border-secondary">
        <img src="{{ asset('images/logo.jpg') }}" alt="Connect Sport" class="rounded-circle flex-shrink-0" style="width:36px;height:36px;object-fit:cover;">
        <div class="overflow-hidden">
            <strong class="d-block text-truncate">Connect Sport</strong>
            <small class="text-warning text-uppercase">Espace agent</small>
        </div>
        <button type="button" class="btn btn-sm p-0 ms-auto d-lg-none text-white" onclick="closeAgentSidebar()" aria-label="Fermer la sidebar">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <nav class="flex-grow-1 overflow-auto p-2">
        <div class="text-uppercase small fw-bold text-white-50 px-2 py-2">Espace agent</div>
        <a href="{{ route('agent.dashboard') }}" class="agent-sidebar-link {{ request()->routeIs('agent.dashboard') ? 'active' : '' }}"><i class="bi bi-grid-1x2"></i>Tableau de bord</a>
        <a href="{{ route('agent.joueurs') }}" class="agent-sidebar-link {{ request()->routeIs('agent.joueurs') ? 'active' : '' }}">
    <i class="bi bi-people"></i>Joueurs représentés
    @php $echeances = auth()->user()->agent?->mandatsExpirantBientot()->count() ?? 0; @endphp
    @if($echeances > 0)
    <span class="badge bg-danger rounded-pill ms-auto" style="font-size:9px;">{{ $echeances }}</span>
    @endif
</a>
        <a href="{{ route('agent.transferts.index') }}" class="agent-sidebar-link {{ request()->routeIs('agent.transferts.index') ? 'active' : '' }}"><i class="bi bi-arrow-left-right"></i>Transferts</a>
        <a href="{{ route('agent.transferts.create') }}" class="agent-sidebar-link {{ request()->routeIs('agent.transferts.create') ? 'active' : '' }}"><i class="bi bi-send"></i>Nouvelle offre</a>
        <a href="{{ route('agent.transferts.historique') }}" class="agent-sidebar-link {{ request()->routeIs('agent.transferts.historique') ? 'active' : '' }}"><i class="bi bi-clock-history"></i>Historique</a>
        <a href="{{ route('agent.partenaires') }}" class="agent-sidebar-link {{ request()->routeIs('agent.partenaires') ? 'active' : '' }}"><i class="bi bi-building"></i>Clubs partenaires</a>
        <a href="{{ route('messages.inbox') }}" class="agent-sidebar-link"><i class="bi bi-chat-dots"></i>Messagerie</a>
        <a href="{{ route('agent.profil') }}" class="agent-sidebar-link {{ request()->routeIs('agent.profil*') ? 'active' : '' }}"><i class="bi bi-person-badge"></i>Profil accrédité</a>
    </nav>

    <div class="border-top border-secondary p-3 d-flex align-items-center gap-2">
        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:36px;height:36px;">
            {{ strtoupper(substr(auth()->user()->prenom ?? auth()->user()->name, 0, 1)) }}
        </div>
        <div class="overflow-hidden">
            <div class="fw-semibold text-truncate small">{{ auth()->user()->prenom ?? auth()->user()->name }}</div>
            <div class="text-white-50 text-uppercase" style="font-size:10px;">Agent</div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="ms-auto">
            @csrf
            <button type="submit" class="btn btn-sm p-0 border-0 bg-transparent text-white-50" title="Déconnexion">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</aside>

<style>
    .agent-sidebar-link {
        display: flex;
        align-items: center;
        gap: .65rem;
        color: rgba(255,255,255,.65);
        text-decoration: none;
        padding: .6rem .75rem;
        border-radius: .4rem;
        margin-bottom: .15rem;
        font-size: .9rem;
    }
    .agent-sidebar-link:hover,
    .agent-sidebar-link.active {
        color: #fff;
        background: #1a56a0;
    }
    .agent-sidebar-link i { width: 1.2rem; text-align: center; }
    @media (max-width: 991px) {
        #agentSidebar { transform: translateX(-100%); transition: transform .25s ease; }
        #agentSidebar.mobile-open { transform: translateX(0); }
    }
</style>

<script>
    window.closeAgentSidebar = function () {
        document.getElementById('agentSidebar')?.classList.remove('mobile-open');
        document.getElementById('sidebar-overlay')?.classList.add('d-none');
    };
    window.toggleMobileSidebar = function () {
        const sidebar = document.getElementById('agentSidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const open = sidebar?.classList.toggle('mobile-open');
        overlay?.classList.toggle('d-none', !open);
    };
</script>
