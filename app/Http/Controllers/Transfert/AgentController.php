<?php
// app/Http/Controllers/Transfert/AgentController.php

namespace App\Http\Controllers\Transfert;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Joueur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transfert;
use App\Models\TransfertHistory;
use App\Models\Club;
use App\Models\Opportunite;
use App\Models\SubscriptionPlan;

class AgentController extends Controller
{
    // ═══ Dashboard agent ═══
    public function dashboard()
    {
        $agent = Auth::user()->agent;

        if (!$agent) {
            return redirect()->route('agent.create')
                             ->with('info', 'Veuillez compléter votre profil agent');
        }

        $agent->load('user');
        $agent->loadCount('clubsPartenaires');
        $stats = [
            'joueurs'    => $agent->joueursActifs()->count(),
            'mandats'    => $agent->joueurs()->count(),
            'transferts' => $agent->transferts()->count(),
            'en_cours'   => $agent->transferts()->whereIn('statut', ['en_attente', 'en_negociation'])->count(),
            'finalises'  => $agent->transferts()->where('statut', 'accepte')->count(),
        ];

        $joueurs = $agent->joueursActifs()->with('user', 'club')->latest('agent_joueur.created_at')->limit(5)->get();
        $transferts = $agent->transferts()->with('joueur.user', 'clubSource', 'clubDestinataire')->latest()->limit(5)->get();
        $mandatsExpirantBientot = $agent->mandatsExpirantBientot()->with('user')->get();
$stats['echeances'] = $mandatsExpirantBientot->count();

        return view('agents.dashboard', compact('agent', 'stats', 'joueurs', 'transferts', 'mandatsExpirantBientot'));
    }

    // ═══ Répertoire agents ═══
    public function index()
    {
        $agents = Agent::actif()
                       ->where('verifie', true)
                       ->orderByDesc('mise_en_avant')
                       ->latest()
                       ->with('user')
                       ->paginate(20);

        return view('agents.index', compact('agents'));
    }

    // ═══ Profil agent ═══
    public function show(Agent $agent)
    {
        $agent->load('user', 'joueurs');
        
        return view('agents.show', compact('agent'));
    }

    // ═══ Statistiques agent ═══
    public function statistiques()
    {
        $agent = Auth::user()->agent;
        abort_unless($agent, 403);
        abort_unless($agent->estPremium(), 403, 'Les statistiques avancées sont réservées au plan Premium.');

        $transferts = $agent->transferts();
        $stats = [
            'joueurs_actifs' => $agent->joueursActifs()->count(),
            'joueurs_total' => $agent->joueurs()->count(),
            'transferts_total' => (clone $transferts)->count(),
            'transferts_acceptes' => (clone $transferts)->where('statut', 'accepte')->count(),
            'montant_transferts' => (clone $transferts)->where('statut', 'accepte')->sum('montant'),
            'mandats_expirant' => $agent->mandatsExpirantBientot()->count(),
        ];

        $transfertsParStatut = (clone $transferts)
            ->selectRaw('statut, COUNT(*) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut');

        return view('agents.statistiques', [
            'agent' => $agent,
            'stats' => $stats,
            'transfertsParStatut' => $transfertsParStatut,
            'estPremium' => true,
        ]);
    }

    // ═══ Profil agent connecté ═══
    public function profil()
    {
        return $this->dashboard();
    }

    // ═══ Créer/compléter profil ═══
    public function create()
    {
        return view('agents.create');
    }

