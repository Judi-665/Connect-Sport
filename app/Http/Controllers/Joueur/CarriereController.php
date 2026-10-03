<?php
// app/Http/Controllers/Joueur/CarriereController.php

namespace App\Http\Controllers\Joueur;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;


class CarriereController extends Controller
{
    public function index()
    {
        $joueur = Auth::user()->joueur;

        $historique = $joueur->carrieres()
                             ->orderBy('date_debut', 'desc')
                             ->paginate(10);

        $clubActuel = $joueur->club;
        $equipesActuelles = $joueur->equipes()
                                   ->wherePivot('actif', true)
                                   ->get();

        return view('joueur.carriere.index', compact('historique', 'clubActuel', 'equipesActuelles'));
    }

    public function profil()
    {
        $joueur = Auth::user()->joueur;

        $stats = [
            'clubs'    => $joueur->carrieres()->distinct('club_id')->count('club_id'),
            'equipes'  => $joueur->equipes()->count(),
            'licences' => $joueur->licences()->count(),
            'matchs'   => $joueur->statistiques()->sum('matchs_joues'),
            'buts'     => $joueur->statistiques()->sum('buts'),
            'passes'   => $joueur->statistiques()->sum('passes_decisives'),
        ];

        $carriereTimeline = $this->buildTimeline($joueur);

        return view('joueur.carriere.profil', compact('joueur', 'stats', 'carriereTimeline'));
    }

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

    // Détail par saison — alimente la courbe d'évolution
    $parSaison = $joueur->statistiques()
                        ->selectRaw('
                            saison,
                            SUM(matchs_joues) as matchs,
                            SUM(buts) as buts,
                            SUM(passes_decisives) as passes,
                            AVG(note_moyenne) as note_moyenne
                        ')
                        ->groupBy('saison')
                        ->orderBy('saison')
                        ->get();

    return view('joueur.carriere.stats-globales', compact('stats', 'parSaison'));
}
    public function transferts()
    {
        $joueur = Auth::user()->joueur;

        $transferts = $joueur->transferts()
                             ->orderBy('created_at', 'desc')
                             ->paginate(15);

        return view('joueur.carriere.transferts', compact('transferts'));
    }

    public function downloadCV()
{
    $joueur = Auth::user()->joueur;
    $joueur->load('user', 'club');

    $stats = $joueur->statistiques()
                    ->selectRaw('
                        COUNT(DISTINCT saison) as saisons,
                        SUM(matchs_joues) as matchs,
                        SUM(buts) as buts,
                        SUM(passes_decisives) as passes,
                        AVG(note_moyenne) as note_moyenne
                    ')
                    ->first();

    $carrieres = $joueur->carrieres()
                        ->with('club')
                        ->orderBy('date_debut', 'desc')
                        ->get();

    $pdf = Pdf::loadView('joueur.carriere.cv-pdf', compact('joueur', 'stats', 'carrieres'));

    $nomFichier = 'CV_' . Str::slug($joueur->nomComplet()) . '.pdf';

    return $pdf->download($nomFichier);
}

    private function buildTimeline($joueur)
    {
        $events = [];

        foreach ($joueur->carrieres as $carriere) {
            $events[] = ['type' => 'carriere', 'date' => $carriere->date_debut, 'data' => $carriere];
        }

        foreach ($joueur->transferts as $transfert) {
            $events[] = ['type' => 'transfert', 'date' => $transfert->date_effet, 'data' => $transfert];
        }

        usort($events, fn($a, $b) => $a['date'] <=> $b['date']);
        return $events;
    }
}