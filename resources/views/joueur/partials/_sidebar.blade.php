{{-- resources/views/joueur/partials/_sidebar.blade.php --}}
@php
    if (!isset($joueur)) {
        $joueur = auth()->user()->joueur ?? null;
    }
    $user = auth()->user();
    $avatarUrl = $user->avatar ? asset('storage/' . $user->avatar) : null;
    $joueurAvatarUrl = $joueur?->avatar ? asset('storage/' . $joueur->avatar) : null;
    $initiales = $joueur
        ? strtoupper(substr($joueur->prenom ?? $user->prenom ?? 'U', 0, 1) . substr($joueur->nom ?? $user->name ?? 'J', 0, 1))
        : 'J';

    // Plan actif — source de vérité : abonnements (même logique que le club)
    $subActif  = $joueur?->subscriptionActive();
    $planSlug  = $subActif?->plan?->slug ?? 'gratuit';
    $isStandard = in_array($planSlug, ['standard', 'premium']);
    $isPremium  = $planSlug === 'premium';
    $planNom    = $subActif?->plan?->nom ?? ucfirst($planSlug);
    $options    = [
        ['label' => 'Options Standard', 'active' => $isStandard],
        ['label' => 'Options Premium', 'active' => $isPremium],
    ];
    $badgeClass = match($planSlug) {
        'premium'  => 'bg-warning text-dark',
        'standard' => 'bg-primary',
        default    => 'bg-secondary',
    };

    // Helpers pour générer l'URL selon accès (même pattern que le club)
    $urlStandard = fn(string $route) => $isStandard
        ? route($route)
        : route('joueur.locked', ['fonction' => $route, 'plan' => 'standard']);

    $urlPremium = fn(string $route) => $isPremium
        ? route($route)
        : route('joueur.locked', ['fonction' => $route, 'plan' => 'premium']);
@endphp

{{-- Overlay mobile --}}
<div id="sidebar-overlay"
     class="d-none position-fixed top-0 start-0 w-100 h-100"
     style="background:rgba(0,0,0,.5);z-index:99;"
     onclick="closeSidebar()">
</div>

