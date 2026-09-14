<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Licences — {{ $club->nom }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }

        .footer {
            position: fixed; bottom: 0; left: 0; right: 0;
            border-top: 1px solid #ccc;
            padding: 6px 30px;
            font-size: 9px; color: #888;
            display: flex; justify-content: space-between;
        }

        .header {
            padding: 20px 30px 16px;
            border-bottom: 2px solid #1A56A0;
            margin-bottom: 20px;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .logo-img {
            width: 48px; height: 48px;
            border-radius: 50%;
            object-fit: cover;
        }
        .brand {
            font-size: 22px;
            font-weight: bold;
            color: #1A56A0;
            letter-spacing: 2px;
        }
        .brand span { color: #F97316; }
        .header-right {
            text-align: right;
            font-size: 10px;
            color: #555;
            line-height: 1.6;
        }
        .club-nom {
            font-size: 14px;
            font-weight: bold;
            color: #1A56A0;
            margin-top: 12px;
        }
        .club-sub {
            font-size: 10px;
            color: #666;
            margin-top: 2px;
        }

        /* ── Stats ── */
        .stats {
            display: flex;
            gap: 10px;
            padding: 0 30px;
            margin-bottom: 22px;
        }
        .stat {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            text-align: center;
        }
        .stat-val {
            font-size: 20px;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 4px;
        }
        .stat-lbl {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #888;
        }

        /* ── Section ── */
        .section {
            margin: 0 30px 8px;
            padding: 5px 12px;
            background: #f1f5f9;
            border-left: 3px solid #1A56A0;
            font-size: 10px;
            font-weight: bold;
            color: #1A56A0;
        }

        /* ── Tableau ── */
        .tbl { padding: 0 30px; margin-bottom: 20px; }

        table { width: 100%; border-collapse: collapse; }

        thead th {
            background: #1A56A0;
            color: #fff;
            padding: 8px 9px;
            font-size: 9px;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        tbody td {
            padding: 8px 9px;
            border-bottom: 1px solid #eee;
            font-size: 10px;
            vertical-align: middle;
        }
        tbody tr:nth-child(even) td { background: #f9f9f9; }

        /* ── Badges ── */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 8.5px;
            font-weight: bold;
        }
        .b-active { background: #d1fae5; color: #065f46; }
        .b-warn   { background: #fef3c7; color: #92400e; }
        .b-danger { background: #fee2e2; color: #991b1b; }

        /* ── Note finale ── */
        .note {
            margin: 10px 30px 40px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            font-size: 9px;
            color: #999;
            text-align: center;
        }

        /* ── Vide ── */
        .empty {
            padding: 40px 30px;
            text-align: center;
            color: #999;
            font-size: 12px;
        }
    </style>
</head>
<body>

{{-- Pied de page fixe --}}
<div class="footer">
    <span><strong>CONNECT<span style="color:#F97316;">SPORT</span></strong> — Document confidentiel</span>
    <span>{{ $club->nom }} · {{ now()->format('d/m/Y') }}</span>
</div>

{{-- En-tête --}}
<div class="header">
    <div class="header-top">
        <div class="header-left">
            <img src="{{ public_path('images/logo.jpg') }}"
                 class="logo-img" alt="ConnectSport">
            <div class="brand">CONNECT<span>SPORT</span></div>
        </div>
        <div class="header-right">
            <div>Liste officielle des licences</div>
            <div>{{ now()->format('d/m/Y') }}</div>
        </div>
    </div>
    <div class="club-nom">{{ $club->nom }}</div>
    <div class="club-sub">
        {{ $club->sport->nom ?? 'Multi-sport' }}
        @if($club->ville) · {{ $club->ville }} @endif
        @if($club->pays)  · {{ $club->pays }}  @endif
    </div>
</div>

{{-- Stats --}}
@php
    $total   = $licences->count();
    $actives = $licences->filter(fn($l) => !$l->estExpiree() && $l->joursRestants() > 30)->count();
    $bientot = $licences->filter(fn($l) => !$l->estExpiree() && $l->joursRestants() <= 30)->count();
    $expir   = $licences->filter(fn($l) => $l->estExpiree())->count();
@endphp

<div class="stats">
    <div class="stat">
        <div class="stat-val" style="color:#1A56A0;">{{ $total }}</div>
        <div class="stat-lbl">Total</div>
    </div>
    <div class="stat">
        <div class="stat-val" style="color:#10b981;">{{ $actives }}</div>
        <div class="stat-lbl">Actives</div>
    </div>
    <div class="stat">
        <div class="stat-val" style="color:#F97316;">{{ $bientot }}</div>
        <div class="stat-lbl">Expirent bientôt</div>
    </div>
    <div class="stat">
        <div class="stat-val" style="color:#dc3545;">{{ $expir }}</div>
        <div class="stat-lbl">Expirées</div>
    </div>
</div>

{{-- Tableau groupé par catégorie --}}
@foreach(['senior' => 'Seniors', 'cadet' => 'Cadets', 'junior' => 'Juniors', 'veteran' => 'Vétérans / Loisir'] as $cat => $label)
    @php $groupe = $licences->where('categorie', $cat); @endphp
    @if($groupe->isNotEmpty())

        <div class="section">{{ $label }} — {{ $groupe->count() }} licence(s)</div>

        <div class="tbl">
            <table>
                <thead>
                    <tr>
                        <th style="width:26%;">Joueur</th>
                        <th style="width:14%;">N° Licence</th>
                        <th style="width:11%;">Poste</th>
                        <th style="width:12%;">Début</th>
                        <th style="width:12%;">Expiration</th>
                        <th style="width:9%;">Restant</th>
                        <th style="width:10%;">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($groupe->sortBy('date_expiration') as $licence)
                    @php
                        $exp   = $licence->estExpiree();
                        $jours = $licence->joursRestants();
                        $bc    = $exp ? 'b-danger' : ($jours <= 30 ? 'b-warn' : 'b-active');
                        $bl    = $exp ? 'Expirée'  : ($jours <= 30 ? 'Bientôt' : 'Active');
                    @endphp
                    <tr>
                        <td><strong>{{ $licence->joueur->nomComplet() }}</strong></td>
                        <td>{{ $licence->numero_licence ?? '—' }}</td>
                        <td>{{ $licence->joueur->poste ?? '—' }}</td>
                        <td>{{ $licence->date_debut->format('d/m/Y') }}</td>
                        <td>{{ $licence->date_expiration->format('d/m/Y') }}</td>
                        <td>
                            @if($exp)
                                <span style="color:#dc3545;font-weight:bold;">—</span>
                            @else
                                <span style="color:{{ $jours <= 30 ? '#F97316' : '#10b981' }};font-weight:bold;">
                                    {{ $jours }}j
                                </span>
                            @endif
                        </td>
                        <td><span class="badge {{ $bc }}">{{ $bl }}</span></td>
                    </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>

    @endif
@endforeach

{{-- Aucune licence --}}
@if($licences->isEmpty())
    <div class="empty">
        Aucune licence enregistrée pour ce club.
    </div>
@endif

{{-- Note finale --}}
<div class="note">
    Document généré automatiquement par ConnectSport ·
    Strictement confidentiel · usage interne réservé au club <strong>{{ $club->nom }}</strong>
</div>

</body>
</html>