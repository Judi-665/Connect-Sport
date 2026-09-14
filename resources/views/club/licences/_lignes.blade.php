@php
    $expiree       = $licence->estExpiree();
    $joursRestants = $licence->joursRestants();
    $badge = $expiree
        ? ['class' => 'bg-danger',              'label' => 'Expirée']
        : ($joursRestants <= 30
            ? ['class' => 'bg-warning text-dark', 'label' => 'Expire bientôt']
            : ['class' => 'bg-success',           'label' => 'Active']);
@endphp

<tr id="ligne-{{ $licence->id }}">
    <td class="ps-4 py-3">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                 style="width:38px;height:38px;font-size:13px;">
                {{ strtoupper(substr($licence->joueur->user->prenom ?? '?', 0, 1)) }}{{ strtoupper(substr($licence->joueur->user->name ?? '', 0, 1)) }}
            </div>
            <div>
                <div class="fw-semibold small">{{ $licence->joueur->nomComplet() }}</div>
                <div class="text-muted" style="font-size:11px;">{{ $licence->joueur->poste ?? '—' }}</div>
            </div>
        </div>
    </td>
    <td class="small">{{ $licence->numero_licence ?? '—' }}</td>
    <td><span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">{{ ucfirst($licence->categorie) }}</span></td>
    <td class="small">{{ $licence->date_debut->format('d/m/Y') }}</td>
    <td class="small">
        {{ $licence->date_expiration->format('d/m/Y') }}
        @if(!$expiree && $joursRestants <= 30)
            <span class="badge bg-warning text-dark ms-1 rounded-pill">{{ $joursRestants }}j</span>
        @endif
    </td>
    <td><span class="badge {{ $badge['class'] }} rounded-pill">{{ $badge['label'] }}</span></td>
    <td class="text-end pe-4">
        <div class="d-flex gap-1 justify-content-end">
            <a href="{{ route('club.licences.show', $licence) }}"
               class="btn btn-sm btn-outline-secondary rounded-2" title="Voir le détail">
                <i class="bi bi-eye"></i>
            </a>
            <a href="{{ route('club.licences.download', $licence) }}"
               class="btn btn-sm btn-outline-info rounded-2" title="Télécharger PDF">
                <i class="bi bi-download"></i>
            </a>
            <button type="button"
                    class="btn btn-sm btn-outline-danger rounded-2"
                    data-action="supprimer"
                    data-id="{{ $licence->id }}"
                    data-nom="{{ $licence->joueur->nomComplet() }}"
                    title="Supprimer">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </td>
</tr>