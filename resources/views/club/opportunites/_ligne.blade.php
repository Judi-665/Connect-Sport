@php
    $expire  = $opportunite->estExpiree();
    $typeBadge = match($opportunite->type) {
        'recrutement' => ['class' => 'bg-primary',          'label' => 'Recrutement'],
        'selection'   => ['class' => 'bg-info',             'label' => 'Sélection'],
        'bourse'      => ['class' => 'bg-success',          'label' => 'Bourse'],
        'stage'       => ['class' => 'bg-warning text-dark','label' => 'Stage'],
        default       => ['class' => 'bg-secondary',        'label' => ucfirst($opportunite->type)],
    };
    $statutBadge = $expire
        ? ['class' => 'bg-danger',  'label' => 'Expirée']
        : ($opportunite->active
            ? ['class' => 'bg-success', 'label' => 'Active']
            : ['class' => 'bg-secondary', 'label' => 'Inactive']);
@endphp

<tr id="opp-{{ $opportunite->id }}">
    <td class="ps-4 py-3">
        <div class="d-flex align-items-center gap-2">
            @if($opportunite->mise_en_avant)
                <i class="bi bi-star-fill text-warning" title="Mise en avant"></i>
            @endif
            <div>
                <div class="fw-semibold small">{{ $opportunite->titre }}</div>
                <div class="text-muted" style="font-size:11px;">
                    {{ $opportunite->sport_cible }}
                    @if($opportunite->poste_cible)
                        · {{ $opportunite->poste_cible }}
                    @endif
                </div>
            </div>
        </div>
    </td>
    <td>
        <span class="badge {{ $typeBadge['class'] }} rounded-pill" style="font-size:10px;">
            {{ $typeBadge['label'] }}
        </span>
    </td>
    <td class="small text-muted">
        <i class="bi bi-geo-alt me-1"></i>{{ $opportunite->lieu }}, {{ $opportunite->pays }}
    </td>
    <td class="small">
        @if($opportunite->date_limite)
            <span class="{{ $expire ? 'text-danger fw-semibold' : '' }}">
                {{ $opportunite->date_limite->format('d/m/Y') }}
            </span>
        @else
            <span class="text-muted">—</span>
        @endif
    </td>
    <td class="small text-center">
        {{ $opportunite->places_disponibles ?? '—' }}
    </td>
    <td>
        <span class="badge {{ $statutBadge['class'] }} rounded-pill" style="font-size:10px;">
            {{ $statutBadge['label'] }}
        </span>
    </td>
    <td class="text-end pe-4">
        <div class="d-flex gap-1 justify-content-end">
            <a href="{{ route('club.opportunites.show', $opportunite) }}"
               class="btn btn-sm btn-outline-secondary rounded-2" title="Voir">
                <i class="bi bi-eye"></i>
            </a>
            <a href="{{ route('club.opportunites.edit', $opportunite) }}"
               class="btn btn-sm btn-outline-warning rounded-2" title="Modifier">
                <i class="bi bi-pencil"></i>
            </a>
            <button type="button"
                    class="btn btn-sm btn-outline-danger rounded-2"
                    data-action="supprimer"
                    data-id="{{ $opportunite->id }}"
                    data-titre="{{ $opportunite->titre }}"
                    title="Supprimer">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </td>
</tr>