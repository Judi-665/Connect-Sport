{{-- resources/views/admin/partials/_sidebar.blade.php --}}
@php
    $user      = auth()->user();
    $initiales = strtoupper(substr($user->name ?? 'AD', 0, 2));
    $agentsEnAttenteNav = \App\Models\Agent::where('verifie', false)->count();
@endphp

{{-- Overlay mobile --}}
<div id="sidebar-overlay"
     class="d-none position-fixed top-0 start-0 w-100 h-100"
     style="background:rgba(0,0,0,.5);z-index:99;"
     onclick="closeSidebar()">
</div>

<aside id="adminSidebar"
       class="d-flex flex-column position-fixed top-0 start-0 h-100 border-end bg-white"
       style="width:240px;z-index:100;transition:transform .25s ease, width .25s ease;overflow:hidden;">

    {{-- Brand --}}
    <div class="d-flex align-items-center gap-2 px-3 py-3 border-bottom">
        <div id="sidebar-brand-fallback"
             class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0 fw-bold text-white"
             style="width:36px;height:36px;background:#1A56A0;font-size:13px;">
            <i class="bi bi-shield-check"></i>
        </div>

        <div class="overflow-hidden sidebar-text">
            <span class="d-block fw-bold text-truncate" style="font-size:13px;color:var(--text);">
                {{ $user->name ?? 'Administrateur' }}
            </span>
            <span class="d-block text-uppercase fw-semibold" style="font-size:10px;color:#1A56A0;letter-spacing:1px;">
                SUPER ADMIN
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
           style="font-size:9px;letter-spacing:1.5px;color:rgba(0,0,0,.35);">
            Principal
        </p>

        <ul class="list-unstyled mb-2">
            {{-- Dashboard --}}
            <li>
                <a href="{{ route('admin.dashboard') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ request()->routeIs('admin.dashboard') ? 'sidebar-link-active' : 'text-secondary' }}"
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

            {{-- Utilisateurs --}}
            <li>
                <a href="{{ route('admin.users') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ request()->routeIs('admin.users*') ? 'sidebar-link-active' : 'text-secondary' }}"
                   title="Utilisateurs">
                    <svg class="flex-shrink-0" width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span class="small fw-medium sidebar-text">Utilisateurs</span>
                </a>
            </li>

            {{-- Clubs --}}
            <li>
                <a href="{{ route('admin.clubs') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ request()->routeIs('admin.clubs*') ? 'sidebar-link-active' : 'text-secondary' }}"
                   title="Clubs">
                    <svg class="flex-shrink-0" width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                        <path d="M2 17l10 5 10-5"/>
                        <path d="M2 12l10 5 10-5"/>
                    </svg>
                    <span class="small fw-medium sidebar-text">Clubs</span>
                </a>
            </li>

            {{-- Joueurs --}}
            <li>
                <a href="{{ route('admin.joueurs') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ request()->routeIs('admin.joueurs*') ? 'sidebar-link-active' : 'text-secondary' }}"
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

            {{-- Agents & Vérification --}}
            <li>
                <a href="{{ route('admin.agents.index') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ request()->routeIs('admin.agents*') ? 'sidebar-link-active' : 'text-secondary' }}"
                   title="Agents">
                    <svg class="flex-shrink-0" width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                    </svg>
                    <span class="small fw-medium sidebar-text">Agents</span>
                    @if(($agentsEnAttenteNav ?? 0) > 0)
                        <span class="badge bg-warning text-dark rounded-pill ms-auto sidebar-text" style="font-size:10px;font-weight:700;">
                            {{ $agentsEnAttenteNav }}
                        </span>
                    @endif
                </a>
            </li>
        </ul>

        {{-- ── GESTION SPORTIVE & FINANCES ── --}}
        <p class="text-uppercase fw-bold px-2 mb-1 mt-3 sidebar-section-label"
           style="font-size:9px;letter-spacing:1.5px;color:rgba(0,0,0,.35);">
            Gestion & Finances
        </p>

        <ul class="list-unstyled mb-2">
            {{-- Transferts --}}
            <li>
                <a href="{{ route('admin.transferts') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ request()->routeIs('admin.transferts*') ? 'sidebar-link-active' : 'text-secondary' }}"
                   title="Transferts">
                    <svg class="flex-shrink-0" width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
                        <path d="m7 17 10-10M17 7H7v10"/>
                    </svg>
                    <span class="small fw-medium sidebar-text">Transferts</span>
                </a>
            </li>

            {{-- Opportunités --}}
            <li>
                <a href="{{ route('admin.opportunites') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ request()->routeIs('admin.opportunites*') ? 'sidebar-link-active' : 'text-secondary' }}"
                   title="Opportunités">
                    <svg class="flex-shrink-0" width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polygon points="12 8 8 12 12 16 12 8"/>
                    </svg>
                    <span class="small fw-medium sidebar-text">Opportunités</span>
                </a>
            </li>

            {{-- Abonnements / FedaPay --}}
            <li>
                <a href="{{ route('admin.paiements') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none sidebar-link
                          {{ request()->routeIs('admin.paiements*') ? 'sidebar-link-active' : 'text-secondary' }}"
                   title="Paiements">
                    <svg class="flex-shrink-0" width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                        <line x1="1" y1="10" x2="23" y2="10"/>
                    </svg>
                    <span class="small fw-medium sidebar-text">Paiements / FedaPay</span>
                </a>
            </li>
        </ul>

    </nav>

    {{-- Footer Sidebar : Profil Admin + Déconnexion --}}
    <div class="p-3 border-top bg-light">
        <a href="{{ route('admin.profile') }}" class="d-flex align-items-center gap-2 mb-2 text-decoration-none p-1 rounded-2 hover-bg">
            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                 style="width:32px;height:32px;background:#1A56A0;font-size:12px;">
                {{ $initiales }}
            </div>
            <div class="overflow-hidden sidebar-text">
                <span class="d-block fw-semibold text-truncate small" style="color:var(--text);">
                    {{ $user->name }}
                </span>
                <span class="d-block text-muted" style="font-size:11px;">
                    {{ $user->email }}
                </span>
            </div>
            <i class="bi bi-gear ms-auto text-muted small"></i>
        </a>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.profile') }}" class="btn btn-sm btn-outline-primary w-50 rounded-pill d-flex align-items-center justify-content-center gap-1" style="font-size:11px;">
                <i class="bi bi-person"></i>
                <span class="sidebar-text">Profil</span>
            </a>
            <form method="POST" action="{{ route('admin.logout') }}" class="w-50">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger w-100 rounded-pill d-flex align-items-center justify-content-center gap-1" style="font-size:11px;">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="sidebar-text">Sortir</span>
                </button>
            </form>
        </div>
    </div>

</aside>

