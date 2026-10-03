<?php
// app/Http/Controllers/Supporter/SupporterController.php

namespace App\Http\Controllers\Supporter;

use App\Http\Controllers\Controller;
use App\Models\Supporter;
use App\Models\Club;
use App\Models\Sport;
use App\Models\EvenementAgenda;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupporterController extends Controller
{
    /**
     * Récupère ou crée automatiquement le profil supporter pour l'utilisateur connecté.
     */
    private function getSupporter(): Supporter
    {
        $user = $this->authenticatedUser();
        $supporter = $user->supporter;

        if (!$supporter) {
            $supporter = Supporter::create([
                'user_id' => $user->id,
                'pays'    => 'Bénin',
                'actif'   => 1,
            ]);
        }

        return $supporter;
    }

    /**
     * Dashboard principal du supporter avec son fil d'actualité enrichi.
     */
    public function dashboard(Request $request)
    {
        $supporter = $this->getSupporter();
        $followedClubs = $supporter->clubs()->with('sport')->withCount('supporters')->get();
        $clubIds = $followedClubs->pluck('id')->toArray();

        // Filtre optionnel par club spécifique dans le fil
        $selectedClubId = $request->get('club_id');
        $activeClubIds = ($selectedClubId && in_array($selectedClubId, $clubIds))
            ? [(int) $selectedClubId]
            : $clubIds;

        // Prochains matchs (Agenda)
        $prochainsMatchs = empty($clubIds)
            ? collect()
            : EvenementAgenda::whereIn('club_id', $activeClubIds)
                ->where('debut_at', '>=', now())
                ->whereIn('visibilite', ['public', 'club'])
                ->with('club')
                ->orderBy('debut_at', 'asc')
                ->take(10)
                ->get();

        // Derniers résultats (Matchs terminés avec scores)
        $derniersResultats = empty($clubIds)
            ? collect()
            : EvenementAgenda::whereIn('club_id', $activeClubIds)
                ->where('debut_at', '<', now())
                ->whereNotNull('score_nous')
                ->whereNotNull('score_eux')
                ->with('club')
                ->orderBy('debut_at', 'desc')
                ->take(10)
                ->get();

        // Photos et médias publiés
        $medias = empty($clubIds)
            ? collect()
            : Media::whereIn('club_id', $activeClubIds)
                ->whereIn('visibilite', ['public', 'club'])
                ->where('payant', false)
                ->with(['club', 'evenement', 'reactions' => fn($query) => $query->where('user_id', Auth::id())])
                ->withCount([
                    'reactions as likes_count' => fn($query) => $query->where('type', 'like'),
                    'reactions as loves_count' => fn($query) => $query->where('type', 'love'),
                ])
                ->latest()
                ->take(12)
                ->get();

        // Suggestions de clubs à découvrir
        $clubsSuggeres = Club::whereNotIn('id', $clubIds)
            ->with('sport')
            ->withCount('supporters')
            ->inRandomOrder()
            ->take(6)
            ->get();

        // Statistiques
        $stats = [
            'clubs_suivis'          => count($clubIds),
            'prochains_matchs'      => $prochainsMatchs->count(),
            'derniers_resultats'    => $derniersResultats->count(),
            'medias_recents'        => $medias->count(),
            'notifications_actives' => $followedClubs->filter(fn($c) => (bool) $c->pivot->notifications_actives)->count(),
        ];

        return view('supporter.dashboard', compact(
            'supporter',
            'followedClubs',
            'clubIds',
            'selectedClubId',
            'prochainsMatchs',
            'derniersResultats',
            'medias',
            'clubsSuggeres',
            'stats'
        ));
    }

    /**
     * Page dédiée "Mes clubs suivis".
     */
    public function mesClubs(Request $request)
    {
        $supporter = $this->getSupporter();

        $query = $supporter->clubs()->with('sport')->withCount('supporters');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('ville', 'like', "%{$search}%");
            });
        }

        if ($request->filled('sport_id')) {
            $query->where('sport_id', $request->sport_id);
        }

        $clubs = $query->paginate(12)->withQueryString();
        $sports = Sport::orderBy('nom')->get();

        return view('supporter.clubs', compact('supporter', 'clubs', 'sports'));
    }

    /**
     * Page de découverte de clubs pour s'abonner.
     */
    public function decouvrir(Request $request)
    {
        $supporter = $this->getSupporter();
        $followedClubIds = $supporter->clubs()->pluck('clubs.id')->toArray();

        $query = Club::with('sport')->withCount('supporters');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('ville', 'like', "%{$search}%")
                  ->orWhere('pays', 'like', "%{$search}%");
            });
        }

        if ($request->filled('sport_id')) {
            $query->where('sport_id', $request->sport_id);
        }

        $clubs = $query->latest()->paginate(12)->withQueryString();
        $sports = Sport::orderBy('nom')->get();

        return view('supporter.decouvrir', compact('supporter', 'clubs', 'followedClubIds', 'sports'));
    }

    /**
     * S'abonner (gratuitement) à un club.
     */
    public function suivreClub(Club $club, Request $request)
    {
        $supporter = $this->getSupporter();

        if (!$supporter->isFollowing($club)) {
            $supporter->clubs()->attach($club->id, [
                'type_abonnement'       => 'gratuit',
                'notifications_actives' => 1,
                'abonnement_expire_at'  => null,
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'               => true,
                'is_following'          => true,
                'notifications_actives' => true,
                'club_id'               => $club->id,
                'club_nom'              => $club->nom,
                'message'               => "Vous suivez désormais {$club->nom} !",
            ]);
        }

        return back()->with('success', "Vous êtes maintenant abonné à {$club->nom} !");
    }

    /**
     * Se désabonner d'un club.
     */
    public function quitterClub(Club $club, Request $request)
    {
        $supporter = $this->getSupporter();

        $supporter->clubs()->detach($club->id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'      => true,
                'is_following' => false,
                'club_id'      => $club->id,
                'club_nom'     => $club->nom,
                'message'      => "Vous ne suivez plus {$club->nom}.",
            ]);
        }

        return back()->with('success', "Vous vous êtes désabonné de {$club->nom}.");
    }

    /**
     * Activer / couper les notifications pour un club suivi.
     */
    public function toggleNotification(Club $club, Request $request)
    {
        $supporter = $this->getSupporter();
        $match = $supporter->clubs()->where('clubs.id', $club->id)->first();

        if (!$match) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Vous ne suivez pas ce club.'], 404);
            }
            return back()->with('error', 'Vous ne suivez pas ce club.');
        }

        $current = (bool) $match->pivot->notifications_actives;
        $nouvelEtat = !$current;

        $supporter->clubs()->updateExistingPivot($club->id, [
            'notifications_actives' => $nouvelEtat ? 1 : 0,
        ]);

        $message = $nouvelEtat
            ? "Notifications activées pour {$club->nom}."
            : "Notifications coupées pour {$club->nom}.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'               => true,
                'notifications_actives' => $nouvelEtat,
                'club_id'               => $club->id,
                'club_nom'              => $club->nom,
                'message'               => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Profil du supporter.
     */
    public function profil()
    {
        $supporter = $this->getSupporter();
        $user = $this->authenticatedUser();
        $clubsCount = $supporter->clubs()->count();

        return view('supporter.profil', compact('supporter', 'user', 'clubsCount'));
    }

    /**
     * Mise à jour du profil supporter.
     */
    public function updateProfil(Request $request)
    {
        $supporter = $this->getSupporter();
        $user = $this->authenticatedUser();

        $validatedUser = $request->validate([
            'name'      => 'required|string|max:100',
            'prenom'    => 'nullable|string|max:100',
            'telephone' => 'nullable|string|max:30',
        ]);

        $validatedSupporter = $request->validate([
            'ville' => 'nullable|string|max:100',
            'pays'  => 'required|string|max:100',
            'bio'   => 'nullable|string|max:500',
        ]);

        $user->update($validatedUser);
        $supporter->update($validatedSupporter);

        return redirect()->route('supporter.profile')->with('success', 'Profil mis à jour avec succès.');
    }

    private function authenticatedUser(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        return $user;
    }
}
