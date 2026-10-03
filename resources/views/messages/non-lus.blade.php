{{-- resources/views/messages/non-lus.blade.php --}}
@extends('layouts.app')
@section('title', 'Messages non lus — Connect Sport')

@section('content')
<div class="container py-5" style="max-width:780px;">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <p class="text-uppercase small fw-bold mb-1" style="color:#0D9488;letter-spacing:1px;">Messagerie</p>
            <h1 class="h3 fw-bold mb-0">Messages non lus</h1>
        </div>
        <div class="d-flex gap-2">
            <form method="POST" action="{{ route('messages.read-all') }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-outline-secondary rounded-pill btn-sm">
                    <i class="bi bi-check2-all me-1"></i> Tout marquer comme lu
                </button>
            </form>
            <a href="{{ route('messages.inbox') }}" class="btn btn-outline-secondary rounded-pill btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        @forelse($messages as $message)
            @php
                $partner = $message->expediteur;
                $initiale = strtoupper(substr($partner?->prenom ?? $partner?->name ?? '?', 0, 1));
            @endphp
            <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                @if($partner?->avatar)
                    <img src="{{ Storage::url($partner->avatar) }}" alt="{{ $partner->name }}"
                         class="rounded-circle object-fit-cover flex-shrink-0" style="width:44px;height:44px;">
                @else
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                         style="width:44px;height:44px;background:linear-gradient(135deg,#0D2E5C,#0D9488);color:#fff;font-size:14px;">
                        {{ $initiale }}
                    </div>
                @endif
                <div class="flex-grow-1 overflow-hidden">
                    <div class="fw-semibold">{{ $partner?->prenom ? $partner->prenom . ' ' . $partner->name : ($partner?->name ?? 'Inconnu') }}</div>
                    <div class="text-muted text-truncate small">{{ $message->contenu }}</div>
                </div>
                <div class="d-flex flex-column align-items-end gap-1 flex-shrink-0">
                    <small class="text-muted" style="font-size:11px;">{{ $message->created_at->diffForHumans() }}</small>
                    <div class="d-flex gap-1">
                        <a href="{{ route('messages.conversation', $partner) }}"
                           class="btn btn-sm rounded-pill px-2 py-1" style="background:#0D9488;color:#fff;font-size:11px;">
                            Repondre
                        </a>
                        <form method="POST" action="{{ route('messages.read', $message) }}" class="mb-0">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1" style="font-size:11px;" title="Marquer comme lu">
                                <i class="bi bi-check2"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-5">
                <i class="bi bi-envelope-check" style="font-size:2.5rem;opacity:.3;"></i>
                <p class="mt-3 small fw-semibold">Aucun message non lu</p>
            </div>
        @endforelse
    </div>

    {{ $messages->links() }}
</div>
@endsection