<aside id="joueurSidebar"
       class="d-flex flex-column position-fixed top-0 start-0 h-100 border-end bg-white"
       style="width:240px;z-index:100;transition:transform .25s ease, width .25s ease;overflow:hidden;">

    {{-- Brand / Profil --}}
    <div class="d-flex align-items-center gap-2 px-3 py-3 border-bottom">
        @if($avatarUrl || $joueurAvatarUrl)
            <img src="{{ $avatarUrl ?? $joueurAvatarUrl }}" id="sidebar-avatar-img"
                 class="rounded-2 flex-shrink-0 object-fit-cover"
                 style="width:36px;height:36px;" alt="avatar">
        @else
            <div id="sidebar-avatar-fallback"
                 class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0 fw-bold text-white"
                 style="width:36px;height:36px;background:#F97316;font-size:13px;">
                {{ $initiales }}
            </div>
        @endif

        <div class="overflow-hidden sidebar-text">
            <span class="d-block fw-bold text-truncate" style="font-size:13px;">
                {{ $joueur->prenom ?? $user->prenom ?? $user->name }}
            </span>
            <span class="d-block text-uppercase" style="font-size:10px;color:#F97316;letter-spacing:1px;">
                Joueur · {{ ucfirst($planSlug) }}
            </span>
        </div>

        <button class="btn btn-sm p-0 border-0 bg-transparent ms-auto d-lg-none text-secondary"
                onclick="closeSidebar()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>

    {{-- Résumé abonnement et droits --}}
    <div class="sidebar-plan-card mx-2 mt-2 p-2 rounded-3 border"
         style="background:rgba(249,115,22,.06);border-color:rgba(249,115,22,.18) !important;">
        <div class="d-flex align-items-center justify-content-between gap-2">
            <span class="text-uppercase fw-bold text-muted sidebar-text" style="font-size:9px;letter-spacing:.8px;">
                Abonnement actuel
            </span>
            <span class="badge {{ $badgeClass ?? 'bg-secondary' }} rounded-pill sidebar-text" style="font-size:8px;">
                {{ $planNom }}
            </span>
        </div>
        @if($subActif?->fin_at)
            <div class="small text-muted sidebar-text mt-1">
                Jusqu'au {{ $subActif->fin_at->format('d/m/Y') }}
            </div>
        @endif
        <div class="mt-2 sidebar-text">
            @foreach($options as $option)
                <div class="d-flex align-items-center gap-1 small {{ $option['active'] ? 'text-success' : 'text-muted' }}">
                    <i class="bi {{ $option['active'] ? 'bi-check-circle-fill' : 'bi-lock-fill' }}" style="font-size:10px;"></i>
                    <span>{{ $option['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-grow-1 overflow-auto py-2 px-2" style="scrollbar-width:none;">

        {{-- ── PRINCIPAL ── --}}
        <p class="text-uppercase fw-bold px-2 mb-1 mt-2 sidebar-section-label"
           style="font-size:9px;letter-spacing:1.5px;color:rgba(0,0,0,.3);">Principal</p>

        <ul class="list-unstyled mb-2">

            {{-- Dashboard — gratuit  --}}
            <li>
                <a href="{{ route('joueur.dashboard') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ request()->routeIs('joueur.dashboard') ? 'sidebar-link-active' : 'text-secondary' }}">
                    <i class="bi bi-speedometer2 fs-6"></i>
                    <span class="small fw-medium sidebar-text">Dashboard</span>
                </a>
            </li>

            {{-- Mon profil — gratuit  --}}
            <li>
                <a href="{{ route('joueur.profil') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ request()->routeIs('joueur.profil') ? 'sidebar-link-active' : 'text-secondary' }}">
                    <i class="bi bi-person-circle fs-6"></i>
                    <span class="small fw-medium sidebar-text">Mon profil</span>
                </a>
            </li>

            {{-- Difficultés & Souhaits — gratuit  --}}
            <li>
                <a href="{{ route('joueur.difficultes.index') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ request()->routeIs('joueur.difficultes.*') ? 'sidebar-link-active' : 'text-secondary' }}">
                    <i class="bi bi-chat-dots fs-6"></i>
                    <span class="small fw-medium sidebar-text">Difficultés & Souhaits</span>
                </a>
            </li>

            {{-- Carrière — standard --}}
            <li>
                <a href="{{ $urlStandard('joueur.carriere.index') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ !$isStandard ? 'sidebar-link-locked' : '' }}
                          {{ request()->routeIs('joueur.carriere.*') && !request()->routeIs('joueur.carriere.recommandations') ? 'sidebar-link-active' : 'text-secondary' }}"
                   title="{{ $isStandard ? 'Carrière' : 'Carrière — Plan Standard requis' }}">
                    <i class="bi bi-graph-up fs-6 {{ !$isStandard ? 'text-muted' : '' }}"></i>
                    <span class="small fw-medium sidebar-text {{ !$isStandard ? 'text-muted' : '' }}">Carrière</span>
                    @if(!$isStandard)
                        <i class="bi bi-lock ms-auto text-muted"></i>
                        <span class="badge bg-warning text-dark rounded-pill sidebar-text ms-1" style="font-size:8px;padding:2px 6px;">Standard</span>
                    @endif
                </a>
            </li>

            {{-- Transferts — standard  --}}
            <li>
                <a href="{{ $urlStandard('joueur.transferts.index') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ !$isStandard ? 'sidebar-link-locked' : '' }}
                          {{ request()->routeIs('joueur.transferts.*') ? 'sidebar-link-active' : 'text-secondary' }}"
                   title="{{ $isStandard ? 'Transferts' : 'Transferts — Plan Standard requis' }}">
                    <i class="bi bi-arrow-left-right fs-6 {{ !$isStandard ? 'text-muted' : '' }}"></i>
                    <span class="small fw-medium sidebar-text {{ !$isStandard ? 'text-muted' : '' }}">Transferts</span>
                    @if(!$isStandard)
                        <i class="bi bi-lock ms-auto text-muted"></i>
                        <span class="badge bg-warning text-dark rounded-pill sidebar-text ms-1" style="font-size:8px;padding:2px 6px;">Standard</span>
                    @endif
                </a>
            </li>

        </ul>

        {{-- ── CROISSANCE ── --}}
        <p class="text-uppercase fw-bold px-2 mb-1 mt-3 sidebar-section-label"
           style="font-size:9px;letter-spacing:1.5px;color:rgba(0,0,0,.3);">Croissance</p>

        <ul class="list-unstyled mb-2">

            {{-- Opportunités — standard  --}}
            <li>
                <a href="{{ $urlStandard('joueur.opportunites.index') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ !$isStandard ? 'sidebar-link-locked' : '' }}
                          {{ request()->routeIs('joueur.opportunites.index') ? 'sidebar-link-active' : 'text-secondary' }}"
                   title="{{ $isStandard ? 'Opportunités' : 'Opportunités — Plan Standard requis' }}">
                    <i class="bi bi-lightning fs-6 {{ !$isStandard ? 'text-muted' : '' }}"></i>
                    <span class="small fw-medium sidebar-text {{ !$isStandard ? 'text-muted' : '' }}">Opportunités</span>
                    @if(!$isStandard)
                        <i class="bi bi-lock ms-auto text-muted"></i>
                        <span class="badge bg-warning text-dark rounded-pill sidebar-text ms-1" style="font-size:8px;padding:2px 6px;">Standard</span>
                    @endif
                </a>
            </li>

            {{-- IA Recommandations — premium  --}}
            <li>
                <a href="{{ $urlPremium('joueur.carriere.recommandations') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ !$isPremium ? 'sidebar-link-locked' : '' }}
                          {{ request()->routeIs('joueur.carriere.recommandations') ? 'sidebar-link-active' : 'text-secondary' }}"
                   title="{{ $isPremium ? 'IA Recommandations' : 'IA Recommandations — Plan Premium requis' }}">
                    <i class="bi bi-robot fs-6 {{ !$isPremium ? 'text-muted' : '' }}"></i>
                    <span class="small fw-medium sidebar-text {{ !$isPremium ? 'text-muted' : '' }}">IA </span>
                    @if(!$isPremium)
                        <i class="bi bi-lock ms-auto text-muted"></i>
                        <span class="badge bg-warning text-dark rounded-pill sidebar-text ms-1" style="font-size:8px;padding:2px 6px;">Premium</span>
                    @endif
                </a>
            </li>

            {{-- Abonnement — tous  --}}
            <li>
                <a href="{{ route('joueur.abonnement.index') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ request()->routeIs('joueur.abonnement.*') ? 'sidebar-link-active' : 'text-secondary' }}">
                    <i class="bi bi-gem fs-6"></i>
                    <span class="small fw-medium sidebar-text">Abonnement</span>
                    <span class="badge {{ $badgeClass }} rounded-pill ms-auto sidebar-text" style="font-size:9px;">
                        {{ ucfirst($planSlug) }}
                    </span>
                </a>
            </li>

        </ul>
    </nav>

    {{-- Zone utilisateur – upload avatar --}}
    <div class="border-top px-2 py-2">
        <div class="d-flex align-items-center gap-2 px-2 py-2 rounded-2" style="background:rgba(0,0,0,.04);">
            <label for="sidebar-avatar-upload"
                   class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0 fw-bold text-white mb-0"
                   style="width:30px;height:30px;background:#F97316;cursor:pointer;overflow:hidden;">
                @if($avatarUrl || $joueurAvatarUrl)
                    <img src="{{ $avatarUrl ?? $joueurAvatarUrl }}" id="sidebar-avatar-img" style="width:30px;height:30px;object-fit:cover;">
                @else
                    <span id="sidebar-avatar-initiales" style="font-size:11px;">{{ $initiales }}</span>
                @endif
                <input type="file" id="sidebar-avatar-upload" accept="image/*" class="d-none">
            </label>

            <div class="flex-grow-1 overflow-hidden sidebar-text">
                <span class="d-block fw-semibold text-truncate" style="font-size:12px;">
                    {{ $joueur->prenom ?? $user->prenom ?? $user->name }}
                </span>
                <span class="d-block text-muted" style="font-size:10px;">Joueur</span>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                @csrf
                <button type="submit" class="btn btn-sm p-1 border-0 bg-transparent text-secondary" title="Déconnexion">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
        <div id="sidebar-toast" class="d-none text-center rounded-2 mt-1 py-1"
             style="font-size:11px;background:rgba(16,185,129,.15);color:#10b981;">
            Avatar mis à jour ✓
        </div>
    </div>

    {{-- Toggle desktop --}}
    <button id="sidebarToggle"
            class="position-absolute btn p-0 d-none d-lg-flex align-items-center justify-content-center rounded-circle bg-white border"
            style="width:22px;height:22px;top:50%;right:-11px;transform:translateY(-50%);z-index:10;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="10" height="10">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
    </button>
</aside>

<style>
    :root, [data-bs-theme="light"] {
        --sb-active-bg:   rgba(249,115,22,.10);
        --sb-active-text: #F97316;
        --sb-hover-bg:    rgba(0,0,0,.04);
    }
    [data-bs-theme="dark"] {
        --sb-active-bg:   rgba(249,115,22,.12);
        --sb-active-text: #F97316;
        --sb-hover-bg:    rgba(255,255,255,.06);
    }
    [data-bs-theme="dark"] #joueurSidebar {
        background: #13151c !important;
        border-color: rgba(255,255,255,.07) !important;
    }
    [data-bs-theme="dark"] #joueurSidebar .text-secondary {
        color: rgba(255,255,255,.55) !important;
    }
    [data-bs-theme="dark"] #joueurSidebar .sidebar-section-label {
        color: rgba(255,255,255,.25) !important;
    }
    [data-bs-theme="dark"] #joueurSidebar [style*="background:rgba(0,0,0,.04)"] {
        background: rgba(255,255,255,.04) !important;
    }
    [data-bs-theme="dark"] #joueurSidebar .sidebar-plan-card {
        background: rgba(249,115,22,.10) !important;
        border-color: rgba(249,115,22,.25) !important;
    }
    [data-bs-theme="dark"] #sidebarToggle {
        background: #13151c !important;
        border-color: rgba(255,255,255,.15) !important;
        color: rgba(255,255,255,.4) !important;
    }

    .sidebar-link { transition: background .15s, color .15s; }
    .sidebar-link:hover { background: var(--sb-hover-bg); color: inherit !important; }
    .sidebar-link-active { background: var(--sb-active-bg) !important; color: var(--sb-active-text) !important; }
    .sidebar-link-locked { opacity: .7; }
    .sidebar-link-locked:hover { background: var(--sb-hover-bg) !important; color: inherit !important; }

    @media (min-width: 992px) {
        #joueurSidebar { transform: none !important; }
        #joueurSidebar.collapsed { width: 64px !important; }
        #joueurSidebar.collapsed .sidebar-text,
        #joueurSidebar.collapsed .sidebar-section-label,
        #joueurSidebar.collapsed hr,
        #joueurSidebar.collapsed .sidebar-plan-card { display: none !important; }
        #joueurSidebar.collapsed .sidebar-link { justify-content: center !important; padding: 10px !important; }
        #joueurSidebar.collapsed .d-flex.align-items-center.gap-2.px-2.py-2 { justify-content: center !important; }
    }

    @media (max-width: 991px) {
        #joueurSidebar { transform: translateX(-100%); width: 240px !important; box-shadow: 4px 0 24px rgba(0,0,0,.15); }
        #joueurSidebar.mobile-open { transform: translateX(0); }
    }
