<?php
// app/Http/Controllers/Joueur/CandidatureController.php

namespace App\Http\Controllers\Joueur;

use App\Http\Controllers\Controller;
use App\Models\Candidature;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CandidatureController extends Controller
{
    // ═══ Mes candidatures ═══
    public function indexJoueur()
    {
        $joueur = Auth::user()->joueur;

        $candidatures = $joueur->candidatures()
                               ->with('club')
                               ->latest()
                               ->paginate(15);

        return view('joueur.candidatures.index', compact('candidatures'));
    }

    // ═══ Formulaire de candidature vers un club ═══
    public function create(Club $club)
    {
        $joueur = Auth::user()->joueur;

        if ($joueur->club_id) {
            return back()->with('error', 'Vous appartenez déjà à un club.');
        }

        $dejaEnAttente = $joueur->candidatures()
                                ->where('club_id', $club->id)
                                ->where('statut', 'en_attente')
                                ->exists();

        if ($dejaEnAttente) {
            return back()->with('error', 'Vous avez déjà une candidature en attente pour ce club.');
        }

        return view('joueur.candidatures.create', compact('club'));
    }

    // ═══ Envoyer la candidature ═══
    public function store(Request $request, Club $club)
    {
        $joueur = Auth::user()->joueur;

        if ($joueur->club_id) {
            return back()->with('error', 'Vous appartenez déjà à un club.');
        }

        $request->validate([
            'poste_propose' => 'nullable|string|max:60',
            'message'       => 'nullable|string|max:1000',
        ]);

        Candidature::create([
            'joueur_id'     => $joueur->id,
            'club_id'       => $club->id,
            'poste_propose' => $request->poste_propose,
            'message'       => $request->message,
            'statut'        => 'en_attente',
        ]);

        return redirect()->route('joueur.candidatures.index')
                         ->with('success', 'Candidature envoyée au club.');
    }

    // ═══ Annuler une candidature en attente ═══
    public function annuler(Candidature $candidature)
    {
        $joueur = Auth::user()->joueur;

        if ($candidature->joueur_id !== $joueur->id) {
            abort(403);
        }

        if (!$candidature->estEnAttente()) {
            return back()->with('error', 'Cette candidature ne peut plus être annulée.');
        }

        $candidature->update(['statut' => 'annulee']);

        return back()->with('success', 'Candidature annulée.');
    }
}