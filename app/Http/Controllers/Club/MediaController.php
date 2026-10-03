<?php
// app/Http/Controllers/Club/MediaController.php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\ParentJoueur;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MediaController extends Controller
{
    // ═══ Liste des médias ═══
    public function index()
    {
        $club   = Auth::user()->club;
        $medias = $club->medias()->withCount([
            'reactions as likes_count' => fn($query) => $query->where('type', 'like'),
            'reactions as loves_count' => fn($query) => $query->where('type', 'love'),
        ]);
        $photos = (clone $medias)->photos()->latest()->paginate(12, ['*'], 'photos');
        $videos = (clone $medias)->videos()->latest()->paginate(8, ['*'], 'videos');

        return view('club.medias.index', compact('club', 'photos', 'videos'));
    }

    // ═══ Formulaire upload ═══
   public function create()
{
    $club       = Auth::user()->club;
    $joueurs    = $club->joueurs()->with('user')->get();
    $evenements = $club->evenements()->orderBy('debut_at', 'desc')->limit(20)->get();

    return view('club.medias.create', compact('club', 'joueurs', 'evenements'));
}

    //visibité
    public function updateVisibilite(Request $request, Media $media)
{
    $this->autoriserAcces($media);

    $request->validate([
        'visibilite' => 'required|in:public,club,premium',
    ]);

    $media->update(['visibilite' => $request->visibilite]);

    return response()->json([
        'message'    => 'Visibilité mise à jour.',
        'visibilite' => $media->visibilite,
    ]);
}

    // ═══ Enregistrer média ═══
   public function store(Request $request)
{
    $request->validate([
        'type'         => 'required|in:photo,video',
    ]);

    $fileRules = $request->input('type') === 'photo'
        ? ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240']
        : ['required', 'file', 'mimetypes:video/mp4,video/webm,video/quicktime', 'max:102400'];

    $request->validate([
        'fichier'      => $fileRules,
        'titre'        => 'nullable|string|max:150',
        'description'  => 'nullable|string|max:5000',
        'visibilite'   => 'required|in:public,club,premium',
        'joueur_id'    => 'nullable|integer|exists:joueurs,id',
        'evenement_id' => 'nullable|integer|exists:evenements_agenda,id',
        'payant'       => 'boolean',
        'prix'         => 'nullable|required_if:payant,1|numeric|min:0',
    ]);

    $club   = Auth::user()->club;
    abort_unless($club, 403);

    if ($request->filled('joueur_id')) {
        abort_unless($club->joueurs()->whereKey($request->integer('joueur_id'))->exists(), 422);
    }
    if ($request->filled('evenement_id')) {
        abort_unless($club->evenements()->whereKey($request->integer('evenement_id'))->exists(), 422);
    }

    $chemin = $request->file('fichier')->store('medias/' . $club->id, 'media_private');

    $media = Media::create([
        'club_id'      => $club->id,
        'joueur_id'    => $request->joueur_id,
        'evenement_id' => $request->evenement_id,
        'type'         => $request->type,
        'chemin'       => $chemin,
        'titre'        => $request->titre,
        'description'  => $request->description,
        'visibilite'   => $request->visibilite,
        'payant'       => $request->boolean('payant'),
        'prix'         => $request->boolean('payant') ? $request->prix : null,
        'taille_ko'    => (int)($request->file('fichier')->getSize() / 1024),
    ]);

    return response()->json(['message' => 'Média ajouté.', 'id' => $media->id], 201);
}

    public function file(Request $request, Media $media)
    {
        abort_unless($this->peutConsulter($media), 404);

        $variant = $request->query('variant');
        abort_unless($variant === null || $variant === 'thumbnail', 404);
        $path = $variant === 'thumbnail' ? $media->miniature : $media->chemin;
        abort_unless($path, 404);

        $disk = Storage::disk('media_private');
        abort_unless($disk->exists($path), 404);

        $mime = mime_content_type($disk->path($path)) ?: 'application/octet-stream';
        abort_unless(
            in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'video/mp4', 'video/webm', 'video/quicktime'], true),
            404
        );

        return response()->file($disk->path($path), [
            'Content-Type' => $mime,
            'X-Content-Type-Options' => 'nosniff',
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
            'Cache-Control' => $media->visibilite === 'public' && !$media->payant
                ? 'public, max-age=300'
                : 'private, no-store',
        ]);
    }

    // ═══ Afficher média ═══
    public function show(Media $media)
    {
        $media->incrementerVues();
        return view('club.medias.show', compact('media'));
    }

    // ═══ Supprimer média ═══
    public function destroy(Media $media)
{
    $this->autoriserAcces($media);
    Storage::disk('media_private')->delete($media->chemin);
    if ($media->miniature) {
        Storage::disk('media_private')->delete($media->miniature);
    }
    $media->delete();
    return response()->json(['message' => 'Média supprimé.']);
}


    // ═══ Vérifier accès ═══
    private function autoriserAcces(Media $media): void
    {
        if ($media->club_id !== Auth::user()->club->id) {
            abort(403, 'Accès non autorisé.');
        }
    }

    private function peutConsulter(Media $media): bool
    {
        $user = Auth::user();
        $club = $user?->club;

        if ($club && $club->id === $media->club_id) {
            return true;
        }

        $premiumAccess = DB::table('club_supporter')
            ->where('supporter_id', $user?->supporter?->id)
            ->where('club_id', $media->club_id)
            ->where('type_abonnement', 'premium')
            ->where(fn ($query) => $query->whereNull('abonnement_expire_at')
                ->orWhere('abonnement_expire_at', '>=', now()))
            ->exists();

        if ($media->payant) {
            return $premiumAccess;
        }

        if ($media->visibilite === 'public') {
            return true;
        }

        if (!$user) {
            return false;
        }

        if ($media->visibilite === 'premium') {
            return $premiumAccess;
        }

        if ($user->joueur?->club_id === $media->club_id) {
            return true;
        }

        if ($user->supporter?->clubs()->where('clubs.id', $media->club_id)->exists()) {
            return true;
        }

        return ParentJoueur::query()
            ->where('user_id', $user->id)
            ->where('actif', true)
            ->whereHas('joueur', fn ($query) => $query->where('club_id', $media->club_id))
            ->exists();
    }
}