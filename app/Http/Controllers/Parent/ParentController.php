<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Joueur;
use App\Models\ParentJoueur;
use App\Models\DifficulteVoeu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ParentController extends Controller
{
    // ═══ Dashboard : liste des enfants liés ═══
    public function dashboard()
    {
        $user = $this->authenticatedUser();
        $liens = $user->parentsJoueurs()
                     ->actif()
                     ->whereNotNull('joueur_id')
                     ->has('joueur')
                     ->with([
                         'joueur.club',
                         'joueur.equipe',
                         'joueur.user',
                         'joueur.licenceActive',
                     ])
                     ->get();

        $clubsEnfants = $liens->map(fn ($lien) => $lien->joueur?->club)
            ->filter()
            ->unique('id')
            ->values();

        // KPI globaux
        $stats = [
            'enfants'          => $liens->count(),
            'avec_club'        => $liens->filter(fn($l) => $l->joueur?->club_id)->count(),
            'licences_actives' => $liens->filter(fn($l) => $l->joueur?->licenceActive)->count(),
        ];

        return view('parent.dashboard', compact('liens', 'clubsEnfants', 'stats'));
    }

    // ═══ Recherche d'un enfant à lier ═══
    public function addJoueur(Request $request)
    {
        $user = $this->authenticatedUser();
        $dejaLies = $user->parentsJoueurs()
                         ->whereNotNull('joueur_id')
                         ->pluck('joueur_id')
                         ->filter()
                         ->toArray();

        $query = Joueur::query()
                       ->with(['user', 'club', 'equipe', 'licenceActive'])
                       ->whereNotIn('id', $dejaLies);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('prenom', 'like', "%{$search}%")
                       ->orWhereRaw("CONCAT(COALESCE(prenom, ''), ' ', COALESCE(name, '')) LIKE ?", ["%{$search}%"])
                       ->orWhereRaw("CONCAT(COALESCE(name, ''), ' ', COALESCE(prenom, '')) LIKE ?", ["%{$search}%"]);
                })
                ->orWhereHas('club', function ($cq) use ($search) {
                    $cq->where('nom', 'like', "%{$search}%");
                })
                ->orWhere('poste', 'like', "%{$search}%")
                ->orWhere('telephone', 'like', "%{$search}%");
            });

            $resultats = $query->limit(20)->get();
        } else {
            // Afficher les joueurs récents pour faciliter la sélection immédiate
            $resultats = $query->latest()->limit(12)->get();
        }

        return view('parent.add-joueur', compact('resultats'));
    }

    // ═══ Créer le lien parent → enfant ═══
    public function confirmJoueur(Request $request)
    {
        $validated = $request->validate([
            'joueur_id' => 'required|integer|exists:joueurs,id',
            'lien'      => 'required|in:pere,mere,tuteur,autre',
        ]);

        $existant = ParentJoueur::where('user_id', Auth::id())
            ->where('joueur_id', $validated['joueur_id'])
            ->first();

        if ($existant?->actif) {
            return back()->with('error', 'Vous êtes déjà lié à ce joueur.');
        }

        ParentJoueur::updateOrCreate(
            ['user_id' => Auth::id(), 'joueur_id' => $validated['joueur_id']],
            [
                'lien' => $validated['lien'],
                'acces_stats' => false,
                'acces_agenda' => false,
                'actif' => false,
            ]
        );

        return redirect()->route('parent.dashboard')
                         ->with('success', 'Demande envoyée. Le joueur doit confirmer le lien avant tout accès.');
    }

    // ═══ Profil complet de l'enfant ═══
    public function showJoueur(Joueur $joueur)
    {
        $lien = $this->lienActif($joueur);

        $joueur->load('user', 'club', 'equipe', 'licenceActive');

        $licenceActive = $joueur->licenceActive;

        $statsSaison = $joueur->statistiques()
                              ->saisonEnCours()
                              ->valides()
                              ->get();

        $aggregats = [
            'matchs_joues'     => $statsSaison->sum('matchs_joues'),
            'buts'             => $statsSaison->sum('buts'),
            'passes_decisives' => $statsSaison->sum('passes_decisives'),
            'minutes_jouees'   => $statsSaison->sum('minutes_jouees'),
        ];

        // Prochain événement du club
        $prochainMatch = null;
        if ($joueur->club) {
            $prochainMatch = $joueur->club->evenements()
                ->whereIn('visibilite', ['public', 'club'])
                ->aVenir()
                ->first();
        }

        return view('parent.joueur-profil', compact('joueur', 'lien', 'licenceActive', 'aggregats', 'prochainMatch'));
    }

    // ═══ Statistiques de l'enfant ═══
    public function statJoueur(Joueur $joueur)
    {
        $lien = $this->lienActif($joueur);
        abort_unless($lien->acces_stats, 403, "Vous n'avez pas accès aux statistiques de ce joueur.");

        $statsSaison = $joueur->statistiques()
                              ->valides()
                              ->with('evenement')
                              ->latest()
                              ->get();

        $aggregats = [
            'matchs_joues'     => $statsSaison->sum('matchs_joues'),
            'buts'             => $statsSaison->sum('buts'),
            'passes_decisives' => $statsSaison->sum('passes_decisives'),
            'minutes_jouees'   => $statsSaison->sum('minutes_jouees'),
            'cartons_jaunes'   => $statsSaison->sum('cartons_jaunes'),
            'cartons_rouges'   => $statsSaison->sum('cartons_rouges'),
            'note_moyenne'     => $statsSaison->whereNotNull('note_moyenne')->count()
                                    ? round($statsSaison->avg('note_moyenne'), 2)
                                    : null,
        ];

        $joueur->load('user', 'club', 'equipe');

        return view('parent.joueur-stats', compact('joueur', 'lien', 'statsSaison', 'aggregats'));
    }

    // ═══ Agenda du club de l'enfant ═══
    public function agendaJoueur(Joueur $joueur)
    {
        $lien = $this->lienActif($joueur);
        abort_unless($lien->acces_agenda, 403, "Vous n'avez pas accès à l'agenda de ce joueur.");

        $club   = $joueur->club;
        $aVenir = collect();
        $passes = collect();

        if ($club) {
            $base = $club->evenements()->whereIn('visibilite', ['public', 'club']);

            // Le scope aVenir() inclut déjà un orderBy ASC
            $aVenir = (clone $base)->aVenir()->get();

            // Événements passés : les 10 derniers
            $passes = (clone $base)->passes()->limit(10)->get();
        }

        $joueur->load('user');

        return view('parent.joueur-agenda', compact('joueur', 'lien', 'club', 'aVenir', 'passes'));
    }

    // ═══ Contacter le club de l'enfant (messagerie) ═══
    public function contacterClub(Joueur $joueur)
    {
        $this->lienActif($joueur);

        abort_unless($joueur->club, 404, "Ce joueur n'appartient à aucun club actuellement.");

        return redirect()->route('messages.conversation', $joueur->club->user_id);
    }

    // ═══ Signaler une difficulté / exprimer un voeu ═══
    public function signalement(Joueur $joueur)
    {
        $lien = $this->lienActif($joueur);
        $joueur->load('user', 'club');

        $signalementsExistants = DifficulteVoeu::where('joueur_id', $joueur->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('parent.joueur-signalement', compact('joueur', 'lien', 'signalementsExistants'));
    }

    public function storeSignalement(Request $request, Joueur $joueur)
    {
        $this->lienActif($joueur);

        $validated = $request->validate([
            'type'      => 'required|in:difficulte,voeu',
            'categorie' => 'required|string|max:100',
            'titre'     => 'required|string|max:255',
            'contenu'   => 'required|string|max:2000',
            'priorite'  => 'required|in:basse,normale,haute',
        ]);

        DifficulteVoeu::create([
            'joueur_id'    => $joueur->id,
            'type'         => $validated['type'],
            'categorie'    => $validated['categorie'],
            'titre'        => $validated['titre'],
            'contenu'      => $validated['contenu'],
            'priorite'     => $validated['priorite'],
            'visible_club' => true,
            'resolu'       => false,
        ]);

        return redirect()->route('parent.joueur.show', $joueur)
                         ->with('success', 'Votre signalement a été transmis au club.');
    }

    // ═══ Transferts de l'enfant (lecture seule) ═══
    public function transfertsJoueur(Joueur $joueur)
    {
        $this->lienActif($joueur);

        $transferts = $joueur->transferts()
            ->with('clubSource', 'clubDestinataire', 'agent')
            ->latest()
            ->get();

        $joueur->load('user', 'club');

        return view('parent.joueur-transferts', compact('joueur', 'transferts'));
    }

    // ═══ Retirer le lien avec un enfant ═══
    public function removeJoueur(ParentJoueur $parent)
    {
        abort_unless($parent->user_id === Auth::id(), 403);
        $parent->delete();

        return redirect()->route('parent.dashboard')
                         ->with('success', 'Lien supprimé avec succès.');
    }

    // ═══ Afficher le formulaire de profil ═══
    public function profil()
    {
        $user = $this->authenticatedUser();
        return view('parent.profil', compact('user'));
    }

    // ═══ Mettre à jour les infos du profil ═══
    public function updateProfil(Request $request)
    {
        $user = $this->authenticatedUser();

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'prenom'    => 'nullable|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:30',
            'lien_type' => 'nullable|in:pere,mere,autre',
            'avatar'    => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } else {
            unset($validated['avatar']);
        }

        // Le champ lien_type n'appartient pas au modèle User — on met à jour les liens existants
        if ($request->filled('lien_type')) {
            ParentJoueur::where('user_id', $user->id)
                ->update(['lien' => $validated['lien_type']]);
        }
        unset($validated['lien_type']);

        $user->fill($validated)->save();

        return redirect()->route('parent.profil')
                         ->with('success', 'Profil mis à jour avec succès.');
    }

    // ═══ Changer le mot de passe ═══
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', 'min:8'],
        ]);

        $this->authenticatedUser()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('parent.profil')
                         ->with('success', 'Mot de passe modifié avec succès.');
    }

    // ═══ Helper : vérifie que le parent connecté est lié à ce joueur ═══
    private function lienActif(Joueur $joueur): ParentJoueur
    {
        $lien = $this->authenticatedUser()->parentsJoueurs()
                    ->where('joueur_id', $joueur->id)
                    ->actif()
                    ->first();

        abort_unless($lien, 403, "Vous n'êtes pas lié à ce joueur.");

        return $lien;
    }

    private function authenticatedUser(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        return $user;
    }
}