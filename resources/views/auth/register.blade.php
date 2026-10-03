{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.app')

@section('title', 'Inscription — Connect Sport')

@section('content')

<div class="min-vh-100 d-flex align-items-center justify-content-center py-5"
     style="background:var(--bg2);">

    <div class="w-100" style="max-width:680px;padding:0 16px;">

        {{-- Logo + badge --}}
        <div class="text-center mb-4">
            <h2 class="fw-black mb-2"
                style="font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:2px;color:var(--text);">
                CONNECT<span style="color:#F97316;">SPORT</span>
            </h2>
            <span class="badge rounded-pill px-3 py-2"
                  style="background:rgba(249,115,22,.15);border:1px solid rgba(249,115,22,.35);
                         color:#F97316;font-size:11px;letter-spacing:1px;">
                <i class="bi bi-lightning-fill me-1"></i> REJOINDRE CONNECT SPORT
            </span>
        </div>

        <div class="text-center mb-4">
            <h1 class="fw-black mb-1"
                style="font-family:'Bebas Neue',sans-serif;font-size:clamp(28px,5vw,40px);
                       letter-spacing:1px;color:var(--text);">
                Créer un compte
            </h1>
            <p style="color:var(--text2);font-size:14px;">Choisissez votre profil pour commencer</p>
        </div>

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

        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- ── SECTION 1 : Choix du rôle ── --}}
            <div class="rounded-4 p-4 mb-3"
                 style="background:var(--card-bg);border:1px solid var(--border);box-shadow:var(--shadow);">
                <p class="fw-bold text-uppercase mb-3"
                   style="font-size:.7rem;letter-spacing:2px;color:#F97316;">
                    <i class="bi bi-person-badge me-2"></i>VOTRE PROFIL
                </p>
                @php
                $roles = [
                    ['value'=>'club',      'icon'=>'bi-trophy-fill',    'label'=>'Club',      'desc'=>'Gérez votre club'],
                    ['value'=>'joueur',    'icon'=>'bi-dribbble',       'label'=>'Joueur',    'desc'=>'Valorisez votre profil'],
                    ['value'=>'agent',     'icon'=>'bi-briefcase-fill', 'label'=>'Agent',     'desc'=>'Représentez des joueurs'],
                    ['value'=>'supporter', 'icon'=>'bi-heart-fill',     'label'=>'Supporter', 'desc'=>'Suivez vos clubs'],
                    ['value'=>'parent',    'icon'=>'bi-people-fill',    'label'=>'Parent',    'desc'=>'Suivez votre enfant'],
                ];
                @endphp
                <div class="row g-2">
                    @foreach($roles as $r)
                    <div class="col-6 col-sm-4 col-md">
                        <input type="radio" name="role" value="{{ $r['value'] }}"
                               id="role_{{ $r['value'] }}" class="d-none role-radio"
                               {{ old('role', 'club') === $r['value'] ? 'checked' : '' }}>
                        <label for="role_{{ $r['value'] }}"
                               class="d-flex flex-column align-items-center justify-content-center
                                      rounded-3 p-3 w-100 h-100 role-label"
                               style="cursor:pointer;border:1.5px solid var(--border);
                                      background:var(--bg2);transition:all .2s;min-height:80px;">
                            <i class="bi {{ $r['icon'] }} mb-1" style="font-size:20px;color:var(--text3);"></i>
                            <span class="fw-bold" style="font-size:13px;color:var(--text2);">{{ $r['label'] }}</span>
                            <span style="font-size:10px;color:var(--text3);margin-top:2px;">{{ $r['desc'] }}</span>
                        </label>
                    </div>
                    @endforeach
                </div>
                @error('role')
                <div class="text-danger mt-2" style="font-size:12px;">{{ $message }}</div>
                @enderror
            </div>

            {{-- ══════════════════════════════════════
                 CLUB
            ══════════════════════════════════════ --}}
            <div id="fields_club" class="role-fields rounded-4 p-4 mb-3"
                 style="background:var(--card-bg);border:1px solid var(--border);box-shadow:var(--shadow);">
                <p class="fw-bold text-uppercase mb-3" style="font-size:.7rem;letter-spacing:2px;color:#F97316;">
                    <i class="bi bi-building me-2"></i>INFORMATIONS DU CLUB
                </p>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="cs-label">Nom du club <span class="text-danger">*</span></label>
                        <input type="text" name="club_nom" class="form-control cs-input rounded-3"
                               placeholder="Ex: AS Cotonou FC" value="{{ old('club_nom') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="cs-label">Sport principal <span class="text-danger">*</span></label>
                        <select name="club_sport_id" class="form-select cs-input rounded-3">
                            <option value="">Sélectionner</option>
                            @if(isset($sports))
                                @foreach($sports as $sport)
                                <option value="{{ $sport->id }}" @selected(old('club_sport_id')==$sport->id)>
                                    {{ $sport->nom }}
                                </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="cs-label">Niveau de compétition</label>
                        <select name="club_niveau" class="form-select cs-input rounded-3">
                            <option value="">Sélectionner</option>
                            @foreach(['National','Régional','Départemental','Loisir'] as $niv)
                            <option value="{{ $niv }}" @selected(old('club_niveau')===$niv)>{{ $niv }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="cs-label">Ville <span class="text-danger">*</span></label>
                        <input type="text" name="club_ville" class="form-control cs-input rounded-3"
                               placeholder="Ex: Cotonou" value="{{ old('club_ville') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="cs-label">Pays</label>
                        <input type="text" name="club_pays" class="form-control cs-input rounded-3"
                               placeholder="Ex: Bénin" value="{{ old('club_pays') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="cs-label">Téléphone</label>
                        <input type="text" name="club_telephone" class="form-control cs-input rounded-3"
                               placeholder="Ex: +229 97000000" value="{{ old('club_telephone') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="cs-label">Année de fondation</label>
                        <input type="number" name="club_annee" class="form-control cs-input rounded-3"
                               placeholder="Ex: 2005" min="1900" max="{{ date('Y') }}"
                               value="{{ old('club_annee') }}">
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════
                 JOUEUR — rattachement après acceptation du club
            ══════════════════════════════════════ --}}
            <div id="fields_joueur" class="role-fields rounded-4 p-4 mb-3 d-none"
                 style="background:var(--card-bg);border:1px solid var(--border);box-shadow:var(--shadow);">
                <p class="fw-bold text-uppercase mb-3" style="font-size:.7rem;letter-spacing:2px;color:#F97316;">
                    <i class="bi bi-person me-2"></i>INFORMATIONS DU JOUEUR
                </p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="cs-label">Prénom <span class="text-danger">*</span></label>
                        <input type="text" name="prenom" class="form-control cs-input rounded-3"
                               placeholder="Ex: Koffi" value="{{ old('prenom') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="cs-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="nom_famille" class="form-control cs-input rounded-3"
                               placeholder="Ex: Mensah" value="{{ old('nom_famille') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="cs-label">Date de naissance</label>
                        <input type="date" name="date_naissance" class="form-control cs-input rounded-3"
                               value="{{ old('date_naissance') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="cs-label">Genre</label>
                        <select name="genre" class="form-select cs-input rounded-3">
                            <option value="">Sélectionner</option>
                            <option value="masculin" @selected(old('genre')==='masculin')>Masculin</option>
                            <option value="feminin"  @selected(old('genre')==='feminin')>Féminin</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="cs-label">Sport pratiqué <span class="text-danger">*</span></label>
                        <select name="sport" class="form-select cs-input rounded-3" id="joueur_sport">
                            <option value="">Sélectionner</option>
                            @foreach(['Football','Handball','Basketball','Volleyball','Athlétisme','Natation','Rugby','Tennis','Autre'] as $sp)
                            <option value="{{ $sp }}" @selected(old('sport')===$sp)>{{ $sp }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="cs-label">Poste / Spécialité <span class="text-danger">*</span></label>
                        <input type="text" name="poste" class="form-control cs-input rounded-3"
                               placeholder="Ex: Gardien, Pivot, Sprinter, Meneur..."
                               value="{{ old('poste') }}">
                        <small style="color:var(--text3);font-size:11px;">
                            Précisez selon votre sport (Gardien, Attaquant, Pivot, Meneur, Libéro, Sprinter...)
                        </small>
                    </div>
                    <div class="col-md-4">
                        <label class="cs-label">Catégorie</label>
                        <select name="categorie" class="form-select cs-input rounded-3">
                            <option value="">Sélectionner</option>
                            @foreach(['junior' => 'Junior', 'cadet' => 'Cadet', 'senior' => 'Senior', 'veteran' => 'Vétéran'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('categorie') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="cs-label">Pays</label>
                        <input type="text" name="pays" class="form-control cs-input rounded-3"
                               placeholder="Ex: Bénin" value="{{ old('pays') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="cs-label">Taille (cm)</label>
                        <input type="number" name="taille" class="form-control cs-input rounded-3"
                               placeholder="Ex: 178" min="100" max="250"
                               value="{{ old('taille') }}">
                    </div>
                    <div class="col-12 small text-muted">Votre profil démarre sans club. Le rattachement ne sera effectué qu’après acceptation d’une candidature par le club.</div>
                    <div class="col-12">
                        <label class="cs-label">Disponibilité</label>
                        <div class="d-flex gap-3 mt-1">
                            <div class="form-check">
                                <input type="radio" name="disponible" value="1" id="dispo_oui"
                                       class="form-check-input cs-check"
                                       {{ old('disponible','1') === '1' ? 'checked' : '' }}>
                                <label for="dispo_oui" class="form-check-label"
                                       style="font-size:14px;color:var(--text2);">
                                    Disponible (recherche un club)
                                </label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="disponible" value="0" id="dispo_non"
                                       class="form-check-input cs-check"
                                       {{ old('disponible') === '0' ? 'checked' : '' }}>
                                <label for="dispo_non" class="form-check-label"
                                       style="font-size:14px;color:var(--text2);">
                                    Sous contrat
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════
                 AGENT — indépendant, pas rattaché à un club
            ══════════════════════════════════════ --}}
            <div id="fields_agent" class="role-fields rounded-4 p-4 mb-3 d-none"
                 style="background:var(--card-bg);border:1px solid var(--border);box-shadow:var(--shadow);">
                <p class="fw-bold text-uppercase mb-3" style="font-size:.7rem;letter-spacing:2px;color:#F97316;">
                    <i class="bi bi-briefcase me-2"></i>INFORMATIONS DE L'AGENT
                </p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="cs-label">Prénom <span class="text-danger">*</span></label>
                        <input type="text" name="agent_prenom" class="form-control cs-input rounded-3"
                               placeholder="Ex: Jean" value="{{ old('agent_prenom') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="cs-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="agent_nom" class="form-control cs-input rounded-3"
                               placeholder="Ex: Dupont" value="{{ old('agent_nom') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="cs-label">Pays d'activité</label>
                        <input type="text" name="agent_pays" class="form-control cs-input rounded-3"
                               placeholder="Ex: Bénin, Sénégal..." value="{{ old('agent_pays') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="cs-label">Téléphone</label>
                        <input type="text" name="telephone" class="form-control cs-input rounded-3"
                               placeholder="Ex: +229 97000000" value="{{ old('telephone') }}">
                    </div>
                    <div class="col-12">
                        <label class="cs-label">Numéro de licence / accréditation (optionnel)</label>
                        <input type="text" name="licence_agent" class="form-control cs-input rounded-3"
                               placeholder="Ex: AGT-2024-00123" value="{{ old('licence_agent') }}">
                        <small style="color:var(--text3);font-size:11px;">
                            L'agent est indépendant. Il peut collaborer avec plusieurs clubs après inscription.
                        </small>
                    </div>
                    <div class="col-12">
                        <label class="cs-label">Sports couverts</label>
                        <div class="d-flex flex-wrap gap-2 mt-1">
                            @foreach(['Football','Handball','Basketball','Volleyball','Athlétisme','Autre'] as $sp)
                            <div class="form-check">
                                <input type="checkbox" name="agent_sports[]" value="{{ $sp }}"
                                       id="asp_{{ $loop->index }}" class="form-check-input cs-check"
                                       {{ in_array($sp, old('agent_sports',[])) ? 'checked' : '' }}>
                                <label for="asp_{{ $loop->index }}" class="form-check-label"
                                       style="font-size:13px;color:var(--text2);">{{ $sp }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════
                 SUPPORTER — rattaché à un club
            ══════════════════════════════════════ --}}
            <div id="fields_supporter" class="role-fields rounded-4 p-4 mb-3 d-none"
                 style="background:var(--card-bg);border:1px solid var(--border);box-shadow:var(--shadow);">
                <p class="fw-bold text-uppercase mb-3" style="font-size:.7rem;letter-spacing:2px;color:#F97316;">
                    <i class="bi bi-heart me-2"></i>INFORMATIONS DU SUPPORTER
                </p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="cs-label">Prénom <span class="text-danger">*</span></label>
                        <input type="text" name="supporter_prenom" class="form-control cs-input rounded-3"
                               placeholder="Ex: Marie" value="{{ old('supporter_prenom') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="cs-label">Nom</label>
                        <input type="text" name="supporter_nom" class="form-control cs-input rounded-3"
                               placeholder="Ex: Koné" value="{{ old('supporter_nom') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="cs-label">Pays</label>
                        <input type="text" name="supporter_pays" class="form-control cs-input rounded-3"
                               placeholder="Ex: Sénégal" value="{{ old('supporter_pays') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="cs-label">Téléphone</label>
                        <input type="text" name="telephone" class="form-control cs-input rounded-3"
                               placeholder="Ex: +221 77000000" value="{{ old('telephone') }}">
                    </div>
                    <div class="col-12">
                        <label class="cs-label">Club favori</label>
                        <select name="club_id" class="form-select cs-input rounded-3">
                            <option value="">-- Choisir un club --</option>
                            @if(isset($clubs) && $clubs->count())
                                @foreach($clubs as $club)
                                <option value="{{ $club->id }}" @selected(old('club_id')==$club->id)>
                                    {{ $club->nom }} — {{ $club->ville ?? '' }}
                                </option>
                                @endforeach
                            @endif
                        </select>
                        <small style="color:var(--text3);font-size:11px;">
                            Vous pourrez suivre d'autres clubs depuis votre tableau de bord.
                        </small>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════
                 PARENT — suivi d'un joueur mineur
            ══════════════════════════════════════ --}}
            <div id="fields_parent" class="role-fields rounded-4 p-4 mb-3 d-none"
                 style="background:var(--card-bg);border:1px solid var(--border);box-shadow:var(--shadow);">
                <p class="fw-bold text-uppercase mb-3" style="font-size:.7rem;letter-spacing:2px;color:#F97316;">
                    <i class="bi bi-people me-2"></i>INFORMATIONS DU PARENT
                </p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="cs-label">Prénom <span class="text-danger">*</span></label>
                        <input type="text" name="parent_prenom" class="form-control cs-input rounded-3"
                               placeholder="Ex: Adama" value="{{ old('parent_prenom') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="cs-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="parent_nom" class="form-control cs-input rounded-3"
                               placeholder="Ex: Koné" value="{{ old('parent_nom') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="cs-label">Téléphone</label>
                        <input type="text" name="telephone" class="form-control cs-input rounded-3"
                               placeholder="Ex: +229 97000000" value="{{ old('telephone') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="cs-label">Pays</label>
                        <input type="text" name="parent_pays" class="form-control cs-input rounded-3"
                               placeholder="Ex: Bénin" value="{{ old('parent_pays') }}">
                    </div>
                    <div class="col-12 small text-muted">Vous pourrez demander un lien avec le profil du joueur depuis votre tableau de bord. Le joueur devra le confirmer.</div>
                </div>
            </div>

            {{-- ── SECTION 3 : Compte ── --}}
            <div class="rounded-4 p-4 mb-3"
                 style="background:var(--card-bg);border:1px solid var(--border);box-shadow:var(--shadow);">
                <p class="fw-bold text-uppercase mb-3" style="font-size:.7rem;letter-spacing:2px;color:#F97316;">
                    <i class="bi bi-shield-lock me-2"></i>INFORMATIONS DU COMPTE
                </p>
                <div class="mb-3">
                    <label class="cs-label"><i class="bi bi-envelope me-1"></i>EMAIL <span class="text-danger">*</span></label>
                    <input type="email" name="email"
                           class="form-control cs-input rounded-3 @error('email') is-invalid @enderror"
                           placeholder="votre@email.com"
                           value="{{ old('email') }}" required autocomplete="username">
                    @error('email')
                    <div class="invalid-feedback" style="font-size:12px;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="cs-label"><i class="bi bi-lock me-1"></i>MOT DE PASSE <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input type="password" name="password" id="reg_password"
                                   class="form-control cs-input rounded-3 @error('password') is-invalid @enderror"
                                   placeholder="••••••••" required autocomplete="new-password"
                                   style="padding-right:48px;">
                            <button type="button" onclick="togglePassword('reg_password','eyeReg1')"
                                    class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0"
                                    style="background:transparent;color:var(--text3);font-size:18px;">
                                <i class="bi bi-eye" id="eyeReg1"></i>
                            </button>
                        </div>
                        <small style="color:var(--text3);font-size:11px;">Minimum 8 caractères</small>
                        @error('password')
                        <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="cs-label"><i class="bi bi-lock-fill me-1"></i>CONFIRMER <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input type="password" name="password_confirmation" id="reg_password2"
                                   class="form-control cs-input rounded-3"
                                   placeholder="••••••••" required autocomplete="new-password"
                                   style="padding-right:48px;">
                            <button type="button" onclick="togglePassword('reg_password2','eyeReg2')"
                                    class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0"
                                    style="background:transparent;color:var(--text3);font-size:18px;">
                                <i class="bi bi-eye" id="eyeReg2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit"
                    class="btn w-100 fw-bold rounded-3 py-3 mb-3"
                    style="background:#F97316;color:#fff;font-size:16px;border:none;transition:background .2s;"
                    onmouseover="this.style.background='#ea6a0b'"
                    onmouseout="this.style.background='#F97316'">
                <i class="bi bi-person-plus me-2"></i>Créer mon compte gratuitement
            </button>

        </form>

        <div class="text-center mt-3 mb-5">
            <p style="color:var(--text2);font-size:14px;">
                Déjà inscrit ?
                <a href="{{ route('login') }}" style="color:#F97316;font-weight:600;text-decoration:none;">
                    Se connecter
                </a>
            </p>
        </div>

    </div>
