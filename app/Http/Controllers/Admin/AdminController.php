<?php
// app/Http/Controllers/Admin/AdminController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Club;
use App\Models\Joueur;
use App\Models\Transfert;
use App\Models\Opportunite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // ═══ Dashboard admin ═══
    public function dashboard()
    {
        $stats = [
            'users'         => User::count(),
            'clubs'         => Club::count(),
            'joueurs'       => Joueur::count(),
            'transferts'    => Transfert::count(),
            'opportunites'  => Opportunite::count(),
        ];

        $activite = [
            'users_7j'      => User::where('created_at', '>=', now()->subDays(7))->count(),
            'clubs_7j'      => Club::where('created_at', '>=', now()->subDays(7))->count(),
            'transferts_7j' => Transfert::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('admin.dashboard', compact('stats', 'activite'));
    }

    // ═══ Gestion utilisateurs ═══
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
        }

        $users = $query->orderBy('created_at', 'desc')
                      ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    // ═══ Afficher utilisateur ═══
    public function showUser(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    // ═══ Éditer utilisateur ═══
    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    // ═══ Mettre à jour utilisateur ═══
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|in:club,joueur,parent,agent,supporter,admin',
            'actif'    => 'sometimes|boolean',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.show', $user)
                        ->with('success', 'Utilisateur mis à jour');
    }

    // ═══ Bloquer utilisateur ═══
    public function blockUser(User $user)
    {
        $user->update(['actif' => false]);
        return back()->with('success', 'Utilisateur bloqué');
    }

    // ═══ Débloquer utilisateur ═══
    public function unblockUser(User $user)
    {
        $user->update(['actif' => true]);
        return back()->with('success', 'Utilisateur débloqué');
    }

    // ═══ Gestion clubs ═══
    public function clubs(Request $request)
    {
        $query = Club::query();

        if ($request->has('search')) {
            $query->where('nom', 'like', "%{$request->search}%");
        }

        $clubs = $query->with('user')
                      ->orderBy('created_at', 'desc')
                      ->paginate(20);

        return view('admin.clubs.index', compact('clubs'));
    }

    // ═══ Afficher club ═══
    public function showClub(Club $club)
    {
        $club->load('user', 'joueurs', 'equipes', 'sponsors');
        return view('admin.clubs.show', compact('club'));
    }

    // ═══ Activer/Désactiver club ═══
    public function toggleClub(Club $club)
    {
        $club->update(['actif' => !$club->actif]);
        return back()->with('success', 'Statut du club modifié');
    }

    // ═══ Gestion transferts ═══
    public function transferts(Request $request)
    {
        $query = Transfert::query();

        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        $transferts = $query->with('joueur.user', 'clubSource', 'clubDestinataire')
                           ->orderBy('created_at', 'desc')
                           ->paginate(20);

        return view('admin.transferts.index', compact('transferts'));
    }

    // ═══ Détails transfert ═══
    public function showTransfert(Transfert $transfert)
    {
        $transfert->load('joueur.user', 'clubSource', 'clubDestinataire', 'agent.user');
        return view('admin.transferts.show', compact('transfert'));
    }

    // ═══ Valider transfert ═══
    public function validateTransfert(Transfert $transfert)
    {
        $transfert->update([
            'statut'      => 'accepte',
            'finalise_at' => now(),
        ]);

        return back()->with('success', 'Transfert validé');
    }

    // ═══ Gestion opportunités ═══
    public function opportunites()
    {
        $opportunites = Opportunite::with('club.user')
                                  ->orderBy('created_at', 'desc')
                                  ->paginate(20);

        return view('admin.opportunites.index', compact('opportunites'));
    }

    // ═══ Mettre en avant opportunité ═══
    public function featuredOpportunite(Opportunite $opportunite)
    {
        $opportunite->update(['mise_en_avant' => !$opportunite->mise_en_avant]);
        return back()->with('success', 'Opportunité mise à jour');
    }

    // ═══ Signalements ═══
    public function reports()
    {
        // À implémenter selon le modèle Report
        return view('admin.reports.index');
    }

    // ═══ Logs système ═══
    public function logs()
    {
        // Afficher les logs
        return view('admin.logs.index');
    }

    // ═══ Paramètres ═══
    public function settings()
    {
        return view('admin.settings.index');
    }

    // ═══ Mettre à jour paramètres ═══
    public function updateSettings(Request $request)
    {
        // Mettre à jour config
        return back()->with('success', 'Paramètres mis à jour');
    }
}
