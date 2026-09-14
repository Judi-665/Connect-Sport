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

        return view('agents.dashboard', compact('agent', 'stats', 'joueurs', 'transferts'));
    }

    // ═══ Répertoire agents ═══
    public function index()
    {
        $agents = Agent::actif()
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
        $agent = Auth::user()->agent;
        if (!$agent) {
            return redirect()->route('agent.create');
        }
        return view('agents.edit', compact('agent'));
    }

    // ═══ Mettre à jour ═══
    public function update(Request $request)
    {
        $agent = Auth::user()->agent;
        abort_unless($agent, 403);

        $validated = $request->validate([
            'agence'   => 'required|string|max:200',
            'ville'    => 'required|string|max:100',
            'pays'     => 'required|string|max:100',
            'telephone' => 'required|string|max:20',
            'bio'      => 'nullable|string',
            'site_web' => 'nullable|url',
        ]);

        $agent->update($validated);

        return redirect()->route('agent.profil')
                        ->with('success', 'Profil mis à jour');
    }

    // ═══ Représenter un joueur ═══
    public function representeJoueur(Request $request)
    {
        $agent = Auth::user()->agent;

        $validated = $request->validate([
            'joueur_id' => 'required|exists:joueurs,id',
        ]);

        $joueur = Joueur::find($validated['joueur_id']);

        if ($joueur->agent_id && $joueur->agent_id !== $agent->id) {
            return back()->with('error', 'Ce joueur est déjà représenté');
        }

        $agent->joueurs()->syncWithoutDetaching([$joueur->id]);

        return back()->with('success', 'Joueur ajouté à votre portefeuille');
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
        $clubs = Club::actif()->whereNotIn('id', $joueurs->pluck('club_id')->filter())->orderBy('nom')->get();

        return view('agents.offre-create', compact('agent', 'joueurs', 'clubs'));
    }

    public function storeOffre(Request $request)
    {
        $agent = Auth::user()->agent;
        abort_unless($agent, 403);

        $validated = $request->validate([
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
}
