<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; }
        body {
            font-family: Helvetica, Arial, sans-serif;
            color: #1a1a1a;
            font-size: 11px;
            margin: 0;
        }

        /* ── Structure 2 colonnes ── */
        .layout { width: 100%; border-collapse: collapse; }
        .sidebar {
            width: 34%;
            background: #0D2E5C;
            color: #fff;
            vertical-align: top;
            padding: 28px 20px;
        }
        .main {
            width: 66%;
            vertical-align: top;
            padding: 30px 28px;
        }

        /* ── Sidebar ── */
        .avatar {
            width: 74px; height: 74px;
            background: #F97316;
            border-radius: 50%;
            text-align: center;
            line-height: 74px;
            font-size: 26px;
            font-weight: bold;
            color: #fff;
            margin: 0 auto 16px auto;
        }
        .sidebar h1 {
            font-size: 17px;
            text-align: center;
            margin: 0 0 2px 0;
            letter-spacing: .5px;
        }
        .sidebar .poste {
            text-align: center;
            color: #F97316;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 1.2px;
            margin-bottom: 24px;
        }
        .side-title {
            color: #5B9BD5;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 1.2px;
            border-bottom: 1px solid rgba(255,255,255,.15);
            padding-bottom: 5px;
            margin: 20px 0 10px 0;
        }
        .side-row { padding: 3px 0; font-size: 10px; color: #cfd8e3; }
        .side-row strong { color: #fff; display: block; font-size: 10.5px; }

        .stat-box {
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 4px;
            padding: 8px 6px;
            text-align: center;
            margin-bottom: 8px;
        }
        .stat-box .val { display: block; font-size: 17px; font-weight: bold; color: #F97316; }
        .stat-box .lbl { font-size: 8px; color: #9fb0c3; text-transform: uppercase; letter-spacing: .5px; }

        /* ── Main content ── */
        .section-title {
            color: #0D2E5C;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
            font-weight: bold;
            border-bottom: 2px solid #F97316;
            padding-bottom: 5px;
            margin: 0 0 12px 0;
        }
        .section { margin-bottom: 22px; }
        .bio { font-size: 10.5px; line-height: 1.6; color: #333; margin: 0; }

        .parcours-item {
            padding: 0 0 12px 14px;
            border-left: 2px solid #eee;
            margin-left: 4px;
            position: relative;
            margin-bottom: 4px;
        }
        .parcours-item .dot {
            width: 8px; height: 8px;
            background: #F97316;
            border-radius: 50%;
            position: absolute;
            left: -5px; top: 2px;
        }
        .parcours-item .club { font-weight: bold; font-size: 12px; color: #0D2E5C; }
        .parcours-item .poste { font-size: 10px; color: #666; }
        .parcours-item .periode {
            font-size: 9px; color: #F97316; text-transform: uppercase;
            letter-spacing: .5px; margin-top: 2px;
        }

        .footer {
            text-align: center; color: #aaa; font-size: 8px;
            padding: 10px; border-top: 1px solid #eee;
        }
    </style>
</head>
<body>

    <table class="layout">
        <tr>
            {{-- ══════════ SIDEBAR ══════════ --}}
            <td class="sidebar">

                <div class="avatar">
                    {{ strtoupper(substr($joueur->prenom ?? $joueur->user->name ?? 'J', 0, 1)) }}{{ strtoupper(substr($joueur->nom ?? '', 0, 1)) }}
                </div>
                <h1>{{ $joueur->nomComplet() }}</h1>
                <div class="poste">{{ $joueur->poste ?? 'Poste non renseigné' }}</div>

                <div class="side-title">Profil</div>
                <div class="side-row"><strong>{{ $joueur->age() ?? '—' }} ans</strong>Âge</div>
                <div class="side-row"><strong>{{ $joueur->nationalite ?? '—' }}</strong>Nationalité</div>
                <div class="side-row"><strong>{{ $joueur->ville ?? '—' }}, {{ $joueur->pays }}</strong>Localisation</div>
                <div class="side-row"><strong>{{ $joueur->club->nom ?? 'Sans club' }}</strong>Club actuel</div>

                <div class="side-title">Statistiques</div>
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="width:50%; padding-right:4px;">
                            <div class="stat-box"><span class="val">{{ $stats->matchs ?? 0 }}</span><span class="lbl">Matchs</span></div>
                        </td>
                        <td style="width:50%; padding-left:4px;">
                            <div class="stat-box"><span class="val">{{ $stats->buts ?? 0 }}</span><span class="lbl">Buts</span></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-right:4px;">
                            <div class="stat-box"><span class="val">{{ $stats->passes ?? 0 }}</span><span class="lbl">Passes</span></div>
                        </td>
                        <td style="padding-left:4px;">
                            <div class="stat-box"><span class="val">{{ $stats->note_moyenne ? number_format($stats->note_moyenne, 1) : '—' }}</span><span class="lbl">Note /10</span></div>
                        </td>
                    </tr>
                </table>
                <div class="side-row" style="text-align:center; margin-top:4px;">
                    {{ $stats->saisons ?? 0 }} saison(s) enregistrée(s)
                </div>

            </td>

            {{-- ══════════ CONTENU PRINCIPAL ══════════ --}}
            <td class="main">

                @if($joueur->bio)
                <div class="section">
                    <div class="section-title">À propos</div>
                    <p class="bio">{{ $joueur->bio }}</p>
                </div>
                @endif

                <div class="section">
                    <div class="section-title">Parcours</div>
                    @forelse($carrieres as $c)
                    <div class="parcours-item">
                        <div class="dot"></div>
                        <div class="club">{{ $c->club->nom }}</div>
                        <div class="poste">{{ $c->poste ?? 'Poste non renseigné' }}</div>
                        <div class="periode">
                            {{ $c->date_debut->format('m/Y') }} —
                            {{ $c->estActuelle() ? "Aujourd'hui" : $c->date_fin->format('m/Y') }}
                        </div>
                    </div>
                    @empty
                    <p style="color:#999; font-size:10.5px;">Aucun historique renseigné.</p>
                    @endforelse
                </div>

            </td>
        </tr>
    </table>

    <div class="footer">
        Généré via Connect Sport — {{ now()->format('d/m/Y') }}
    </div>

</body>
</html>