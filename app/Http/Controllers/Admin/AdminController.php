<?php
// app/Http/Controllers/Admin/AdminController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Club;
use App\Models\Joueur;
use App\Models\Transfert;
use App\Models\Opportunite;
use App\Models\Agent;
use App\Models\Abonnement;
use App\Models\Licence;
use App\Models\Supporter;
use App\Models\ParentJoueur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // ═══════════════════════════════════════════════════════════════════════
    // 1. DASHBOARD
    // ═══════════════════════════════════════════════════════════════════════
    public function dashboard()
    {
        $stats = [
            'users'        => User::count(),
            'clubs'        => Club::count(),
            'joueurs'      => Joueur::count(),
            'agents'       => Agent::count(),
            'transferts'   => Transfert::count(),
            'opportunites' => Opportunite::count(),
        ];

        $activite = [
            'users_7j'        => User::where('created_at', '>=', now()->subDays(7))->count(),
            'clubs_7j'        => Club::where('created_at', '>=', now()->subDays(7))->count(),
            'joueurs_7j'      => Joueur::where('created_at', '>=', now()->subDays(7))->count(),
            'transferts_7j'   => Transfert::where('created_at', '>=', now()->subDays(7))->count(),
            'opportunites_7j' => Opportunite::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        $agentsEnAttente = Agent::where('verifie', false)->count();

        $abonnementsActifs = [
            'total'   => Abonnement::actif()->count(),
            'clubs'   => Abonnement::actif()->whereNotNull('club_id')->count(),
            'joueurs' => Abonnement::actif()->whereNotNull('joueur_id')->count(),
            'agents'  => Abonnement::actif()->whereNotNull('agent_id')->count(),
        ];

        $transactionsFedaPay = [
            'total_7j' => (float) Abonnement::where('created_at', '>=', now()->subDays(7))
                                            ->where('statut', 'actif')
                                            ->whereNotNull('fedapay_transaction_id')
                                            ->sum('montant'),
            'total_global' => (float) Abonnement::where('statut', 'actif')
                                                ->whereNotNull('fedapay_transaction_id')
                                                ->sum('montant'),
            'count_7j' => Abonnement::where('created_at', '>=', now()->subDays(7))
                                    ->where('statut', 'actif')
                                    ->whereNotNull('fedapay_transaction_id')
                                    ->count(),
        ];

        $licencesExpirantBientot = Licence::with('joueur.user', 'club')
                                          ->expirentBientot(30)
                                          ->orderBy('date_expiration', 'asc')
                                          ->take(5)
                                          ->get();
        $licencesExpirantBientotCount = Licence::expirentBientot(30)->count();

        $abonnementsExpirantBientot = Abonnement::actif()
                                                ->whereNotNull('fin_at')
                                                ->where('fin_at', '<=', now()->addDays(15))
                                                ->where('fin_at', '>=', now())
                                                ->with(['club', 'joueur.user', 'agent.user'])
                                                ->orderBy('fin_at', 'asc')
                                                ->take(5)
                                                ->get();
        $abonnementsExpirantBientotCount = Abonnement::actif()
                                                     ->whereNotNull('fin_at')
                                                     ->where('fin_at', '<=', now()->addDays(15))
                                                     ->where('fin_at', '>=', now())
                                                     ->count();

        $agentsEnAttenteList = Agent::where('verifie', false)
                                    ->with('user')
                                    ->orderByDesc('created_at')
                                    ->take(5)
                                    ->get();

        $derniersTransferts = Transfert::with('joueur.user', 'clubSource', 'clubDestinataire')
                                       ->orderByDesc('created_at')
                                       ->take(5)
                                       ->get();

        $derniersUsers = User::orderByDesc('created_at')
                             ->take(6)
                             ->get();

        return view('admin.dashboard', compact(
            'stats',
            'activite',
            'agentsEnAttente',
            'agentsEnAttenteList',
            'abonnementsActifs',
            'transactionsFedaPay',
            'licencesExpirantBientot',
            'licencesExpirantBientotCount',
            'abonnementsExpirantBientot',
            'abonnementsExpirantBientotCount',
            'derniersTransferts',
            'derniersUsers'
        ));
    }

    // ═══════════════════════════════════════════════════════════════════════
    // 2. GESTION DES UTILISATEURS (TOUS RÔLES)
    // ═══════════════════════════════════════════════════════════════════════
    public function users(Request $request)
    {
        $query = User::query()->with(['club', 'joueur', 'agent', 'supporter']);

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('statut')) {
            if ($request->statut === 'actif') {
                $query->where('actif', true);
            } elseif ($request->statut === 'bloque') {
                $query->where('actif', false);
            }
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('prenom', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('telephone', 'like', "%{$s}%");
            });
        }

        $users = $query->orderByDesc('created_at')->paginate(20);

        $counts = [
            'total'     => User::count(),
            'clubs'     => User::where('role', 'club')->count(),
            'joueurs'   => User::where('role', 'joueur')->count(),
            'agents'    => User::where('role', 'agent')->count(),
            'parents'   => User::where('role', 'parent')->count(),
            'supporters'=> User::where('role', 'supporter')->count(),
            'admins'    => User::where('role', 'admin')->count(),
            'bloques'   => User::where('actif', false)->count(),
        ];

        return view('admin.users.index', compact('users', 'counts'));
    }

    public function showUser(User $user)
    {
        $user->load(['club', 'joueur.club', 'joueur.licences', 'joueur.carrieres', 'joueur.statistiques', 'agent.joueurs.user', 'supporter']);
        return view('admin.users.show', compact('user'));
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'prenom'    => 'nullable|string|max:100',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'role'      => 'required|in:club,joueur,parent,agent,supporter,admin',
            'telephone' => 'nullable|string|max:20',
            'actif'     => 'sometimes|boolean',
        ], [
            'name.required'  => 'Le nom est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.unique'   => 'Cet email est déjà utilisé.',
        ]);

        $ancienRole = $user->role;
        $nouveauRole = $validated['role'];

        $user->update([
            'name'      => $validated['name'],
            'prenom'    => $validated['prenom'] ?? null,
            'email'     => strtolower($validated['email']),
            'telephone' => $validated['telephone'] ?? null,
            'role'      => $nouveauRole,
            'actif'     => $request->has('actif') ? (bool)$request->actif : true,
        ]);

        // Si le rôle a changé, initialiser le profil du nouveau rôle si inexistant (sans écraser l'historique)
        if ($ancienRole !== $nouveauRole) {
            $this->initialiserProfilRole($user, $nouveauRole);
        }

        return redirect()->route('admin.users.show', $user)
                         ->with('success', 'Utilisateur et rôle mis à jour avec succès.');
    }

    public function blockUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas bloquer votre propre compte administrateur.']);
        }

        $user->update(['actif' => false]);
        return back()->with('success', 'Le compte utilisateur a été bloqué.');
    }

    public function unblockUser(User $user)
    {
        $user->update(['actif' => true]);
        return back()->with('success', 'Le compte utilisateur a été réactivé.');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:club,joueur,parent,agent,supporter,admin',
        ]);

        $ancienRole = $user->role;
        $nouveauRole = $request->role;

        $user->update(['role' => $nouveauRole]);

        if ($ancienRole !== $nouveauRole) {
            $this->initialiserProfilRole($user, $nouveauRole);
        }

        return back()->with('success', 'Rôle de l\'utilisateur mis à jour avec succès.');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas supprimer votre propre compte administrateur.']);
        }

        $user->delete();
        return redirect()->route('admin.users')->with('success', 'Utilisateur supprimé.');
    }

    private function initialiserProfilRole(User $user, string $role): void
    {
        match ($role) {
            'club' => Club::firstOrCreate(
                ['user_id' => $user->id],
                ['nom' => $user->name, 'ville' => 'Non spécifiée', 'pays' => 'Bénin', 'actif' => true]
            ),
            'joueur' => Joueur::firstOrCreate(
                ['user_id' => $user->id],
                ['poste' => 'Milieu', 'categorie' => 'Senior', 'sans_club' => true, 'visible_recruteur' => true, 'actif' => true]
            ),
            'agent' => Agent::firstOrCreate(
                ['user_id' => $user->id],
                ['agence' => $user->name . ' Agency', 'verifie' => false, 'actif' => true]
            ),
            'supporter' => Supporter::firstOrCreate(
                ['user_id' => $user->id],
                ['ville' => 'Non spécifiée']
            ),
            'parent' => ParentJoueur::firstOrCreate(
                ['user_id' => $user->id]
            ),
            default => null,
        };
    }

    // ═══════════════════════════════════════════════════════════════════════
    // 3. GESTION DES CLUBS
    // ═══════════════════════════════════════════════════════════════════════
    public function clubs(Request $request)
    {
        $query = Club::query()->with(['user', 'joueurs', 'equipes', 'abonnements']);

        if ($request->filled('search')) {
            $query->where('nom', 'like', "%{$request->search}%")
                  ->orWhere('ville', 'like', "%{$request->search}%");
        }

        if ($request->filled('statut')) {
            if ($request->statut === 'actif') {
                $query->where('actif', true);
            } elseif ($request->statut === 'inactif') {
                $query->where('actif', false);
            }
        }

        $clubs = $query->orderByDesc('created_at')->paginate(20);

        return view('admin.clubs.index', compact('clubs'));
    }

    public function showClub(Club $club)
    {
        $club->load([
            'user',
            'joueurs.user',
            'equipes.joueurs.user',
            'sponsors',
            'abonnements.plan',
            'licences.joueur.user'
        ]);

        return view('admin.clubs.show', compact('club'));
    }

    public function toggleClub(Club $club)
    {
        $club->update(['actif' => !$club->actif]);
        $message = $club->actif ? 'Club activé avec succès.' : 'Club désactivé.';
        return back()->with('success', $message);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // 4. GESTION DES AGENTS
    // ═══════════════════════════════════════════════════════════════════════
    public function agents(Request $request)
    {
        $query = Agent::with(['user', 'joueurs.user']);

        if ($request->filled('statut')) {
            match ($request->statut) {
                'en_attente' => $query->where('verifie', false),
                'verifie'    => $query->where('verifie', true),
                'bloque'     => $query->where('actif', false),
                default      => null,
            };
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('agence', 'like', "%{$s}%")
                  ->orWhere('numero_accreditation', 'like', "%{$s}%")
                  ->orWhereHas('user', function($qu) use ($s) {
                      $qu->where('name', 'like', "%{$s}%")
                         ->orWhere('prenom', 'like', "%{$s}%")
                         ->orWhere('email', 'like', "%{$s}%");
                  });
            });
        }

        $agents = $query->orderBy('verifie')
                        ->orderByDesc('created_at')
                        ->paginate(20);

        $enAttente = Agent::where('verifie', false)->count();

        // Calcul des commissions globales (transferts finalisés impliquant des agents)
        $commissionsGlobales = (float) Transfert::whereNotNull('agent_id')
                                                ->where('statut', 'accepte')
                                                ->sum('montant') * 0.10; // estimation 10%

        return view('admin.agents.index', compact('agents', 'enAttente', 'commissionsGlobales'));
    }

    public function showAgent(Agent $agent)
    {
        $agent->load(['user', 'joueurs.user', 'transferts.joueur.user', 'abonnements.plan']);

        $commissionsAgent = (float) Transfert::where('agent_id', $agent->id)
                                             ->where('statut', 'accepte')
                                             ->sum('montant') * 0.10;

        return view('admin.agents.show', compact('agent', 'commissionsAgent'));
    }

    public function verifierAgent(Agent $agent)
    {
        $agent->update(['verifie' => true]);
        return back()->with('success', 'Agent vérifié avec succès : ' . ($agent->agence ?? $agent->nomComplet()));
    }

    public function rejeterAgent(Agent $agent)
    {
        $agent->update(['verifie' => false]);
        return back()->with('success', 'Vérification annulée / rejetée pour : ' . ($agent->agence ?? $agent->nomComplet()));
    }

    public function toggleAgent(Agent $agent)
    {
        $agent->update(['actif' => !$agent->actif]);
        $msg = $agent->actif ? 'Agent débloqué.' : 'Agent bloqué.';
        return back()->with('success', $msg);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // 5. GESTION DES JOUEURS
    // ═══════════════════════════════════════════════════════════════════════
    public function joueurs(Request $request)
    {
        $query = Joueur::with(['user', 'club', 'licences', 'statistiques']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('user', function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('prenom', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        if ($request->filled('sans_club')) {
            $query->where('sans_club', (bool)$request->sans_club);
        }

        if ($request->filled('visible_recruteur')) {
            $query->where('visible_recruteur', (bool)$request->visible_recruteur);
        }

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        $joueurs = $query->orderByDesc('created_at')->paginate(20);

        return view('admin.joueurs.index', compact('joueurs'));
    }

    public function showJoueur(Joueur $joueur)
    {
        $joueur->load(['user', 'club', 'equipe', 'licences', 'carrieres', 'statistiques', 'abonnements.plan']);
        return view('admin.joueurs.show', compact('joueur'));
    }

    public function toggleVisibiliteJoueur(Joueur $joueur)
    {
        $joueur->update(['visible_recruteur' => !$joueur->visible_recruteur]);
        $msg = $joueur->visible_recruteur ? 'Visibilité recruteur activée.' : 'Visibilité recruteur masquée.';
        return back()->with('success', $msg);
    }

    public function toggleActifJoueur(Joueur $joueur)
    {
        $joueur->update(['actif' => !$joueur->actif]);
        $msg = $joueur->actif ? 'Profil joueur réactivé.' : 'Profil joueur désactivé.';
        return back()->with('success', $msg);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // 6. SUPERVISION DES TRANSFERTS
    // ═══════════════════════════════════════════════════════════════════════
    public function transferts(Request $request)
    {
        $query = Transfert::query()->with(['joueur.user', 'clubSource', 'clubDestinataire', 'agent.user']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transferts = $query->orderByDesc('created_at')->paginate(20);

        return view('admin.transferts.index', compact('transferts'));
    }

    public function showTransfert(Transfert $transfert)
    {
        $transfert->load(['joueur.user', 'clubSource', 'clubDestinataire', 'agent.user']);
        return view('admin.transferts.show', compact('transfert'));
    }

    public function validateTransfert(Request $request, Transfert $transfert)
    {
        $motif = $request->input('motif', 'Validation forcée par l\'administrateur.');

        $transfert->update([
            'statut'      => 'accepte',
            'finalise_at' => now(),
            'note_club_destinataire' => ($transfert->note_club_destinataire ? $transfert->note_club_destinataire . "\n" : '') . '[ADMIN FORCE VALIDATION] ' . $motif,
        ]);

        // Mise à jour du club du joueur si transfert définitif
        if ($transfert->club_destinataire_id && $transfert->joueur) {
            $transfert->joueur->update([
                'club_id'   => $transfert->club_destinataire_id,
                'sans_club' => false,
            ]);
        }

        return back()->with('success', 'Transfert validé de force avec succès.');
    }

    public function rejectTransfert(Request $request, Transfert $transfert)
    {
        $motif = $request->input('motif', 'Rejet / Annulation forcée par l\'administrateur.');

        $transfert->update([
            'statut' => 'refuse',
            'note_club_destinataire' => ($transfert->note_club_destinataire ? $transfert->note_club_destinataire . "\n" : '') . '[ADMIN FORCE REJET] ' . $motif,
        ]);

        return back()->with('success', 'Transfert annulé / rejeté de force en cas de litige.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // 7. GESTION DES OPPORTUNITÉS
    // ═══════════════════════════════════════════════════════════════════════
    public function opportunites(Request $request)
    {
        $query = Opportunite::with(['club.user']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('active')) {
            $query->where('active', (bool)$request->active);
        }

        if ($request->filled('mise_en_avant')) {
            $query->where('mise_en_avant', (bool)$request->mise_en_avant);
        }

        $opportunites = $query->orderByDesc('created_at')->paginate(20);

        return view('admin.opportunites.index', compact('opportunites'));
    }

    public function featuredOpportunite(Opportunite $opportunite)
    {
        $opportunite->update(['mise_en_avant' => !$opportunite->mise_en_avant]);
        $msg = $opportunite->mise_en_avant ? 'Opportunité mise en avant.' : 'Mise en avant retirée.';
        return back()->with('success', $msg);
    }

    public function toggleOpportunite(Opportunite $opportunite)
    {
        $opportunite->update(['active' => !$opportunite->active]);
        $msg = $opportunite->active ? 'Opportunité activée.' : 'Opportunité désactivée (non conforme).';
        return back()->with('success', $msg);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // 8. ABONNEMENTS ET PAIEMENTS FEDAPAY
    // ═══════════════════════════════════════════════════════════════════════
    public function paiements(Request $request)
    {
        $query = Abonnement::with(['club', 'joueur.user', 'agent.user', 'plan']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('type_acteur')) {
            match ($request->type_acteur) {
                'club'   => $query->whereNotNull('club_id'),
                'joueur' => $query->whereNotNull('joueur_id'),
                'agent'  => $query->whereNotNull('agent_id'),
                default  => null,
            };
        }

        if ($request->filled('plan')) {
            $query->where('plan', $request->plan);
        }

        $abonnements = $query->orderByDesc('created_at')->paginate(20);

        $sommairePaiements = [
            'total_encaissé' => (float) Abonnement::where('statut', 'actif')->sum('montant'),
            'nb_actifs'       => Abonnement::actif()->count(),
            'nb_en_attente'   => Abonnement::where('statut', 'en_attente')->count(),
            'nb_expires'      => Abonnement::where('statut', 'expire')->count(),
        ];

        return view('admin.paiements.index', compact('abonnements', 'sommairePaiements'));
    }

    // ═══════════════════════════════════════════════════════════════════════
    // 9. PROFIL & MOT DE PASSE DE L'ADMINISTRATEUR
    // ═══════════════════════════════════════════════════════════════════════
    public function profil()
    {
        $user = Auth::user();
        return view('admin.profil.index', compact('user'));
    }

    public function updateProfil(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'prenom'    => 'nullable|string|max:100',
            'email'     => 'required|email|lowercase|max:150|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:20',
        ], [
            'name.required'  => 'Le nom est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.unique'   => 'Cette adresse email est déjà utilisée.',
        ]);

        $user->update([
            'name'      => $validated['name'],
            'prenom'    => $validated['prenom'] ?? null,
            'email'     => strtolower($validated['email']),
            'telephone' => $validated['telephone'] ?? null,
        ]);

        return back()->with('success_profil', 'Vos informations de profil ont été mises à jour avec succès.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)],
        ], [
            'current_password.required'         => 'Le mot de passe actuel est obligatoire.',
            'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
            'password.required'                 => 'Le nouveau mot de passe est obligatoire.',
            'password.confirmed'                => 'Les nouveaux mots de passe ne correspondent pas.',
            'password.min'                      => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        return back()->with('success_password', 'Votre mot de passe a été modifié avec succès.');
    }
}

