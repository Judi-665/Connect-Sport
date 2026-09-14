<?php
// app/Http/Controllers/Joueur/JoueurController.php

namespace App\Http\Controllers\Joueur;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Joueur;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class JoueurController extends Controller
{
    // ═══ Dashboard joueur ═══
    public function dashboard()
    {
        /** @var User $user */
        $user   = Auth::user();
        $joueur = $user->joueur;

        if (!$joueur) {
            return redirect()->route('joueur.profil.create')
                             ->with('info', 'Veuillez créer votre profil joueur.');
        }

        $stats = $joueur->statistiques()
                        ->saisonEnCours()
                        ->valides()
                        ->first();

        $licenceActive     = $joueur->licenceActive;
        $prochainEvenement = $joueur->club?->evenements()
                                          ->aVenir()
                                          ->first();
        $evenements = $joueur->club?->evenements()
                              ->aVenir()
                              ->take(5)
                              ->get() ?? collect();
        $notifications = Notification::where('notifiable_type', User::class)
                                     ->where('notifiable_id', $user->id)
                                     ->latest()
                                     ->take(5)
                                     ->get();

        return view('joueur.dashboard', compact(
            'joueur',
            'stats',
            'licenceActive',
            'prochainEvenement',
            'evenements',
            'notifications'
        ));
    }
public function locked(Request $request)
{
    $plan = $request->get('plan', 'standard');
    $fonction = $request->get('fonction', '');
    return view('joueur.locked', compact('plan', 'fonction'));
}

    public function abonnement()
    {
        $joueur = Auth::user()->joueur;

        return view('joueur.abonnement.index', [
            'joueur' => $joueur,
            'plan' => $joueur?->plan ?? 'free',
        ]);
    }

    // ═══ Afficher profil public ═══
    public function index(Request $request)
{
    $query = Joueur::with('user');

    // Filtre recherche par nom/prénom
    if ($request->filled('search')) {
        $query->whereHas('user', function($q) use ($request) {
            $q->where('name', 'like', '%'.$request->search.'%')
              ->orWhere('prenom', 'like', '%'.$request->search.'%');
        });
    }

    // Filtre par poste
    if ($request->filled('poste')) {
        $query->where('poste', 'like', '%'.$request->poste.'%');
    }

    // Filtre par pays
    if ($request->filled('pays')) {
        $query->where('nationalite', 'like', '%'.$request->pays.'%');
    }

    // Par défaut, on n'affiche que les joueurs "visibles recruteur" ou tous ?
    // On laisse le scope visibleRecruteur si tu veux.
    $joueurs = $query->paginate(12);

    return view('joueurs.index', compact('joueurs'));
}

// ═══ Détail public d’un joueur (déjà existant mais on s’assure qu’il est bien public) ═══
public function show(Joueur $joueur)
{
    $joueur->load('user', 'club', 'equipe', 'statistiques');
    return view('joueurs.show', compact('joueur'));
}

    // ═══ Formulaire création profil ═══
    public function create()
    {
        $clubs   = Club::actif()->with('sport')->get();
        $equipes = collect();

        return view('joueur.create', compact('clubs', 'equipes'));
    }

    // ═══ Enregistrer profil ═══
    public function store(Request $request)
    {
        $request->validate([
            'poste'          => 'nullable|string|max:60',
            'categorie'      => 'required|in:junior,cadet,senior,veteran',
            'date_naissance' => 'nullable|date|before:today',
            'nationalite'    => 'nullable|string|max:60',
            'telephone'      => 'nullable|string|max:20',
            'ville'          => 'nullable|string|max:100',
            'bio'            => 'nullable|string|max:1000',
            'club_id'        => 'nullable|exists:clubs,id',
            'equipe_id'      => 'nullable|exists:equipes,id',
            'avatar'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Upload avatar
        if ($request->hasFile('avatar')) {
            /** @var User $user */
            $user       = Auth::user();
            $avatarPath = $request->file('avatar')->store('avatars/joueurs', 'public');

            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->avatar = $avatarPath;
            $user->save();
        }

        Joueur::create([
            'user_id'        => Auth::id(),
            'club_id'        => $request->club_id,
            'equipe_id'      => $request->equipe_id,
            'poste'          => $request->poste,
            'categorie'      => $request->categorie,
            'date_naissance' => $request->date_naissance,
            'nationalite'    => $request->nationalite,
            'telephone'      => $request->telephone,
            'ville'          => $request->ville,
            'bio'            => $request->bio,
            'sans_club'      => $request->club_id ? 0 : 1,
        ]);

        return redirect()->route('joueur.dashboard')
                         ->with('success', 'Profil joueur créé avec succès !');
    }

    // ═══ Formulaire modification ═══
    public function edit()
    {
        /** @var User $user */
        $user   = Auth::user();
        $joueur = $user->joueur;

        $clubs   = Club::actif()->with('sport')->get();
        $equipes = $joueur->club
            ? $joueur->club->equipes()->get()
            : collect();

        return view('joueur.edit', compact('joueur', 'clubs', 'equipes'));
    }

    // ═══ Mettre à jour profil ═══
    public function update(Request $request)
    {
        /** @var User $user */
        $user   = Auth::user();
        $joueur = $user->joueur;

        $request->validate([
            'poste'             => 'nullable|string|max:60',
            'categorie'         => 'required|in:junior,cadet,senior,veteran',
            'date_naissance'    => 'nullable|date|before:today',
            'nationalite'       => 'nullable|string|max:60',
            'telephone'         => 'nullable|string|max:20',
            'ville'             => 'nullable|string|max:100',
            'bio'               => 'nullable|string|max:1000',
            'club_id'           => 'nullable|exists:clubs,id',
            'equipe_id'         => 'nullable|exists:equipes,id',
            'visible_recruteur' => 'boolean',
            'avatar'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Upload avatar
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars/joueurs', 'public');

            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->avatar = $avatarPath;
            $user->save();
        }

        $joueur->update([
            'club_id'           => $request->club_id,
            'equipe_id'         => $request->equipe_id,
            'poste'             => $request->poste,
            'categorie'         => $request->categorie,
            'date_naissance'    => $request->date_naissance,
            'nationalite'       => $request->nationalite,
            'telephone'         => $request->telephone,
            'ville'             => $request->ville,
            'bio'               => $request->bio,
            'sans_club'         => $request->club_id ? 0 : 1,
            'visible_recruteur' => $request->boolean('visible_recruteur'),
        ]);

        return redirect()->route('joueur.profil')
                         ->with('success', 'Profil mis à jour avec succès !');
    }

    // ═══ Liste joueurs sans club (recrutement) ═══
    public function sansClub()
{
    $joueurs = Joueur::sansClub()
                    ->visibleRecruteur()
                    ->with('user')
                    ->paginate(20);

    return view('joueurs.index', compact('joueurs'));
}


}