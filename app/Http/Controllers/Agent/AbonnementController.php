<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Abonnement;
use App\Models\SubscriptionPlan;
use App\Services\FedaPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AbonnementController extends Controller
{
    public function __construct(protected FedaPayService $fedaPay) {}

    public function index()
    {
        $agent      = Auth::user()->agent;
        $abonnement = $agent->subscriptionActive();
        $planActif  = $abonnement?->subscriptionPlan;
        $plans      = SubscriptionPlan::where('type_acteur', 'agent')->where('actif', true)->orderBy('prix')->get();
        $historique = Abonnement::where('agent_id', $agent->id)->with('subscriptionPlan')->latest()->take(5)->get();

        return view('agents.abonnement.index', compact('agent', 'abonnement', 'planActif', 'plans', 'historique'));
    }

    public function choisir()
    {
        $agent     = Auth::user()->agent;
        $plans     = SubscriptionPlan::where('type_acteur', 'agent')->where('actif', true)->orderBy('prix')->get();
        $planActif = $agent->subscriptionActive()?->subscriptionPlan;

        return view('agents.abonnement.choisir', compact('plans', 'planActif', 'agent'));
    }

    public function souscrire(Request $request)
    {
        $request->validate(['plan_id' => 'required|exists:subscription_plans,id']);

        $agent = Auth::user()->agent;
        $user  = Auth::user();
        $plan  = SubscriptionPlan::where('type_acteur', 'agent')->findOrFail($request->plan_id);

        if ($plan->prix == 0) {
            return $this->activerDirectement($agent, $plan);
        }

        $subActif = $agent->subscriptionActive();
        if ($subActif && !$this->peutUpgrader($subActif->subscriptionPlan, $plan)) {
            return response()->json(['message' => 'Vous ne pouvez pas passer à un plan inférieur avant expiration.'], 422);
        }

        $abonnement = Abonnement::create([
            'agent_id'             => $agent->id,
            'subscription_plan_id' => $plan->id,
            'plan'                 => Str::before($plan->slug, '-'),
            'montant'              => $plan->prix,
            'devise'               => 'XOF',
            'statut'               => 'en_attente',
            'debut_at'             => now(),
        ]);

        try {
            $result = $this->fedaPay->creerTransaction([
                'description'  => 'ConnectSport — Plan ' . $plan->nom,
                'amount'       => (int) $plan->prix,
                'currency'     => 'XOF',
                'callback_url' => route('agent.abonnement.callback'),
                'return_url'   => route('agent.abonnement.index'),
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
            Log::error('FedaPay error (agent): ' . $e->getMessage());
            $abonnement->delete();
            return response()->json(['message' => 'Erreur lors de l\'initialisation du paiement. Réessayez.'], 500);
        }
    }

    public function callback(Request $request)
    {
        try {
            $transactionId = $request->input('id');
            if (!$transactionId) {
                return response()->json(['error' => 'ID manquant'], 400);
            }

            $result = $this->fedaPay->verifierTransaction((string) $transactionId);
            $abonnement = Abonnement::where('fedapay_transaction_id', (string) $transactionId)->with('subscriptionPlan')->first();

            if (!$abonnement) {
                return response()->json(['error' => 'Abonnement introuvable'], 404);
            }

            if ($result->statut === 'approved') {
                $this->confirmerAbonnement($abonnement);
            } elseif (in_array($result->statut, ['declined', 'cancelled'])) {
                $abonnement->update(['statut' => 'annule']);
            }

            if ($request->isMethod('GET') || Auth::check()) {
                return redirect()->route('agent.abonnement.index');
            }

            return response()->json(['message' => 'OK']);
        } catch (\Exception $e) {
            Log::error('FedaPay callback (agent): ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    public function succes(Request $request)
    {
        $agent      = Auth::user()->agent;
        $abonnement = null;

        if ($request->filled('ab_id')) {
            $abonnement = Abonnement::where('id', $request->ab_id)->where('agent_id', $agent->id)->with('subscriptionPlan')->first();

            if ($abonnement?->statut === 'en_attente' && $abonnement->fedapay_transaction_id) {
                try {
                    $result = $this->fedaPay->verifierTransaction($abonnement->fedapay_transaction_id);
                    if ($result->statut === 'approved') {
                        $this->confirmerAbonnement($abonnement);
                        $abonnement->refresh();
                    }
                } catch (\Exception $e) {
                    Log::error('FedaPay succes check (agent): ' . $e->getMessage());
                }
            }
        }

        $subActif = $agent->subscriptionActive();
        return view('agents.abonnement.succes', compact('abonnement', 'subActif', 'agent'));
    }

    private function confirmerAbonnement(Abonnement $abonnement): void
    {
        Abonnement::where('agent_id', $abonnement->agent_id)->where('statut', 'actif')->where('id', '!=', $abonnement->id)->update(['statut' => 'expire']);

        $debut    = now();
        $expireLe = match($abonnement->subscriptionPlan?->frequence) {
            'mensuel' => $debut->copy()->addMonth(),
            'annuel'  => $debut->copy()->addYear(),
            default   => null,
        };

        $abonnement->update(['statut' => 'actif', 'debut_at' => $debut, 'fin_at' => $expireLe, 'metadata' => ['confirmed_at' => now()->toISOString()]]);
        $abonnement->agent?->update([
            'mise_en_avant' => (bool) $abonnement->subscriptionPlan?->mise_en_avant,
        ]);
    }

    private function activerDirectement($agent, SubscriptionPlan $plan)
    {
        Abonnement::where('agent_id', $agent->id)->where('statut', 'actif')->update(['statut' => 'expire']);

        $abonnement = Abonnement::create([
            'agent_id'             => $agent->id,
            'subscription_plan_id' => $plan->id,
            'plan'                 => Str::before($plan->slug, '-'),
            'montant'              => 0,
            'devise'               => 'XOF',
            'statut'               => 'actif',
            'debut_at'             => now(),
            'fin_at'               => null,
        ]);

        $agent->update(['mise_en_avant' => (bool) $plan->mise_en_avant]);

        return response()->json(['message' => 'Plan gratuit activé.', 'redirect' => route('agent.abonnement.index')]);
    }

    private function peutUpgrader(?SubscriptionPlan $actuel, SubscriptionPlan $nouveau): bool
    {
        if (!$actuel) return true;
        $niveaux = ['gratuit' => 0, 'standard' => 1, 'premium' => 2];
        $niveauActuel  = $niveaux[Str::before($actuel->slug, '-')] ?? 0;
        $niveauNouveau = $niveaux[Str::before($nouveau->slug, '-')] ?? 0;
        return $niveauNouveau >= $niveauActuel;
    }
}