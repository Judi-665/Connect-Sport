{{-- resources/views/partials/footer.blade.php --}}

@php
    $user = auth()->user();
    $isDashboard = $user && request()->routeIs(
        'club.*', 'joueur.*', 'agent.*', 'supporter.*', 'parent.*', 'admin.*', 'messages.*'
    );
@endphp

@if(!$isDashboard)
<footer class="cs-footer">

    <div class="container">
        <div class="row g-5">

            {{-- Col 1 — Brand --}}
            <div class="col-lg-3 col-md-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Connect Sport"
                         style="width:42px;height:42px;border-radius:50%;object-fit:cover;">
                    <span class="cs-footer-brand-name">
                        CONNECT<span>SPORT</span>
                    </span>
                </div>
                <p class="cs-footer-tagline">
                    La plateforme multi-sport qui connecte clubs, joueurs, agents, supporters et sponsors en un seul endroit.
                </p>
                <div class="d-flex gap-2 mt-3">
                    <a href="#" class="cs-footer-social" title="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="cs-footer-social" title="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="cs-footer-social" title="Twitter/X"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="cs-footer-social" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    <a href="#" class="cs-footer-social" title="YouTube"><i class="bi bi-youtube"></i></a>
                </div>
            </div>

            {{-- Col 2 — Plateforme --}}
            <div class="col-lg-2 col-md-6">
                <div class="cs-footer-title">Plateforme</div>
                <ul class="cs-footer-links">
                    <li><a href="{{ route('clubs.index') }}"><i class="bi bi-shield"></i> Clubs</a></li>
                    <li><a href="{{ route('joueurs.sans-club') }}"><i class="bi bi-people"></i> Joueurs</a></li>
                    <li><a href="{{ route('opportunites.index') }}"><i class="bi bi-lightning"></i> Opportunités</a></li>
                    <li><a href="{{ route('register') }}"><i class="bi bi-person-plus"></i> S'inscrire</a></li>
                    <li><a href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right"></i> Connexion</a></li>
                </ul>
            </div>

            {{-- Col 3 — Acteurs --}}
            <div class="col-lg-2 col-md-6">
                <div class="cs-footer-title">Acteurs</div>
                <ul class="cs-footer-links">
                    <li><a href="#"><i class="bi bi-building"></i> Pour les clubs</a></li>
                    <li><a href="#"><i class="bi bi-person-badge"></i> Pour les joueurs</a></li>
                    <li><a href="#"><i class="bi bi-briefcase"></i> Pour les agents</a></li>
                    <li><a href="#"><i class="bi bi-heart"></i> Pour les supporters</a></li>
                    <li><a href="#"><i class="bi bi-patch-star"></i> Pour les sponsors</a></li>
                </ul>
            </div>

            {{-- Col 4 — Services --}}
            <div class="col-lg-2 col-md-6">
                <div class="cs-footer-title">Services</div>
                <ul class="cs-footer-links">
                    <li><a href="#"><i class="bi bi-arrow-left-right"></i> Transferts</a></li>
                    <li><a href="#"><i class="bi bi-camera-video"></i> Formations</a></li>
                    <li><a href="#"><i class="bi bi-file-earmark-pdf"></i> Licences</a></li>
                    <li><a href="#"><i class="bi bi-calendar-event"></i> Agenda</a></li>
                    <li><a href="#"><i class="bi bi-robot"></i> IA Carrière</a></li>
                </ul>
            </div>

            {{-- Col 5 — Sports & Contact --}}
            <div class="col-lg-3 col-md-6">
                <div class="cs-footer-title">Sports disponibles</div>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="cs-sport-tag"><i class="bi bi-dribbble me-1"></i>Handball</span>
                    <span class="cs-sport-tag">Football</span>
                    <span class="cs-sport-tag">Basketball</span>
                    <span class="cs-sport-tag">Volleyball</span>
                    <span class="cs-sport-tag">Tennis</span>
                    <span class="cs-sport-tag">+ Plus</span>
                </div>

                <div class="cs-footer-title mt-4">Contact</div>
                <ul class="cs-footer-links">
                    <li>
                        <a href="mailto:contact@connectsport.com">
                            <i class="bi bi-envelope"></i> contact@connectsport.com
                        </a>
                    </li>
                    <li>
                        <a href="#"><i class="bi bi-whatsapp"></i> WhatsApp</a>
                    </li>
                </ul>
            </div>

        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="cs-footer-bottom mt-5">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <span class="cs-footer-copy">
                    © {{ date('Y') }} <a href="{{ route('home') }}">Connect Sport</a>. Tous droits réservés.
                </span>
                <div class="d-flex gap-3">
                    <a href="#" class="cs-footer-legal-link">Confidentialité</a>
                    <a href="#" class="cs-footer-legal-link">CGU</a>
                    <a href="#" class="cs-footer-legal-link">Mentions légales</a>
                    <a href="#" class="cs-footer-legal-link">Cookies</a>
                </div>
            </div>
        </div>
    </div>

