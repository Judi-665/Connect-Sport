<?php
// app/Http/Middleware/CheckAbonnement.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAbonnement
{
    public function handle(Request $request, Closure $next, string $feature = null)
    {
        $club = $request->user()?->club;

        if (!$club) return $next($request);

        $abonnement = $club->subscriptionActive();

        // Pas d'abonnement actif
        if (!$abonnement) {
            return response()->view('club.locked', [
                'message'    => 'Vous devez avoir un abonnement actif pour accéder à cette page.',
                'planRequis' => 'standard',
            ], 403);
        }

        // Feature spécifique requise
        if ($feature && !$abonnement->plan->$feature) {
            $planRequis = match($feature) {
    'stats_avancees', 'stockage_etendu',
    'mise_en_avant', 'outils_marketing',
    'notifications_ciblees',
    'galerie_media'                      => 'premium',  // ← ajouter ici
    default                              => 'standard',
};

            return response()->view('club.locked', [
                'message'    => 'Cette fonctionnalité nécessite un abonnement ' . ucfirst($planRequis) . '.',
                'planRequis' => $planRequis,
            ], 403);
        }

        return $next($request);
    }
}