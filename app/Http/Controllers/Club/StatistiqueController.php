<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\EvenementAgenda;
use App\Models\Joueur;
use App\Models\Statistique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatistiqueController extends Controller
{
    // ═══ Hub : vue d'ensemble de tous les joueurs du club ═══
    public function indexClub()
    {
        $club = Auth::user()->club;

        $joueurs = $club->joueurs()
                        ->with('user')
                        ->withCount(['statistiques as stats_en_attente' => fn($q) => $q->where('valide', 0)])
                        ->withSum('statistiques as total_matchs', 'matchs_joues')
                        ->withSum('statistiques as total_buts', 'buts')
                        ->withSum('statistiques as total_passes', 'passes_decisives')
                        ->paginate(20);

        return view('club.statistiques.index', compact('club', 'joueurs'));
    }

    // ═══ Formulaire des statistiques générales du club ═══
    public function enregistrer()
    {
        $club = Auth::user()->club;
        abort_if(!$club, 403);

        $club->load('sport');
        $joueurs = $club->joueurs()->with('user')->orderBy('id')->get();
        $anneeEnCours = (int) date('Y');
        $annees = range($anneeEnCours - 20, $anneeEnCours + 1);

        return view('club.stats.create-general', compact('club', 'joueurs', 'annees', 'anneeEnCours'));
    }

    // ═══ Enregistrement des statistiques générales d'un joueur ═══
    public function enregistrerStore(Request $request)
    {
        $club = Auth::user()->club;
        abort_if(!$club, 403);

        $data = $request->validate([
            'joueur_id'         => ['required', 'integer', 'exists:joueurs,id'],
            'annee_debut'       => ['required', 'integer', 'min:2000', 'max:2100'],
            'annee_fin'         => ['required', 'integer', 'min:2000', 'max:2100'],
            'matchs_joues'      => 'required|integer|min:0',
            'matchs_titulaire'  => 'nullable|integer|min:0',
            'matchs_remplacant' => 'nullable|integer|min:0',
            'minutes_jouees'    => 'nullable|integer|min:0',
            'buts'              => 'nullable|integer|min:0',
            'passes_decisives'  => 'nullable|integer|min:0',
            'cartons_jaunes'    => 'nullable|integer|min:0',
            'cartons_rouges'    => 'nullable|integer|min:0',
            'note_moyenne'      => 'nullable|numeric|min:0|max:10',
            'stats_complementaires' => 'nullable|array',
            'stats_complementaires.*' => 'nullable|integer|min:0',
        ], [
            'matchs_joues.integer'      => 'Le champ matchs joués doit être un nombre entier.',
            'matchs_remplacant.integer' => 'Le champ matchs remplaçant doit être un nombre entier.',
        ]);

        abort_if(!$club->joueurs()->whereKey($data['joueur_id'])->exists(), 403,
            'Ce joueur ne fait pas partie de votre club.');

        if ((int) $data['annee_fin'] !== (int) $data['annee_debut'] + 1) {
            return back()->withErrors([
                'annee_fin' => 'L’année de fin doit suivre immédiatement l’année de début.',
            ])->withInput();
        }

        $saison = $data['annee_debut'] . '-' . $data['annee_fin'];

        $titulaire = $data['matchs_titulaire'] ?? 0;
        $remplacant = $data['matchs_remplacant'] ?? 0;
        if (($titulaire + $remplacant) > $data['matchs_joues']) {
            return back()->withErrors([
                'matchs_joues' => 'La somme titulaire + remplaçant dépasse le nombre de matchs joués.',
            ])->withInput();
        }

        $statistique = Statistique::firstOrNew([
            'club_id' => $club->id,
            'joueur_id' => $data['joueur_id'],
            'saison' => $saison,
            'evenement_id' => null,
        ]);
        $statistique->fill([
            'matchs_joues' => $data['matchs_joues'],
            'matchs_titulaire' => $titulaire,
            'matchs_remplacant' => $remplacant,
            'minutes_jouees' => $data['minutes_jouees'] ?? 0,
            'buts' => $data['buts'] ?? 0,
            'passes_decisives' => $data['passes_decisives'] ?? 0,
            'cartons_jaunes' => $data['cartons_jaunes'] ?? 0,
            'cartons_rouges' => $data['cartons_rouges'] ?? 0,
            'note_moyenne' => $data['note_moyenne'] ?? null,
            'stats_complementaires' => $data['stats_complementaires'] ?? null,
            'valide' => false,
            'valide_at' => null,
        ])->save();

        return redirect()->route('club.statistiques.index')
            ->with('success', 'Les statistiques du joueur ont été enregistrées et sont en attente de validation.');
    }

    // ═══ Enregistrement depuis la fiche d'un joueur ═══
    public function storeDepuisJoueur(Request $request, Joueur $joueur)
    {
        $club = Auth::user()->club;
        abort_if(!$club || $joueur->club_id !== $club->id, 403,
            'Ce joueur ne fait pas partie de votre club.');

        $data = $request->validate([
            'saison'            => ['required', 'regex:/^\d{4}-\d{4}$/'],
            'evenement_id'      => ['nullable', 'integer', 'exists:evenements_agenda,id'],
            'matchs_joues'      => 'required|integer|min:0',
            'matchs_titulaire'  => 'nullable|integer|min:0',
            'matchs_remplacant' => 'nullable|integer|min:0',
            'minutes_jouees'    => 'nullable|integer|min:0',
            'buts'              => 'nullable|integer|min:0',
            'passes_decisives'  => 'nullable|integer|min:0',
            'cartons_jaunes'    => 'nullable|integer|min:0',
            'cartons_rouges'    => 'nullable|integer|min:0',
            'note_moyenne'      => 'nullable|numeric|min:0|max:10',
            'visibilite'        => 'required|in:restreinte,publique',
            'valide'            => 'nullable|boolean',
        ], [
            'matchs_joues.integer'      => 'Le champ matchs joués doit être un nombre entier.',
            'matchs_remplacant.integer' => 'Le champ matchs remplaçant doit être un nombre entier.',
        ]);

        $evenementId = $data['evenement_id'] ?? null;
        if ($evenementId && !$club->evenements()->whereKey($evenementId)->exists()) {
            abort(403, 'Cet événement ne fait pas partie de votre club.');
        }

        $titulaire = $data['matchs_titulaire'] ?? 0;
        $remplacant = $data['matchs_remplacant'] ?? 0;
        if (($titulaire + $remplacant) > $data['matchs_joues']) {
            return back()->withErrors([
                'matchs_joues' => 'La somme titulaire + remplaçant dépasse le nombre de matchs joués.',
            ])->withInput();
        }

        Statistique::updateOrCreate(
            ['joueur_id' => $joueur->id, 'evenement_id' => $evenementId],
            [
                'club_id' => $club->id,
                'saison' => $data['saison'],
                'matchs_joues' => $data['matchs_joues'],
                'matchs_titulaire' => $titulaire,
                'matchs_remplacant' => $remplacant,
                'minutes_jouees' => $data['minutes_jouees'] ?? 0,
                'buts' => $data['buts'] ?? 0,
                'passes_decisives' => $data['passes_decisives'] ?? 0,
                'cartons_jaunes' => $data['cartons_jaunes'] ?? 0,
                'cartons_rouges' => $data['cartons_rouges'] ?? 0,
                'note_moyenne' => $data['note_moyenne'] ?? null,
                'visibilite' => $data['visibilite'],
                'valide' => (bool) ($data['valide'] ?? false),
                'valide_at' => !empty($data['valide']) ? now() : null,
            ]
        );

        return back()->with('success', 'La performance du joueur a été enregistrée.');
    }

    // ═══ Détail d'un joueur — historique + validation ═══
    public function index(Joueur $joueur)
    {
        $club = Auth::user()->club;
        abort_if($joueur->club_id !== $club->id, 403);

        $saisons = $joueur->statistiques()->select('saison')->distinct()->pluck('saison');
        $saisonSelectionnee = request('saison', $saisons->first());

        $stats = $joueur->statistiques()
                        ->when($saisonSelectionnee, fn($q) => $q->where('saison', $saisonSelectionnee))
                        ->with('evenement')
                        ->orderByDesc('created_at')
                        ->get();

        return view('joueur.stats.index', compact('joueur', 'stats', 'saisons', 'saisonSelectionnee'));
    }

    // ═══ Formulaire de saisie après un match ═══
    public function create(EvenementAgenda $evenement)
    {
        $club = Auth::user()->club;
        abort_if(!$club || $evenement->club_id !== $club->id, 403);

        $joueurs = $club->joueurs()->with('user')->get();

        return view('club.stats.create', compact('evenement', 'joueurs'));
    }

    // ═══ Enregistrer les stats (règle titulaire/remplaçant incluse) ═══
    public function store(Request $request, EvenementAgenda $evenement)
    {
        $club = Auth::user()->club;
        abort_if(!$club || $evenement->club_id !== $club->id, 403);

        $data = $request->validate([
            'joueur_id'         => ['required', 'integer', 'exists:joueurs,id'],
            'matchs_joues'      => 'required|integer|min:0',
            'matchs_titulaire'  => 'nullable|integer|min:0',
            'matchs_remplacant' => 'nullable|integer|min:0',
            'minutes_jouees'    => 'nullable|integer|min:0',
            'buts'              => 'nullable|integer|min:0',
            'passes_decisives'  => 'nullable|integer|min:0',
            'cartons_jaunes'    => 'nullable|integer|min:0',
            'cartons_rouges'    => 'nullable|integer|min:0',
            'note_moyenne'      => 'nullable|numeric|min:0|max:10',
        ]);

        $joueur = $club->joueurs()->whereKey($data['joueur_id'])->first();
        abort_if(!$joueur, 403, 'Ce joueur ne fait pas partie de votre club.');

        $titulaire  = $data['matchs_titulaire']  ?? 0;
        $remplacant = $data['matchs_remplacant'] ?? 0;
        if (($titulaire + $remplacant) > $data['matchs_joues']) {
            return back()
                ->withErrors(['matchs_joues' => 'La somme titulaire + remplaçant dépasse le nombre de matchs joués.'])
                ->withInput();
        }

        $saison = date('Y') . '-' . (date('Y') + 1);

        Statistique::updateOrCreate(
            ['joueur_id' => $data['joueur_id'], 'evenement_id' => $evenement->id],
            [
                'club_id'           => $club->id,
                'saison'            => $saison,
                'matchs_joues'      => $data['matchs_joues'],
                'matchs_titulaire'  => $titulaire,
                'matchs_remplacant' => $remplacant,
                'minutes_jouees'    => $data['minutes_jouees'] ?? 0,
                'buts'              => $data['buts'] ?? 0,
                'passes_decisives'  => $data['passes_decisives'] ?? 0,
                'cartons_jaunes'    => $data['cartons_jaunes'] ?? 0,
                'cartons_rouges'    => $data['cartons_rouges'] ?? 0,
                'note_moyenne'      => $data['note_moyenne'] ?? null,
            ]
        );

        return redirect()->route('club.agenda.show', $evenement)
                         ->with('success', 'Statistiques enregistrées !');
    }

    // ═══ Valider une statistique ═══
    public function valider(Statistique $statistique)
    {
        abort_if($statistique->club_id !== Auth::user()->club->id, 403);
        $statistique->valider();
        return back()->with('success', 'Statistiques validées !');
    }
}