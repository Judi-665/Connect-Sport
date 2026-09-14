{{-- club/partials/_agenda_prochain.blade.php --}}
<div class="cs-card">
    <div class="cs-card__header">
        <h2 class="cs-card__title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            Prochains événements
        </h2>
        <a href="{{ route('club.agenda.create') }}" class="cs-card__action cs-card__action--add">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Ajouter
        </a>
    </div>

    <div class="cs-card__body">
        @forelse($evenementsProchains as $evenement)
        @php
            $date = \Carbon\Carbon::parse($evenement->date_debut);
            $typeColors = [
                'match'        => 'danger',
                'entrainement' => 'primary',
                'reunion'      => 'info',
                'tournoi'      => 'warning',
            ];
            $color = $typeColors[$evenement->type ?? ''] ?? 'neutral';
        @endphp
        <div class="cs-event-row">
            <div class="cs-event-row__date">
                <span class="cs-event-row__day">{{ $date->format('d') }}</span>
                <span class="cs-event-row__month">{{ strtoupper($date->format('M')) }}</span>
            </div>
            <div class="cs-event-row__body">
                <span class="cs-event-row__title">{{ $evenement->titre }}</span>
                <div class="cs-event-row__meta">
                    <span class="cs-tag cs-tag--{{ $color }} cs-tag--xs">{{ ucfirst($evenement->type ?? 'événement') }}</span>
                    @if($evenement->lieu)
                        <span class="cs-event-row__lieu">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            {{ $evenement->lieu }}
                        </span>
                    @endif
                    <span class="cs-event-row__heure">{{ $date->format('H\hi') }}</span>
                </div>
            </div>
            <a href="{{ route('club.agenda.edit', $evenement) }}" class="cs-event-row__edit" aria-label="Modifier l'événement">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
            </a>
        </div>
        @empty
        <div class="cs-empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="40" height="40">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <p>Aucun événement à venir.</p>
            <a href="{{ route('club.agenda.create') }}" class="cs-btn cs-btn--sm cs-btn--primary">Créer un événement</a>
        </div>
        @endforelse
    </div>

    @if($evenementsProchains->isNotEmpty())
    <div class="cs-card__footer">
        <a href="{{ route('club.agenda.index') }}" class="cs-card__footer-link">Voir tout l'agenda</a>
    </div>
    @endif
</div>