    // ═══ Enregistrer profil ═══
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'numero_accreditation' => 'required|string|unique:agents,numero_accreditation',
            'agence'               => 'required|string|max:200',
            'ville'                => 'required|string|max:100',
            'pays'                 => 'required|string|max:100',
            'telephone'            => 'required|string|max:20',
            'bio'                  => 'nullable|string',
            'site_web'             => 'nullable|url',
        ]);

        Agent::create(array_merge($validated, [
            'user_id' => $user->id,
            'actif'   => true,
        ]));

        return redirect()->route('agent.profil')
                        ->with('success', 'Profil agent créé');
    }

    // ═══ Modifier profil ═══
    public function edit()
    {
        $user  = Auth::user();
        $agent = $user->agent;
        if (!$agent) {
            return redirect()->route('agent.create');
        }
        return view('agents.edit', compact('agent', 'user'));
    }

    // ═══ Mettre à jour ═══
    public function update(Request $request)
    {
        $user  = Auth::user();
        $agent = $user->agent;
        abort_unless($agent, 403);

        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'prenom'    => 'nullable|string|max:100',
            'email'     => 'required|email|lowercase|max:150|unique:users,email,' . $user->id,
            'agence'    => 'required|string|max:200',
            'ville'     => 'required|string|max:100',
            'pays'      => 'required|string|max:100',
            'telephone' => 'required|string|max:20',
            'bio'       => 'nullable|string',
            'site_web'  => 'nullable|url',
            'avatar'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'name.required'     => 'Le nom est obligatoire.',
            'email.required'    => 'L\'adresse email est obligatoire.',
            'email.unique'      => 'Cette adresse email est déjà utilisée.',
            'agence.required'   => 'Le nom de l\'agence est obligatoire.',
            'avatar.image'      => 'Le fichier doit être une image.',
            'avatar.max'        => 'L\'image ne doit pas dépasser 2 Mo.',
        ]);

        // Gestion de l'avatar photo
        $avatarPath = $user->avatar;
        if ($request->hasFile('avatar')) {
            if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars/agents', 'public');
        }

        // Mettre à jour User
        $user->update([
            'name'      => $validated['name'],
            'prenom'    => $validated['prenom'] ?? null,
            'email'     => strtolower($validated['email']),
            'telephone' => $validated['telephone'],
            'avatar'    => $avatarPath,
        ]);

        // Mettre à jour Agent
        $agent->update([
            'agence'    => $validated['agence'],
            'ville'     => $validated['ville'],
            'pays'      => $validated['pays'],
            'telephone' => $validated['telephone'],
            'bio'       => $validated['bio'] ?? null,
            'site_web'  => $validated['site_web'] ?? null,
        ]);

        return redirect()->route('agent.profil')
                        ->with('success', 'Profil et photo mis à jour avec succès.');
    }


    // ═══ Représenter un joueur ═══
    public function representeJoueur(Request $request, Joueur $joueur)
{
    $agent = Auth::user()->agent;
    abort_unless($agent, 403);

    $validated = $request->validate([
        'debut_mandat'           => 'required|date',
        'fin_mandat'             => 'required|date|after:debut_mandat',
        'commission_pourcentage' => 'required|numeric|min:0|max:100',
        'note'                   => 'nullable|string|max:1000',
    ]);

    $dejaRepresente = $joueur->agents()
                             ->wherePivot('statut', 'actif')
                             ->where('agents.id', '!=', $agent->id)
                             ->exists();

    if ($dejaRepresente) {
        return back()->with('error', 'Ce joueur est déjà représenté par un autre agent.');
    }

    $planActif = $agent->planActif() ?? SubscriptionPlan::where('type_acteur', 'agent')
        ->where('slug', 'gratuit')
        ->first();
    $maxJoueurs = $planActif?->max_joueurs_representes;

    if ($maxJoueurs !== null && $agent->joueursActifs()->where('joueurs.id', '!=', $joueur->id)->count() >= $maxJoueurs) {
        return back()->with('error', "Limite de {$maxJoueurs} joueurs représentés atteinte pour votre plan actuel. Passez à un plan supérieur pour en représenter davantage.");
    }

    $agent->joueurs()->syncWithoutDetaching([
        $joueur->id => [
            'debut_mandat'           => $validated['debut_mandat'],
            'fin_mandat'             => $validated['fin_mandat'],
            'commission_pourcentage' => $validated['commission_pourcentage'],
            'statut'                 => 'actif',
            'note'                   => $validated['note'] ?? null,
        ],
    ]);

    return redirect()->route('agent.joueurs')->with('success', 'Mandat signé avec ' . $joueur->nomComplet() . '.');
}

    // ═══ Lister mes joueurs ═══
    public function mesJoueurs()
    {
        $agent = Auth::user()->agent;

        $joueurs = $agent->joueurs()
                        ->with('user', 'club', 'equipe')
                        ->paginate(15);

        return view('agents.mes-joueurs', compact('joueurs'));
    }

    // ═══ Lister mes transferts ═══
    public function mesTransferts()
    {
        $agent = Auth::user()->agent;

        $transferts = $agent->transferts()
                   ->with(['joueur.user', 'clubSource', 'clubDestinataire'])
                           ->orderBy('created_at', 'desc')
                           ->paginate(15);

        return view('agents.mes-transferts', compact('transferts'));
    }

    public function createOffre()
    {
        $agent = Auth::user()->agent;
        abort_unless($agent, 403);

        $joueurs = $agent->joueursActifs()->with('user', 'club')->get();
        $clubsSources = Club::actif()
            ->whereIn('id', $joueurs->pluck('club_id')->filter()->unique())
            ->orderBy('nom')
            ->get();
        $clubs = Club::actif()->whereNotIn('id', $joueurs->pluck('club_id')->filter())->orderBy('nom')->get();

        return view('agents.offre-create', compact('agent', 'joueurs', 'clubs', 'clubsSources'));
    }

    public function storeOffre(Request $request)
    {
        $agent = Auth::user()->agent;
        abort_unless($agent, 403);

        $validated = $request->validate([
            'club_source_id'       => 'nullable|integer|exists:clubs,id',
            'joueur_id'            => 'required|integer|exists:joueurs,id',
            'club_destinataire_id' => 'required|integer|exists:clubs,id',
            'type'                 => 'required|in:definitif,pret,essai',
            'montant'              => 'nullable|numeric|min:0',
            'devise'               => 'required|string|max:5',
            'montant_confidentiel' => 'sometimes|boolean',
            'date_effet'           => 'nullable|date|after_or_equal:today',
            'date_fin_pret'        => 'nullable|date|after:date_effet',
            'agent_note'           => 'nullable|string|max:2000',
        ]);

        $joueur = $agent->joueursActifs()->with('club')->findOrFail($validated['joueur_id']);
        abort_if(!$joueur->club_id, 422, 'Le joueur doit être rattaché à un club source.');
        if (isset($validated['club_source_id'])) {
            abort_unless((int) $validated['club_source_id'] === (int) $joueur->club_id, 422, 'Le club source ne correspond pas au joueur sélectionné.');
        }
        abort_if($joueur->club_id === (int) $validated['club_destinataire_id'], 422, 'Le club destinataire doit être différent du club source.');

        $transfert = Transfert::create(array_merge($validated, [
            'club_source_id' => $joueur->club_id,
            'agent_id'       => $agent->id,
            'statut'         => 'en_attente',
        ]));

        $this->recordHistory($transfert, null, 'en_attente', $validated['agent_note'] ?? null);
        $agent->clubsPartenaires()->syncWithoutDetaching([
            $joueur->club_id,
            $validated['club_destinataire_id'],
        ]);

        return redirect()->route('agent.transferts.show', $transfert)->with('success', 'Offre de transfert soumise.');
    }

    public function showTransfert(Transfert $transfert)
    {
        $agent = Auth::user()->agent;
        abort_unless($agent && $transfert->agent_id === $agent->id, 403);

        $transfert->load(['joueur.user', 'clubSource.user', 'clubDestinataire.user', 'historique.user']);
        return view('agents.transfert-show', compact('agent', 'transfert'));
    }

    public function historique()
    {
        $agent = Auth::user()->agent;
        abort_unless($agent, 403);

        $transferts = $agent->transferts()->with(['joueur.user', 'clubSource', 'clubDestinataire'])
                           ->whereIn('statut', ['accepte', 'refuse', 'annule'])
                           ->latest()->paginate(15);
        return view('agents.historique', compact('transferts'));
    }

    public function partenaires()
    {
        $agent = Auth::user()->agent;
        abort_unless($agent, 403);

        $clubs = $agent->clubsPartenaires()->with('user')->orderBy('nom')->paginate(15);
        return view('agents.partenaires', compact('clubs'));
    }

    // ═══ Négocier un transfert ═══
