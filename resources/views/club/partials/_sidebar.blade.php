{{-- resources/views/club/partials/_sidebar.blade.php --}}
@php
    if (!isset($club)) {
        $club = auth()->user()->club ?? null;
    }
    $user        = auth()->user();
    $avatarUrl   = $user?->avatar ? asset('storage/' . $user->avatar) : null;
    $clubLogoUrl = $club?->logo   ? asset('storage/' . $club->logo)   : null;
    $initiales   = $club ? strtoupper(substr($club->nom, 0, 2)) : 'CL';

    // Plan actif — logique CDC
    $subActif   = $club?->subscriptionActive();
    $plan       = $subActif?->plan;
    $planSlug   = $plan?->slug ?? 'gratuit';

    // Gratuit  : dashboard, joueurs, équipes, licences, agenda
    // Standard : + transferts, sponsors, opportunités
    // Premium  : + médias, stats avancées
    $isStandard = in_array($planSlug, ['standard', 'premium']);
    $isPremium  = $planSlug === 'premium';

    // Helpers pour générer l'URL selon accès
    $urlStandard = fn(string $route) => $isStandard
        ? route($route)
        : route('club.locked', ['planRequis' => 'standard']);

    $urlPremium = fn(string $route) => $isPremium
        ? route($route)
        : route('club.locked', ['planRequis' => 'premium']);
@endphp

{{-- Overlay mobile --}}
<div id="sidebar-overlay"
     class="d-none position-fixed top-0 start-0 w-100 h-100"
     style="background:rgba(0,0,0,.5);z-index:99;"
     onclick="closeSidebar()">
</div>

