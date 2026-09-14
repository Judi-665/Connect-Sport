<?php
// app/Http/Controllers/Club/AgendaController.php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\EvenementAgenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgendaController extends Controller
{
    // ═══ Liste des événements ═══
            public function index(Request $request)
        {
            $club  = Auth::user()->club;
            $query = $club->evenements()->orderBy('debut_at');

            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }
            if ($request->filled('visibilite')) {
                $query->where('visibilite', $request->visibilite);
            }
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('titre', 'like', '%'.$request->search.'%')
                    ->orWhere('lieu', 'like', '%'.$request->search.'%')
                    ->orWhere('adversaire_nom', 'like', '%'.$request->search.'%');
                });
            }

            $aVenir = (clone $query)->aVenir()->get();
            $passes = (clone $query)->passes()->orderBy('debut_at', 'desc')->limit(10)->get();

            return view('club.agenda.index', compact('club', 'aVenir', 'passes'));
        }

    // ═══ Formulaire création ═══
    public function create()
    {
        $club = Auth::user()->club;
        return view('club.agenda.create', compact('club'));
    }

    // ═══ Enregistrer événement ═══
   public function store(Request $request)
{
    $request->validate([
        'titre'              => 'required|string|max:150',
        'type'               => 'required|in:match,entrainement,evenement',
        'description'        => 'nullable|string',
        'lieu'               => 'nullable|string|max:150',
        'debut_at'           => 'required|date',
        'fin_at'             => 'nullable|date|after:debut_at',
        'adversaire_nom'     => 'nullable|string|max:120',
        'domicile_exterieur' => 'nullable|in:domicile,exterieur,neutre',
        'visibilite'         => 'required|in:public,club,equipe',
    ]);

    $club      = Auth::user()->club;
    $evenement = $club->evenements()->create($request->only([
        'titre', 'type', 'description', 'lieu',
        'debut_at', 'fin_at', 'adversaire_nom',
        'domicile_exterieur', 'visibilite',
    ]));

    return response()->json([
        'message'   => 'Événement créé avec succès.',
        'evenement' => $evenement->id,
    ], 201);
}


    // ═══ Afficher événement ═══
    public function show(EvenementAgenda $evenement)
    {
        $this->autoriserAcces($evenement);
        $evenement->load('medias', 'statistiques.joueur.user');
        return view('club.agenda.show', compact('evenement'));
    }

    // ═══ Formulaire modification ═══
    public function edit(EvenementAgenda $evenement)
    {
        $this->autoriserAcces($evenement);
        return view('club.agenda.edit', compact('evenement'));
    }

    // ═══ Mettre à jour événement ═══
    public function update(Request $request, EvenementAgenda $evenement)
    {
        $this->autoriserAcces($evenement);

        $request->validate([
            'titre'              => 'required|string|max:150',
            'type'               => 'required|in:match,entrainement,evenement',
            'description'        => 'nullable|string',
            'lieu'               => 'nullable|string|max:150',
            'debut_at'           => 'required|date',
            'fin_at'             => 'nullable|date|after:debut_at',
            'adversaire_nom'     => 'nullable|string|max:120',
            'domicile_exterieur' => 'nullable|in:domicile,exterieur,neutre',
            'visibilite'         => 'required|in:public,club,equipe',
            'score_nous'         => 'nullable|integer|min:0',
            'score_eux'          => 'nullable|integer|min:0',
            'resultat'           => 'nullable|in:victoire,defaite,nul',
        ]);

        $evenement->update($request->only([
            'titre', 'type', 'description', 'lieu',
            'debut_at', 'fin_at', 'adversaire_nom',
            'domicile_exterieur', 'visibilite',
            'score_nous', 'score_eux', 'resultat',
        ]));

        return redirect()->route('club.agenda.show', $evenement)
                         ->with('success', 'Événement mis à jour !');
    }

    // ═══ Supprimer événement ═══
   public function destroy(EvenementAgenda $evenement)
{
    $this->autoriserAcces($evenement);
    $evenement->delete();
    return response()->json(['message' => 'Événement supprimé.']);
}

    // ═══ Vérifier accès ═══
    private function autoriserAcces(EvenementAgenda $evenement): void
    {
        if ($evenement->club_id !== Auth::user()->club->id) {
            abort(403, 'Accès non autorisé.');
        }
    }
}