<?php

namespace App\Http\Controllers\Joueur;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AgendaController extends Controller
{
    public function index()
    {
        $joueur = Auth::user()->joueur;
        $club   = $joueur?->club;

        if (!$club) {
            return view('joueur.agenda.index', [
                'club'      => null,
                'prochains' => collect(),
                'resultats' => collect(),
            ]);
        }

        $prochains = $club->evenements()
                          ->aVenir()
                          ->orderBy('debut_at')
                          ->paginate(9, ['*'], 'prochains');

        $resultats = $club->evenements()
                          ->where('debut_at', '<', now())
                          ->orderByDesc('debut_at')
                          ->paginate(9, ['*'], 'resultats');

        return view('joueur.agenda.index', compact('club', 'prochains', 'resultats'));
    }

    public function show(\App\Models\EvenementAgenda $evenement)
{
    $joueur = Auth::user()->joueur;

    abort_if(!$joueur || $evenement->club_id !== $joueur->club_id, 403,
        "Cet événement n'appartient pas à votre club.");

    $evenement->load('medias', 'statistiques.joueur.user', 'club');

    return view('joueur.agenda.show', compact('evenement'));
}
}