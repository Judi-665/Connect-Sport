{{--
    resources/views/partials/_ecosysteme.blade.php
    6 cards acteurs — sobre, lisible, Bootstrap 5 natif.
    Dark/light via variables Bootstrap 5. Aucun emoji, aucun sticker.
--}}

@php
$stats  = $stats ?? [];
$acteurs = [
    [
        'abbr'     => 'CLB',
        'color'    => '#3B82F6',
        'titre'    => 'Club sportif',
        'desc'     => 'Gérez votre club de A à Z.',
        'features' => ['Licences PDF par catégorie', 'Agenda, matchs & convocations', 'Sponsors et partenaires'],
        'stat'     => number_format($stats['clubs'] ?? 0).' clubs inscrits',
    ],
    [
        'abbr'     => 'JOU',
        'color'    => '#F97316',
        'titre'    => 'Joueur',
        'desc'     => 'Valorisez votre profil et votre carrière.',
        'features' => ['Profil & statistiques publics', 'Demandes de transfert', 'Accès aux formations'],
        'stat'     => number_format($stats['joueurs'] ?? 0).' joueurs actifs',
    ],
    [
        'abbr'     => 'AGT',
        'color'    => '#38BDF8',
        'titre'    => 'Agent sportif',
        'desc'     => 'Gérez vos mandats et négociez les transferts.',
        'features' => ['Portefeuille de joueurs', 'Négociations inter-clubs', 'Messagerie directe clubs'],
        'stat'     => number_format($stats['agents'] ?? 0).' agents',
    ],
    [
        'abbr'     => 'SUP',
        'color'    => '#8B5CF6',
        'titre'    => 'Supporter',
        'desc'     => 'Suivez vos clubs et rejoignez la communauté.',
        'features' => ['Suivi multi-clubs', 'Actualités & résultats', 'Abonnement premium'],
        'stat'     => number_format($stats['supporters'] ?? 0).' supporters',
    ],
    [
        'abbr'     => 'PAR',
        'color'    => '#10B981',
        'titre'    => 'Parent',
        'desc'     => 'Suivez le parcours sportif de votre enfant.',
        'features' => ['Suivi des performances', 'Agenda & convocations', 'Messagerie avec le club'],
        'stat'     => number_format($stats['parents'] ?? 0).' parents',
    ],
    [
        'abbr'     => 'SPO',
        'color'    => '#F59E0B',
        'titre'    => 'Sponsor',
        'desc'     => 'Visibilité garantie auprès des clubs.',
        'features' => ['Logo maillot domicile & extérieur', 'Pancartes matchs', 'Profil digital avec lien'],
        'stat'     => number_format($stats['sponsors'] ?? 0).' sponsors',
    ],
];
@endphp

<section class="py-5 bg-body">
    <div class="container">

        {{-- En-tête --}}
        <div class="text-center mb-5">
            <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                <div style="width:24px;height:2px;background:#F97316;border-radius:2px;"></div>
                <span class="fw-semibold text-uppercase text-warning"
                      style="font-size:.67rem;letter-spacing:.16em;">
                    Pour tous les acteurs du sport
                </span>
                <div style="width:24px;height:2px;background:#F97316;border-radius:2px;"></div>
            </div>
            <h2 class="fw-black mb-2"
                style="font-family:'Bebas Neue',sans-serif;
                       font-size:clamp(28px,3.8vw,48px);
                       letter-spacing:.04em;">
                Une plateforme, tous les rôles
            </h2>
            <p class="text-body-secondary mb-0 mx-auto"
               style="max-width:460px;font-size:.87rem;">
                Chaque acteur dispose de son espace et de ses outils dédiés.
            </p>
        </div>

        {{-- Grille 6 cards --}}
        <div class="row g-3">
            @foreach($acteurs as $a)
            <div class="col-sm-6 col-lg-4">
                <div class="card border rounded-4 h-100 cs-role-card">
                    <div class="card-body p-4">

                        {{-- Icône + titre --}}
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center fw-black flex-shrink-0"
                                 style="width:46px;height:46px;
                                        background:{{ $a['color'] }}1a;
                                        color:{{ $a['color'] }};
                                        font-family:'Bebas Neue',sans-serif;
                                        font-size:13px;letter-spacing:1.5px;
                                        border:1px solid {{ $a['color'] }}33;">
                                {{ $a['abbr'] }}
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0" style="font-size:.93rem;">
                                    {{ $a['titre'] }}
                                </h5>
                                <span class="text-body-secondary" style="font-size:.73rem;">
                                    {{ $a['stat'] }}
                                </span>
                            </div>
                        </div>

                        {{-- Description courte --}}
                        <p class="text-body-secondary mb-3"
                           style="font-size:.80rem;line-height:1.6;">
                            {{ $a['desc'] }}
                        </p>

                        {{-- 3 fonctionnalités --}}
                        <ul class="list-unstyled mb-4">
                            @foreach($a['features'] as $f)
                            <li class="d-flex align-items-center gap-2 mb-2"
                                style="font-size:.80rem;">
                                <span class="rounded-circle flex-shrink-0"
                                      style="width:6px;height:6px;
                                             background:{{ $a['color'] }};
                                             display:inline-block;">
                                </span>
                                <span class="text-body-secondary">{{ $f }}</span>
                            </li>
                            @endforeach
                        </ul>

                        {{-- CTA --}}
                        <a href="{{ route('register') }}"
                           class="btn btn-sm fw-semibold rounded-3 w-100"
                           style="background:{{ $a['color'] }}1a;
                                  color:{{ $a['color'] }};
                                  border:1px solid {{ $a['color'] }}33;
                                  font-size:.80rem;">
                            Rejoindre
                        </a>

                    </div>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</section>

<style>
.cs-role-card {
    transition: transform .18s ease, box-shadow .18s ease;
}
.cs-role-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,.08);
}
</style>