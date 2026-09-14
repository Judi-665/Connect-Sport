<?php
// app/Http/Controllers/Club/EquipeController.php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Equipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipeController extends Controller
{
    // ═══ Liste des équipes ═══
    public function index()
    {
        $club    = Auth::user()->club;
        $equipes = $club->equipes()->actif()->orderBy('categorie')
                ->with(['joueurs.licenceActive'])
                ->get();

        return view('club.equipes.index', compact('club', 'equipes'));
    }

    // ═══ Formulaire création ═══
    public function create()
    {
        $club = Auth::user()->club;
        return view('club.equipes.create', compact('club'));
    }

    // ═══ Enregistrer équipe ═══
    public function store(Request $request)
    {
        $request->validate([
            'nom'         => 'required|string|max:100',
            'genre'       => 'required|in:masculin,feminin,mixte',
            'categorie'   => 'required|in:junior,cadet,senior,veteran,autre',
            'description' => 'nullable|string',
        ]);

        $club = Auth::user()->club;

        $club->equipes()->create([
            'nom'         => $request->nom,
            'genre'       => $request->genre,
            'categorie'   => $request->categorie,
            'description' => $request->description,
        ]);

        return redirect()->route('club.equipes.index')
                         ->with('success', 'Équipe créée avec succès !');
    }

    // ═══ Afficher équipe ═══
    public function show(Equipe $equipe)
    {
        $this->autoriserAcces($equipe);
        $joueurs = $equipe->joueurs()->with('user')->get();

        return view('club.equipes.show', compact('equipe', 'joueurs'));
    }

    // ═══ Formulaire modification ═══
    public function edit(Equipe $equipe)
    {
        $this->autoriserAcces($equipe);
        return view('club.equipes.edit', compact('equipe'));
    }

    // ═══ Mettre à jour équipe ═══
    public function update(Request $request, Equipe $equipe)
    {
        $this->autoriserAcces($equipe);

        $request->validate([
            'nom'         => 'required|string|max:100',
            'genre'       => 'required|in:masculin,feminin,mixte',
            'categorie'   => 'required|in:junior,cadet,senior,veteran,autre',
            'description' => 'nullable|string',
        ]);

        $equipe->update($request->only('nom', 'genre', 'categorie', 'description'));

        return redirect()->route('club.equipes.index')
                         ->with('success', 'Équipe mise à jour !');
    }

    // ═══ Supprimer équipe ═══
    public function destroy(Equipe $equipe)
    {
        $this->autoriserAcces($equipe);
        $equipe->update(['actif' => false]);

        return redirect()->route('club.equipes.index')
                         ->with('success', 'Équipe désactivée.');
    }

    // ═══ Vérifier que l'équipe appartient au club connecté ═══
    private function autoriserAcces(Equipe $equipe): void
    {
        if ($equipe->club_id !== Auth::user()->club->id) {
            abort(403, 'Accès non autorisé.');
        }
    }
}