</footer>
@endif

{{-- ── Footer minimal pour les pages dashboard ── --}}
@if($isDashboard)
<footer class="cs-footer-minimal">
    <span>© {{ date('Y') }} <a href="{{ route('home') }}">Connect Sport</a></span>
    <div class="d-flex gap-3">
        <a href="#" class="cs-footer-legal-link">CGU</a>
        <a href="#" class="cs-footer-legal-link">Confidentialité</a>
    </div>
</footer>
@endif

<style>
/* ── Variables thème clair ── */
:root,
[data-bs-theme="light"],
[data-theme="light"] {
    --footer-bg:          #0D2E5C;
    --footer-border:      #1E3A5F;
    --footer-text:        #64748B;
    --footer-title:       #CBD5E1;
    --footer-link:        #64748B;
    --footer-link-hover:  #F97316;
    --footer-sport-bg:    #0f2240;
    --footer-sport-clr:   #5B9BD5;
    --footer-sport-bdr:   #1E3A5F;
    --footer-copy:        #475569;
    --footer-minimal-bg:  rgba(0,0,0,.03);
    --footer-minimal-bdr: rgba(0,0,0,.07);
    --footer-minimal-clr: rgba(0,0,0,.4);
}

/* ── Variables thème sombre ── */
[data-bs-theme="dark"],
[data-theme="dark"] {
    --footer-bg:          #060c18;
    --footer-border:      #0d1e36;
    --footer-text:        #475569;
    --footer-title:       #94A3B8;
    --footer-link:        #475569;
    --footer-link-hover:  #F97316;
    --footer-sport-bg:    #080f20;
    --footer-sport-clr:   #4a82b8;
    --footer-sport-bdr:   #0d1e36;
    --footer-copy:        #334155;
    --footer-minimal-bg:  rgba(255,255,255,.02);
    --footer-minimal-bdr: rgba(255,255,255,.06);
    --footer-minimal-clr: rgba(255,255,255,.25);
}

/* ── Footer général ── */
.cs-footer {
    background: var(--footer-bg);
    color: var(--footer-text);
    padding: 60px 0 0;
    margin-top: auto;
    transition: background .25s ease;
}

.cs-footer-brand-name {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 22px; letter-spacing: 2px; color: #fff;
}
.cs-footer-brand-name span { color: #F97316; }

.cs-footer-tagline {
    font-size: 13px; color: var(--footer-text);
    line-height: 1.6; max-width: 260px; margin-top: 8px;
}

.cs-footer-title {
    font-size: 11px; font-weight: 700; letter-spacing: 1.5px;
    text-transform: uppercase; color: var(--footer-title); margin-bottom: 16px;
}

.cs-footer-links { list-style: none; padding: 0; margin: 0; }
.cs-footer-links li { margin-bottom: 10px; }
.cs-footer-links a {
    color: var(--footer-link); text-decoration: none;
    font-size: 14px; font-weight: 500;
    display: flex; align-items: center; gap: 8px;
    transition: color .2s;
}
.cs-footer-links a i { font-size: 13px; }
.cs-footer-links a:hover { color: var(--footer-link-hover); }

.cs-footer-social {
    width: 36px; height: 36px; border-radius: 50%;
    border: 1px solid var(--footer-border);
    background: transparent; color: var(--footer-text);
    display: flex; align-items: center; justify-content: center;
    text-decoration: none; font-size: 16px;
    transition: all .2s;
}
.cs-footer-social:hover {
    background: #F97316; border-color: #F97316; color: #fff;
}

.cs-sport-tag {
    background: var(--footer-sport-bg);
    color: var(--footer-sport-clr);
    border: 1px solid var(--footer-sport-bdr);
    border-radius: 20px; padding: 4px 12px;
    font-size: 12px; font-weight: 500;
}

.cs-footer-bottom {
    border-top: 1px solid var(--footer-border);
    padding: 20px 0;
}
.cs-footer-copy { font-size: 13px; color: var(--footer-copy); }
.cs-footer-copy a { color: #5B9BD5; text-decoration: none; }
.cs-footer-copy a:hover { color: #F97316; }
.cs-footer-legal-link {
    font-size: 12px; color: var(--footer-copy);
    text-decoration: none; transition: color .2s;
}
.cs-footer-legal-link:hover { color: #F97316; }

/* ── Footer minimal (dashboard) ── */
.cs-footer-minimal {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 8px;
    padding: 12px 24px;
    background: var(--footer-minimal-bg);
    border-top: 1px solid var(--footer-minimal-bdr);
    font-size: 12px; color: var(--footer-minimal-clr);
    transition: background .25s ease, border-color .25s ease;
}
.cs-footer-minimal a {
    color: var(--footer-minimal-clr); text-decoration: none;
}
.cs-footer-minimal a:hover { color: #F97316; }
</style>