public function negocier(Request $request, Transfert $transfert)
{
    $agent = Auth::user()->agent;
    abort_unless($agent && $transfert->agent_id === $agent->id, 403);

    $validated = $request->validate([
        'note'   => 'nullable|string|max:1000',
        'statut' => 'required|in:en_negociation,accepte,refuse,annule',
    ]);

    // Vérifier que l'agent représente bien le joueur du transfert
    $ancienStatut = $transfert->statut;
    $transfert->update([
        'statut'     => $validated['statut'],
        'agent_note' => $validated['note'],
        'finalise_at' => $validated['statut'] === 'accepte' ? now() : $transfert->finalise_at,
    ]);
    $this->recordHistory($transfert, $ancienStatut, $validated['statut'], $validated['note']);

    return back()->with('success', 'Négociation mise à jour.');
}

    private function recordHistory(Transfert $transfert, ?string $ancienStatut, string $nouveauStatut, ?string $note = null): void
    {
        TransfertHistory::create([
            'transfert_id' => $transfert->id,
            'user_id'      => Auth::id(),
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => $nouveauStatut,
            'note'          => $note,
        ]);
    }

    public function createMandat(Joueur $joueur)
{
    $agent = Auth::user()->agent;
    abort_unless($agent, 403);

    return view('agents.mandat-create', compact('joueur'));
}
public function resilierMandat(Joueur $joueur)
{
    $agent = Auth::user()->agent;
    abort_unless($agent, 403);

    $agent->joueurs()->updateExistingPivot($joueur->id, ['statut' => 'resilie']);

    return back()->with('success', 'Mandat résilié.');
}

