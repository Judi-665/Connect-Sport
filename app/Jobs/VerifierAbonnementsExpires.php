<?php
// app/Jobs/VerifierAbonnementsExpires.php

namespace App\Jobs;

use App\Models\Abonnement;
use App\Models\SubscriptionPlan;
use App\Notifications\AbonnementExpireNotification;
use App\Notifications\AbonnementBientotExpireNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VerifierAbonnementsExpires implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // ── Abonnements expirés → retour plan gratuit ──
        Abonnement::where('statut', 'actif')
            ->whereNotNull('fin_at')
            ->where('fin_at', '<', now())
            ->each(function ($ab) {
                $ab->update(['statut' => 'expire']);

                $typeActeur = $ab->agent_id ? 'agent' : ($ab->club_id ? 'club' : 'joueur');
                $planGratuit = SubscriptionPlan::where('slug', 'gratuit')
                    ->where('type_acteur', $typeActeur)->first();

                if ($planGratuit) {
                    Abonnement::create([
                        'club_id'              => $ab->club_id,
                        'joueur_id'            => $ab->joueur_id,
                        'agent_id'             => $ab->agent_id,
                        'subscription_plan_id' => $planGratuit->id,
                        'plan'                 => 'gratuit',
                        'montant'              => 0,
                        'devise'               => 'XOF',
                        'statut'               => 'actif',
                        'debut_at'             => now(),
                        'fin_at'               => null,
                    ]);
                }

                if ($ab->agent) {
                    $ab->agent->update(['mise_en_avant' => false]);
                }

                $ab->abonnable()?->user?->notify(new AbonnementExpireNotification($ab));
            });

        // ── Rappel J-7 ──
        Abonnement::expirentBientot(7)
            ->where('rappel_7j_envoye', false)
            ->each(function ($ab) {
                $ab->abonnable()?->user?->notify(new AbonnementBientotExpireNotification($ab));
                $ab->update(['rappel_7j_envoye' => true]);
            });

        // ── Rappel J-1 ──
        Abonnement::expirentBientot(1)
            ->where('rappel_1j_envoye', false)
            ->each(function ($ab) {
                $ab->abonnable()?->user?->notify(new AbonnementBientotExpireNotification($ab));
                $ab->update(['rappel_1j_envoye' => true]);
            });
    }
}