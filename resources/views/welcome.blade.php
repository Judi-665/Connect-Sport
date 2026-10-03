{{--
    resources/views/welcome.blade.php
    Thème dark/light via data-bs-theme sur <html> (géré par le navbar).
    Bootstrap 5 natif — variables CSS utilisées systématiquement.
    Section rôles remplacée par @include('partials._ecosysteme').
--}}
@extends(auth()->check() && auth()->user()->role === 'agent' ? 'layouts.agent' : 'layouts.app')

@section('title', 'Accueil')
@section('description', 'Connect Sport — La plateforme multi-sport qui connecte clubs, joueurs, agents et supporters.')

@if(auth()->check() && auth()->user()->role === 'agent')
    @section('page-title', 'Accueil')
    @section('sidebar-nav')
        @include('agents.partials.nav')
    @endsection
@endif

@section('content')

{{-- ═══════════════════════════════════════════════════════════════
     HERO — fond dégradé fixe (indépendant du thème)
═══════════════════════════════════════════════════════════════════ --}}
<section class="position-relative overflow-hidden" style="min-height:600px;">

   {{-- Arrière-plan avec carrousel d'images floues --}}
<div class="position-absolute top-0 start-0 w-100 h-100" aria-hidden="true" style="z-index:0;">
    {{-- Conteneur du cadran arrondi --}}
    <div class="cadran-carousel position-relative w-100 h-100 rounded-4 overflow-hidden shadow-lg">
        {{-- Deux calques pour le crossfade des images --}}
        <div class="carousel-bg-img" id="bgImg1" style="background-image: url('{{ asset('images/fond/format1.jpg') }}'); opacity:1;"></div>
        <div class="carousel-bg-img" id="bgImg2" style="background-image: url('{{ asset('images/fond/format1.jpg') }}'); opacity:0;"></div>
    </div>

    {{-- Overlay dégradé très léger pour bien voir l'image --}}
    <div class="position-absolute top-0 start-0 w-100 h-100 rounded-4"
         style="background: linear-gradient(135deg, rgba(13,46,92,0.4) 0%, rgba(13,46,92,0.35) 40%, rgba(26,86,160,0.3) 100%); z-index:1;">
    </div>
</div>

<style>
    .carousel-bg-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        filter: blur(1px);       /* flou minime – l'image reste très lisible */
        transform: scale(1.02);
        transition: opacity 1.2s ease-in-out;
        will-change: opacity;
    }
    .rounded-4 {
        border-radius: 2rem !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Liste des images (générée dynamiquement depuis Laravel)
        const images = @json(array_map(fn($i) => asset("images/fond/format{$i}.jpg"), range(1,7)));

        let currentIndex = 0;          // image actuellement affichée
        let nextIndex = 1;             // prochaine image à charger
        let isTransitioning = false;

        const bgImg1 = document.getElementById('bgImg1');
        const bgImg2 = document.getElementById('bgImg2');

        // Préchargement des images pour éviter les flashs
        function preloadImage(url) {
            return new Promise((resolve) => {
                const img = new Image();
                img.onload = () => resolve(url);
                img.onerror = () => resolve(url); // fallback
                img.src = url;
            });
        }

        // Change l'image sur le calque inactif puis fait le fondu
        async function rotateBackground() {
            if (isTransitioning) return;
            isTransitioning = true;

            // Déterminer quel calque est actif (opacity 1) et l'inactif
            const isImg1Active = parseFloat(bgImg1.style.opacity) === 1;
            const activeLayer = isImg1Active ? bgImg1 : bgImg2;
            const inactiveLayer = isImg1Active ? bgImg2 : bgImg1;

            // Prochaine image
            const nextImageUrl = images[nextIndex];

            // Précharger l'image avant de l'appliquer
            await preloadImage(nextImageUrl);

            // Appliquer la nouvelle image sur le calque inactif
            inactiveLayer.style.backgroundImage = `url('${nextImageUrl}')`;

            // Forcer un reflow pour que la transition se déclenche
            inactiveLayer.offsetHeight;

            // Crossfade : calque inactif devient visible, actif devient transparent
            activeLayer.style.opacity = '0';
            inactiveLayer.style.opacity = '1';

            // Mettre à jour les indices de boucle
            currentIndex = nextIndex;
            nextIndex = (nextIndex + 1) % images.length;

            // Réactiver après la fin de la transition
            setTimeout(() => {
                isTransitioning = false;
            }, 1200); // durée identique à la transition CSS
        }

        // Démarrer le carrousel (changement toutes les 6 secondes)
        let interval = setInterval(rotateBackground, 6000);

        // Optionnel : nettoyer l'intervalle si la page est dynamique (ex: Livewire)
        window.addEventListener('beforeunload', () => clearInterval(interval));
    });
</script>

    {{-- Contenu --}}
    <div class="container position-relative py-5" style="z-index:2;">
        <div class="row align-items-center g-5 py-4">

            {{-- Bloc texte gauche --}}
            <div class="col-lg-7">

                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3"
                     style="background:rgba(249,115,22,.15);border:1px solid rgba(249,115,22,.32);">
                    <div class="rounded-circle" style="width:7px;height:7px;background:#F97316;"></div>
                    <span class="fw-semibold text-uppercase"
                          style="font-size:.67rem;letter-spacing:.13em;color:#F97316;">
                        Plateforme multi-sport &mdash; Afrique francophone
                    </span>
                </div>

                <h1 class="fw-black text-white mb-3 lh-1"
                    style="font-family:'Bebas Neue',sans-serif;
                           font-size:clamp(54px,9vw,96px);
                           letter-spacing:.04em;">
                    CONNECT<span style="color:#F97316;">SPORT</span>
                </h1>

                <p class="mb-4 text-white" style="opacity:.72;font-size:1.05rem;max-width:480px;line-height:1.75;">
                    La plateforme qui connecte clubs, joueurs, agents, supporters et sponsors.
                    Gérez vos licences, découvrez des opportunités et simplifiez vos transferts.
                </p>

                <div class="d-flex gap-3 flex-wrap mb-5">
                    @auth
                        @php
                            $dashRoute = match(auth()->user()->role ?? '') {
                                'club'      => 'club.dashboard',
                                'joueur'    => 'joueur.dashboard',
                                'parent'    => 'parent.dashboard',
                                'agent'     => 'agent.dashboard',
                                'supporter' => 'supporter.dashboard',
                                'admin'     => 'admin.dashboard',
                                default     => 'dashboard',
                            };
                        @endphp
                        <a href="{{ route($dashRoute) }}"
                           class="btn btn-warning fw-bold px-4 py-2 rounded-3">
                            Mon tableau de bord
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                           class="btn btn-warning fw-bold px-4 py-2 rounded-3">
                            Rejoindre gratuitement
                        </a>
                        <a href="{{ route('clubs.index') }}"
                           class="btn btn-outline-light fw-semibold px-4 py-2 rounded-3">
                            Explorer les clubs
                        </a>
                    @endauth
                </div>

                {{-- KPIs héro --}}
                <div class="row g-3 pt-4 border-top border-white border-opacity-10">
                    @php
                        $stats = $stats ?? [];
                        $kpis = [
                            ['val' => number_format($stats['clubs']        ?? 0), 'label' => 'Clubs inscrits'],
                            ['val' => number_format($stats['joueurs']      ?? 0), 'label' => 'Joueurs actifs'],
                            ['val' => number_format($stats['opportunites'] ?? 0), 'label' => 'Opportunités'],
                            ['val' => ($stats['sports']                    ?? 0), 'label' => 'Sports couverts'],
                        ];
                    @endphp
                    @foreach($kpis as $k)
                    <div class="col-6 col-sm-3">
                        <div class="fw-black text-white lh-1 mb-1"
                             style="font-family:'Bebas Neue',sans-serif;font-size:2.4rem;">
                            {{ $k['val'] }}<span style="color:#F97316;">+</span>
                        </div>
                        <div class="text-white text-uppercase"
                             style="opacity:.45;font-size:.67rem;letter-spacing:.09em;">
                            {{ $k['label'] }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Carte "Qui peut rejoindre" --}}
            <div class="col-lg-5 d-none d-lg-block">
                <div class="rounded-4 p-4"
                     style="background:rgba(255,255,255,.06);
                            backdrop-filter:blur(20px);
                            -webkit-backdrop-filter:blur(20px);
                            border:1px solid rgba(255,255,255,.12);">

                    <p class="fw-semibold text-uppercase text-white mb-4"
                       style="font-size:.64rem;letter-spacing:.17em;opacity:.5;">
                        Qui peut rejoindre
                    </p>

                    @php
                        $acteurs = [
                            ['color'=>'#3B82F6','bg'=>'rgba(59,130,246,.18)',  'nom'=>'Clubs sportifs',       'desc'=>'Gestion équipes, licences, agenda, sponsors'],
                            ['color'=>'#F97316','bg'=>'rgba(249,115,22,.18)',   'nom'=>'Joueurs',              'desc'=>'Profil, statistiques, transferts, formations'],
                            ['color'=>'#38BDF8','bg'=>'rgba(56,189,248,.18)',   'nom'=>'Agents sportifs',      'desc'=>'Portefeuille joueurs, négociation, mandats'],
                            ['color'=>'#10B981','bg'=>'rgba(16,185,129,.18)',   'nom'=>'Supporters & Parents', 'desc'=>'Suivi clubs, agenda, notifications'],
                            ['color'=>'#F59E0B','bg'=>'rgba(245,158,11,.18)',   'nom'=>'Sponsors',             'desc'=>'Visibilité maillot, pancartes, digital'],
                        ];
                    @endphp

                    @foreach($acteurs as $a)
                    <div class="d-flex align-items-center gap-3 py-3
                                {{ !$loop->last ? 'border-bottom border-white border-opacity-10' : '' }}">
                        <div class="rounded-3 flex-shrink-0"
                             style="width:8px;height:8px;background:{{ $a['color'] }};border-radius:50%;">
                        </div>
                        <div>
                            <div class="fw-semibold text-white" style="font-size:.85rem;">{{ $a['nom'] }}</div>
                            <div class="text-white" style="font-size:.73rem;opacity:.5;line-height:1.5;">
                                {{ $a['desc'] }}
                            </div>
                        </div>
                        <div class="ms-auto flex-shrink-0 rounded-pill px-2 py-1"
                             style="background:{{ $a['bg'] }};font-size:.65rem;color:{{ $a['color'] }};white-space:nowrap;">
                            Rejoindre
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     FLUX 3 COLONNES
     Couleurs via variables Bootstrap 5 — adaptatif dark/light auto.
═══════════════════════════════════════════════════════════════════ --}}
<section class="py-5 bg-body-tertiary">
    <div class="container">
        <div class="row g-4">

            {{-- Colonne gauche --}}
            <div class="col-lg-3 d-none d-lg-block">

                <div class="card border shadow-sm rounded-4 mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div style="width:18px;height:2px;background:var(--bs-primary);border-radius:2px;"></div>
                            <p class="fw-semibold text-uppercase text-primary mb-0"
                               style="font-size:.63rem;letter-spacing:.13em;">
                                Espaces disponibles
                            </p>
                        </div>
                        @php
                            $navRoles = [
                                ['label'=>'Club sportif', 'color'=>'text-primary'],
                                ['label'=>'Joueur',       'color'=>'text-warning'],
                                ['label'=>'Agent',        'color'=>'text-info'],
                                ['label'=>'Supporter',    'color'=>'text-danger'],
                                ['label'=>'Parent',       'color'=>'text-success'],
                                ['label'=>'Sponsor',      'color'=>'text-secondary'],
                            ];
                        @endphp
                        @foreach($navRoles as $r)
                        <a href="{{ route('register') }}"
                           class="d-flex align-items-center gap-2 py-2 px-2 rounded-3
                                  text-decoration-none text-body mb-1 cs-hover-link">
                            <span class="rounded-circle {{ $r['color'] }}"
                                  style="width:7px;height:7px;background:currentColor;flex-shrink:0;display:inline-block;">
                            </span>
                            <span class="fw-medium" style="font-size:.85rem;">{{ $r['label'] }}</span>
                        </a>
                        @endforeach
                        <div class="mt-3 pt-3 border-top">
                            <a href="{{ route('register') }}"
                               class="btn btn-warning w-100 fw-bold btn-sm rounded-3">
                                Créer mon compte
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-sm rounded-4">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div style="width:18px;height:2px;background:var(--bs-primary);border-radius:2px;"></div>
                            <p class="fw-semibold text-uppercase text-primary mb-0"
                               style="font-size:.63rem;letter-spacing:.13em;">
                                Sports couverts
                            </p>
                        </div>
                        @php $sportsList = ['Football','Handball','Basketball','Volleyball','Athlétisme','Natation']; @endphp
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($sportsList as $sp)
                            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary fw-medium"
                                  style="font-size:.72rem;">
                                {{ $sp }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Colonne centrale --}}
            <div class="col-lg-6">
                @include('partials._clubs_recents', ['clubs' => $clubs ?? collect()])
                <div class="my-4"></div>
                @if(!auth()->user()?->isSupporter())
                    @include('partials._opportunites',  ['opportunites' => $opportunites ?? collect()])
                @endif
            </div>

            {{-- Colonne droite --}}
            <div class="col-lg-3 d-none d-lg-block">

                <div class="card border shadow-sm rounded-4 mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div style="width:18px;height:2px;background:var(--bs-primary);border-radius:2px;"></div>
                            <p class="fw-semibold text-uppercase text-primary mb-0"
                               style="font-size:.63rem;letter-spacing:.13em;">
                                Tendances
                            </p>
                        </div>
                        @php
                            $tendances = [
                                ['tag'=>'#Football',    'posts'=>'124 publications'],
                                ['tag'=>'#Transferts',  'posts'=>'89 publications'],
                                ['tag'=>'#Handball',    'posts'=>'67 publications'],
                                ['tag'=>'#Formation',   'posts'=>'54 publications'],
                                ['tag'=>'#Recrutement', 'posts'=>'41 publications'],
                            ];
                        @endphp
                        @foreach($tendances as $t)
                        <div class="d-flex justify-content-between align-items-center py-2
                                    {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div>
                                <div class="fw-bold text-primary" style="font-size:.82rem;">{{ $t['tag'] }}</div>
                                <div class="text-body-secondary" style="font-size:.72rem;">{{ $t['posts'] }}</div>
                            </div>
                            <svg width="14" height="14" viewBox="0 0 16 16" fill="none"
                                 stroke="#10B981" stroke-width="1.8" stroke-linecap="round">
                                <path d="M4 12 L12 4 M12 4 H7 M12 4 V9"/>
                            </svg>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="card border shadow-sm rounded-4">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div style="width:18px;height:2px;background:var(--bs-primary);border-radius:2px;"></div>
                            <p class="fw-semibold text-uppercase text-primary mb-0"
                               style="font-size:.63rem;letter-spacing:.13em;">
                                À suivre
                            </p>
                        </div>
                        @php
                            $suggestions = [
                                ['initiales'=>'AS','nom'=>'AS Cotonou FC', 'sport'=>'Football',   'membres'=>'120 membres'],
                                ['initiales'=>'BC','nom'=>'BC Lomé Stars', 'sport'=>'Basketball', 'membres'=>'85 membres'],
                                ['initiales'=>'HC','nom'=>'HC Abidjan',    'sport'=>'Handball',   'membres'=>'62 membres'],
                            ];
                        @endphp
                        @foreach($suggestions as $s)
                        <div class="d-flex align-items-center gap-3 py-2
                                    {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="rounded-3 bg-primary text-white d-flex align-items-center
                                        justify-content-center fw-bold flex-shrink-0"
                                 style="width:38px;height:38px;font-size:.78rem;">
                                {{ $s['initiales'] }}
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="fw-semibold text-truncate" style="font-size:.82rem;">{{ $s['nom'] }}</div>
                                <div class="text-body-secondary" style="font-size:.72rem;">
                                    {{ $s['sport'] }} &bull; {{ $s['membres'] }}
                                </div>
                            </div>
                            <a href="{{ route('register') }}"
                               class="btn btn-outline-primary btn-sm rounded-pill px-3"
                               style="font-size:.72rem;white-space:nowrap;">
                                Suivre
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     ÉCOSYSTÈME INTERACTIF — remplace la section "6 cards rôles"
═══════════════════════════════════════════════════════════════════ --}}
@include('partials._ecosysteme', ['stats' => $stats ?? []])

