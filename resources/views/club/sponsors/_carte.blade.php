@php
    $expire  = $sponsor->estExpire();
    $jours   = $sponsor->joursRestants();
    $badge   = $expire
        ? ['class' => 'bg-danger',              'label' => 'Expiré']
        : ($jours <= 30
            ? ['class' => 'bg-warning text-dark', 'label' => 'Expire bientôt']
            : ['class' => 'bg-success',           'label' => 'Actif']);
    $visiBadge = match($sponsor->type_visibilite) {
        'logo'    => ['class' => 'bg-primary',   'label' => 'Logo maillot',      'icon' => 'bi-shield'],
        'banniere'=> ['class' => 'bg-info',      'label' => 'Pancarte/Bannière', 'icon' => 'bi-megaphone'],
        default   => ['class' => 'bg-secondary', 'label' => 'Tous supports',     'icon' => 'bi-stars'],
    };
@endphp

<div class="col-md-6 col-lg-4" id="sponsor-{{ $sponsor->id }}">
    <div class="card border-0 shadow-sm rounded-4 h-100">
        <div class="card-body p-4">

            {{-- Logo + Nom --}}
            <div class="d-flex align-items-center gap-3 mb-3">
                @if($sponsor->logo)
                    <img src="{{ asset('storage/' . $sponsor->logo) }}"
                         class="rounded-3 border object-fit-cover flex-shrink-0"
                         style="width:52px;height:52px;" alt="{{ $sponsor->nom }}">
                @else
                    <div class="rounded-3 bg-light d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:52px;height:52px;">
                        <i class="bi bi-building fs-4 text-muted"></i>
                    </div>
                @endif
                <div class="overflow-hidden">
                    <div class="fw-bold text-truncate">{{ $sponsor->nom }}</div>
                    @if($sponsor->site_web)
                        <a href="{{ $sponsor->site_web }}" target="_blank"
                           class="text-muted small text-decoration-none text-truncate d-block">
                            <i class="bi bi-link-45deg me-1"></i>{{ $sponsor->site_web }}
                        </a>
                    @endif
                </div>
            </div>

            {{-- Badges --}}
            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="badge {{ $badge['class'] }} rounded-pill">
                    {{ $badge['label'] }}
                </span>
                <span class="badge {{ $visiBadge['class'] }} rounded-pill">
                    <i class="bi {{ $visiBadge['icon'] }} me-1"></i>
                    {{ $visiBadge['label'] }}
                </span>
            </div>

            {{-- Infos --}}
            <ul class="list-unstyled small text-muted mb-3">
                <li class="mb-1">
                    <i class="bi bi-calendar me-2"></i>
                    {{ $sponsor->debut_partenariat->format('d/m/Y') }}
                    → {{ $sponsor->fin_partenariat->format('d/m/Y') }}
                    @if(!$expire && $jours <= 30)
                        <span class="badge bg-warning text-dark ms-1 rounded-pill">{{ $jours }}j</span>
                    @endif
                </li>
                <li class="mb-1">
                    <i class="bi bi-cash me-2"></i>{{ $sponsor->montantAffiche() }}
                </li>
                @if($sponsor->email_contact)
                    <li class="mb-1">
                        <i class="bi bi-envelope me-2"></i>{{ $sponsor->email_contact }}
                    </li>
                @endif
                @if($sponsor->telephone_contact)
                    <li>
                        <i class="bi bi-telephone me-2"></i>{{ $sponsor->telephone_contact }}
                    </li>
                @endif
            </ul>

            {{-- Actions --}}
            <div class="d-flex gap-2 pt-2 border-top">
                <a href="{{ route('club.sponsors.show', $sponsor) }}"
                   class="btn btn-sm btn-outline-secondary rounded-2 flex-fill">
                    <i class="bi bi-eye me-1"></i> Voir
                </a>
                <a href="{{ route('club.sponsors.edit', $sponsor) }}"
                   class="btn btn-sm btn-outline-warning rounded-2 flex-fill">
                    <i class="bi bi-pencil me-1"></i> Modifier
                </a>
                <button type="button"
                        class="btn btn-sm btn-outline-danger rounded-2"
                        data-action="supprimer"
                        data-id="{{ $sponsor->id }}"
                        data-nom="{{ $sponsor->nom }}">
                    <i class="bi bi-trash"></i>
                </button>
            </div>

        </div>
    </div>
</div>