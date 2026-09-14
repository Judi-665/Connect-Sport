<?php
// app/Http/Controllers/Club/StatistiqueController.php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Joueur;
use App\Models\Statistique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatistiqueController extends Controller
{
    /**
     * Saisir les statistiques d'un joueur pour un match/événement
     * Seul le club auquel appartient le joueur peut publier ses stats (CDC 4.3)
     */
    public function store(Request $request, Joueur $joueur)
    {
        $club = Auth::user()->club;

        abort_if(!$club, 403);
        abort_if($joueur->club_id !== $club->id, 403, 'Ce joueur n\'appartient pas à votre club.');

        $data = $request->validate([
            'saison'                 => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
            'evenement_id'           => ['nullable', 'exists:evenement_agendas,id'],
            'matchs_joues'           => ['required', 'integer', 'min:0'],
            'matchs_titulaire'       => ['nullable', 'integer', 'min:0'],
            'matchs_remplacant'      => ['nullable', 'integer', 'min:0'],
            'minutes_jouees'         => ['nullable', 'integer', 'min:0'],
            'buts'                   => ['nullable', 'integer', 'min:0'],
            'passes_decisives'       => ['nullable', 'integer', 'min:0'],
            'cartons_jaunes'         => ['nullable', 'integer', 'min:0'],
            'cartons_rouges'         => ['nullable', 'integer', 'min:0'],
            'note_moyenne'           => ['nullable', 'numeric', 'min:0', 'max:10'],
            'stats_complementaires'  => ['nullable', 'array'],
            'visibilite'             => ['required', 'in:publique,restreinte'],  // CDC 4.3
            'valide'                 => ['boolean'],
        ]);

        // Cohérence : titulaire + remplaçant ne dépasse pas matchs_joues
        $titulaire   = $data['matchs_titulaire']  ?? 0;
        $remplacant  = $data['matchs_remplacant'] ?? 0;
        if (($titulaire + $remplacant) > $data['matchs_joues']) {
            return back()
                ->withErrors(['matchs_joues' => 'La somme titulaire + remplaçant dépasse le nombre de matchs joués.'])
                ->withInput();
        }

        $statistique = Statistique::create([
            'joueur_id'              => $joueur->id,
            'club_id'                => $club->id,
            'evenement_id'           => $data['evenement_id']          ?? null,
            'saison'                 => $data['saison'],
            'matchs_joues'           => $data['matchs_joues'],
            'matchs_titulaire'       => $data['matchs_titulaire']      ?? 0,
            'matchs_remplacant'      => $data['matchs_remplacant']     ?? 0,
            'minutes_jouees'         => $data['minutes_jouees']        ?? 0,
            'buts'                   => $data['buts']                  ?? 0,
            'passes_decisives'       => $data['passes_decisives']      ?? 0,
            'cartons_jaunes'         => $data['cartons_jaunes']        ?? 0,
            'cartons_rouges'         => $data['cartons_rouges']        ?? 0,
            'note_moyenne'           => $data['note_moyenne']          ?? null,
            'stats_complementaires'  => $data['stats_complementaires'] ?? null,
            'visibilite'             => $data['visibilite'],
            // Si le club valide directement à la saisie
            'valide'                 => $data['valide']                ?? false,
            'valide_at'              => ($data['valide'] ?? false) ? now() : null,
        ]);

        return redirect()
            ->route('club.joueurs.show', $joueur)
            ->with('success', 'Statistiques enregistrées avec succès.');
    }

    /**
     * Modifier des statistiques existantes
     * Seul le club propriétaire peut modifier (CDC 4.3 : le joueur ne peut pas modifier)
     */
    public function update(Request $request, Statistique $statistique)
    {
        $club = Auth::user()->club;

        abort_if(!$club, 403);
        abort_if($statistique->club_id !== $club->id, 403, 'Ces statistiques n\'appartiennent pas à votre club.');

        $data = $request->validate([
            'saison'                 => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
            'evenement_id'           => ['nullable', 'exists:evenement_agendas,id'],
            'matchs_joues'           => ['required', 'integer', 'min:0'],
            'matchs_titulaire'       => ['nullable', 'integer', 'min:0'],
            'matchs_remplacant'      => ['nullable', 'integer', 'min:0'],
            'minutes_jouees'         => ['nullable', 'integer', 'min:0'],
            'buts'                   => ['nullable', 'integer', 'min:0'],
            'passes_decisives'       => ['nullable', 'integer', 'min:0'],
            'cartons_jaunes'         => ['nullable', 'integer', 'min:0'],
            'cartons_rouges'         => ['nullable', 'integer', 'min:0'],
            'note_moyenne'           => ['nullable', 'numeric', 'min:0', 'max:10'],
            'stats_complementaires'  => ['nullable', 'array'],
            'visibilite'             => ['required', 'in:publique,restreinte'],  // CDC 4.3
            'valide'                 => ['boolean'],
        ]);

        // Cohérence titulaire + remplaçant
        $titulaire  = $data['matchs_titulaire']  ?? 0;
        $remplacant = $data['matchs_remplacant'] ?? 0;
        if (($titulaire + $remplacant) > $data['matchs_joues']) {
            return back()
                ->withErrors(['matchs_joues' => 'La somme titulaire + remplaçant dépasse le nombre de matchs joués.'])
                ->withInput();
        }

        // Gestion de la validation : si on passe de non-validé à validé, on horodate
        $valide   = $data['valide'] ?? false;
        $valideAt = $statistique->valide_at;

        if ($valide && !$statistique->valide) {
            $valideAt = now();
        } elseif (!$valide) {
            $valideAt = null;
        }

        $statistique->update([
            'saison'                 => $data['saison'],
            'evenement_id'           => $data['evenement_id']          ?? null,
            'matchs_joues'           => $data['matchs_joues'],
            'matchs_titulaire'       => $data['matchs_titulaire']      ?? 0,
            'matchs_remplacant'      => $data['matchs_remplacant']     ?? 0,
            'minutes_jouees'         => $data['minutes_jouees']        ?? 0,
            'buts'                   => $data['buts']                  ?? 0,
            'passes_decisives'       => $data['passes_decisives']      ?? 0,
            'cartons_jaunes'         => $data['cartons_jaunes']        ?? 0,
            'cartons_rouges'         => $data['cartons_rouges']        ?? 0,
            'note_moyenne'           => $data['note_moyenne']          ?? null,
            'stats_complementaires'  => $data['stats_complementaires'] ?? null,
            'visibilite'             => $data['visibilite'],
            'valide'                 => $valide,
            'valide_at'              => $valideAt,
        ]);

        return redirect()
            ->route('club.joueurs.show', $statistique->joueur_id)
            ->with('success', 'Statistiques mises à jour avec succès.');
    }
}