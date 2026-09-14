@php
    $user = auth()->user();
    $avatarUrl = $user->avatar ? asset('storage/' . $user->avatar) : null;
    $initiales = strtoupper(substr($user->prenom ?? $user->name ?? 'J', 0, 1) . substr($user->name ?? '', 0, 1));
@endphp

<div class="d-flex align-items-center justify-content-between gap-3 mb-4 p-3 bg-body rounded-3 shadow-sm">
    <div class="d-flex align-items-center gap-3 min-width-0">
        @if($avatarUrl)
            <img src="{{ $avatarUrl }}" alt="Photo de profil" class="rounded-circle flex-shrink-0" style="width:48px;height:48px;object-fit:cover;">
        @else
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0" style="width:48px;height:48px;background:#F97316;">
                {{ $initiales }}
            </div>
        @endif
        <div class="min-width-0">
            <div class="fw-semibold text-truncate">{{ $user->prenom ?? $user->name }} {{ $user->prenom ? $user->name : '' }}</div>
            <div class="text-muted small">{{ $joueur->poste ?? 'Joueur sportif' }} · {{ $joueur->club?->nom ?? 'Sans club' }}</div>
        </div>
    </div>
    <a href="{{ route('joueur.profil') }}" class="btn btn-outline-secondary btn-sm flex-shrink-0">
        <i class="bi bi-person-circle me-1"></i>Mon profil
    </a>
</div>