<aside id="clubSidebar"
       class="d-flex flex-column position-fixed top-0 start-0 h-100 border-end bg-white"
       style="width:240px;z-index:100;transition:transform .25s ease, width .25s ease;overflow:hidden;">

    {{-- Brand --}}
    <div class="d-flex align-items-center gap-2 px-3 py-3 border-bottom">
        @if($avatarUrl)
            <img src="{{ $avatarUrl }}" id="sidebar-brand-logo"
                 class="rounded-2 flex-shrink-0 object-fit-cover"
                 style="width:36px;height:36px;" alt="avatar">
        @elseif($clubLogoUrl)
            <img src="{{ $clubLogoUrl }}" id="sidebar-brand-logo"
                 class="rounded-2 flex-shrink-0 object-fit-cover"
                 style="width:36px;height:36px;" alt="logo">
        @else
            <div id="sidebar-brand-fallback"
                 class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0 fw-bold text-white"
                 style="width:36px;height:36px;background:#F97316;font-size:13px;">
                {{ $initiales }}
            </div>
        @endif

        <div class="overflow-hidden sidebar-text">
            <span class="d-block fw-bold text-truncate" style="font-size:13px;">
                {{ $club->nom ?? 'Mon Club' }}
            </span>
            <span class="d-block text-uppercase" style="font-size:10px;color:#F97316;letter-spacing:1px;">
                Club · {{ ucfirst($planSlug) }}
            </span>
        </div>

        <button class="btn btn-sm p-0 border-0 bg-transparent ms-auto d-lg-none text-secondary"
                onclick="closeSidebar()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-grow-1 overflow-auto py-2 px-2" style="scrollbar-width:none;">

        {{-- ── PRINCIPAL ── --}}
        <p class="text-uppercase fw-bold px-2 mb-1 mt-2 sidebar-section-label"
           style="font-size:9px;letter-spacing:1.5px;color:rgba(0,0,0,.3);">
            Principal
        </p>

        <ul class="list-unstyled mb-2">

            {{-- Dashboard — gratuit  --}}
            <li>
                <a href="{{ route('club.dashboard') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ request()->routeIs('club.dashboard') ? 'sidebar-link-active' : 'text-secondary' }}"
                   title="Dashboard">
                    <svg class="flex-shrink-0" width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7" rx="1"/>
                        <rect x="14" y="3" width="7" height="7" rx="1"/>
                        <rect x="14" y="14" width="7" height="7" rx="1"/>
                        <rect x="3" y="14" width="7" height="7" rx="1"/>
                    </svg>
                    <span class="small fw-medium sidebar-text">Dashboard</span>
                </a>
            </li>

            {{-- Joueurs — gratuit  --}}
            <li>
                <a href="{{ route('club.joueurs.index') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ request()->routeIs('club.joueurs.*') ? 'sidebar-link-active' : 'text-secondary' }}"
                   title="Joueurs">
                    <svg class="flex-shrink-0" width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span class="small fw-medium sidebar-text">Joueurs</span>
                </a>
            </li>

            {{-- Équipes — gratuit  --}}
            <li>
                <a href="{{ route('club.equipes.index') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ request()->routeIs('club.equipes.*') ? 'sidebar-link-active' : 'text-secondary' }}"
                   title="Équipes">
                    <svg class="flex-shrink-0" width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                        <path d="M2 17l10 5 10-5"/>
                        <path d="M2 12l10 5 10-5"/>
                    </svg>
                    <span class="small fw-medium sidebar-text">Équipes</span>
                </a>
            </li>

            {{-- Licences — gratuit  --}}
            <li>
                <a href="{{ route('club.licences.index') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ request()->routeIs('club.licences.*') ? 'sidebar-link-active' : 'text-secondary' }}"
                   title="Licences">
                    <svg class="flex-shrink-0" width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    <span class="small fw-medium sidebar-text">Licences</span>
                </a>
            </li>

            {{-- Agenda — gratuit ✅ --}}
            <li>
                <a href="{{ route('club.agenda.index') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ request()->routeIs('club.agenda.*') ? 'sidebar-link-active' : 'text-secondary' }}"
                   title="Agenda">
                    <svg class="flex-shrink-0" width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <span class="small fw-medium sidebar-text">Agenda</span>
                </a>
            </li>

            {{-- Transferts — standard 🔒 --}}
            <li>
                <a href="{{ $urlStandard('club.transferts.index') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ !$isStandard ? 'sidebar-link-locked' : '' }}
                          {{ request()->routeIs('club.transferts.*') ? 'sidebar-link-active' : 'text-secondary' }}"
                   title="{{ $isStandard ? 'Transferts' : 'Transferts — Plan Standard requis' }}">
                    <svg class="flex-shrink-0" width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
                        <path d="m7 17 10-10M17 7H7v10"/>
                    </svg>
                    <span class="small fw-medium sidebar-text {{ !$isStandard ? 'text-muted' : '' }}">
                        Transferts
                    </span>
                    @if(!$isStandard)
                        {{-- Cadenas toujours visible même collapsed --}}
                        <svg class="ms-auto flex-shrink-0" width="13" height="13"
                             viewBox="0 0 24 24" fill="none" stroke="#adb5bd" stroke-width="2.5">
                            <rect x="3" y="11" width="18" height="11" rx="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <span class="badge bg-warning text-dark rounded-pill sidebar-text ms-1"
                              style="font-size:8px;padding:2px 6px;">Standard</span>
                    @endif
                </a>
            </li>

        </ul>

        {{-- ── CROISSANCE ── --}}
        <p class="text-uppercase fw-bold px-2 mb-1 mt-3 sidebar-section-label"
           style="font-size:9px;letter-spacing:1.5px;color:rgba(0,0,0,.3);">
            Croissance
        </p>

        <ul class="list-unstyled mb-2">

            {{-- Sponsors — standard  --}}
            <li>
                <a href="{{ $urlStandard('club.sponsors.index') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ !$isStandard ? 'sidebar-link-locked' : '' }}
                          {{ request()->routeIs('club.sponsors.*') ? 'sidebar-link-active' : 'text-secondary' }}"
                   title="{{ $isStandard ? 'Sponsors' : 'Sponsors — Plan Standard requis' }}">
                    <svg class="flex-shrink-0" width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <span class="small fw-medium sidebar-text {{ !$isStandard ? 'text-muted' : '' }}">
                        Sponsors
                    </span>
                    @if(!$isStandard)
                        <svg class="ms-auto flex-shrink-0" width="13" height="13"
                             viewBox="0 0 24 24" fill="none" stroke="#adb5bd" stroke-width="2.5">
                            <rect x="3" y="11" width="18" height="11" rx="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <span class="badge bg-warning text-dark rounded-pill sidebar-text ms-1"
                              style="font-size:8px;padding:2px 6px;">Standard</span>
                    @endif
                </a>
            </li>

            {{-- Médias — premium 🔒🔒 --}}
            <li>
                <a href="{{ $urlPremium('club.medias.index') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ !$isPremium ? 'sidebar-link-locked' : '' }}
                          {{ request()->routeIs('club.medias.*') ? 'sidebar-link-active' : 'text-secondary' }}"
                   title="{{ $isPremium ? 'Médias' : 'Médias — Plan Premium requis' }}">
                    <svg class="flex-shrink-0" width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21 15 16 10 5 21"/>
                    </svg>
                    <span class="small fw-medium sidebar-text {{ !$isPremium ? 'text-muted' : '' }}">
                        Médias
                    </span>
                    @if(!$isPremium)
                        <svg class="ms-auto flex-shrink-0" width="13" height="13"
                             viewBox="0 0 24 24" fill="none" stroke="#adb5bd" stroke-width="2.5">
                            <rect x="3" y="11" width="18" height="11" rx="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <span class="badge bg-warning text-dark rounded-pill sidebar-text ms-1"
                              style="font-size:8px;padding:2px 6px;">Premium</span>
                    @endif
                </a>
            </li>

            {{-- Opportunités — standard 🔒 --}}
            <li>
                <a href="{{ $urlStandard('club.opportunites.index') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ !$isStandard ? 'sidebar-link-locked' : '' }}
                          {{ request()->routeIs('club.opportunites.*') ? 'sidebar-link-active' : 'text-secondary' }}"
                   title="{{ $isStandard ? 'Opportunités' : 'Opportunités — Plan Standard requis' }}">
                    <svg class="flex-shrink-0" width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <span class="small fw-medium sidebar-text {{ !$isStandard ? 'text-muted' : '' }}">
                        Opportunités
                    </span>
                    @if(!$isStandard)
                        <svg class="ms-auto flex-shrink-0" width="13" height="13"
                             viewBox="0 0 24 24" fill="none" stroke="#adb5bd" stroke-width="2.5">
                            <rect x="3" y="11" width="18" height="11" rx="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <span class="badge bg-warning text-dark rounded-pill sidebar-text ms-1"
                              style="font-size:8px;padding:2px 6px;">Standard</span>
                    @endif
                </a>
            </li>

            {{-- Abonnement — tous ✅ --}}
            <li>
                <a href="{{ route('club.abonnement.index') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ request()->routeIs('club.abonnement.*') ? 'sidebar-link-active' : 'text-secondary' }}"
                   title="Abonnement">
                    <svg class="flex-shrink-0" width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <span class="small fw-medium sidebar-text">Abonnement</span>
                    @php
                        $badgeCls = match($planSlug) {
                            'premium'  => 'bg-warning text-dark',
                            'standard' => 'bg-primary',
                            default    => 'bg-secondary',
                        };
                    @endphp
                    <span class="badge {{ $badgeCls }} rounded-pill ms-auto sidebar-text"
                          style="font-size:9px;">
                        {{ ucfirst($planSlug) }}
                    </span>
                </a>
            </li>

        </ul>

        <hr class="my-2 opacity-25">

        {{-- Paramètres — tous ✅ --}}
        <ul class="list-unstyled">
            <li>
                <a href="{{ route('club.parametres.index') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ request()->routeIs('club.parametres.*') ? 'sidebar-link-active' : 'text-secondary' }}"
                   title="Paramètres">
                    <svg class="flex-shrink-0" width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                    </svg>
                    <span class="small fw-medium sidebar-text">Paramètres</span>
                </a>
            </li>
        </ul>

    </nav>

    {{-- Zone utilisateur --}}
    <div class="border-top px-2 py-2">
        <div class="d-flex align-items-center gap-2 px-2 py-2 rounded-2"
             style="background:rgba(0,0,0,.04);">
            <label for="sidebar-logo-upload"
                   class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0 fw-bold text-white mb-0"
                   style="width:30px;height:30px;background:#F97316;cursor:pointer;overflow:hidden;"
                   title="Changer le logo">
                @if(optional($club)->logo)
                    <img src="{{ asset('storage/' . $club->logo) }}"
                         id="sidebar-avatar-img"
                         style="width:30px;height:30px;object-fit:cover;">
                @else
                    <span id="sidebar-avatar-initiales" style="font-size:11px;">{{ $initiales }}</span>
                @endif
                <input type="file" id="sidebar-logo-upload" accept="image/*" class="d-none">
            </label>

            <div class="flex-grow-1 overflow-hidden sidebar-text">
                <span class="d-block fw-semibold text-truncate" style="font-size:12px;">
                    {{ $club->nom ?? $user->name }}
                </span>
                <span class="d-block text-muted" style="font-size:10px;">Administrateur</span>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                @csrf
                <button type="submit"
                        class="btn btn-sm p-1 border-0 bg-transparent text-secondary"
                        title="Déconnexion">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                </button>
            </form>
        </div>

        <div id="sidebar-toast"
             class="d-none text-center rounded-2 mt-1 py-1"
             style="font-size:11px;background:rgba(16,185,129,.15);color:#10b981;">
            Logo mis à jour ✓
        </div>
    </div>

    {{-- Toggle desktop --}}
    <button id="sidebarToggle"
            class="position-absolute btn p-0 d-none d-lg-flex align-items-center justify-content-center rounded-circle bg-white border"
            style="width:22px;height:22px;top:50%;right:-11px;transform:translateY(-50%);z-index:10;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
             width="10" height="10">
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
    [data-bs-theme="dark"] #clubSidebar {
        background: #13151c !important;
        border-color: rgba(255,255,255,.07) !important;
    }
    [data-bs-theme="dark"] #clubSidebar .text-secondary {
        color: rgba(255,255,255,.55) !important;
    }
    [data-bs-theme="dark"] #clubSidebar .sidebar-section-label {
        color: rgba(255,255,255,.25) !important;
    }
    [data-bs-theme="dark"] #clubSidebar [style*="background:rgba(0,0,0,.04)"] {
        background: rgba(255,255,255,.04) !important;
    }
    [data-bs-theme="dark"] #sidebarToggle {
        background: #13151c !important;
        border-color: rgba(255,255,255,.15) !important;
        color: rgba(255,255,255,.4) !important;
    }

    .sidebar-link { transition: background .15s, color .15s; }
    .sidebar-link:hover { background: var(--sb-hover-bg); }
    .sidebar-link-active { background: var(--sb-active-bg) !important; color: var(--sb-active-text) !important; }

    /* Lien verrouillé — légèrement atténué, pas de hover orange */
    .sidebar-link-locked { opacity: .7; }
    .sidebar-link-locked:hover { background: var(--sb-hover-bg) !important; color: inherit !important; }

    @media (min-width: 992px) {
        #clubSidebar { transform: none !important; }
        #clubSidebar.collapsed { width: 64px !important; }
        #clubSidebar.collapsed .sidebar-text,
        #clubSidebar.collapsed .sidebar-section-label,
        #clubSidebar.collapsed hr { display: none !important; }
        #clubSidebar.collapsed .sidebar-link {
            justify-content: center !important;
            padding: 10px !important;
        }
        #clubSidebar.collapsed .d-flex.align-items-center.gap-2.px-2.py-2 {
            justify-content: center !important;
        }
    }

    @media (max-width: 991px) {
        #clubSidebar {
            transform: translateX(-100%);
            width: 240px !important;
            box-shadow: 4px 0 24px rgba(0,0,0,.15);
        }
        #clubSidebar.mobile-open { transform: translateX(0); }
    }
