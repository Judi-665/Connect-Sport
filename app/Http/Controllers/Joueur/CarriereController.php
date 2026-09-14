<?php
// app/Http/Controllers/Joueur/CarriereController.php

namespace App\Http\Controllers\Joueur;

use App\Http\Controllers\Controller;
use App\Models\Joueur;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarriereController extends Controller
{
    // ═══ Historique carrière ═══
    public function index()
    {
        $joueur = Auth::user()->joueur;

        $historique = $joueur->historique()
                             ->orderBy('date_debut', 'desc')
                             ->paginate(10);

        $clubActuel = $joueur->club;
        $equipesActuelles = $joueur->equipes()
                                   ->where('pivot_actif', true)
                                   ->get();

        return view('joueur.carriere.index', compact(
            'historique',
            'clubActuel',
            'equipesActuelles'
        ));
    }

    // ═══ Profil carrière ═══
    public function profil()
    {
        $joueur = Auth::user()->joueur;

        $stats = [
            'clubs'      => $joueur->clubs()->count(),
            'equipes'    => $joueur->equipes()->count(),
            'licences'   => $joueur->licences()->count(),
            'matchs'     => $joueur->statistiques()->sum('matchs_joues'),
            'buts'       => $joueur->statistiques()->sum('buts'),
            'passes'     => $joueur->statistiques()->sum('passes_decisives'),
        ];

        $carriereTimeline = $this->buildTimeline($joueur);

        return view('joueur.carriere.profil', compact('joueur', 'stats', 'carriereTimeline'));
    }

    // ═══ Statistiques globales ═══
    public function statistiquesGlobales()
    {
        $joueur = Auth::user()->joueur;

        $stats = $joueur->statistiques()
                        ->selectRaw('
                            COUNT(DISTINCT saison) as saisons,
                            SUM(matchs_joues) as matchs_total,
                            SUM(matchs_titulaire) as titulaire,
                            SUM(matchs_remplacant) as remplacant,
                            SUM(minutes_jouees) as minutes,
                            SUM(buts) as buts,
                            SUM(passes_decisives) as passes,
                            SUM(cartons_jaunes) as cartons_jaunes,
                            SUM(cartons_rouges) as cartons_rouges,
                            AVG(note_moyenne) as note_moyenne
                        ')
                        ->first();

        return view('joueur.carriere.stats-globales', compact('stats'));
    }

    // ═══ Parcours transfers ═══
    public function transfers()
    {
        $joueur = Auth::user()->joueur;

        $transfers = $joueur->transfers()
                            ->orderBy('created_at', 'desc')
                            ->paginate(15);

        return view('joueur.carriere.transfers', compact('transfers'));
    }

    // ═══ Télécharger CV ═══
    public function downloadCV()
    {
        $joueur = Auth::user()->joueur;
        
        // Générer PDF du CV
        return response()->download('path/to/cv.pdf');
    }

    // ═══ Helper: construire timeline ═══
    private function buildTimeline($joueur)
    {
        $events = [];

        // Ajouter les clubs
        foreach ($joueur->clubs as $club) {
            $events[] = [
                'type' => 'club',
                'date' => $club->pivot->date_debut,
                'data' => $club,
            ];
        }

        // Ajouter les transfers
        foreach ($joueur->transfers as $transfer) {
            $events[] = [
                'type' => 'transfer',
                'date' => $transfer->date_effet,
                'data' => $transfer,
            ];
        }

        usort($events, fn($a, $b) => $a['date'] <=> $b['date']);
        return $events;
    }
}
