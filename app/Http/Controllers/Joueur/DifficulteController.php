<?php
// app/Http/Controllers/Joueur/DifficulteController.php

namespace App\Http\Controllers\Joueur;

use App\Http\Controllers\Controller;
use App\Models\DifficulteVoeu;
use App\Models\Joueur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DifficulteController extends Controller
{
    // ═══ Lister difficultés et voeux ═══
    public function index()
    {
        $joueur = Auth::user()->joueur;

        $difficultes = $joueur->difficultesVoeux()
                              ->difficultes()
                              ->orderBy('priorite', 'asc')
                              ->paginate(10);

        $voeux = $joueur->difficultesVoeux()
                        ->voeux()
                        ->orderBy('priorite', 'asc')
                        ->paginate(10);

        $stats = [
            'difficultes'    => $joueur->difficultesVoeux()->difficultes()->count(),
            'voeux'          => $joueur->difficultesVoeux()->voeux()->count(),
            'resouls'        => $joueur->difficultesVoeux()->resolus()->count(),
            'non_resoulus'   => $joueur->difficultesVoeux()->nonResolus()->count(),
        ];

        return view('joueur.difficultes.index', compact('difficultes', 'voeux', 'stats'));
    }

    // ═══ Afficher une difficulté/voeu ═══
    public function show(DifficulteVoeu $difficulte)
    {
        $this->authorize('view', $difficulte);
        return view('joueur.difficultes.show', compact('difficulte'));
    }

    // ═══ Créer difficulté ═══
    public function createDifficulte()
    {
        return view('joueur.difficultes.create-difficulte');
    }

    // ═══ Stocker difficulté ═══
    public function storeDifficulte(Request $request)
    {
        $joueur = Auth::user()->joueur;

        $validated = $request->validate([
            'titre'           => 'required|string|max:200',
            'contenu'         => 'required|string',
            'categorie'       => 'required|string',
            'priorite'        => 'required|in:basse,normale,haute,critique',
            'visible_club'    => 'sometimes|boolean',
            'visible_agent'   => 'sometimes|boolean',
        ]);

        $validated['joueur_id'] = $joueur->id;
        $validated['type'] = 'difficulte';
        $validated['resolu'] = false;

        DifficulteVoeu::create($validated);

        return redirect()->route('joueur.difficultes.index')
                        ->with('success', 'Difficulté signalée');
    }

    // ═══ Créer voeu ═══
    public function createVoeu()
    {
        return view('joueur.difficultes.create-voeu');
    }

    // ═══ Stocker voeu ═══
    public function storeVoeu(Request $request)
    {
        $joueur = Auth::user()->joueur;

        $validated = $request->validate([
            'titre'           => 'required|string|max:200',
            'contenu'         => 'required|string',
            'priorite'        => 'required|in:basse,normale,haute',
            'visible_club'    => 'sometimes|boolean',
            'visible_agent'   => 'sometimes|boolean',
        ]);

        $validated['joueur_id'] = $joueur->id;
        $validated['type'] = 'voeu';
        $validated['resolu'] = false;

        DifficulteVoeu::create($validated);

        return redirect()->route('joueur.difficultes.index')
                        ->with('success', 'Voeu ajouté');
    }

    // ═══ Marquer comme résolu ═══
    public function marquerResolu(Request $request, DifficulteVoeu $difficulte)
    {
        $this->authorize('update', $difficulte);

        $validated = $request->validate([
            'note_resolution' => 'nullable|string|max:500',
        ]);

        $difficulte->marquerResolu($validated['note_resolution'] ?? null);

        return redirect()->route('joueur.difficultes.index')
                        ->with('success', 'Difficulté résolue');
    }

    // ═══ Supprimer ═══
    public function destroy(DifficulteVoeu $difficulte)
    {
        $this->authorize('delete', $difficulte);
        $difficulte->delete();

        return redirect()->route('joueur.difficultes.index')
                        ->with('success', 'Supprimé');
    }
}
