{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')

@section('title', 'Connexion — Connect Sport')

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
                  style="background:rgba(249,115,22,.12);border:1px solid rgba(249,115,22,.35);
                         color:#F97316;font-size:11px;letter-spacing:1px;">
                <i class="bi bi-box-arrow-in-right me-1"></i> CONNEXION
            </span>
        </div>

        <div class="text-center mb-4">
            <h1 class="fw-black mb-1"
                style="font-family:'Bebas Neue',sans-serif;font-size:clamp(28px,5vw,38px);
                       letter-spacing:1px;color:var(--text);">
                Se connecter
            </h1>
            <p style="color:var(--text2);font-size:14px;">Accédez à votre espace Connect Sport</p>
        </div>

        {{-- Message inscription validée --}}
        @if(session('status') === 'inscription_validee')
        <div class="rounded-3 p-3 mb-4 d-flex align-items-start gap-2"
             style="background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.3);">
            <i class="bi bi-check-circle-fill mt-1 flex-shrink-0" style="color:#10B981;font-size:15px;"></i>
            <div>
                <div class="fw-semibold" style="color:#10B981;font-size:13px;">Inscription validée</div>
                <div style="color:var(--text2);font-size:12px;margin-top:2px;">
                    Votre compte a bien été créé. Veuillez vous connecter pour accéder à votre espace.
                </div>
            </div>
        </div>
        @endif

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

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold mb-1"
                           style="font-size:.75rem;color:var(--text2);letter-spacing:.08em;text-transform:uppercase;">
                        <i class="bi bi-envelope me-1"></i>Adresse email
                    </label>
                    <input type="email" name="email"
                           class="form-control rounded-3 cs-field @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           placeholder="votre@email.com"
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
                        @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           style="font-size:12px;color:#F97316;text-decoration:none;">
                            Mot de passe oublié ?
                        </a>
                        @endif
                    </div>
                    <div class="position-relative">
                        <input type="password" name="password" id="pwd"
                               class="form-control rounded-3 cs-field @error('password') is-invalid @enderror"
                               placeholder="••••••••" required autocomplete="current-password"
                               style="padding-right:48px;">
                        <button type="button" onclick="togglePwd('pwd','eye1')"
                                class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0"
                                style="background:transparent;color:var(--text3);font-size:17px;z-index:5;">
                            <i class="bi bi-eye" id="eye1"></i>
                        </button>
                    </div>
                    @error('password')
                    <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Se souvenir --}}
                <div class="mb-4">
                    <div class="form-check">
                        <input type="checkbox" name="remember" id="remember_me"
                               class="form-check-input cs-check"
                               style="width:17px;height:17px;">
                        <label for="remember_me" class="form-check-label ms-1"
                               style="font-size:14px;color:var(--text2);">
                            Se souvenir de moi
                        </label>
                    </div>
                </div>

                <button type="submit"
                        class="btn w-100 fw-bold rounded-3 py-3"
                        style="background:#F97316;color:#fff;font-size:15px;border:none;transition:background .2s;"
                        onmouseover="this.style.background='#ea6a0b'"
                        onmouseout="this.style.background='#F97316'">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
                </button>

            </form>
        </div>

        <div class="text-center mt-4">
            <p style="color:var(--text2);font-size:14px;">
                Pas encore de compte ?
                <a href="{{ route('register') }}"
                   style="color:#F97316;font-weight:600;text-decoration:none;">
                    S'inscrire gratuitement
                </a>
            </p>
        </div>

    </div>
</div>

<style>
.cs-field {
    background: var(--bg2) !important;
    border-color: var(--border) !important;
    color: var(--text) !important;
    height: 48px;
    font-size: 15px;
    transition: border-color .2s, box-shadow .2s;
}
.cs-field::placeholder { color: var(--text3) !important; }
.cs-field:focus {
    border-color: #F97316 !important;
    box-shadow: 0 0 0 3px rgba(249,115,22,.15) !important;
    background: var(--bg2) !important;
    color: var(--text) !important;
}
.cs-check { border-color: var(--border) !important; background-color: var(--bg2) !important; }
.cs-check:checked { background-color: #F97316 !important; border-color: #F97316 !important; }
</style>

<script>
function togglePwd(fId, iId) {
    const f = document.getElementById(fId);
    const i = document.getElementById(iId);
    f.type = f.type === 'password' ? 'text' : 'password';
    i.className = f.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
</script>

@endsection