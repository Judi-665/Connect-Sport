{{-- resources/views/club/profil/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Paramètres — ' . optional($club)->nom ?? 'Mon Club')

@push('styles')
<style>
/* Ton CSS inchangé (garde-le) */
.cs-profil-wrap { display: flex; min-height: 100vh; padding-top: 64px; background: var(--bg2); transition: background .25s; }
.cs-profil-main { flex: 1; margin-left: 240px; display: flex; flex-direction: column; transition: margin-left .25s; }
body.sidebar-collapsed .cs-profil-main { margin-left: 64px; }
.cs-profil-content { flex: 1; padding: 24px; max-width: 800px; }
.cs-section-label { font-size: 10px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: var(--text3); margin-bottom: 12px; margin-top: 24px; display: flex; align-items: center; gap-8px; }
.cs-section-label:first-child { margin-top: 0; }
.cs-field-readonly { background: var(--bg2) !important; border: 1px solid var(--border) !important; color: var(--text) !important; border-radius: 10px !important; padding: 10px 14px !important; font-size: 14px; display: flex; align-items: center; justify-content: space-between; }
.cs-field-readonly .cs-check-badge { font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 20px; background: rgba(16,185,129,.1); color: #10b981; flex-shrink: 0; }
.cs-input { background: var(--card-bg) !important; border: 1px solid var(--border) !important; color: var(--text) !important; border-radius: 10px !important; padding: 10px 14px !important; font-size: 14px; transition: border-color .15s, box-shadow .15s; width: 100%; }
.cs-input:focus { border-color: #F97316 !important; box-shadow: 0 0 0 3px rgba(249,115,22,.12) !important; outline: none; }
.cs-input::placeholder { color: var(--text3) !important; }
.cs-input.is-invalid { border-color: #dc3545 !important; }
.cs-label { display: block; font-size: 12px; font-weight: 600; color: var(--text2); margin-bottom: 5px; }
.cs-upload-zone { border: 1.5px dashed var(--border); border-radius: 12px; padding: 20px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; cursor: pointer; transition: border-color .2s; background: var(--bg2); }
.cs-upload-zone:hover { border-color: #F97316; }
.cs-upload-zone span { font-size: 12px; color: var(--text3); }
.cs-form-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 16px; padding: 24px; margin-bottom: 16px; transition: background .25s, border-color .25s; }
.cs-form-card__title { font-size: 13px; font-weight: 700; color: var(--text); margin-bottom: 18px; display: flex; align-items: center; gap: 8px; padding-bottom: 14px; border-bottom: 1px solid var(--border); }
.cs-form-card__title i { color: #F97316; }
@media (max-width: 768px) { .cs-profil-main { margin-left: 0; } .cs-profil-content { padding: 16px; max-width: 100%; } }
</style>
@endpush

@section('content')
@php
    $club = $club ?? auth()->user()->club ?? null;
    $user = auth()->user();
@endphp

<div class="cs-profil-wrap">
    <div class="cs-profil-main">
        @include('club.partials._header')

        <div class="cs-profil-content">
            <h1 class="cs-page-title mb-1">Paramètres du club</h1>
            <p class="cs-page-subtitle mb-4" style="font-size:13px;color:var(--text3);">
                Complétez et mettez à jour les informations de votre club.
            </p>

            @if($errors->any())
                <div class="alert alert-danger rounded-3 py-2 mb-4">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $e)
                            <li style="font-size:13px;">{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success rounded-3 py-2 mb-4" style="font-size:13px;">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                </div>
            @endif

            {{-- Bloc 1 : Infos enregistrées à l'inscription (lecture seule) --}}
            <div class="cs-form-card">
                <div class="cs-form-card__title">
                    <i class="bi bi-lock-fill"></i>
                    Informations enregistrées à l'inscription
                </div>

                <div class="row g-3">
                    {{-- Nom --}}
                    <div class="col-12">
                        <label class="cs-label">Nom du club</label>
                        <div class="cs-field-readonly">
                            <span>{{ optional($club)->nom ?? '—' }}</span>
                            <span class="cs-check-badge"><i class="bi bi-check-lg me-1"></i>Enregistré</span>
                        </div>
                    </div>

                    {{-- Sport --}}
                    <div class="col-md-6">
                        <label class="cs-label">Sport principal</label>
                        <div class="cs-field-readonly">
                            <span>{{ optional(optional($club)->sport)->nom ?? '—' }}</span>
                            <span class="cs-check-badge"><i class="bi bi-check-lg me-1"></i>Enregistré</span>
                        </div>
                    </div>

                    {{-- Abonnement --}}
                    <div class="col-md-6">
                        <label class="cs-label">Abonnement</label>
                        <div class="cs-field-readonly">
                            <span>{{ ucfirst(optional($club)->abonnement ?? 'gratuit') }}</span>
                            <span class="cs-check-badge"><i class="bi bi-check-lg me-1"></i>Actif</span>
                        </div>
                    </div>

                    {{-- Ville --}}
                    <div class="col-md-6">
                        <label class="cs-label">Ville</label>
                        <div class="cs-field-readonly">
                            <span>{{ optional($club)->ville ?? '—' }}</span>
                            @if(optional($club)->ville)
                                <span class="cs-check-badge"><i class="bi bi-check-lg me-1"></i>Enregistré</span>
                            @endif
                        </div>
                    </div>

                    {{-- Pays --}}
                    <div class="col-md-6">
                        <label class="cs-label">Pays</label>
                        <div class="cs-field-readonly">
                            <span>{{ optional($club)->pays ?? '—' }}</span>
                            @if(optional($club)->pays)
                                <span class="cs-check-badge"><i class="bi bi-check-lg me-1"></i>Enregistré</span>
                            @endif
                        </div>
                    </div>

                    {{-- Téléphone (inscription) --}}
                    <div class="col-md-6">
                        <label class="cs-label">Téléphone</label>
                        <div class="cs-field-readonly">
                            <span>{{ optional($club)->telephone ?? '—' }}</span>
                            @if(optional($club)->telephone)
                                <span class="cs-check-badge"><i class="bi bi-check-lg me-1"></i>Enregistré</span>
                            @endif
                        </div>
                    </div>

                    {{-- Statut --}}
                    <div class="col-md-6">
                        <label class="cs-label">Statut</label>
                        <div class="cs-field-readonly">
                            <span>{{ optional($club)->actif ? 'Actif' : 'Inactif' }}</span>
                            <span class="cs-check-badge" style="{{ optional($club)->actif ? '' : 'background:rgba(239,68,68,.1);color:#ef4444;' }}">
                                {{ optional($club)->actif ? '✓ Actif' : '✗ Inactif' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bloc 2 : Infos à compléter / modifier (formulaire) --}}
            <form method="POST" action="{{ route('club.profil.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="cs-form-card">
                    <div class="cs-form-card__title">
                        <i class="bi bi-pencil-square"></i>
                        Informations à compléter
                    </div>

                    <div class="row g-3">
                        {{-- Description --}}
                        <div class="col-12">
                            <label for="description" class="cs-label">Description du club</label>
                            <textarea id="description" name="description" rows="4"
                                      class="cs-input @error('description') is-invalid @enderror"
                                      placeholder="Présentez votre club, son histoire, ses valeurs...">{{ old('description', optional($club)->description ?? '') }}</textarea>
                            @error('description')
                                <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Adresse --}}
                        <div class="col-12">
                            <label for="adresse" class="cs-label">Adresse complète</label>
                            <input type="text" id="adresse" name="adresse"
                                   class="cs-input @error('adresse') is-invalid @enderror"
                                   placeholder="Ex : Quartier Zogbo, Cotonou"
                                   value="{{ old('adresse', optional($club)->adresse ?? '') }}">
                            @error('adresse')
                                <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Site web --}}
                        <div class="col-md-6">
                            <label for="site_web" class="cs-label">Site web</label>
                            <input type="url" id="site_web" name="site_web"
                                   class="cs-input @error('site_web') is-invalid @enderror"
                                   placeholder="https://monclub.com"
                                   value="{{ old('site_web', optional($club)->site_web ?? '') }}">
                            @error('site_web')
                                <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Téléphone (modifiable) --}}
                        <div class="col-md-6">
                            <label for="telephone" class="cs-label">Téléphone <span style="color:var(--text3);font-weight:400;">(modifier)</span></label>
                            <input type="text" id="telephone" name="telephone"
                                   class="cs-input @error('telephone') is-invalid @enderror"
                                   placeholder="Ex : +229 97000000"
                                   value="{{ old('telephone', optional($club)->telephone ?? '') }}">
                            @error('telephone')
                                <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Bloc 3 : Logo --}}
                <div class="cs-form-card">
                    <div class="cs-form-card__title">
                        <i class="bi bi-image"></i> Logo du club
                    </div>

                    <div class="d-flex align-items-start gap-4 flex-wrap">
                        <div class="text-center">
                            @if(optional($club)->logo)
                                <img src="{{ asset('storage/' . $club->logo) }}"
                                     id="logo-preview"
                                     class="rounded-3 border"
                                     style="width:80px;height:80px;object-fit:cover;"
                                     alt="Logo actuel">
                                <div class="mt-1" style="font-size:11px;color:var(--text3);">Logo actuel</div>
                            @else
                                <div id="logo-preview-placeholder"
                                     class="rounded-3 d-flex align-items-center justify-content-center"
                                     style="width:80px;height:80px;background:var(--bg2);border:1px solid var(--border);">
                                    <i class="bi bi-building" style="font-size:28px;color:var(--text3);"></i>
                                </div>
                                <div class="mt-1" style="font-size:11px;color:var(--text3);">Aucun logo</div>
                            @endif
                        </div>

                        <div class="flex-1" style="flex:1;min-width:200px;">
                            <label for="logo" class="cs-upload-zone">
                                <i class="bi bi-cloud-upload" style="font-size:24px;color:var(--text3);"></i>
                                <span id="logo-name" style="font-weight:600;color:var(--text2);">Cliquer pour uploader</span>
                                <span>PNG, JPG, WEBP — max 2 Mo</span>
                                <input type="file" id="logo" name="logo" accept="image/*" class="d-none">
                            </label>
                            @error('logo')
                                <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 justify-content-end">
                    <a href="{{ route('club.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">Annuler</a>
                    <button type="submit" class="btn btn-warning rounded-pill px-5 fw-semibold">
                        <i class="bi bi-check-lg me-1"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('logo').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        document.getElementById('logo-name').textContent = file.name;
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('logo-preview');
            const placeholder = document.getElementById('logo-preview-placeholder');
            if (preview) {
                preview.src = e.target.result;
            } else if (placeholder) {
                placeholder.innerHTML = `<img src="${e.target.result}" style="width:80px;height:80px;object-fit:cover;border-radius:10px;">`;
            }
        };
        reader.readAsDataURL(file);
    });
</script>
@endpush