{{-- resources/views/admin/auth/login.blade.php --}}
@extends('layouts.app')

@section('title', 'Connexion Administration — Connect Sport')

@section('content')

<div class="d-flex align-items-center justify-content-center py-5 min-vh-100"
     style="background:var(--bg2);">

    <div class="w-100" style="max-width:460px;padding:0 16px;">

        {{-- Logo --}}
        <div class="text-center mb-4">
            <h2 class="fw-black mb-2"
                style="font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:2px;color:var(--text);">
                CONNECT<span style="color:#F97316;">SPORT</span>
            </h2>
            <span class="badge rounded-pill px-3 py-2"
                  style="background:rgba(26,86,160,.12);border:1px solid rgba(26,86,160,.35);
                         color:#1A56A0;font-size:11px;letter-spacing:1px;">
                <i class="bi bi-shield-lock-fill me-1"></i> ESPACE ADMINISTRATION
            </span>
        </div>

        <div class="text-center mb-4">
            <h1 class="fw-black mb-1"
                style="font-family:'Bebas Neue',sans-serif;font-size:clamp(28px,5vw,38px);
                       letter-spacing:1px;color:var(--text);">
                Connexion Admin
            </h1>
            <p style="color:var(--text2);font-size:14px;">Accédez au panneau de gestion de la plateforme</p>
        </div>

        {{-- Carte formulaire --}}
        <div class="rounded-4 p-4"
             style="background:var(--card-bg);border:1px solid var(--border);box-shadow:var(--shadow);">

            {{-- Erreurs --}}
            @if($errors->any())
            <div class="rounded-3 p-3 mb-4"
                 style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);">
                @foreach($errors->all() as $e)
                <div class="d-flex align-items-center gap-2" style="font-size:13px;color:#EF4444;">
                    <i class="bi bi-exclamation-circle flex-shrink-0"></i>{{ $e }}
                </div>
                @endforeach
            </div>
            @endif

            @if(session('success'))
            <div class="rounded-3 p-3 mb-4 d-flex align-items-center gap-2"
                 style="background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.25);color:#10B981;font-size:13px;">
                <i class="bi bi-check-circle flex-shrink-0"></i>{{ session('success') }}
            </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold mb-1"
                           style="font-size:.75rem;color:var(--text2);letter-spacing:.08em;text-transform:uppercase;">
                        <i class="bi bi-envelope me-1"></i>Adresse email administrateur
                    </label>
                    <input type="email" name="email"
                           class="form-control rounded-3 cs-field @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           placeholder="admin@connectsport.com"
                           required autofocus autocomplete="username">
                    @error('email')
                    <div class="invalid-feedback" style="font-size:12px;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Mot de passe --}}
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-semibold mb-0"
                               style="font-size:.75rem;color:var(--text2);letter-spacing:.08em;text-transform:uppercase;">
                            <i class="bi bi-lock me-1"></i>Mot de passe
                        </label>
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" id="passwordField"
                               class="form-control rounded-start-3 cs-field @error('password') is-invalid @enderror"
                               placeholder="••••••••"
                               required autocomplete="current-password">
                        <button type="button" class="btn btn-outline-secondary rounded-end-3"
                                onclick="togglePassword('passwordField', 'passwordEye')"
                                style="border-color:var(--border);">
                            <i class="bi bi-eye" id="passwordEye"></i>
                        </button>
                    </div>
                    @error('password')
                    <div class="invalid-feedback d-block" style="font-size:12px;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Se souvenir de moi --}}
                <div class="form-check mb-4">
                    <input type="checkbox" name="remember" class="form-check-input" id="rememberMe">
                    <label class="form-check-label" for="rememberMe"
                           style="font-size:13px;color:var(--text2);">
                        Garder ma session active
                    </label>
                </div>

                {{-- Bouton connexion --}}
                <button type="submit" class="btn w-100 fw-bold py-2 rounded-3 text-white"
                        style="background:#1A56A0;font-size:15px;letter-spacing:.5px;">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Accéder au panneau d'administration
                </button>
            </form>

        </div>

        {{-- Retour au site --}}
        <div class="text-center mt-4">
            <a href="{{ route('home') }}" class="text-decoration-none" style="font-size:13px;color:var(--text2);">
                <i class="bi bi-arrow-left me-1"></i>Retour au site public
            </a>
        </div>

    </div>

</div>

@push('scripts')
<script>
function togglePassword(fieldId, iconId) {
    const input = document.getElementById(fieldId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>
@endpush

@endsection
