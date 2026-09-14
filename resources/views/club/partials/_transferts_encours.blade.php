{{-- club/partials/_transferts_encours.blade.php --}}
<div class="cs-card">
    <div class="cs-card__header">
        <h2 class="cs-card__title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                <polyline points="17 1 21 5 17 9"/>
                <path d="M3 11V9a4 4 0 0 1 4-4h14"/>
                <polyline points="7 23 3 19 7 15"/>
                <path d="M21 13v2a4 4 0 0 1-4 4H3"/>
            </svg>
            Transferts en cours
        </h2>
        <a href="{{ route('club.transferts.index') }}" class="cs-card__action">Voir tous</a>
    </div>

    <div class="cs-card__body cs-card__body--flush">
        @forelse($transfertsEnCours as $transfert)
        @php
            $statutColors = [
                'en_attente'  => 'warning',
                'en_cours'    => 'primary',
                'accepte'     => 'success',
                'refuse'      => 'danger',
                'annule'      => 'neutral',
            ];
            $statutLabels = [
                'en_attente' => 'En attente',
                'en_cours'   => 'En cours',
                'accepte'    => 'Accepté',
                'refuse'     => 'Refusé',
                'annule'     => 'Annulé',
            ];
            $color = $statutColors[$transfert->statut] ?? 'neutral';
            $label = $statutLabels[$transfert->statut] ?? $transfert->statut;
        @endphp
        <div class="cs-transfert-row">
            {{-- Joueur --}}
            <div class="cs-transfert-row__joueur">
                <div class="cs-transfert-row__avatar">
                    {{ strtoupper(substr($transfert->joueur->user->prenom ?? '?', 0, 1)) }}
                </div>
                <div>
                    <span class="cs-transfert-row__name">{{ $transfert->joueur->nomComplet() }}</span>
                    <span class="cs-transfert-row__poste">{{ $transfert->joueur->poste ?? '—' }}</span>
                </div>
            </div>

            {{-- Direction --}}
            <div class="cs-transfert-row__direction">
                @if($transfert->club_source_id === $club->id)
                    <span class="cs-transfert-row__dir cs-transfert-row__dir--sortant" title="Départ">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                            <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                        </svg>
                        Départ
                    </span>
                @else
                    <span class="cs-transfert-row__dir cs-transfert-row__dir--arrivant" title="Arrivée">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                            <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                        </svg>
                        Arrivée
                    </span>
                @endif
            </div>

            {{-- Club opposé --}}
            <div class="cs-transfert-row__club">
                @if($transfert->club_source_id === $club->id)
                    <span>→ {{ $transfert->clubDestinataire->nom ?? '—' }}</span>
                @else
                    <span>← {{ $transfert->clubSource->nom ?? '—' }}</span>
                @endif
            </div>

            {{-- Statut --}}
            <div class="cs-transfert-row__statut">
                <span class="cs-tag cs-tag--{{ $color }}">{{ $label }}</span>
            </div>

            {{-- Action --}}
            <a href="{{ route('club.transferts.show', $transfert) }}" class="cs-transfert-row__link" aria-label="Voir le transfert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </a>
        </div>
        @empty
        <div class="cs-empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="40" height="40">
                <polyline points="17 1 21 5 17 9"/>
                <path d="M3 11V9a4 4 0 0 1 4-4h14"/>
                <polyline points="7 23 3 19 7 15"/>
                <path d="M21 13v2a4 4 0 0 1-4 4H3"/>
            </svg>
            <p>Aucun transfert en cours.</p>
        </div>
        @endforelse
    </div>
</div>