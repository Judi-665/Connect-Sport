<?php
// app/Http/Controllers/Parent/ParentController.php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\ParentJoueur;
use App\Models\Joueur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentController extends Controller
{
    // ═══ Dashboard parent ═══
    public function dashboard()
    {
        $parent = Auth::user()->parentJoueur;

        if (!$parent) {
            return redirect()->route('parent.create')
                             ->with('info', 'Veuillez configurer votre profil parent');
        }

        $joueurs = $parent->joueurs()
                         ->with('club', 'equipe')
                         ->get();

        return view('parent.dashboard', compact('parent', 'joueurs'));
    }

    // ═══ Créer profil parent ═══
    public function create()
    {
        return view('parent.create');
    }

    // ═══ Enregistrer profil ═══
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'lien'            => 'required|in:pere,mere,tuteur,tutrice',
            'joueur_id'       => 'required|exists:joueurs,id',
            'acces_stats'     => 'sometimes|boolean',
            'acces_agenda'    => 'sometimes|boolean',
        ]);

        $existant = ParentJoueur::where('user_id', $user->id)
                               ->where('joueur_id', $validated['joueur_id'])
                               ->exists();

        if ($existant) {
            return back()->with('error', 'Vous êtes déjà lié à ce joueur');
        }

        ParentJoueur::create(array_merge($validated, [
            'user_id' => $user->id,
            'actif'   => true,
        ]));

        return redirect()->route('parent.dashboard')
                        ->with('success', 'Profil parent créé');
    }

    // ═══ Ajouter un autre joueur ═══
    public function addJoueur()
    {
        $joueurs = Joueur::all();
        return view('parent.add-joueur', compact('joueurs'));
    }

    // ═══ Confirmer joueur ═══
    public function confirmJoueur(Request $request)
    {
        $parent = Auth::user()->parentJoueur;

        $validated = $request->validate([
            'joueur_id' => 'required|exists:joueurs,id',
            'lien'      => 'required|in:pere,mere,tuteur,tutrice',
        ]);

        $existant = ParentJoueur::where('user_id', Auth::id())
                               ->where('joueur_id', $validated['joueur_id'])
                               ->exists();

        if ($existant) {
            return back()->with('error', 'Vous êtes déjà lié à ce joueur');
        }

        ParentJoueur::create(array_merge($validated, [
            'user_id' => Auth::id(),
            'actif'   => true,
        ]));

        return back()->with('success', 'Joueur ajouté');
    }

    // ═══ Statistiques joueur suivi ═══
    public function statJoueur(Joueur $joueur)
    {
        $parent = Auth::user()->parentJoueur;

        // Vérifier que le parent est bien lié au joueur
        if (!$parent || !$parent->joueurs()->where('joueur_id', $joueur->id)->exists()) {
            abort(403);
        }

        $stats = $joueur->statistiques()
                       ->saisonEnCours()
                       ->first();

        $licenceActive = $joueur->licences()->active()->first();
        $club = $joueur->club;
        $evenements = $club?->evenements()->aVenir()->limit(5)->get();

        return view('parent.stats-joueur', compact('joueur', 'stats', 'licenceActive', 'evenements'));
    }

    // ═══ Agenda joueur ═══
    public function agendaJoueur(Joueur $joueur)
    {
        $parent = Auth::user()->parentJoueur;

        if (!$parent || !$parent->joueurs()->where('joueur_id', $joueur->id)->exists()) {
            abort(403);
        }

        $evenements = $joueur->club?->evenements()
                                    ->orderBy('debut_at', 'asc')
                                    ->get();

        return view('parent.agenda-joueur', compact('joueur', 'evenements'));
    }

    // ═══ Modifier accès ═══
    public function modifierAcces(Request $request, ParentJoueur $parent)
    {
        $this->authorize('update', $parent);

        $validated = $request->validate([
            'acces_stats'  => 'sometimes|boolean',
            'acces_agenda' => 'sometimes|boolean',
        ]);

        $parent->update($validated);

        return back()->with('success', 'Accès modifié');
    }

    // ═══ Désactiver lien ═══
    public function removeJoueur(ParentJoueur $parent)
    {
        $this->authorize('delete', $parent);
        $parent->delete();

        return redirect()->route('parent.dashboard')
                        ->with('success', 'Lien supprimé');
    }
}
