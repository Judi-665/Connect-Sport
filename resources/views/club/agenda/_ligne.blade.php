@php
    $typeIcon = match($evenement->type) {
        'match'        => 'bi-trophy',
        'entrainement' => 'bi-people',
        default        => 'bi-calendar-event',
    };
    $typeBadge = match($evenement->type) {
        'match'        => 'bg-warning text-dark',
        'entrainement' => 'bg-success',
        default        => 'bg-info',
    };
    $visiBadge = match($evenement->visibilite) {
        'public' => ['class' => 'bg-warning', 'label' => 'Public'],
        'club'   => ['class' => 'bg-secondary', 'label' => 'Club'],
        default  => ['class' => 'bg-dark', 'label' => 'Équipe'],
    };
@endphp

<div id="ev-{{ $evenement->id }}"
     class="d-flex align-items-start gap-3 px-4 py-3 border-bottom">

    {{-- Date bloc --}}
    <div class="text-center flex-shrink-0 rounded-3 py-2 px-2"
         style="width:52px;background:#f1f5f9;">
        <div class="fw-bold fs-5 lh-1" style="color:#F97316;">
            {{ $evenement->debut_at->format('d') }}
        </div>
        <div class="text-muted" style="font-size:10px;text-transform:uppercase;">
            {{ $evenement->debut_at->format('M') }}
        </div>
        <div class="text-muted" style="font-size:9px;">
            {{ $evenement->debut_at->format('Y') }}
        </div>
    </div>

    {{-- Infos --}}
    <div class="flex-grow-1">
        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
            <span class="badge {{ $typeBadge }} rounded-pill" style="font-size:10px;">
                <i class="bi {{ $typeIcon }} me-1"></i>{{ ucfirst($evenement->type) }}
            </span>
            <span class="badge {{ $visiBadge['class'] }} rounded-pill" style="font-size:10px;">
                {{ $visiBadge['label'] }}
            </span>
            @if($evenement->domicile_exterieur)
                <span class="badge bg-light text-dark rounded-pill" style="font-size:10px;">
                    {{ ucfirst($evenement->domicile_exterieur) }}
                </span>
            @endif
        </div>

        <div class="fw-semibold small mb-1">{{ $evenement->titre }}</div>

        <div class="d-flex flex-wrap gap-3" style="font-size:11px;color:#718096;">
            <span><i class="bi bi-clock me-1"></i>{{ $evenement->debut_at->format('H:i') }}</span>
            @if($evenement->fin_at)
                <span>→ {{ $evenement->fin_at->format('H:i') }}</span>
            @endif
            @if($evenement->lieu)
                <span><i class="bi bi-geo-alt me-1"></i>{{ $evenement->lieu }}</span>
            @endif
            @if($evenement->adversaire_nom)
                <span><i class="bi bi-shield me-1"></i>vs {{ $evenement->adversaire_nom }}</span>
            @endif
        </div>
    </div>

    {{-- Actions --}}
        <div class="d-flex gap-1 flex-shrink-0">
            <a href="{{ route('club.agenda.show', $evenement) }}"
            class="btn btn-sm btn-outline-secondary rounded-2" title="Voir">
                <i class="bi bi-eye"></i>
            </a>
            <a href="{{ route('club.agenda.edit', $evenement) }}"
            class="btn btn-sm btn-outline-warning rounded-2" title="Modifier">
                <i class="bi bi-pencil"></i>
            </a>
            <button type="button"
                    class="btn btn-sm btn-outline-danger rounded-2"
                    data-action="supprimer"
                    data-id="{{ $evenement->id }}"
                    data-titre="{{ $evenement->titre }}"
                    title="Supprimer">
                <i class="bi bi-trash"></i>
            </button>
        </div>
</div>