</style>

<script>
(function () {
    const sidebar = document.getElementById('joueurSidebar');
    const btnDesk = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('sidebar-overlay');
    const KEY     = 'joueur_sidebar_collapsed';

    function updateMainMargin() {
        const main = document.querySelector('.main-with-sidebar');
        if (!main) return;
        main.style.marginLeft = (window.innerWidth < 992) ? '0' : (sidebar.classList.contains('collapsed') ? '64px' : '240px');
    }

    function applyDesktop(collapsed) {
        sidebar.classList.toggle('collapsed', collapsed);
        document.body.classList.toggle('sidebar-collapsed', collapsed);
        if (btnDesk) btnDesk.querySelector('svg').style.transform = collapsed ? 'rotate(180deg)' : '';
        localStorage.setItem(KEY, collapsed ? '1' : '0');
        updateMainMargin();
    }

    window.toggleMobileSidebar = function () {
        const open = sidebar.classList.contains('mobile-open');
        sidebar.classList.toggle('mobile-open', !open);
        if (overlay) overlay.classList.toggle('d-none', open);
    };
    window.closeSidebar = function () {
        sidebar.classList.remove('mobile-open');
        if (overlay) overlay.classList.add('d-none');
    };

    if (window.innerWidth >= 992) applyDesktop(localStorage.getItem(KEY) === '1');
    else updateMainMargin();

    if (btnDesk) btnDesk.addEventListener('click', () => applyDesktop(!sidebar.classList.contains('collapsed')));

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            sidebar.classList.remove('mobile-open');
            if (overlay) overlay.classList.add('d-none');
            applyDesktop(localStorage.getItem(KEY) === '1');
        } else updateMainMargin();
    });
})();