</div>

<style>
.cs-label {
    display:block;
    font-size:.78rem;
    font-weight:600;
    color:var(--text2);
    margin-bottom:4px;
}
.cs-input {
    background: var(--bg2) !important;
    border-color: var(--border) !important;
    color: var(--text) !important;
    height: 48px;
    font-size: 15px;
}
.cs-input::placeholder { color: var(--text3) !important; }
.cs-input:focus {
    border-color: #F97316 !important;
    box-shadow: 0 0 0 3px rgba(249,115,22,.15) !important;
    color: var(--text) !important;
    background: var(--bg2) !important;
}
.form-select.cs-input { height:48px; }
.cs-check { border-color:var(--border)!important; background-color:var(--bg2)!important; }
.cs-check:checked { background-color:#F97316!important; border-color:#F97316!important; }
.role-radio:checked + .role-label { border-color:#F97316!important; background:rgba(249,115,22,.1)!important; }
.role-radio:checked + .role-label i { color:#F97316!important; }
.role-radio:checked + .role-label span:first-of-type { color:#F97316!important; }
.role-label:hover { border-color:rgba(249,115,22,.5)!important; background:rgba(249,115,22,.05)!important; }
</style>

<script>
function togglePassword(fId, iId) {
    const f = document.getElementById(fId);
    const i = document.getElementById(iId);
    f.type = f.type === 'password' ? 'text' : 'password';
    i.className = f.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}

function switchRole(role) {
    document.querySelectorAll('.role-fields').forEach(el => el.classList.add('d-none'));
    const target = document.getElementById('fields_' + role);
    if (target) target.classList.remove('d-none');
}

document.querySelectorAll('.role-label').forEach(label => {
    label.addEventListener('click', function() {
        const radio = document.getElementById(this.getAttribute('for'));
        if (radio) switchRole(radio.value);
    });
});

(function() {
    const checked = document.querySelector('.role-radio:checked');
    if (checked) switchRole(checked.value);
})();
</script>

@endsection