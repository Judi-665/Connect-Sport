<?php
// app/Http/Controllers/Club/MediaController.php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    // ═══ Liste des médias ═══
    public function index()
    {
        $club   = Auth::user()->club;
        $photos = $club->medias()->photos()->latest()->paginate(12, ['*'], 'photos');
        $videos = $club->medias()->videos()->latest()->paginate(8, ['*'], 'videos');

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
        'fichier'      => 'required|file|max:51200',
        'titre'        => 'nullable|string|max:150',
        'description'  => 'nullable|string',
        'visibilite'   => 'required|in:public,club,premium',
        'joueur_id'    => 'nullable|exists:joueurs,id',
        'evenement_id' => 'nullable|exists:evenements_agenda,id',
        'payant'       => 'boolean',
        'prix'         => 'nullable|required_if:payant,1|numeric|min:0',
    ]);

    $club   = Auth::user()->club;
    $chemin = $request->file('fichier')->store('medias/' . $club->id, 'public');

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
    Storage::disk('public')->delete($media->chemin);
    if ($media->miniature) {
        Storage::disk('public')->delete($media->miniature);
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
}