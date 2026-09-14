<?php
// app/Http/Controllers/Club/AbonnementController.php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Abonnement;
use App\Models\SubscriptionPlan;
use App\Services\FedaPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AbonnementController extends Controller
{
    public function __construct(protected FedaPayService $fedaPay) {}

    // ═══ Mon abonnement ═══
    public function index()
    {
        $club       = Auth::user()->club;
        $abonnement = $club->subscriptionActive();
        $planActif  = $abonnement?->subscriptionPlan; // ← relation renommée
        $plans      = SubscriptionPlan::where('actif', true)->orderBy('prix')->get();
        $historique = Abonnement::where('club_id', $club->id)
                        ->with('subscriptionPlan')
                        ->latest()
                        ->take(5)
                        ->get();

        return view('club.abonnement.index',
            compact('club', 'abonnement', 'planActif', 'plans', 'historique'));
    }

    // ═══ Choisir un plan ═══
    public function choisir()
    {
        $club      = Auth::user()->club;
        $plans     = SubscriptionPlan::where('actif', true)->orderBy('prix')->get();
        $planActif = $club->subscriptionActive()?->subscriptionPlan; // ← relation renommée

        return view('club.abonnement.choisir', compact('plans', 'planActif', 'club'));
    }

    // ═══ Initier le paiement ═══
    public function souscrire(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $club = Auth::user()->club;
        $user = Auth::user();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        // Plan gratuit → activation directe
        if ($plan->prix == 0) {
            return $this->activerDirectement($club, $plan);
        }

        // Vérifier downgrade interdit
        $subActif = $club->subscriptionActive();
        if ($subActif && !$this->peutUpgrader($subActif->subscriptionPlan, $plan)) {
            return response()->json([
                'message' => 'Vous ne pouvez pas passer à un plan inférieur avant expiration.',
            ], 422);
        }

        // Créer abonnement en_attente
        $abonnement = Abonnement::create([
            'club_id'              => $club->id,
            'subscription_plan_id' => $plan->id,
            'plan'                 => $plan->slug,
            'montant'              => $plan->prix,
            'devise'               => 'XOF',
            'statut'               => 'en_attente',
            'debut_at'             => now(),
        ]);

        // Initier transaction FedaPay
        try {
            $result = $this->fedaPay->creerTransaction([
                'description'  => 'ConnectSport — Plan ' . $plan->nom,
                'amount'       => (int) $plan->prix,
                'currency'     => 'XOF',
                'callback_url' => route('club.abonnement.callback'),
                'return_url'   => route('club.abonnement.succes') . '?ab_id=' . $abonnement->id,
                'customer'     => [
                    'firstname' => $user->prenom ?? $user->name,
                    'lastname'  => $user->name,
                    'email'     => $user->email,
                    'phone'     => $user->telephone ?? '',
                ],
            ]);

            $abonnement->update([
                'fedapay_transaction_id' => (string) $result->transaction_id,
                'fedapay_token'          => $result->token,
            ]);

            return response()->json(['payment_url' => $result->payment_url]);

        } catch (\Exception $e) {
            Log::error('FedaPay error: ' . $e->getMessage());
            $abonnement->delete();
            return response()->json([
                'message' => 'Erreur lors de l\'initialisation du paiement. Réessayez.',
            ], 500);
        }
    }

    // ═══ Callback FedaPay (webhook POST) ═══
    public function callback(Request $request)
    {
        try {
            $transactionId = $request->input('id');
            if (!$transactionId) {
                return response()->json(['error' => 'ID manquant'], 400);
            }

            $result = $this->fedaPay->verifierTransaction((string) $transactionId);

            $abonnement = Abonnement::where('fedapay_transaction_id', (string) $transactionId)
                                    ->with('subscriptionPlan')
                                    ->first();

            if (!$abonnement) {
                return response()->json(['error' => 'Abonnement introuvable'], 404);
            }

            if ($result->statut === 'approved') {
                $this->confirmerAbonnement($abonnement);
            } elseif (in_array($result->statut, ['declined', 'cancelled'])) {
                $abonnement->update(['statut' => 'annule']);
            }

            return response()->json(['message' => 'OK']);

        } catch (\Exception $e) {
            Log::error('FedaPay callback: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    // ═══ Page succès (retour après paiement) ═══
    public function succes(Request $request)
    {
        $club       = Auth::user()->club;
        $abonnement = null;

        if ($request->filled('ab_id')) {
            $abonnement = Abonnement::where('id', $request->ab_id)
                                    ->where('club_id', $club->id)
                                    ->with('subscriptionPlan')
                                    ->first();

            // Si toujours en_attente → vérifier FedaPay
            if ($abonnement?->statut === 'en_attente' && $abonnement->fedapay_transaction_id) {
                try {
                    $result = $this->fedaPay->verifierTransaction($abonnement->fedapay_transaction_id);
                    if ($result->statut === 'approved') {
                        $this->confirmerAbonnement($abonnement);
                        $abonnement->refresh();
                    }
                } catch (\Exception $e) {
                    Log::error('FedaPay succes check: ' . $e->getMessage());
                }
            }
        }

        $subActif = $club->subscriptionActive();
        return view('club.abonnement.succes', compact('abonnement', 'subActif', 'club'));
    }

    // ═══ Helpers privés ═══

    private function confirmerAbonnement(Abonnement $abonnement): void
    {
        // Désactiver l'ancien actif
        Abonnement::where('club_id', $abonnement->club_id)
                  ->where('statut', 'actif')
                  ->where('id', '!=', $abonnement->id)
                  ->update(['statut' => 'expire']);

        $debut    = now();
        $expireLe = match($abonnement->subscriptionPlan?->frequence) {
            'mensuel' => $debut->copy()->addMonth(),
            'annuel'  => $debut->copy()->addYear(),
            default   => null,
        };

        $abonnement->update([
            'statut'   => 'actif',
            'debut_at' => $debut,
            'fin_at'   => $expireLe,
            'metadata' => ['confirmed_at' => now()->toISOString()],
        ]);
    }

    private function activerDirectement($club, SubscriptionPlan $plan)
    {
        Abonnement::where('club_id', $club->id)
                  ->where('statut', 'actif')
                  ->update(['statut' => 'expire']);

        Abonnement::create([
            'club_id'              => $club->id,
            'subscription_plan_id' => $plan->id,
            'plan'                 => $plan->slug,
            'montant'              => 0,
            'devise'               => 'XOF',
            'statut'               => 'actif',
            'debut_at'             => now(),
            'fin_at'               => null,
        ]);

        return response()->json([
            'message'  => 'Plan gratuit activé.',
            'redirect' => route('club.abonnement.index'),
        ]);
    }

    private function peutUpgrader(?SubscriptionPlan $actuel, SubscriptionPlan $nouveau): bool
    {
        if (!$actuel) return true;
        $niveaux = ['gratuit' => 0, 'standard' => 1, 'premium' => 2];
        return ($niveaux[$nouveau->slug] ?? 0) >= ($niveaux[$actuel->slug] ?? 0);
    }
}