</style>

<script>
(function () {
    const sidebar = document.getElementById('clubSidebar');
    const btnDesk = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('sidebar-overlay');
    const KEY     = 'cs_sidebar_collapsed';

    function updateMainMargin() {
        const main = document.querySelector('.main-with-sidebar');
        if (!main) return;
        main.style.marginLeft = (window.innerWidth < 992)
            ? '0'
            : (sidebar.classList.contains('collapsed') ? '64px' : '240px');
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

    if (window.innerWidth >= 992) {
        applyDesktop(localStorage.getItem(KEY) === '1');
    } else {
        updateMainMargin();
    }

    if (btnDesk) btnDesk.addEventListener('click', () => applyDesktop(!sidebar.classList.contains('collapsed')));

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            sidebar.classList.remove('mobile-open');
            if (overlay) overlay.classList.add('d-none');
            applyDesktop(localStorage.getItem(KEY) === '1');
        } else {
            updateMainMargin();
        }
    });
})();

// Upload logo
document.getElementById('sidebar-logo-upload').addEventListener('change', async function () {
    const file = this.files[0];
    if (!file) return;
    if (file.size > 2 * 1024 * 1024) { alert('Max 2 Mo.'); return; }

    const fd = new FormData();
    fd.append('logo', file);
    fd.append('_method', 'PUT');

    try {
        const res = await fetch('{{ route('club.parametres.profil') }}', {
            method: 'POST', body: fd,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        });
        if (!res.ok) throw new Error();

        const reader = new FileReader();
        reader.onload = e => {
            const src = e.target.result;
            const img  = document.getElementById('sidebar-avatar-img');
            const ini  = document.getElementById('sidebar-avatar-initiales');
            const brand = document.getElementById('sidebar-brand-logo');
            const bfall = document.getElementById('sidebar-brand-fallback');
            if (img)   img.src = src;
            else if (ini) ini.outerHTML = `<img id="sidebar-avatar-img" src="${src}" style="width:30px;height:30px;object-fit:cover;">`;
            if (brand) brand.src = src;
            else if (bfall) bfall.outerHTML = `<img id="sidebar-brand-logo" src="${src}" class="rounded-2 flex-shrink-0 object-fit-cover" style="width:36px;height:36px;">`;
            const preview = document.getElementById('profil-logo-preview');
            if (preview) preview.src = src;
        };
        reader.readAsDataURL(file);

        const toast = document.getElementById('sidebar-toast');
        toast.classList.remove('d-none');
        setTimeout(() => toast.classList.add('d-none'), 3000);
    } catch { alert('Erreur lors de la mise à jour du logo.'); }
});
</script>