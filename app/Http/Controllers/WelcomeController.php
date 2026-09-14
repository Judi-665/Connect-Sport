<?php
// app/Http/Controllers/WelcomeController.php
// Adaptez les noms de modèles selon votre projet.

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\User;
use App\Models\Opportunite;
use App\Models\Sport;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    public function index(): View
    {
        // ── Statistiques globales ──────────────────────────────────────────────
        // Toutes les clés utilisées dans welcome.blade.php et _ecosysteme.blade.php.
        // Remplacez les noms de modèles/colonnes selon votre schéma réel.
        $stats = [
            // Acteurs
            'clubs'       => Club::count(),
            'joueurs'     => User::where('role', 'joueur')->count(),
            'agents'      => User::where('role', 'agent')->count(),
            'sponsors'    => User::where('role', 'sponsor')->count(),
            'parents'     => User::where('role', 'parent')->count(),
            'supporters'  => User::where('role', 'supporter')->count(),

            // Activité
            'opportunites' => Opportunite::count(),
            'transferts'   => 0, // Opportunite::where('type','transfert')->count()
            'licences'     => 0, // Licence::count()

            // Sports
            'sports'      => Sport::count(),
        ];

        // ── Clubs récents ──────────────────────────────────────────────────────
        $clubs = Club::with('sport')
            ->withCount('joueurs')
            ->latest()
            ->take(12)     // 4 slides × 3 clubs
            ->get();

        // ── Opportunités récentes ──────────────────────────────────────────────
        $opportunites = Opportunite::with(['sport', 'club'])
            ->latest()
            ->take(5)
            ->get();

        return view('welcome', compact('stats', 'clubs', 'opportunites'));
    }
}