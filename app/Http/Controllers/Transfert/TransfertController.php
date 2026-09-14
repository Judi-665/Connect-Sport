<?php
// app/Http/Controllers/Transfert/TransfertController.php

namespace App\Http\Controllers\Transfert;

use App\Http\Controllers\Controller;
use App\Models\Transfert;
use App\Models\Club;
use App\Models\Agent;
use App\Models\TransfertHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransfertController extends Controller
{
    // ═══ Côté club : liste ═══
    public function index()
    {
        $club = Auth::user()->club;

        $query = Transfert::where(function ($q) use ($club) {
            $q->where('club_source_id', $club->id)
              ->orWhere('club_destinataire_id', $club->id);
        })->with(['joueur.user', 'clubSource', 'clubDestinataire']);

        if (request()->filled('statut')) {
            $query->where('statut', request('statut'));
        }
        if (request()->filled('type')) {
            $query->where('type', request('type'));
        }
        if (request()->filled('direction')) {
            if (request('direction') === 'sortant') {
                $query->where('club_source_id', $club->id);
            } else {
                $query->where('club_destinataire_id', $club->id);
            }
        }

        $transferts = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'en_attente' => Transfert::where(fn($q) => $q->where('club_source_id', $club->id)->orWhere('club_destinataire_id', $club->id))->enAttente()->count(),
            'en_cours'   => Transfert::where(fn($q) => $q->where('club_source_id', $club->id)->orWhere('club_destinataire_id', $club->id))->enNegociation()->count(),
            'acceptes'   => Transfert::where(fn($q) => $q->where('club_source_id', $club->id)->orWhere('club_destinataire_id', $club->id))->acceptes()->count(),
            'definitifs' => Transfert::where(fn($q) => $q->where('club_source_id', $club->id)->orWhere('club_destinataire_id', $club->id))->definitifs()->count(),
        ];

        return view('club.transferts.index', compact('club', 'transferts', 'stats'));
    }

    // ═══ Côté club : formulaire création ═══
    public function create()
    {
        $club    = Auth::user()->club;
        $joueurs = $club->joueurs()->with('user')->actif()->get();
        $clubs   = Club::actif()->where('id', '!=', $club->id)->get();
        $agents  = Agent::with('user')->get();

        return view('club.transferts.create', compact('club', 'joueurs', 'clubs', 'agents'));
    }

    // ═══ Côté club : enregistrer transfert ═══
    public function store(Request $request)
    {
        $club = Auth::user()->club;

        $validated = $request->validate([
            'joueur_id'            => 'required|exists:joueurs,id',
            'club_destinataire_id' => 'required|exists:clubs,id',
            'agent_id'             => 'nullable|exists:agents,id',
            'type'                 => 'required|in:definitif,pret',
            'montant'              => 'nullable|numeric|min:0',
            'devise'               => 'required|string|max:3',
            'montant_confidentiel' => 'sometimes|boolean',
            'note_joueur'          => 'nullable|string|max:500',
            'date_effet'           => 'required|date|after_or_equal:today',
            'date_fin_pret'        => 'required_if:type,pret|nullable|date|after:date_effet',
        ]);

        $validated['club_source_id'] = $club->id;
        $validated['statut']         = 'en_attente';

        $transfert = Transfert::create($validated);
        $this->recordHistory($transfert, null, 'en_attente', $validated['note_joueur'] ?? null);

        return redirect()->route('club.transferts.index')
                         ->with('success', 'Demande de transfert créée.');
    }

    // ═══ Côté club : afficher transfert ═══
    public function show(Transfert $transfert)
    {
        $club = Auth::user()->club;
        abort_if(
            $transfert->club_source_id !== $club->id &&
            $transfert->club_destinataire_id !== $club->id,
            403
        );
        $transfert->load('joueur.user', 'clubSource', 'clubDestinataire', 'agent');
        return view('club.transferts.show', compact('transfert', 'club'));
    }

    // ═══ Côté club : éditer transfert ═══
    public function edit(Transfert $transfert)
    {
        $club = Auth::user()->club;
        abort_if($transfert->club_source_id !== $club->id, 403);
        abort_if($transfert->statut !== 'en_attente', 403);

        $joueurs = $club->joueurs()->with('user')->get();
        $clubs   = Club::actif()->where('id', '!=', $club->id)->get();
        $agents  = Agent::with('user')->get();

        return view('club.transferts.edit', compact('transfert', 'club', 'joueurs', 'clubs', 'agents'));
    }

    // ═══ Côté club : mettre à jour transfert ═══
    public function update(Request $request, Transfert $transfert)
    {
        $club = Auth::user()->club;
        abort_if($transfert->club_source_id !== $club->id, 403);
        abort_if($transfert->statut !== 'en_attente', 403);

        $request->validate([
            'statut'                 => 'required|in:en_attente,en_negociation,accepte,refuse,annule',
            'note_joueur'            => 'nullable|string|max:500',
            'note_club_source'       => 'nullable|string|max:500',
            'note_club_destinataire' => 'nullable|string|max:500',
        ]);

        $ancienStatut = $transfert->statut;
        $data = ['statut' => $request->statut];

        if ($request->statut === 'accepte') {
            $data['finalise_at'] = now();
        }
        if ($request->filled('note_joueur')) {
            $data['note_joueur'] = $request->note_joueur;
        }
        if ($request->filled('note_club_source')) {
            $data['note_club_source'] = $request->note_club_source;
        }
        if ($request->filled('note_club_destinataire')) {
            $data['note_club_destinataire'] = $request->note_club_destinataire;
        }

        $transfert->update($data);
        $this->recordHistory($transfert, $ancienStatut, $request->statut, $request->note);

        return response()->json(['message' => 'Transfert mis à jour.', 'statut' => $transfert->statut]);
    }

    // ═══ Côté club : supprimer transfert ═══
    public function destroy(Transfert $transfert)
    {
        $club = Auth::user()->club;
        abort_if($transfert->club_source_id !== $club->id, 403);
        abort_if($transfert->statut !== 'en_attente', 403);

        $transfert->delete();

        return response()->json(['message' => 'Transfert annulé.']);
    }

    // ═══ Côté club : mettre à jour statut ═══
    public function updateStatut(Request $request, Transfert $transfert)
    {
        $club = Auth::user()->club;
        abort_if(
            $transfert->club_source_id !== $club->id &&
            $transfert->club_destinataire_id !== $club->id,
            403
        );

        $request->validate([
            'statut' => 'required|in:en_attente,en_negociation,accepte,refuse,annule',
            'note'   => 'nullable|string|max:500',
        ]);

        $ancienStatut = $transfert->statut;
        $data = ['statut' => $request->statut];

        if ($request->statut === 'accepte') {
            $data['finalise_at'] = now();
        }
        if ($request->filled('note')) {
            $data[$transfert->club_source_id === $club->id
                ? 'note_club_source'
                : 'note_club_destinataire'] = $request->note;
        }

        $transfert->update($data);
        $this->recordHistory($transfert, $ancienStatut, $request->statut, $request->note);

        return response()->json(['message' => 'Statut mis à jour.', 'statut' => $transfert->fresh()->statut]);
    }

    // ═══ Côté joueur : liste ═══
    public function indexJoueur()
    {
        $joueur     = Auth::user()->joueur;
        $transferts = Transfert::where('joueur_id', $joueur->id)
                               ->with(['clubSource', 'clubDestinataire'])
                               ->orderBy('created_at', 'desc')
                               ->paginate(15);

        return view('joueur.transferts.index', compact('transferts', 'joueur'));
    }

    // ═══ Côté joueur : détail ═══
    public function showJoueur(Transfert $transfert)
    {
        $joueur = Auth::user()->joueur;
        abort_if($transfert->joueur_id !== $joueur->id, 403);
        $transfert->load('clubSource', 'clubDestinataire', 'agent');
        return view('joueur.transferts.show', compact('transfert', 'joueur'));
    }

    private function recordHistory(Transfert $transfert, ?string $ancienStatut, string $nouveauStatut, ?string $note = null): void
    {
        TransfertHistory::create([
            'transfert_id' => $transfert->id,
            'user_id' => Auth::id(),
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => $nouveauStatut,
            'note' => $note,
        ]);
    }
}