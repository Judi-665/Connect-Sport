<?php
// app/Http/Controllers/Club/CandidatureController.php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Candidature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CandidatureController extends Controller
{
    // ═══ Liste des candidatures reçues ═══
    public function index()
    {
        $club = Auth::user()->club;

        $candidatures = $club->candidatures()
                             ->with('joueur.user')
                             ->latest()
                             ->paginate(15);

        return view('club.candidatures.index', compact('candidatures'));
    }

    // ═══ Détail d'une candidature ═══
    public function show(Candidature $candidature)
    {
        if ($candidature->club_id !== Auth::user()->club->id) {
            abort(403);
        }

        $candidature->load('joueur.user');

        return view('club.candidatures.show', compact('candidature'));
    }

    // ═══ Accepter ═══
    public function accepter(Candidature $candidature)
    {
        if ($candidature->club_id !== Auth::user()->club->id) {
            abort(403);
        }

        if (!$candidature->estEnAttente()) {
            return back()->with('error', 'Cette candidature a déjà été traitée.');
        }

        $candidature->accepter(Auth::id());

        return back()->with('success', 'Candidature acceptée, le joueur a été rattaché au club.');
    }

    // ═══ Refuser ═══
    public function refuser(Request $request, Candidature $candidature)
    {
        if ($candidature->club_id !== Auth::user()->club->id) {
            abort(403);
        }

        if (!$candidature->estEnAttente()) {
            return back()->with('error', 'Cette candidature a déjà été traitée.');
        }

        $candidature->refuser(Auth::id(), $request->input('note_club'));

        return back()->with('success', 'Candidature refusée.');
    }
}