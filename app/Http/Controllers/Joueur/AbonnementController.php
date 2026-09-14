<?php

namespace App\Http\Controllers\Joueur;

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

    public function index()
    {
        $joueur     = Auth::user()->joueur;
        $abonnement = $joueur->subscriptionActive();
        $planActif  = $abonnement?->subscriptionPlan;
        $plans      = SubscriptionPlan::where('actif', true)
                        ->where('type_acteur', 'joueur')
                        ->orderBy('prix')->get();
        $historique = Abonnement::where('joueur_id', $joueur->id)
                        ->with('subscriptionPlan')->latest()->take(5)->get();

        return view('joueur.abonnement.index',
            compact('joueur', 'abonnement', 'planActif', 'plans', 'historique'));
    }

    public function choisir()
    {
        $joueur    = Auth::user()->joueur;
        $plans     = SubscriptionPlan::where('actif', true)
                        ->where('type_acteur', 'joueur')->orderBy('prix')->get();
        $planActif = $joueur->subscriptionActive()?->subscriptionPlan;

        return view('joueur.abonnement.choisir', compact('plans', 'planActif', 'joueur'));
    }

    public function souscrire(Request $request)
    {
        $request->validate(['plan_id' => 'required|exists:subscription_plans,id']);

        $joueur = Auth::user()->joueur;
        $user   = Auth::user();
        $plan   = SubscriptionPlan::findOrFail($request->plan_id);

        if ($plan->prix == 0) {
            return $this->activerDirectement($joueur, $plan);
        }

        $subActif = $joueur->subscriptionActive();
        if ($subActif && !$this->peutUpgrader($subActif->subscriptionPlan, $plan)) {
            return response()->json([
                'message' => 'Vous ne pouvez pas passer à un plan inférieur avant expiration.',
            ], 422);
        }

        $abonnement = Abonnement::create([
            'joueur_id'            => $joueur->id,
            'subscription_plan_id' => $plan->id,
            'plan'                 => $plan->slug,
            'montant'              => $plan->prix,
            'devise'               => 'XOF',
            'statut'               => 'en_attente',
            'debut_at'             => now(),
        ]);

        try {
            $result = $this->fedaPay->creerTransaction([
                'description'  => 'ConnectSport — Plan Joueur ' . $plan->nom,
                'amount'       => (int) $plan->prix,
                'currency'     => 'XOF',
                'callback_url' => route('joueur.abonnement.callback'),
                'return_url'   => route('joueur.abonnement.succes') . '?ab_id=' . $abonnement->id,
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
            Log::error('FedaPay error (joueur): ' . $e->getMessage());
            $abonnement->delete();
            return response()->json(['message' => 'Erreur lors de l\'initialisation du paiement. Réessayez.'], 500);
        }
    }

    public function callback(Request $request)
    {
        try {
            $transactionId = $request->input('id');
            if (!$transactionId) return response()->json(['error' => 'ID manquant'], 400);

            $result = $this->fedaPay->verifierTransaction((string) $transactionId);
            $abonnement = Abonnement::where('fedapay_transaction_id', (string) $transactionId)
                            ->with('subscriptionPlan')->first();

            if (!$abonnement) return response()->json(['error' => 'Abonnement introuvable'], 404);

            if ($result->statut === 'approved') {
                $this->confirmerAbonnement($abonnement);
            } elseif (in_array($result->statut, ['declined', 'cancelled'])) {
                $abonnement->update(['statut' => 'annule']);
            }

            if ($request->isMethod('GET') || Auth::check()) {
                $route = $result->statut === 'approved'
                    ? 'joueur.abonnement.index'
                    : 'joueur.abonnement.succes';

                return redirect()->route($route, ['ab_id' => $abonnement->id]);
            }

            return response()->json(['message' => 'OK']);
        } catch (\Exception $e) {
            Log::error('FedaPay callback (joueur): ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    public function succes(Request $request)
    {
        $joueur     = Auth::user()->joueur;
        $abonnement = null;

        if ($request->filled('ab_id')) {
            $abonnement = Abonnement::where('id', $request->ab_id)
                            ->where('joueur_id', $joueur->id)
                            ->with('subscriptionPlan')->first();

            if ($abonnement?->statut === 'en_attente' && $abonnement->fedapay_transaction_id) {
                try {
                    $result = $this->fedaPay->verifierTransaction($abonnement->fedapay_transaction_id);
                    if ($result->statut === 'approved') {
                        $this->confirmerAbonnement($abonnement);
                        $abonnement->refresh();
                    }
                } catch (\Exception $e) {
                    Log::error('FedaPay succes check (joueur): ' . $e->getMessage());
                }
            }
        }

        $subActif = $joueur->subscriptionActive();
        return view('joueur.abonnement.succes', compact('abonnement', 'subActif', 'joueur'));
    }

    private function confirmerAbonnement(Abonnement $abonnement): void
    {
        Abonnement::where('joueur_id', $abonnement->joueur_id)
                  ->where('statut', 'actif')->where('id', '!=', $abonnement->id)
                  ->update(['statut' => 'expire']);

        $debut    = now();
        $expireLe = match($abonnement->subscriptionPlan?->frequence) {
            'mensuel' => $debut->copy()->addMonth(),
            'annuel'  => $debut->copy()->addYear(),
            default   => null,
        };

        $abonnement->update([
            'statut' => 'actif', 'debut_at' => $debut, 'fin_at' => $expireLe,
            'rappel_7j_envoye' => false, 'rappel_1j_envoye' => false,
            'metadata' => ['confirmed_at' => now()->toISOString()],
        ]);
    }

    private function activerDirectement($joueur, SubscriptionPlan $plan)
    {
        Abonnement::where('joueur_id', $joueur->id)->where('statut', 'actif')
                  ->update(['statut' => 'expire']);

        Abonnement::create([
            'joueur_id' => $joueur->id, 'subscription_plan_id' => $plan->id,
            'plan' => $plan->slug, 'montant' => 0, 'devise' => 'XOF',
            'statut' => 'actif', 'debut_at' => now(), 'fin_at' => null,
        ]);

        return response()->json(['message' => 'Plan gratuit activé.', 'redirect' => route('joueur.abonnement.index')]);
    }

    private function peutUpgrader(?SubscriptionPlan $actuel, SubscriptionPlan $nouveau): bool
    {
        if (!$actuel) return true;
        $niveaux = ['gratuit' => 0, 'standard' => 1, 'premium' => 2];
        return ($niveaux[$nouveau->slug] ?? 0) >= ($niveaux[$actuel->slug] ?? 0);
    }
}