public function commissions()
{
    $agent = Auth::user()->agent;
    abort_unless($agent, 403);

    $transferts = $agent->transferts()
                        ->where('statut', 'accepte')
                        ->whereNotNull('montant')
                        ->with('joueur', 'clubSource', 'clubDestinataire')
                        ->orderByDesc('finalise_at')
                        ->get()
                        ->map(function ($t) use ($agent) {
                            $pivot = $agent->joueurs()->where('joueurs.id', $t->joueur_id)->first()?->pivot;
                            $t->commission_taux    = $pivot?->commission_pourcentage ?? 0;
                            $t->commission_montant = $t->montant * ($t->commission_taux / 100);
                            return $t;
                        });

    $totalCommissions = $transferts->sum('commission_montant');

    return view('agents.commissions', compact('transferts', 'totalCommissions'));
}

// ═══ Parcourir les opportunités pour placer un joueur ═══
public function opportunites()
{
    $agent = Auth::user()->agent;
    abort_unless($agent, 403);
    abort_unless($agent->estStandardOuPlus(), 403, 'Fonctionnalité réservée au plan Standard ou supérieur.');

    $opportunites = Opportunite::active()
                               ->nonExpirees()
                               ->with('club')
                               ->orderByDesc('mise_en_avant')
                               ->orderByDesc('created_at')
                               ->paginate(15);

    $joueurs = $agent->joueursActifs()->with('user')->get();

    return view('agents.opportunites', compact('opportunites', 'joueurs'));
}

// ═══ Postuler au nom d'un joueur représenté ═══
public function postulerOpportunite(Request $request, Opportunite $opportunite)
{
    $agent = Auth::user()->agent;
    abort_unless($agent, 403);
    abort_unless($agent->estStandardOuPlus(), 403, 'Fonctionnalité réservée au plan Standard ou supérieur.');

    $validated = $request->validate([
        'joueur_id' => 'required|exists:joueurs,id',
    ]);

    $joueur = $agent->joueursActifs()->where('joueurs.id', $validated['joueur_id'])->first();
    abort_unless($joueur, 403, "Vous ne représentez pas ce joueur (mandat inexistant ou inactif).");

    $existant = $opportunite->candidatures()->where('joueur_id', $joueur->id)->exists();
    if ($existant) {
        return back()->with('warning', 'Une candidature existe déjà pour ce joueur sur cette opportunité.');
    }

    $opportunite->candidatures()->attach($joueur->id, [
        'candidature_at' => now(),
        'statut'         => 'en_attente',
    ]);

    return back()->with('success', 'Candidature envoyée pour ' . $joueur->nomComplet() . '.');
}

}
