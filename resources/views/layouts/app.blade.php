<!DOCTYPE html>
<html lang="fr" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ConnectSport')</title>

    {{-- Appliquer le thème avant rendu pour éviter le flash --}}
    <script>
        (function () {
            const t = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', t);
        })();
    </script>

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
        }

        .cs-card {
            background: var(--bs-body-bg);
            color: var(--bs-body-color);
            border: 1px solid var(--bs-border-color);
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(13,46,92,.08);
            transition: background .25s, border-color .25s, color .25s;
        }

        .directory-muted { color: var(--bs-secondary-color); }
        .message-list, .message-surface { background: var(--bs-body-bg); border: 1px solid var(--bs-border-color); border-radius: 12px; box-shadow: 0 2px 12px rgba(13,46,92,.08); overflow: hidden; }
        .message-item { display: block; padding: 16px 18px; color: var(--bs-body-color); text-decoration: none; border-bottom: 1px solid var(--bs-border-color); }
        .message-item:last-child { border-bottom: 0; }
        .message-item:hover { background: var(--bs-tertiary-bg); color: var(--bs-body-color); }
        .message-muted { color: var(--bs-secondary-color); }
        .message-bubble-incoming { background: var(--bs-tertiary-bg); color: var(--bs-body-color); }

        [data-bs-theme="dark"] .cs-card {
            box-shadow: 0 2px 16px rgba(0,0,0,.35);
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bs-secondary-bg); }
        ::-webkit-scrollbar-thumb { background: #5B9BD5; border-radius: 3px; }

        /* Flash alert position */
        .alert-flash {
            position: fixed;
            top: 72px;
            right: 20px;
            z-index: 2000;
            min-width: 300px;
            border-radius: 12px;
            animation: slideIn .3s ease;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(40px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* Sidebar layout */
        .main-with-sidebar {
            margin-left: 240px;
            padding-top: 64px;
            transition: margin-left .25s ease;
            min-height: 100vh;
        }
        body.sidebar-collapsed .main-with-sidebar {
            margin-left: 64px;
        }
        @media (max-width: 991px) {
            .main-with-sidebar { margin-left: 0; }
        }
    </style>

    @stack('styles')
</head>
<body>

    {{-- Navbar --}}
    @include('partials.navbar')

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-flash alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-flash alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info alert-flash alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-flash alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @auth
    @if(in_array(auth()->user()->role, ['club', 'agent', 'joueur', 'parent']))
        <div id="sidebar-overlay"
             class="d-none position-fixed top-0 start-0 w-100 h-100"
             style="background:rgba(0,0,0,.5);z-index:99;"
             onclick="closeSidebar()">
        </div>
    @endif
@endauth

    {{-- Contenu principal --}}
    @auth
        @if(auth()->user()->role === 'club')
            @include('club.partials._sidebar')
            <main class="main-with-sidebar">
                @yield('content')
            </main>
        @elseif(auth()->user()->role === 'agent')
            @include('agent.partials._sidebar')
            <main class="main-with-sidebar">
                @yield('content')
            </main>
        @elseif(auth()->user()->role === 'joueur')
            @include('joueur.partials._sidebar')
            <main class="main-with-sidebar">
                @yield('content')
            </main>
        @elseif(auth()->user()->role === 'parent')
            @include('parent.partials._sidebar')
            <main class="main-with-sidebar">
                @yield('content')
            </main>
        @else
            <main style="padding-top:64px;">
                @yield('content')
            </main>
        @endif
    @else
        <main style="padding-top:64px;">
            @yield('content')
        </main>
    @endauth

    {{-- Footer --}}
    @include('partials.footer')

    {{-- Bootstrap 5 JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    {{-- Alpine.js --}}
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js"></script>

    <script>
        /* ── Thème clair / sombre ── */
        function toggleTheme() {
            const html    = document.documentElement;
            const current = html.getAttribute('data-bs-theme') || 'light';
            const next    = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-bs-theme', next);
            localStorage.setItem('theme', next);
            // Syncer l'icône navbar
            const icon = document.querySelector('#themeToggle i');
            if (icon) icon.className = next === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
        }

        /* ── Auto-dismiss flash après 4s ── */
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                document.querySelectorAll('.alert-flash').forEach(el => {
                    bootstrap.Alert.getOrCreateInstance(el).close();
                });
            }, 4000);
        });

        
        async function uploadUserAvatar(inputElement) {
            const file = inputElement.files[0];
            if (!file) return;

            // Validation taille
            if (file.size > 2 * 1024 * 1024) {
                alert('La photo ne doit pas dépasser 2 Mo.');
                inputElement.value = '';
                return;
            }

            const fd = new FormData();
            fd.append('avatar', file);

            try {
                const res = await fetch('{{ route("profile.avatar") }}', {
                    method: 'POST',
                    body: fd,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!res.ok) {
                    const err = await res.json().catch(() => ({}));
                    alert(err.message ?? 'Erreur lors de la mise à jour.');
                    return;
                }

                // Lire la photo localement
                const reader = new FileReader();
                reader.onload = ({ target }) => {
                    const src = target.result;
                    
                    // Mettre à jour TOUS les avatars de la page
                    updateAllAvatars(src);
                    
                    // Afficher un toast
                    showAvatarToast('Photo de profil mise à jour avec succès !', 'success');
                };
                reader.readAsDataURL(file);

            } catch (err) {
                console.error(err);
                alert('Une erreur est survenue lors de l\'upload.');
            } finally {
                inputElement.value = '';
            }
        }

        /**
         * Mettre à jour TOUS les avatars de la page
         * (Navbar, Sidebar, Paramètres, et partout ailleurs)
         */
        function updateAllAvatars(src) {
            //   Navbar - Avatar bouton
            const navbarImg = document.getElementById('navbar-avatar-img');
            const navbarFallback = document.getElementById('navbar-avatar-fallback');
            if (navbarImg) {
                navbarImg.src = src;
            } else if (navbarFallback) {
                navbarFallback.outerHTML = `<img id="navbar-avatar-img" src="${src}" alt="Avatar" class="rounded-circle object-fit-cover" style="width:32px;height:32px;">`;
            }

            //   Navbar - Menu dropdown
            const menuImg = document.getElementById('navbar-menu-img');
            const menuFallback = document.getElementById('navbar-menu-fallback');
            if (menuImg) {
                menuImg.src = src;
            } else if (menuFallback) {
                menuFallback.outerHTML = `<img id="navbar-menu-img" src="${src}" class="rounded-circle object-fit-cover flex-shrink-0" style="width:40px;height:40px;" alt="Avatar">`;
            }

            //   Sidebar - Avatar utilisateur (si présent)
            const sidebarUserImg = document.getElementById('sidebar-avatar-img');
            const sidebarUserFallback = document.getElementById('sidebar-avatar-initiales');
            if (sidebarUserImg) {
                sidebarUserImg.src = src;
            } else if (sidebarUserFallback) {
                sidebarUserFallback.outerHTML = `<img id="sidebar-avatar-img" src="${src}" style="width:30px;height:30px;object-fit:cover;">`;
            }

            //   Paramètres - Prévisualisation profil
            const profilImg = document.getElementById('profil-avatar-preview');
            const profilFallback = document.getElementById('profil-avatar-fallback');
            if (profilImg) {
                profilImg.src = src;
            } else if (profilFallback) {
                profilFallback.outerHTML = `<img id="profil-avatar-preview" src="${src}" alt="Photo de profil" class="rounded-circle object-fit-cover shadow-sm" style="width:120px;height:120px;">`;
            }
        }

        /**
         * Toast de notification
         */
        function showAvatarToast(message, type = 'success') {
            const id = 'avatar-toast-' + Date.now();
            const bgClass = type === 'success' ? 'text-bg-success' : 'text-bg-danger';
            const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle';

            const html = `
                <div id="${id}" class="toast align-items-center ${bgClass} border-0 rounded-3 shadow"
                     role="alert" style="min-width:280px;">
                    <div class="d-flex">
                        <div class="toast-body fw-semibold small">
                            <i class="bi ${icon} me-2"></i>${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto"
                                data-bs-dismiss="toast"></button>
                    </div>
                </div>`;

            if (!document.getElementById('toast-container-avatar')) {
                const container = document.createElement('div');
                container.id = 'toast-container-avatar';
                container.className = 'position-fixed bottom-0 end-0 p-3';
                container.style.zIndex = '9999';
                document.body.appendChild(container);
            }

            document.getElementById('toast-container-avatar').insertAdjacentHTML('beforeend', html);
            const el = document.getElementById(id);
            new bootstrap.Toast(el, { delay: 3000 }).show();
            el.addEventListener('hidden.bs.toast', () => el.remove());
        }
    </script>

    @stack('scripts')
</body>
</html>