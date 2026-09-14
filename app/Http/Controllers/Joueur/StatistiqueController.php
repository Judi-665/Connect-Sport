<?php
// app/Http/Controllers/Joueur/StatistiqueController.php

namespace App\Http\Controllers\Joueur;

use App\Http\Controllers\Controller;
use App\Models\EvenementAgenda;
use App\Models\Joueur;
use App\Models\Statistique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatistiqueController extends Controller
{
    // ═══ Stats d'un joueur ═══
    public function index(Joueur $joueur)
    {
        $saisons = $joueur->statistiques()
                          ->valides()
                          ->select('saison')
                          ->distinct()
                          ->pluck('saison');

        $saisonSelectionnee = request('saison', $saisons->first());

        $stats = $joueur->statistiques()
                        ->parSaison($saisonSelectionnee)
                        ->valides()
                        ->with('evenement')
                        ->get();

        return view('joueur.stats.index', compact(
            'joueur',
            'stats',
            'saisons',
            'saisonSelectionnee'
        ));
    }

    // ═══ Saisir stats après un match (club) ═══
    public function create(EvenementAgenda $evenement)
    {
        $club    = Auth::user()->club;
        $joueurs = $club->joueurs()->with('user')->get();

        return view('club.stats.create', compact('evenement', 'joueurs'));
    }

    // ═══ Enregistrer stats ═══
    public function store(Request $request, EvenementAgenda $evenement)
    {
        $request->validate([
            'joueur_id'          => 'required|exists:joueurs,id',
            'matchs_joues'       => 'required|integer|min:0',
            'matchs_titulaire'   => 'nullable|integer|min:0',
            'matchs_remplacant'  => 'nullable|integer|min:0',
            'minutes_jouees'     => 'nullable|integer|min:0',
            'buts'               => 'nullable|integer|min:0',
            'passes_decisives'   => 'nullable|integer|min:0',
            'cartons_jaunes'     => 'nullable|integer|min:0',
            'cartons_rouges'     => 'nullable|integer|min:0',
            'note_moyenne'       => 'nullable|numeric|min:0|max:10',
        ]);

        $club   = Auth::user()->club;
        $saison = date('Y') . '-' . (date('Y') + 1);

        Statistique::updateOrCreate(
            [
                'joueur_id'    => $request->joueur_id,
                'evenement_id' => $evenement->id,
            ],
            [
                'club_id'            => $club->id,
                'saison'             => $saison,
                'matchs_joues'       => $request->matchs_joues,
                'matchs_titulaire'   => $request->matchs_titulaire ?? 0,
                'matchs_remplacant'  => $request->matchs_remplacant ?? 0,
                'minutes_jouees'     => $request->minutes_jouees ?? 0,
                'buts'               => $request->buts ?? 0,
                'passes_decisives'   => $request->passes_decisives ?? 0,
                'cartons_jaunes'     => $request->cartons_jaunes ?? 0,
                'cartons_rouges'     => $request->cartons_rouges ?? 0,
                'note_moyenne'       => $request->note_moyenne,
            ]
        );

        return redirect()->route('club.agenda.show', $evenement)
                         ->with('success', 'Statistiques enregistrées !');
    }

    // ═══ Valider les stats (club) ═══
    public function valider(Statistique $statistique)
    {
        if ($statistique->club_id !== Auth::user()->club->id) {
            abort(403);
        }

        $statistique->valider();

        return back()->with('success', 'Statistiques validées !');
    }
}