{{-- ═══════════════════════════════════════════════════════════════
     CTA INSCRIPTION
═══════════════════════════════════════════════════════════════════ --}}
@guest
<section class="py-5" style="background:linear-gradient(135deg,#0D2E5C 0%,#1A56A0 100%);">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <h2 class="fw-black text-white mb-2"
                    style="font-family:'Bebas Neue',sans-serif;
                           font-size:clamp(34px,5vw,60px);
                           letter-spacing:.04em;">
                    Rejoignez Connect<span style="color:#F97316;">Sport</span><br>dès aujourd'hui
                </h2>
                <p class="mb-4 text-white" style="opacity:.68;font-size:.93rem;">
                    Inscription gratuite. Choisissez votre rôle et accédez immédiatement à votre espace.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(['Club','Joueur','Agent','Supporter','Parent'] as $label)
                    <a href="{{ route('register') }}"
                       class="btn btn-outline-light fw-semibold rounded-3">
                        {{ $label }}
                    </a>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-5 text-center text-lg-end">
                <a href="{{ route('register') }}"
                   class="btn btn-warning btn-lg fw-bold px-5 py-3 rounded-4 d-block d-lg-inline-block mb-3">
                    Créer mon compte gratuit
                </a>
                <div>
                    <a href="{{ route('login') }}"
                       class="text-decoration-none text-white"
                       style="font-size:.82rem;opacity:.6;">
                        Déjà inscrit ?
                        <span class="text-warning fw-semibold" style="opacity:1;">Se connecter &rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endguest

<style>
.cs-hover-link:hover { background: var(--bs-tertiary-bg); }
</style>

@endsection