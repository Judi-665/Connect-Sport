<?php
// app/Http/Controllers/Club/ClubController.php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Joueur;
use Illuminate\Support\Facades\Auth;
use App\Models\Sport;
use App\Models\Transfert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClubController extends Controller
{
    // ═══════════════════════════════════════════════════════════
    // PARTIE PUBLIQUE (vitrine)
    // ═══════════════════════════════════════════════════════════

    /**
     * Liste publique des clubs (avec recherche et filtre par sport)
     */
    public function index(Request $request)
    {
        $query = Club::with('sport')
            ->withCount([
                'joueurs' => fn($q) => $q->where('actif', true),
                'supporters',
            ]);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nom', 'like', '%'.$request->search.'%')
                  ->orWhere('ville', 'like', '%'.$request->search.'%');
            });
        }
        if ($request->filled('sport')) {
            $query->whereHas('sport', fn($q) => $q->where('nom', $request->sport));
        }

        $clubs = $query->paginate(12);
        return view('clubs.index', compact('clubs'));
    }

    /**
     * Détail public d'un club (par slug)
     */
    public function show(string $slug)
    {
        $club = Club::where('slug', $slug)
                    ->withCount(['joueurs' => fn($q) => $q->where('actif', true)])
                    ->with([
                        'sport',
                        'sponsors' => fn($q) => $q->actif()
                            ->where(fn($q) => $q->whereNull('fin_partenariat')
                                ->orWhere('fin_partenariat', '>=', today()))
                            ->latest(),
                        'opportunites' => fn($q) => $q->latest()->take(5),
                    ])
                    ->firstOrFail();
        return view('clubs.show', compact('club'));
    }

    /**
     * Liste publique des joueurs appartenant à un club.
     */
    public function publicJoueurs(Club $club)
    {
        $joueurs = $club->joueurs()
                        ->where('actif', true)
                        ->with(['user', 'equipe'])
                        ->latest()
                        ->paginate(12);

        return view('clubs.joueurs', compact('club', 'joueurs'));
    }

    /**
     * Galerie publique des photos et vidéos du club.
     */
    public function publicMedias(Club $club)
    {
        $publicMedia = $club->medias()
                       ->publics()
                       ->where('payant', false)
                       ->withCount([
                           'reactions as likes_count' => fn($query) => $query->where('type', 'like'),
                           'reactions as loves_count' => fn($query) => $query->where('type', 'love'),
                       ])
                       ->with(['reactions' => fn($query) => $query->where('user_id', Auth::id())]);

        $photos = (clone $publicMedia)
                       ->photos()
                       ->latest()
                       ->paginate(12, ['*'], 'photos');

        $videos = (clone $publicMedia)
                       ->videos()
                       ->latest()
                       ->paginate(8, ['*'], 'videos');

        return view('clubs.medias', compact('club', 'photos', 'videos'));
    }

    // ═══════════════════════════════════════════════════════════
    // PARTIE PRIVÉE (club connecté)
    // ═══════════════════════════════════════════════════════════

    /**
     * Dashboard du club connecté
     */
    public function dashboard()
    {
        $club = Auth::user()->club;

        if (!$club) {
    return redirect()->route('club.parametres.index')
                     ->with('info', 'Veuillez compléter votre profil club.');
}

        $stats = [
            'joueurs_total'       => $club->joueurs()->count(),
            'joueurs_actifs'      => $club->joueurs()->actif()->count(),
            'equipes_total'       => $club->equipes()->count(),
            'categories'          => $club->equipes()->whereNotNull('categorie')->distinct()->count('categorie'),
            'licences_actives'    => $club->licences()->active()
                ->where(fn($q) => $q->whereNull('date_expiration')->orWhereDate('date_expiration', '>=', today()))
                ->count(),
            'licences_expirent'   => $club->licences()->active()
                ->whereBetween('date_expiration', [today(), today()->addDays(30)])
                ->count(),
            'evenements_a_venir'  => $club->evenements()->aVenir()->count(),
            'prochain_evenement'  => $club->evenements()->aVenir()->value('debut_at'),
        ];

        $evenementsProchains = $club->evenements()
                    ->aVenir()
                    ->orderBy('debut_at')
                    ->take(5)
                    ->get();

                $transfertsEnCours = Transfert::where(function ($query) use ($club) {
                                        $query->where('club_source_id', $club->id)
                                                    ->orWhere('club_destinataire_id', $club->id);
                                })
                                    ->whereIn('statut', ['en_attente', 'en_negociation'])
                  ->with(['joueur.user', 'clubSource', 'clubDestinataire'])
                  ->latest()
                  ->take(5)
                  ->get();

        $joueurs = $club->joueurs()
                                        ->with(['user', 'equipe'])
                    ->latest()
                    ->take(6)
                    ->get();

                $sponsorsActifs = $club->sponsors()
                        ->actif()
                        ->where(fn($q) => $q->whereNull('fin_partenariat')->orWhereDate('fin_partenariat', '>=', today()))
                        ->latest()
                        ->take(5)
                        ->get();

                $abonnementActif = $club->subscriptionActive();

                return view('club.dashboard', compact(
                        'club', 'stats', 'evenementsProchains', 'transfertsEnCours', 'joueurs', 'sponsorsActifs', 'abonnementActif'
                ));
    }

    /**
     * Afficher le profil du club connecté
     */
    public function profil()
    {
        $club = Auth::user()->club;
        return view('club.profil', compact('club'));
    }

    /**
     * Formulaire de création du profil club
     */
    public function create()
    {
        $sports = Sport::actif()->get();
        return view('club.create', compact('sports'));
    }

    /**
     * Enregistrer le profil club
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom'         => 'required|string|max:120',
            'sport_id'    => 'required|exists:sports,id',
            'ville'       => 'nullable|string|max:100',
            'pays'        => 'nullable|string|max:80',
            'adresse'     => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'site_web'    => 'nullable|url|max:255',
            'telephone'   => 'nullable|string|max:20',
            'logo'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos/clubs', 'public');
        }

        Club::create([
            'user_id'     => Auth::id(),
            'sport_id'    => $request->sport_id,
            'nom'         => $request->nom,
            'slug'        => Str::slug($request->nom) . '-' . uniqid(),
            'ville'       => $request->ville,
            'pays'        => $request->pays ?? 'Bénin',
            'adresse'     => $request->adresse,
            'description' => $request->description,
            'site_web'    => $request->site_web,
            'telephone'   => $request->telephone,
            'logo'        => $logoPath,
        ]);

        return redirect()->route('club.dashboard')
                         ->with('success', 'Profil club créé avec succès !');
    }

    /**
     * Formulaire d'édition du profil club
     */
    public function edit()
    {
        $club   = Auth::user()->club;
        $sports = Sport::actif()->get();
        return view('club.profil.edit', compact('club', 'sports'));
    }

    /**
     * Mettre à jour le profil club
     */
    public function update(Request $request)
{
    $club = Auth::user()->club;

    if (!$club) {
        return redirect()->route('club.parametres.index')
                         ->with('error', 'Aucun club trouvé.');
    }

    $request->validate([
        'adresse'     => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'site_web'    => 'nullable|url|max:255',
        'telephone'   => 'nullable|string|max:20',
        'stade'       => 'nullable|string|max:150',
        'niveau'      => 'nullable|in:National,Régional,Départemental,Loisir',
        'logo'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    $logoPath = $club->logo;

    if ($request->hasFile('logo')) {
        if ($logoPath) {
            Storage::disk('public')->delete($logoPath);
        }
        $logoPath = $request->file('logo')->store('logos/clubs', 'public');
    }

    $club->update([
        'adresse'     => $request->adresse,
        'description' => $request->description,
        'site_web'    => $request->site_web,
        'stade'       => $request->stade,
        'niveau'      => $request->niveau,
        'telephone'   => $request->telephone,
        'logo'        => $logoPath,
    ]);

    if ($request->expectsJson()) {
        return response()->json(['message' => 'Logo mis à jour.', 'logo' => $logoPath]);
    }

    return redirect()->route('club.parametres.index')
                     ->with('success', 'Profil mis à jour avec succès !');
}
    // ═══════════════════════════════════════════════════════════
    // GESTION DES JOUEURS DU CLUB
    // ═══════════════════════════════════════════════════════════

    /**
     * Liste des joueurs du club connecté
     * Filtres : catégorie, poste, équipe, recherche nom
     */
    public function joueurs(Request $request)
    {
        $club = Auth::user()->club;

        abort_if(!$club, 403);

        $query = $club->joueurs()->with(['user', 'equipe', 'licenceActive', 'statistiques' => fn($q) => $q->saisonEnCours()->valides()]);

        // Filtre recherche par nom
        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%'.$request->search.'%')
                                                  ->orWhere('prenom', 'like', '%'.$request->search.'%'));
        }

        // Filtre catégorie (junior, cadet, senior…)
        if ($request->filled('categorie')) {
            $query->parCategorie($request->categorie);
        }

        // Filtre poste
        if ($request->filled('poste')) {
            $query->parPoste($request->poste);
        }

        // Filtre équipe
        if ($request->filled('equipe_id')) {
            $query->where('equipe_id', $request->equipe_id);
        }

        // Filtres statut étendus
        if ($request->filled('statut')) {
            match($request->statut) {
                'actif'             => $query->actif(),
                'inactif'           => $query->where('actif', 0),
                'sans_club'         => $query->sansClub(),
                'visible_recruteur' => $query->visibleRecruteur(),
                default             => null,
            };
        }

        $joueurs = $query->latest()->paginate(15)->withQueryString();

        // Données pour les selects de filtre
        $equipes    = $club->equipes()->orderBy('nom')->get();
        $categories = $club->joueurs()->distinct()->pluck('categorie')->filter()->sort()->values();
        $postes     = $club->joueurs()->distinct()->pluck('poste')->filter()->sort()->values();

        // Compteurs
        $compteurs = [
            'total'             => $club->joueurs()->count(),
            'actifs'            => $club->joueurs()->actif()->count(),
            'visibles_recruteur'=> $club->joueurs()->visibleRecruteur()->count(),
            'sans_club'         => $club->joueurs()->sansClub()->count(),
            'saison'            => date('Y') . '-' . (date('Y') + 1),
        ];

        return view('club.joueurs.index', compact('club', 'joueurs', 'equipes', 'categories', 'postes', 'compteurs'));
    }

    /**
     * Détail d'un joueur du club (profil + stats + historique)
     * Le club peut voir toutes les stats ; seules les stats validées sont publiques
     */
    public function showJoueur(Joueur $joueur)
    {
        $club = Auth::user()->club;

        abort_if(!$club, 403);

        // Vérifier que le joueur appartient bien à ce club
        abort_if($joueur->club_id !== $club->id, 403, 'Ce joueur n\'appartient pas à votre club.');

        $joueur->load([
            'user',
            'equipe',
            'licences'       => fn($q) => $q->latest(),
            'licenceActive',
            'statistiques'   => fn($q) => $q->with('evenement')->latest(),
            'transferts'     => fn($q) => $q->with(['clubSource', 'clubDestinataire'])->latest()->take(5),
            'difficultes'    => fn($q) => $q->latest()->take(5),
            'voeux'          => fn($q) => $q->latest()->take(5),
            'agents'         => fn($q) => $q->wherePivot('statut', 'actif'),
        ]);

        // Stats agrégées saison en cours (validées uniquement pour le calcul)
        $saisonEnCours = date('Y') . '-' . (date('Y') + 1);

        $statsSaison = $joueur->statistiques
            ->where('saison', $saisonEnCours)
            ->where('valide', true);

        $aggregats = [
            'matchs_joues'      => $statsSaison->sum('matchs_joues'),
            'buts'              => $statsSaison->sum('buts'),
            'passes_decisives'  => $statsSaison->sum('passes_decisives'),
            'minutes_jouees'    => $statsSaison->sum('minutes_jouees'),
            'cartons_jaunes'    => $statsSaison->sum('cartons_jaunes'),
            'cartons_rouges'    => $statsSaison->sum('cartons_rouges'),
            'note_moyenne'      => $statsSaison->count()
                                    ? round($statsSaison->avg('note_moyenne'), 2)
                                    : null,
        ];

        // Saisons disponibles pour le filtre historique
        $saisons = $joueur->statistiques
            ->pluck('saison')
            ->unique()
            ->sortDesc()
            ->values();

        // Événements du club pour le select du formulaire
        $evenements = $club->evenements()
            ->orderByDesc('debut_at')
            ->get(['id', 'titre', 'debut_at']);

        return view('club.joueurs.show', compact(
            'club',
            'joueur',
            'aggregats',
            'saisonEnCours',
            'saisons',
            'evenements'
        ));
    }

    public function parametres()
{
    $club = Auth::user()->club;
    $user = Auth::user();
    $abonnementActif = $club?->subscriptionActive();
    $planActif = $abonnementActif?->subscriptionPlan;

    return view('club.parametres.index', compact(
        'club',
        'user',
        'abonnementActif',
        'planActif'
    ));
}
}