@extends('layouts.app')

@section('title', 'Médias — ' . $club->nom)
@section('description', 'Photos et vidéos publiques de ' . $club->nom . '.')

@section('content')
<div class="container py-5">
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
        <div>
            <a href="{{ route('clubs.show', $club) }}" class="text-muted small text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> Retour au club
            </a>
            <h1 class="fw-bold mt-2 mb-1">Médias de {{ $club->nom }}</h1>
            <p class="text-body-secondary mb-0">Photos et vidéos rendues publiques par le club.</p>
        </div>
        @if($club->sport)
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                {{ $club->sport->nom }}
            </span>
        @endif
    </div>

    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold" data-bs-toggle="tab" data-bs-target="#photos-publics"
                    type="button" role="tab">
                <i class="bi bi-images me-2"></i>Photos
                <span class="badge bg-primary rounded-pill ms-1">{{ $photos->total() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#videos-publics"
                    type="button" role="tab">
                <i class="bi bi-camera-video me-2"></i>Vidéos
                <span class="badge bg-warning text-dark rounded-pill ms-1">{{ $videos->total() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="photos-publics" role="tabpanel">
            @if($photos->isEmpty())
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-5 text-body-secondary">
                        <i class="bi bi-images fs-1 d-block mb-3"></i>
                        Aucune photo publique pour le moment.
                    </div>
                </div>
            @else
                <div class="row g-4">
                    @foreach($photos as $photo)
                        <div class="col-sm-6 col-lg-4">
                            <figure class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 mb-0">
                                <a href="{{ $photo->url() }}" target="_blank" class="text-decoration-none">
                                    <img src="{{ $photo->url() }}" class="w-100 object-fit-cover"
                                         style="height:220px;" alt="{{ $photo->titre ?? 'Photo du club' }}">
                                </a>
                                <figcaption class="card-body py-3 text-body">
                                    <div class="fw-semibold text-truncate">{{ $photo->titre ?? 'Photo du club' }}</div>
                                    @if($photo->description)
                                        <div class="small text-body-secondary text-truncate">{{ $photo->description }}</div>
                                    @endif
                                    @include('clubs.partials.media-reactions', ['media' => $photo])
                                </figcaption>
                            </figure>
                        </div>
                    @endforeach
                </div>
                @if($photos->hasPages())
                    <div class="mt-4">{{ $photos->links() }}</div>
                @endif
            @endif
        </div>

        <div class="tab-pane fade" id="videos-publics" role="tabpanel">
            @if($videos->isEmpty())
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-5 text-body-secondary">
                        <i class="bi bi-camera-video fs-1 d-block mb-3"></i>
                        Aucune vidéo publique pour le moment.
                    </div>
                </div>
            @else
                <div class="row g-4">
                    @foreach($videos as $video)
                        <div class="col-sm-6 col-lg-4">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                                    <video class="w-100" style="height:220px;object-fit:contain;" controls preload="metadata" playsinline
                                        @if($video->urlMiniature()) poster="{{ $video->urlMiniature() }}" @endif>
                                    <source src="{{ $video->url() }}">
                                    Votre navigateur ne prend pas en charge la vidéo.
                                </video>
                                <div class="card-body py-3">
                                    <div class="fw-semibold text-truncate">{{ $video->titre ?? 'Vidéo du club' }}</div>
                                    @if($video->description)
                                        <div class="small text-body-secondary text-truncate">{{ $video->description }}</div>
                                    @endif
                                    @include('clubs.partials.media-reactions', ['media' => $video])
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($videos->hasPages())
                    <div class="mt-4">{{ $videos->appends(['photos_page' => $photos->currentPage()])->links() }}</div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection