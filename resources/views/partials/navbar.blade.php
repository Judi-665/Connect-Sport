{{-- resources/views/partials/navbar.blade.php --}}
@php
    $user   = auth()->user();
    $unread = $user ? $user->messagesRecus()->where('lu', false)->count() : 0;
    $avatarUrl = $user?->avatar
        ? asset('storage/' . $user->avatar)
        : null;
    $initiale = strtoupper(substr($user?->prenom ?? $user?->name ?? 'U', 0, 1));
@endphp

<nav class="navbar navbar-expand-lg fixed-top bg-body-tertiary shadow-sm">
    <div class="container-fluid px-3 px-lg-4">

                @auth
            @if(in_array(auth()->user()->role, ['club', 'agent', 'joueur']))
                <button class="btn btn-outline-secondary d-lg-none me-1 p-1"
                        id="sidebar-mobile-btn"
                        onclick="toggleMobileSidebar()"
                        style="width:36px;height:36px;">
                    <i class="bi bi-layout-sidebar"></i>
                </button>
            @endif
        @endauth

        {{-- Brand --}}
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            <img src="{{ asset('images/logo.jpg') }}" alt="Connect Sport"
                 height="38" width="38" class="rounded-circle">
            <span class="fw-black"
                  style="font-family:'Bebas Neue',sans-serif;font-size:1.6rem;letter-spacing:1px;">
                CONNECT<span class="text-warning">SPORT</span>
            </span>
        </a>

        {{-- Toggler mobile --}}
        <button class="navbar-toggler border-0" type="button"
                data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <i class="bi bi-list fs-3"></i>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">

            {{-- Liens centraux --}}
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active fw-semibold' : '' }}"
                       href="{{ route('home') }}">
                        <i class="bi bi-house me-1"></i> Accueil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('clubs.*') ? 'active fw-semibold' : '' }}"
                       href="{{ route('clubs.index') }}">
                        <i class="bi bi-shield me-1"></i> Clubs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('opportunites.*') ? 'active fw-semibold' : '' }}"
                       href="{{ route('opportunites.index') }}">
                        <i class="bi bi-lightning me-1"></i> Opportunités
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('joueurs.*') ? 'active fw-semibold' : '' }}"
                       href="{{ route('joueurs.sans-club') }}">
                        <i class="bi bi-people me-1"></i> Joueurs
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">

                {{-- Toggle thème --}}
                <button class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center"
                        id="themeToggle" onclick="toggleTheme()"
                        style="width:36px;height:36px;">
                    <i class="bi bi-moon-fill"></i>
                </button>

                @auth
                    {{-- Messages --}}
                    <a href="{{ route('messages.inbox') }}"
                       class="btn btn-outline-secondary rounded-circle position-relative d-flex align-items-center justify-content-center"
                       style="width:36px;height:36px;">
                        <i class="bi bi-chat-dots fs-5"></i>
                        @if($unread > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                  style="font-size:.65rem;">
                                {{ $unread > 9 ? '9+' : $unread }}
                            </span>
                        @endif
                    </a>

                    {{-- Input file caché --}}
                    <input type="file" id="navbar-avatar-input"
                           accept="image/jpeg,image/png,image/webp" class="d-none">

                    {{-- Dropdown profil --}}
                    <div class="dropdown">
                        <button class="btn dropdown-toggle d-flex align-items-center gap-2 border rounded-3 p-2"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                style="background:var(--bs-tertiary-bg);">

                            {{-- Avatar avec overlay crayon --}}
                            <div class="position-relative flex-shrink-0"
                                 style="width:32px;height:32px;">

                                @if($avatarUrl)
                                    <img id="navbar-avatar-img"
                                         src="{{ $avatarUrl }}"
                                         alt="Avatar"
                                         class="rounded-circle object-fit-cover"
                                         style="width:32px;height:32px;">
                                @else
                                    <div id="navbar-avatar-fallback"
                                         class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold"
                                         style="width:32px;height:32px;font-size:.85rem;">
                                        {{ $initiale }}
                                    </div>
                                @endif

                                {{-- Overlay crayon SVG --}}
                                <div id="navbar-pencil-overlay"
                                     class="position-absolute top-0 start-0 rounded-circle
                                            d-flex align-items-center justify-content-center"
                                     style="width:32px;height:32px;
                                            background:rgba(0,0,0,.5);
                                            opacity:0;transition:opacity .2s;
                                            cursor:pointer;">
                                    <svg width="13" height="13" viewBox="0 0 24 24"
                                         fill="none" stroke="white" stroke-width="2.5"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </div>
                            </div>

                            {{-- Nom + badge rôle --}}
                            <div class="text-start d-none d-sm-block">
                                <div class="fw-semibold small lh-1">
                                    {{ $user->prenom ?? $user->name }}
                                </div>
                                <div class="mt-1">
                                    @php
                                        $badgeClass = match($user->role) {
                                            'club'      => 'bg-primary',
                                            'joueur'    => 'bg-success',
                                            'parent'    => 'bg-warning text-dark',
                                            'agent'     => 'bg-info text-dark',
                                            'supporter' => 'bg-danger',
                                            'admin'     => 'bg-secondary',
                                            default     => 'bg-dark',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} rounded-pill"
                                          style="font-size:.6rem;">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </div>
                            </div>
                        </button>

                        {{-- Menu dropdown --}}
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2"
                            style="min-width:220px;">

                            {{-- En-tête profil --}}
                            <li class="px-3 py-2 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    @if($avatarUrl)
                                        <img id="navbar-menu-img"
                                             src="{{ $avatarUrl }}"
                                             class="rounded-circle object-fit-cover flex-shrink-0"
                                             style="width:40px;height:40px;" alt="Avatar">
                                    @else
                                        <div id="navbar-menu-fallback"
                                             class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                             style="width:40px;height:40px;font-size:1rem;">
                                            {{ $initiale }}
                                        </div>
                                    @endif
                                    <div class="overflow-hidden">
                                        <div class="fw-semibold small text-truncate">
                                            {{ $user->prenom ?? '' }} {{ $user->name }}
                                        </div>
                                        <div class="text-muted text-truncate" style="font-size:11px;">
                                            {{ $user->email }}
                                        </div>
                                    </div>
                                </div>
                            </li>

                            {{-- Modifier photo --}}
                            <li>
                                <button type="button"
                                        class="dropdown-item d-flex align-items-center gap-2 small py-2"
                                        onclick="document.getElementById('navbar-avatar-input').click()">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    Modifier la photo
                                </button>
                            </li>

                            <li><hr class="dropdown-divider my-1"></li>

                            {{-- Dashboard --}}
                            @php
                                $dashRoute = match($user->role) {
                                    'club'      => 'club.dashboard',
                                    'joueur'    => 'joueur.dashboard',
                                    'parent'    => 'parent.dashboard',
                                    'agent'     => 'agent.dashboard',
                                    'supporter' => 'supporter.dashboard',
                                    'admin'     => 'admin.dashboard',
                                    default     => 'dashboard',
                                };
                                $profileRoute = match($user->role) {
                                    'club'      => 'club.parametres.index',
                                    'joueur'    => 'joueur.profile',
                                    'parent'    => 'parent.profile',
                                    'agent'     => 'agent.profil',
                                    'supporter' => 'supporter.profile',
                                    'admin'     => 'admin.profile',
                                    default     => null,
                                };
                            @endphp

                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 small py-2"
                                   href="{{ route($dashRoute) }}">
                                    <i class="bi bi-speedometer2"></i> Mon dashboard
                                </a>
                            </li>

                            @if($profileRoute)
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 small py-2"
                                   href="{{ route($profileRoute) }}">
                                    <i class="bi bi-person-circle"></i> Profil
                                </a>
                            </li>
                            @endif

                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 small py-2"
                                   href="{{ route('messages.inbox') }}">
                                    <i class="bi bi-chat-dots"></i>
                                    Messages
                                    @if($unread > 0)
                                        <span class="badge bg-danger rounded-pill ms-auto">{{ $unread }}</span>
                                    @endif
                                </a>
                            </li>

                            <li><hr class="dropdown-divider my-1"></li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="dropdown-item d-flex align-items-center gap-2 small py-2 text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>

                    {{-- Toast Bootstrap --}}
                    <div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999;">
                        <div id="avatarToast"
                             class="toast align-items-center text-bg-success border-0"
                             role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="d-flex">
                                <div class="toast-body small">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Photo de profil mise à jour.
                                </div>
                                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                                        data-bs-dismiss="toast"></button>
                            </div>
                        </div>
                    </div>

                @else
                    <a href="{{ route('login') }}"
                       class="btn btn-outline-primary rounded-pill px-3">Connexion</a>
                    <a href="{{ route('register') }}"
                       class="btn btn-warning rounded-pill px-3">
                        <i class="bi bi-person-plus me-1"></i> S'inscrire
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<script>
/* ── Appliquer thème avant rendu ── */
(function () {
    const t = localStorage.getItem('theme');
    if (t === 'dark' || t === 'light') {
        document.documentElement.setAttribute('data-bs-theme', t);
    }
})();

function toggleTheme() {
    const html  = document.documentElement;
    const next  = (html.getAttribute('data-bs-theme') || 'light') === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-bs-theme', next);
    localStorage.setItem('theme', next);
    const icon = document.querySelector('#themeToggle i');
    if (icon) icon.className = next === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
}

document.addEventListener('DOMContentLoaded', () => {

    /* Syncer icône thème */
    const t    = document.documentElement.getAttribute('data-bs-theme') || 'light';
    const icon = document.querySelector('#themeToggle i');
    if (icon) icon.className = t === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';

    /* ── Overlay crayon au survol ── */
    const overlay = document.getElementById('navbar-pencil-overlay');
    if (overlay) {
        const wrap = overlay.closest('.position-relative');
        wrap.addEventListener('mouseenter', () => overlay.style.opacity = '1');
        wrap.addEventListener('mouseleave', () => overlay.style.opacity = '0');
        overlay.addEventListener('click', (e) => {
            e.stopPropagation();
            document.getElementById('navbar-avatar-input').click();
        });
    }

    /* ── Upload avatar ── */
    const input = document.getElementById('navbar-avatar-input');
    if (!input) return;

    input.addEventListener('change', async function () {
        const file = this.files[0];
        if (!file) return;

        if (file.size > 2 * 1024 * 1024) {
            alert('La photo ne doit pas dépasser 2 Mo.');
            this.value = '';
            return;
        }

        // Appeler la fonction globale depuis app.blade.php
        uploadUserAvatar(this);
    });
});

/* Met à jour tous les avatars dans la page */
function _updateAvatar(src) {
    updateAllAvatars(src);
}
</script>