{{-- resources/views/layouts/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Connect Sport</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --cs-dark:        #0D2E5C;
            --cs-primary:     #1A56A0;
            --cs-light-blue:  #5B9BD5;
            --cs-orange:      #F97316;
            --cs-orange-soft: #FFEDD5;
            --bg:             #F4F6FA;
            --bg2:            #FFFFFF;
            --bg3:            #E8EFF8;
            --border:         #E2E8F0;
            --text:           #1A1A2E;
            --text2:          #4A5568;
            --text3:          #94A3B8;
            --card-bg:        #FFFFFF;
            --sidebar-bg:     #0D2E5C;
            --sidebar-text:   #94A3B8;
            --sidebar-active: #FFFFFF;
            --sidebar-hover:  rgba(255,255,255,0.08);
            --sidebar-accent: #F97316;
            --topbar-bg:      rgba(255,255,255,0.96);
            --shadow:         0 2px 12px rgba(13,46,92,0.08);
            --shadow-lg:      0 8px 32px rgba(13,46,92,0.13);
            --radius:         12px;
        }
        [data-theme="dark"] {
            --bg:         #090f1c;
            --bg2:        #0d1628;
            --bg3:        #111e38;
            --border:     #1c2d4a;
            --text:       #EDF2FF;
            --text2:      #94A3B8;
            --text3:      #64748B;
            --card-bg:    #0d1628;
            --sidebar-bg: #060c18;
            --topbar-bg:  rgba(9,15,28,0.97);
            --shadow:     0 2px 16px rgba(0,0,0,0.4);
            --shadow-lg:  0 8px 40px rgba(0,0,0,0.5);
        }

        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Outfit', sans-serif; background: var(--bg); color: var(--text); margin: 0; display: flex; min-height: 100vh; transition: background 0.25s, color 0.25s; }

        /* ── SIDEBAR ── */
        .cs-sidebar {
            width: 240px; flex-shrink: 0;
            background: var(--sidebar-bg);
            display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0; bottom: 0;
            z-index: 200;
            transition: transform 0.3s;
        }
        .cs-sidebar-brand {
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex; align-items: center; gap: 10px;
            text-decoration: none;
        }
        .cs-sidebar-brand img { width: 34px; height: 34px; border-radius: 50%; object-fit: cover; }
        .cs-sidebar-brand-name {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 18px; letter-spacing: 2px; color: #fff; line-height: 1;
        }
        .cs-sidebar-brand-name span { color: var(--cs-orange); }

        .cs-sidebar-section {
            font-size: 9px; font-weight: 700; letter-spacing: 1.5px;
            text-transform: uppercase; color: rgba(255,255,255,0.25);
            padding: 16px 20px 6px;
        }

        .cs-sidebar-nav { flex: 1; padding: 8px 10px; overflow-y: auto; }
        .cs-sidebar-nav a {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 8px;
            color: var(--sidebar-text); text-decoration: none;
            font-size: 13.5px; font-weight: 500;
            transition: all 0.18s; margin-bottom: 2px;
        }
        .cs-sidebar-nav a i { font-size: 16px; width: 20px; flex-shrink: 0; }
        .cs-sidebar-nav a:hover { background: var(--sidebar-hover); color: #fff; }
        .cs-sidebar-nav a.active { background: var(--cs-primary); color: #fff; }
        .cs-sidebar-nav a .cs-badge {
            margin-left: auto; background: var(--cs-orange);
            color: #fff; font-size: 10px; font-weight: 700;
            padding: 1px 7px; border-radius: 20px;
        }

        .cs-sidebar-user {
            padding: 14px; border-top: 1px solid rgba(255,255,255,0.06);
            display: flex; align-items: center; gap: 10px;
        }
        .cs-sidebar-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--cs-primary); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; flex-shrink: 0; overflow: hidden;
        }
        .cs-sidebar-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .cs-sidebar-user-name { font-size: 13px; font-weight: 600; color: #fff; line-height: 1.2; }
        .cs-sidebar-user-role { font-size: 10px; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 0.5px; }
        .cs-sidebar-logout {
            margin-left: auto; color: rgba(255,255,255,0.3);
            padding: 0; border: 0; background: transparent;
            font-size: 16px; text-decoration: none; transition: color 0.2s; cursor: pointer;
        }
        .cs-sidebar-logout:hover { color: #ef4444; }

        /* ── MAIN ── */
        .cs-main { margin-left: 240px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

        /* ── TOPBAR ── */
        .cs-topbar {
            position: sticky; top: 0; z-index: 100;
            background: var(--topbar-bg);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            padding: 0 24px; height: 56px;
            display: flex; align-items: center; gap: 12px;
            transition: background 0.25s, border-color 0.25s;
        }
        .cs-topbar-title { font-size: 16px; font-weight: 700; color: var(--text); flex: 1; }
        .cs-topbar-right { display: flex; align-items: center; gap: 8px; }

        .cs-topbar-btn {
            width: 34px; height: 34px; border-radius: 8px;
            border: 1px solid var(--border); background: var(--bg);
            color: var(--text2); display: flex; align-items: center;
            justify-content: center; cursor: pointer; font-size: 15px;
            transition: all 0.2s; text-decoration: none; position: relative;
        }
        .cs-topbar-btn:hover { background: var(--bg3); color: var(--cs-primary); }
        .cs-notif-dot {
            position: absolute; top: -2px; right: -2px;
            width: 8px; height: 8px; background: var(--cs-orange);
            border-radius: 50%; border: 2px solid var(--topbar-bg);
        }

        /* ── CONTENT ── */
        .cs-content { padding: 24px; flex: 1; }

        /* ── CARDS ── */
        .cs-card {
            background: var(--card-bg); border: 1px solid var(--border);
            border-radius: var(--radius); box-shadow: var(--shadow);
            transition: background 0.25s, border-color 0.25s;
        }
        .cs-card-header {
            padding: 16px 20px; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .cs-card-title { font-size: 14px; font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 8px; }
        .cs-card-body { padding: 20px; }
        .directory-muted { color: var(--text2); }
        .message-list, .message-surface { background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; }
        .message-item { display: block; padding: 16px 18px; color: var(--text); text-decoration: none; border-bottom: 1px solid var(--border); transition: background .18s; }
        .message-item:last-child { border-bottom: 0; }
        .message-item:hover { background: var(--bg); color: var(--text); }
        .message-muted { color: var(--text2); }
        .message-bubble-incoming { background: var(--bg3); color: var(--text); }

        /* KPI */
        .cs-kpi {
            background: var(--card-bg); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 20px;
            transition: all 0.2s;
        }
        .cs-kpi:hover { box-shadow: var(--shadow-lg); transform: translateY(-2px); }
        .cs-kpi-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 12px; }
        .cs-kpi-val { font-family: 'Bebas Neue', sans-serif; font-size: 36px; line-height: 1; color: var(--text); }
        .cs-kpi-lbl { font-size: 12px; color: var(--text3); margin-top: 2px; }
        .cs-kpi-trend { font-size: 11px; font-weight: 600; margin-top: 6px; display: flex; align-items: center; gap: 4px; }
        .trend-up   { color: #10B981; }
        .trend-down { color: #EF4444; }
        .trend-flat { color: var(--text3); }

        /* Table */
        .cs-table { width: 100%; border-collapse: collapse; }
        .cs-table th { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text3); padding: 10px 14px; border-bottom: 1px solid var(--border); text-align: left; }
        .cs-table td { padding: 12px 14px; border-bottom: 1px solid var(--border); font-size: 13.5px; color: var(--text2); }
        .cs-table tr:last-child td { border-bottom: none; }
        .cs-table tr:hover td { background: var(--bg); }

        /* Badge statut */
        .cs-status { font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; display: inline-flex; align-items: center; gap: 4px; }
        .status-en_attente     { background: #FEF3C7; color: #92400E; }
        .status-en_negociation { background: #DBEAFE; color: #1D4ED8; }
        .status-accepte        { background: #D1FAE5; color: #065F46; }
        .status-refuse         { background: #FEE2E2; color: #991B1B; }
        .status-actif          { background: #D1FAE5; color: #065F46; }
        .status-expire         { background: #F3F4F6; color: #6B7280; }

        /* Empty */
        .cs-empty { text-align: center; padding: 40px 20px; color: var(--text3); }
        .cs-empty i { font-size: 36px; display: block; margin-bottom: 10px; opacity: 0.35; }
        .cs-empty p { font-size: 13px; }

        /* Boutons */
        .btn-cs { padding: 8px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; transition: all 0.2s; }
        .btn-cs-primary { background: var(--cs-orange); color: #fff; }
        .btn-cs-primary:hover { background: #ea6a0b; color: #fff; }
        .btn-cs-outline { background: transparent; color: var(--cs-primary); border: 1.5px solid var(--cs-primary); }
        .btn-cs-outline:hover { background: var(--cs-primary); color: #fff; }
        .btn-cs-ghost { background: var(--bg); color: var(--text2); border: 1px solid var(--border); }
        .btn-cs-ghost:hover { background: var(--bg3); }
        .form-label, .form-check-label { color: var(--text); }
        .form-control, .form-select {
            background-color: var(--card-bg);
            border-color: var(--border);
            color: var(--text);
        }
        .form-control::placeholder { color: var(--text3); }
        .form-control:focus, .form-select:focus {
            background-color: var(--card-bg);
            border-color: var(--cs-primary);
            color: var(--text);
            box-shadow: 0 0 0 .2rem rgba(26,86,160,.18);
        }
        .form-select option { background: var(--card-bg); color: var(--text); }
        .form-text { color: var(--text3); }
        .form-control:disabled, .form-select:disabled { background-color: var(--bg3); color: var(--text3); }

        /* Avatar joueur */
        .cs-player-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: var(--cs-primary); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; flex-shrink: 0; overflow: hidden;
        }
        .cs-player-avatar img { width: 100%; height: 100%; object-fit: cover; }

        /* Mobile */
        .cs-mobile-toggle { display: none; }
        @media (max-width: 991px) {
            .cs-sidebar { transform: translateX(-100%); }
            .cs-sidebar.open { transform: translateX(0); }
            .cs-main { margin-left: 0; }
            .cs-mobile-toggle { display: flex; }
            .cs-content { padding: 16px; }
        }

        /* Flash */
        .alert-flash { position: fixed; top: 66px; right: 16px; z-index: 2000; min-width: 280px; border-radius: 10px; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--cs-light-blue); border-radius: 3px; }
    </style>
    @stack('styles')
</head>
<body>

{{-- ── SIDEBAR ── --}}
<aside class="cs-sidebar" id="sidebar">
    <a href="{{ route('home') }}" class="cs-sidebar-brand">
        <img src="{{ asset('images/logo.jpg') }}" alt="Connect Sport">
        <span class="cs-sidebar-brand-name">CONNECT <span>SPORT</span></span>
    </a>

    <nav class="cs-sidebar-nav">
        @yield('sidebar-nav')
    </nav>

    <div class="cs-sidebar-user">
        <div class="cs-sidebar-avatar">
            @if(auth()->user()->avatar)
                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar Agent" style="width:100%;height:100%;object-fit:cover;">
            @else
                {{ strtoupper(substr(auth()->user()->prenom ?? auth()->user()->name, 0, 1)) }}
            @endif
        </div>
        <div>

            <div class="cs-sidebar-user-name">{{ auth()->user()->prenom ?? auth()->user()->name }}</div>
            <div class="cs-sidebar-user-role">{{ auth()->user()->role }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="ms-auto">
            @csrf
            <button type="submit" class="cs-sidebar-logout" title="Déconnexion">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</aside>

{{-- ── MAIN ── --}}
<div class="cs-main">

    {{-- Topbar --}}
    <div class="cs-topbar">
        <button class="cs-topbar-btn cs-mobile-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">
            <i class="bi bi-list"></i>
        </button>
        <span class="cs-topbar-title">@yield('page-title', 'Dashboard')</span>
        <nav class="d-none d-md-flex align-items-center gap-3 me-2" aria-label="Navigation principale">
            <a href="{{ route('home') }}" class="small text-decoration-none {{ request()->routeIs('home') ? 'fw-bold text-primary' : 'text-secondary' }}">Accueil</a>
            <a href="{{ route('clubs.index') }}" class="small text-decoration-none {{ request()->routeIs('clubs.*') ? 'fw-bold text-primary' : 'text-secondary' }}">Clubs</a>
            <a href="{{ route('opportunites.index') }}" class="small text-decoration-none {{ request()->routeIs('opportunites.*') ? 'fw-bold text-primary' : 'text-secondary' }}">Opportunités</a>
            <a href="{{ route('joueurs.sans-club') }}" class="small text-decoration-none {{ request()->routeIs('joueurs.*') ? 'fw-bold text-primary' : 'text-secondary' }}">Joueurs</a>
        </nav>
        <div class="cs-topbar-right">
            <button class="cs-topbar-btn" onclick="toggleTheme()" id="theme-toggle" title="Thème">
                <i class="bi bi-moon-fill"></i>
            </button>
            <a href="{{ route('messages.inbox') }}" class="cs-topbar-btn" title="Messages">
                <i class="bi bi-chat-dots"></i>
                @php $unread = auth()->user()->messagesRecus()->where('lu', false)->count(); @endphp
                @if($unread > 0)<span class="cs-notif-dot"></span>@endif
            </a>

            {{-- Menu Déroulant Profil Agent --}}
            <div class="dropdown">
                <button class="btn btn-sm d-flex align-items-center gap-2 border rounded-pill p-1 pe-2 dropdown-toggle text-decoration-none"
                        type="button" data-bs-toggle="dropdown" aria-expanded="false"
                        style="background:var(--card-bg);border-color:var(--border) !important;">
                    <div class="rounded-circle overflow-hidden d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                         style="width:28px;height:28px;background:#8b5cf6;font-size:12px;">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            {{ strtoupper(substr(auth()->user()->prenom ?? auth()->user()->name, 0, 1)) }}
                        @endif
                    </div>
                    <span class="small fw-semibold d-none d-sm-inline" style="color:var(--text);font-size:12px;">
                        {{ auth()->user()->prenom ?? auth()->user()->name }}
                    </span>
                </button>

                <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 mt-2 p-2"
                    style="min-width:240px;background:var(--card-bg);border:1px solid var(--border) !important;">
                    
                    {{-- En-tête profil --}}
                    <li class="px-3 py-2 border-bottom mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle overflow-hidden d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                 style="width:36px;height:36px;background:#8b5cf6;font-size:14px;">
                                @if(auth()->user()->avatar)
                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    {{ strtoupper(substr(auth()->user()->prenom ?? auth()->user()->name, 0, 1)) }}
                                @endif
                            </div>
                            <div class="overflow-hidden">
                                <div class="fw-bold small text-truncate" style="color:var(--text);">
                                    {{ auth()->user()->prenom ?? '' }} {{ auth()->user()->name }}
                                </div>
                                <div class="text-muted text-truncate" style="font-size:11px;">
                                    {{ auth()->user()->email }}
                                </div>
                            </div>
                        </div>
                    </li>

                    {{-- Liens du menu --}}
                    <li>
                        <a class="dropdown-item rounded-3 d-flex align-items-center gap-2 py-2 small" href="{{ route('agent.dashboard') }}">
                            <i class="bi bi-grid-1x2 text-warning"></i>
                            <span>Mon Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item rounded-3 d-flex align-items-center gap-2 py-2 small" href="{{ route('agent.profil') }}">
                            <i class="bi bi-person-badge text-primary"></i>
                            <span>Mon Profil Accrédité</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item rounded-3 d-flex align-items-center gap-2 py-2 small" href="{{ route('agent.profil') }}">
                            <i class="bi bi-camera text-info"></i>
                            <span>Modifier la photo</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item rounded-3 d-flex align-items-center gap-2 py-2 small" href="{{ route('messages.inbox') }}">
                            <i class="bi bi-chat-dots text-success"></i>
                            <span>Messages</span>
                            @if(isset($unread) && $unread > 0)
                                <span class="badge bg-danger rounded-pill ms-auto" style="font-size:10px;">{{ $unread }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item rounded-3 d-flex align-items-center gap-2 py-2 small" href="{{ route('home') }}">
                            <i class="bi bi-house text-secondary"></i>
                            <span>Accueil public</span>
                        </a>
                    </li>

                    <li><hr class="dropdown-divider my-2"></li>

                    {{-- Déconnexion --}}
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item rounded-3 d-flex align-items-center gap-2 py-2 small text-danger">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Déconnexion</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>


    {{-- Flash --}}
    @if(session('success'))
        <div class="alert alert-success alert-flash alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-flash alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Contenu --}}
    <div class="cs-content">
        @yield('content')
    </div>

</div>

{{-- Overlay mobile --}}
<div id="overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:199;"
     onclick="document.getElementById('sidebar').classList.remove('open');this.style.display='none'"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js"></script>
<script>
    const saved = localStorage.getItem('cs-theme') || 'light';
    document.documentElement.setAttribute('data-theme', saved);
    updateThemeIcon(saved);

    function toggleTheme() {
        const cur  = document.documentElement.getAttribute('data-theme');
        const next = cur === 'light' ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', next);
        localStorage.setItem('cs-theme', next);
        updateThemeIcon(next);
    }
    function updateThemeIcon(t) {
        const btn = document.getElementById('theme-toggle');
        if (btn) btn.innerHTML = t === 'dark' ? '<i class="bi bi-sun-fill"></i>' : '<i class="bi bi-moon-fill"></i>';
    }

    // Overlay sidebar mobile
    document.getElementById('sidebar').addEventListener('transitionend', function() {
        document.getElementById('overlay').style.display =
            this.classList.contains('open') ? 'block' : 'none';
    });

    setTimeout(() => {
        document.querySelectorAll('.alert-flash').forEach(el => {
            bootstrap.Alert.getOrCreateInstance(el).close();
        });
    }, 4000);
</script>
@stack('scripts')
</body>
</html>