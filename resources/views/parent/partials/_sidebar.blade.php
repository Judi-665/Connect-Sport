{{-- resources/views/parent/partials/_sidebar.blade.php --}}
@php
    $user = auth()->user();
    if (!isset($liens)) {
        $liens = $user ? $user->parentsJoueurs()->actif()->whereNotNull('joueur_id')->has('joueur')->with(['joueur.user', 'joueur.club'])->get() : collect();
    }
    $initiale = strtoupper(substr($user->prenom ?? $user->name ?? 'P', 0, 1));
    $unreadCount = $user ? $user->messagesRecus()->where('lu', false)->count() : 0;
@endphp

<aside id="parentSidebar" class="d-flex flex-column position-fixed top-0 start-0 h-100 bg-dark text-white shadow"
       style="width:240px;z-index:100;overflow:hidden;background:#0D2E5C !important;">

    {{-- Brand / Header --}}
    <div class="d-flex align-items-center gap-2 px-3 py-3 border-bottom border-white border-opacity-10">
        <img src="{{ asset('images/logo.jpg') }}" alt="Connect Sport" class="rounded-circle flex-shrink-0" style="width:36px;height:36px;object-fit:cover;">
        <div class="overflow-hidden">
            <strong class="d-block text-truncate text-white" style="font-family:'Bebas Neue',sans-serif;font-size:1.15rem;letter-spacing:1px;">
                CONNECT<span class="text-warning">SPORT</span>
            </strong>
            <small class="badge fw-bold text-uppercase px-2 py-0 rounded-pill" style="font-size:9px;background:#2DD4BF;color:#064E3B;">
                Espace Parent
            </small>
        </div>
        <button type="button" class="btn btn-sm p-0 ms-auto d-lg-none text-white-50 hover-text-white" onclick="closeParentSidebar()" aria-label="Fermer la sidebar">
            <i class="bi bi-x-lg fs-5"></i>
        </button>
    </div>

    {{-- Navigation principale --}}
    <nav class="flex-grow-1 overflow-auto p-2">
        <div class="text-uppercase small fw-bold text-white-50 px-2 py-2" style="font-size:10px;letter-spacing:1px;">
            Navigation
        </div>

        <a href="{{ route('parent.dashboard') }}" class="parent-sidebar-link {{ request()->routeIs('parent.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Tableau de bord</span>
        </a>

        <a href="{{ route('parent.add-joueur') }}" class="parent-sidebar-link {{ request()->routeIs('parent.add-joueur') ? 'active' : '' }}">
            <i class="bi bi-person-plus-fill"></i>
            <span>Lier un enfant</span>
        </a>

        <a href="{{ route('messages.inbox') }}" class="parent-sidebar-link {{ request()->routeIs('messages.*') ? 'active' : '' }}">
            <i class="bi bi-chat-dots-fill"></i>
            <span>Messagerie</span>
            @if($unreadCount > 0)
                <span class="badge bg-danger rounded-pill ms-auto" style="font-size:10px;">{{ $unreadCount }}</span>
            @endif
        </a>

        {{-- Sous-menu des enfants liés --}}
        @if($liens->isNotEmpty())
            <div class="text-uppercase small fw-bold text-white-50 px-2 pt-3 pb-1" style="font-size:10px;letter-spacing:1px;">
                Mes enfants ({{ $liens->count() }})
            </div>

            @foreach($liens as $lien)
                @if($lien->joueur)
                @php
                    $childName = $lien->joueur->user->prenom ?? $lien->joueur->user->name ?? 'Enfant';
                    $isActiveChild = request()->is('parent/joueur/' . $lien->joueur->id . '*');
                @endphp
                <a href="{{ route('parent.joueur.show', $lien->joueur) }}" class="parent-sidebar-link ps-3 {{ $isActiveChild ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i>
                    <span class="text-truncate">{{ $childName }}</span>
                    @if($lien->joueur->club)
                        <small class="badge bg-white bg-opacity-10 text-white-50 ms-auto text-truncate" style="max-width:70px;font-size:9px;">
                            {{ $lien->joueur->club->nom }}
                        </small>
                    @endif
                </a>
                @endif
            @endforeach
        @endif

        {{-- Lien Profil --}}
        <div class="text-uppercase small fw-bold text-white-50 px-2 pt-3 pb-1" style="font-size:10px;letter-spacing:1px;">
            Mon compte
        </div>
        <a href="{{ route('parent.profil') }}" class="parent-sidebar-link {{ request()->routeIs('parent.profil') ? 'active' : '' }}">
            <i class="bi bi-person-gear"></i>
            <span>Mon profil</span>
        </a>

    </nav>

    {{-- Profil utilisateur en bas (dropdown) --}}
    <div class="border-top border-white border-opacity-10 p-2" style="position:relative;">
        <button type="button"
                class="btn w-100 d-flex align-items-center gap-2 text-start p-2 rounded-3"
                style="background:transparent;border:none;color:rgba(255,255,255,.85);"
                id="parentUserMenuBtn"
                onclick="toggleParentUserMenu()">
            @if($user->avatar)
                <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}"
                     class="rounded-circle object-fit-cover flex-shrink-0"
                     style="width:36px;height:36px;">
            @else
                <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0 shadow-sm"
                     style="width:36px;height:36px;background:#0D9488;font-size:14px;">
                    {{ $initiale }}
                </div>
            @endif
            <div class="overflow-hidden flex-grow-1">
                <div class="fw-semibold text-truncate small text-white">{{ $user->prenom ?? $user->name }}</div>
                <div class="text-white-50" style="font-size:10px;letter-spacing:.5px;">PARENT</div>
            </div>
            <i class="bi bi-chevron-up text-white-50" id="parentChevron" style="font-size:.75rem;transition:.2s;"></i>
        </button>

        {{-- Menu popup --}}
        <div id="parentUserMenu"
             class="rounded-3 shadow-lg border border-white border-opacity-10 d-none"
             style="position:absolute;bottom:100%;left:8px;right:8px;background:#0D2E5C;z-index:200;padding:8px 0;">

            <a href="{{ route('parent.profil') }}"
               class="d-flex align-items-center gap-2 px-3 py-2 text-white text-decoration-none"
               style="font-size:.87rem;transition:background .15s;" onmouseover="this.style.background='rgba(255,255,255,.1)'" onmouseout="this.style.background='transparent'">
                <i class="bi bi-person-circle" style="width:18px;"></i> Mon profil
            </a>

            <a href="{{ route('parent.dashboard') }}"
               class="d-flex align-items-center gap-2 px-3 py-2 text-white text-decoration-none"
               style="font-size:.87rem;transition:background .15s;" onmouseover="this.style.background='rgba(255,255,255,.1)'" onmouseout="this.style.background='transparent'">
                <i class="bi bi-grid-1x2-fill" style="width:18px;"></i> Tableau de bord
            </a>

            <a href="{{ route('messages.inbox') }}"
               class="d-flex align-items-center gap-2 px-3 py-2 text-white text-decoration-none"
               style="font-size:.87rem;transition:background .15s;" onmouseover="this.style.background='rgba(255,255,255,.1)'" onmouseout="this.style.background='transparent'">
                <i class="bi bi-chat-dots-fill" style="width:18px;"></i> Messagerie
                @if($unreadCount > 0)
                    <span class="badge bg-danger rounded-pill ms-auto" style="font-size:9px;">{{ $unreadCount }}</span>
                @endif
            </a>

            <div class="border-top border-white border-opacity-10 my-1"></div>

            <form method="POST" action="{{ route('logout') }}" class="mb-0">
                @csrf
                <button type="submit"
                        class="d-flex align-items-center gap-2 px-3 py-2 text-danger text-decoration-none w-100 border-0 bg-transparent"
                        style="font-size:.87rem;transition:background .15s;"
                        onmouseover="this.style.background='rgba(239,68,68,.1)'" onmouseout="this.style.background='transparent'">
                    <i class="bi bi-box-arrow-right" style="width:18px;"></i> Deconnexion
                </button>
            </form>
        </div>
    </div>