// Upload avatar
document.getElementById('sidebar-avatar-upload').addEventListener('change', async function () {
    const file = this.files[0];
    if (!file) return;
    if (file.size > 2 * 1024 * 1024) { alert('Max 2 Mo.'); return; }

    const fd = new FormData();
    fd.append('avatar', file);
    fd.append('_method', 'PUT');

    try {
        const res = await fetch('{{ route('joueur.profil.update') }}', {
            method: 'POST', body: fd,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        });
        if (!res.ok) throw new Error();

        const reader = new FileReader();
        reader.onload = e => {
            const src = e.target.result;
            const img = document.getElementById('sidebar-avatar-img');
            const ini = document.getElementById('sidebar-avatar-initiales');
            if (img) img.src = src;
            else if (ini) ini.outerHTML = `<img id="sidebar-avatar-img" src="${src}" style="width:30px;height:30px;object-fit:cover;">`;
            const headerAvatar = document.querySelector('.navbar .rounded-circle img');
            if (headerAvatar) headerAvatar.src = src;
        };
        reader.readAsDataURL(file);

        const toast = document.getElementById('sidebar-toast');
        toast.classList.remove('d-none');
        setTimeout(() => toast.classList.add('d-none'), 3000);
    } catch { alert('Erreur lors de la mise à jour de l’avatar.'); }
});
</script>