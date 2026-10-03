@php
    $visiBadge = match($media->visibilite) {
        'public'  => ['class' => 'bg-primary',            'label' => 'Public'],
        'club'    => ['class' => 'bg-secondary',          'label' => 'Club'],
        default   => ['class' => 'bg-warning text-dark',  'label' => 'Premium'],
    };
@endphp

<div class="col-md-4 col-lg-3" id="media-{{ $media->id }}">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">

        {{-- Miniature --}}
        <div class="position-relative" style="height:180px;background:#f1f5f9;">
            @if($media->type === 'photo')
                <img src="{{ $media->url() }}"
                     class="w-100 h-100 object-fit-cover"
                     alt="{{ $media->titre ?? 'Photo' }}">
            @else
                <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                    @if($media->miniature)
                        <img src="{{ $media->urlMiniature() }}"
                             class="w-100 h-100 object-fit-cover"
                             alt="{{ $media->titre }}">
                    @else
                        <i class="bi bi-camera-video text-muted" style="font-size:3rem;"></i>
                    @endif
                    <div class="position-absolute top-50 start-50 translate-middle">
                        <div class="rounded-circle bg-dark bg-opacity-50 d-flex align-items-center justify-content-center"
                             style="width:48px;height:48px;">
                            <i class="bi bi-play-fill text-white fs-4"></i>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Badges overlay --}}
            <div class="position-absolute top-0 start-0 p-2 d-flex gap-1">
                <span class="badge {{ $visiBadge['class'] }} rounded-pill"
                      data-badge-visi style="font-size:10px;">
                    {{ $visiBadge['label'] }}
                </span>
                @if($media->payant)
                    <span class="badge bg-dark rounded-pill" style="font-size:10px;">
                        <i class="bi bi-star-fill me-1"></i>Premium
                    </span>
                @endif
            </div>

            {{-- Vues --}}
            <div class="position-absolute bottom-0 end-0 p-2">
                <span class="badge bg-dark bg-opacity-75 rounded-pill" style="font-size:10px;">
                    <i class="bi bi-eye me-1"></i>{{ $media->vues }}
                </span>
            </div>
        </div>

        {{-- Infos --}}
        <div class="card-body p-3">
            <div class="fw-semibold small text-truncate mb-1">
                {{ $media->titre ?? 'Sans titre' }}
            </div>
            <div class="d-flex flex-wrap gap-1 mb-2" style="font-size:10px;color:#718096;">
                @if($media->joueur)
                    <span><i class="bi bi-person me-1"></i>{{ $media->joueur->user->prenom ?? '' }}</span>
                @endif
                @if($media->taille_ko)
                    <span><i class="bi bi-hdd me-1"></i>{{ round($media->taille_ko / 1024, 1) }} Mo</span>
                @endif
                @if($media->dureeFormatee())
                    <span><i class="bi bi-clock me-1"></i>{{ $media->dureeFormatee() }}</span>
                @endif
            </div>

            <div class="d-flex gap-3 small text-muted mb-3" aria-label="Réactions reçues">
                <span><i class="bi bi-hand-thumbs-up me-1"></i>J’aime {{ $media->likes_count ?? 0 }}</span>
                <span><i class="bi bi-heart me-1"></i>J’adore {{ $media->loves_count ?? 0 }}</span>
            </div>

            {{-- Actions --}}
            <div class="d-flex gap-1">
                <a href="{{ $media->url() }}" target="_blank"
                   class="btn btn-sm btn-outline-secondary rounded-2 flex-fill">
                    <i class="bi bi-eye"></i>
                </a>
                <button type="button"
                        class="btn btn-sm btn-outline-primary rounded-2 flex-fill"
                        data-action="visibilite"
                        data-id="{{ $media->id }}"
                        data-titre="{{ $media->titre ?? 'Sans titre' }}"
                        title="Changer la visibilité">
                    <i class="bi bi-shield"></i>
                </button>
                <button type="button"
                        class="btn btn-sm btn-outline-danger rounded-2"
                        data-action="supprimer"
                        data-id="{{ $media->id }}"
                        data-titre="{{ $media->titre ?? 'Sans titre' }}"
                        title="Supprimer">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>

    </div>
</div>