</aside>

<style>
    .parent-sidebar-link {
        display: flex;
        align-items: center;
        gap: .75rem;
        color: rgba(255, 255, 255, .72);
        text-decoration: none;
        padding: .6rem .85rem;
        border-radius: .5rem;
        margin-bottom: .2rem;
        font-size: .88rem;
        font-weight: 500;
        transition: all .2s ease;
    }
    .parent-sidebar-link:hover {
        color: #fff;
        background: rgba(255, 255, 255, .1);
    }
    .parent-sidebar-link.active {
        color: #fff;
        background: #0D9488;
        font-weight: 600;
    }
    .parent-sidebar-link i {
        font-size: 1.05rem;
        width: 1.3rem;
        text-align: center;
        flex-shrink: 0;
    }
    .hover-text-white:hover { color: #fff !important; }
    .hover-text-danger:hover { color: #ef4444 !important; }

    @media (max-width: 991px) {
        #parentSidebar {
            transform: translateX(-100%);
            transition: transform .25s ease;
        }
        #parentSidebar.mobile-open {
            transform: translateX(0);
        }
    }
</style>

<script>
    window.closeParentSidebar = function () {
        document.getElementById('parentSidebar')?.classList.remove('mobile-open');
        document.getElementById('sidebar-overlay')?.classList.add('d-none');
    };
    window.toggleMobileSidebar = function () {
        const sidebar = document.getElementById('parentSidebar') ||
                        document.getElementById('joueurSidebar') ||
                        document.getElementById('clubSidebar') ||
                        document.getElementById('agentSidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const open = sidebar?.classList.toggle('mobile-open');
        overlay?.classList.toggle('d-none', !open);
    };
    window.closeSidebar = function () {
        document.querySelectorAll('#parentSidebar, #joueurSidebar, #clubSidebar, #agentSidebar').forEach(s => s.classList.remove('mobile-open'));
        document.getElementById('sidebar-overlay')?.classList.add('d-none');
    };
    window.toggleParentUserMenu = function () {
        const menu    = document.getElementById('parentUserMenu');
        const chevron = document.getElementById('parentChevron');
        if (!menu) return;
        const isHidden = menu.classList.toggle('d-none');
        if (chevron) {
            chevron.style.transform = isHidden ? 'rotate(0deg)' : 'rotate(180deg)';
        }
    };
    // Fermer le menu si on clique ailleurs
    document.addEventListener('click', function (e) {
        const btn  = document.getElementById('parentUserMenuBtn');
        const menu = document.getElementById('parentUserMenu');
        if (menu && btn && !menu.classList.contains('d-none')) {
            if (!menu.contains(e.target) && !btn.contains(e.target)) {
                menu.classList.add('d-none');
                const chevron = document.getElementById('parentChevron');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }
        }
    });
</script>
