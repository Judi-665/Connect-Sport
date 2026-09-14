{{-- club/partials/_stats_cards.blade.php --}}
<div class="cs-stats">

    {{-- Joueurs --}}
    <div class="cs-stat-card cs-stat-card--joueurs">
        <div class="cs-stat-card__body">
            <div class="cs-stat-card__info">
                <span class="cs-stat-card__label">Joueurs</span>
                <span class="cs-stat-card__value">{{ $stats['joueurs_total'] ?? 0 }}</span>
                @if(isset($stats['joueurs_actifs']))
                    <span class="cs-stat-card__sub">{{ $stats['joueurs_actifs'] }} actifs</span>
                @endif
            </div>
            <div class="cs-stat-card__icon-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
        </div>
        <a href="{{ route('club.joueurs.index') }}" class="cs-stat-card__footer">
            Voir tous les joueurs
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
            </svg>
        </a>
    </div>

    {{-- Équipes --}}
    <div class="cs-stat-card cs-stat-card--equipes">
        <div class="cs-stat-card__body">
            <div class="cs-stat-card__info">
                <span class="cs-stat-card__label">Équipes</span>
                <span class="cs-stat-card__value">{{ $stats['equipes_total'] ?? 0 }}</span>
                @if(isset($stats['categories']))
                    <span class="cs-stat-card__sub">{{ $stats['categories'] }} catégories</span>
                @endif
            </div>
            <div class="cs-stat-card__icon-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                    <path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
                </svg>
            </div>
        </div>
        <a href="{{ route('club.equipes.index') }}" class="cs-stat-card__footer">
            Gérer les équipes
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
            </svg>
        </a>
    </div>

    {{-- Licences actives --}}
    <div class="cs-stat-card cs-stat-card--licences">
        <div class="cs-stat-card__body">
            <div class="cs-stat-card__info">
                <span class="cs-stat-card__label">Licences actives</span>
                <span class="cs-stat-card__value">{{ $stats['licences_actives'] ?? 0 }}</span>
                @if(isset($stats['licences_expirent']))
                    <span class="cs-stat-card__sub cs-stat-card__sub--warn">
                        {{ $stats['licences_expirent'] }} expirent bientôt
                    </span>
                @endif
            </div>
            <div class="cs-stat-card__icon-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
            </div>
        </div>
        <a href="{{ route('club.licences.index') }}" class="cs-stat-card__footer">
            Voir les licences
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
            </svg>
        </a>
    </div>

    {{-- Événements à venir --}}
    <div class="cs-stat-card cs-stat-card--agenda">
        <div class="cs-stat-card__body">
            <div class="cs-stat-card__info">
                <span class="cs-stat-card__label">Événements à venir</span>
                <span class="cs-stat-card__value">{{ $stats['evenements_a_venir'] ?? 0 }}</span>
                @if(isset($stats['prochain_evenement']))
                    <span class="cs-stat-card__sub">
                        Prochain : {{ \Carbon\Carbon::parse($stats['prochain_evenement'])->format('d/m') }}
                    </span>
                @endif
            </div>
            <div class="cs-stat-card__icon-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
        </div>
        <a href="{{ route('club.agenda.index') }}" class="cs-stat-card__footer">
            Voir l'agenda
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
            </svg>
        </a>
    </div>

</div>