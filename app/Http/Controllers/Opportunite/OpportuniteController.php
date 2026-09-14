<?php
// app/Http/Controllers/Opportunite/OpportuniteController.php

namespace App\Http\Controllers\Opportunite;

use App\Http\Controllers\Controller;
use App\Models\Opportunite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpportuniteController extends Controller
{
    // ═══ Lister les opportunités ═══
    public function index(Request $request)
{
    $query = Opportunite::active()
                        ->nonExpirees()
                        ->with('club');

    // Filtre par mot-clé (titre, description, poste)
    if ($request->filled('search')) {
        $query->where(function($q) use ($request) {
            $q->where('titre', 'like', '%'.$request->search.'%')
              ->orWhere('description', 'like', '%'.$request->search.'%')
              ->orWhere('poste_cible', 'like', '%'.$request->search.'%');
        });
    }

    // Filtre par type (recrutement, selection, bourse, stage)
    if ($request->filled('type')) {
        $query->where('type', $request->type);
    }

    // Filtre par pays
    if ($request->filled('pays')) {
        $query->where('pays', 'like', '%'.$request->pays.'%');
    }

    $opportunites = $query->orderBy('mise_en_avant', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate(12); // 12 au lieu de 20 pour meilleure intégration

    $filtres = [
        'types'      => ['recrutement', 'selection', 'bourse', 'stage'],
        'categories' => ['jeunes', 'professionnels', 'tous'],
    ];

    return view('opportunites.index', compact('opportunites', 'filtres'));
}

    // ═══ Filtrer par type ═══
    public function parType(string $type)
    {
        $opportunites = Opportunite::active()
                                   ->nonExpirees()
                                   ->parType($type)
                                   ->paginate(20);

        return view('opportunites.index', compact('opportunites'));
    }

    // ═══ Afficher opportunité ═══
    public function show(Opportunite $opportunite)
    {
        $opportunite->incrementerVues();
        $opportunite->load('club.user');
        
        return view('opportunites.show', compact('opportunite'));
    }

    // ═══ Mes opportunités (club) ═══
   public function indexClub(Request $request)
{
    $club  = Auth::user()->club;
    $query = $club->opportunites()->orderBy('created_at', 'desc');

    if ($request->filled('type')) {
        $query->where('type', $request->type);
    }

    if ($request->filled('statut')) {
        match($request->statut) {
            'active'  => $query->where('active', true)->where(function($q) {
                            $q->whereNull('date_limite')
                              ->orWhere('date_limite', '>=', now());
                         }),
            'expiree' => $query->where('date_limite', '<', now()),
            'avant'   => $query->where('mise_en_avant', true),
            default   => null,
        };
    }

    if ($request->filled('search')) {
        $query->where(function($q) use ($request) {
            $q->where('titre', 'like', '%'.$request->search.'%')
              ->orWhere('poste_cible', 'like', '%'.$request->search.'%')
              ->orWhere('sport_cible', 'like', '%'.$request->search.'%');
        });
    }

    $opportunites = $query->paginate(15);

    return view('club.opportunites.index', compact('club', 'opportunites'));
}

    // ═══ Créer opportunité ═══
    public function create()
    {
        return view('club.opportunites.create');
    }

    // ═══ Enregistrer ═══
    public function store(Request $request)
{
    $club      = Auth::user()->club;
    $validated = $request->validate([
        'titre'              => 'required|string|max:200',
        'type'               => 'required|in:recrutement,selection,bourse,stage',
        'description'        => 'required|string',
        'lieu'               => 'required|string|max:200',
        'pays'               => 'required|string|max:100',
        'budget'             => 'nullable|numeric|min:0',
        'devise'             => 'required|string|max:3',
        'categorie_cible'    => 'required|string|max:100',
        'poste_cible'        => 'nullable|string|max:100',
        'sport_cible'        => 'required|string|max:100',
        'places_disponibles' => 'nullable|integer|min:1',
        'date_limite'        => 'required|date|after_or_equal:today',
        'mise_en_avant'      => 'sometimes|boolean',
    ]);

    $validated['club_id'] = $club->id;
    $validated['user_id'] = Auth::id();
    $validated['active']  = true;

    $opportunite = Opportunite::create($validated);

    return response()->json(['message' => 'Opportunité créée.', 'id' => $opportunite->id], 201);
}


    // ═══ Éditer ═══
    public function edit(Opportunite $opportunite)
    {
        $this->authorize('update', $opportunite);
        return view('opportunites.edit', compact('opportunite'));
    }

    // ═══ Mettre à jour ═══
    public function update(Request $request, Opportunite $opportunite)
{
    $this->authorize('update', $opportunite);

    $validated = $request->validate([
        'titre'              => 'required|string|max:200',
        'type'               => 'required|in:recrutement,selection,bourse,stage',
        'description'        => 'required|string',
        'lieu'               => 'required|string|max:200',
        'pays'               => 'required|string|max:100',
        'budget'             => 'nullable|numeric|min:0',
        'devise'             => 'required|string|max:3',
        'categorie_cible'    => 'required|string|max:100',
        'poste_cible'        => 'nullable|string|max:100',
        'sport_cible'        => 'required|string|max:100',
        'places_disponibles' => 'nullable|integer|min:1',
        'date_limite'        => 'required|date|after_or_equal:today',
        'active'             => 'sometimes|boolean',
        'mise_en_avant'      => 'sometimes|boolean',
    ]);

    $validated['active']        = $request->boolean('active');
    $validated['mise_en_avant'] = $request->boolean('mise_en_avant');

    $opportunite->update($validated);

    return response()->json(['message' => 'Opportunité mise à jour.']);
}
    // ═══ Supprimer ═══
    public function destroy(Opportunite $opportunite)
    {
         $this->authorize('delete', $opportunite);
        $opportunite->delete();
        return response()->json(['message' => 'Opportunité supprimée.']);
    }

    // ═══ Candidater ═══
    public function candidater(Request $request, Opportunite $opportunite)
    {
        $joueur = Auth::user()->joueur;

        if (!$joueur) {
            return redirect()->route('joueur.profil.create');
        }

        $existant = $opportunite->candidatures()
                               ->where('joueur_id', $joueur->id)
                               ->exists();

        if ($existant) {
            return back()->with('warning', 'Vous avez déjà candidaté');
        }

        $opportunite->candidatures()->attach($joueur->id, [
            'candidature_at' => now(),
            'statut' => 'en_attente',
        ]);

        return back()->with('success', 'Candidature envoyée');
    }
}
