<?php
// app/Http/Controllers/Paiement/WebhookController.php

namespace App\Http\Controllers\Paiement;

use App\Http\Controllers\Controller;
use App\Models\Abonnement;
use App\Models\PaiementFormation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    // ═══ Webhook FedaPay ═══
    public function fedapay(Request $request)
    {
        Log::info('FedaPay Webhook received', $request->all());

        $event = $request->input('event');
        $data = $request->input('data');

        try {
            match ($event) {
                'transaction.success'  => $this->handleSuccess($data),
                'transaction.failed'   => $this->handleFailed($data),
                'transaction.pending'  => $this->handlePending($data),
                default                => null,
            };

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Webhook processing error', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // ═══ Traiter paiement réussi ═══
    private function handleSuccess($data)
    {
        $transactionId = $data['transaction_id'];

        // Chercher abonnement
        $abonnement = Abonnement::where('fedapay_transaction_id', $transactionId)->first();
        if ($abonnement) {
            $abonnement->update([
                'statut'   => 'actif',
                'debut_at' => now(),
                'fin_at'   => now()->addYear(),
            ]);

            Log::info('Abonnement activated', ['abonnement_id' => $abonnement->id]);
            return;
        }

        // Chercher paiement formation
        $paiement = PaiementFormation::where('fedapay_transaction_id', $transactionId)->first();
        if ($paiement) {
            $paiement->update([
                'statut'   => 'reussi',
                'paye_at'  => now(),
            ]);

            Log::info('Formation payment succeeded', ['paiement_id' => $paiement->id]);
        }
    }

    // ═══ Traiter paiement échoué ═══
    private function handleFailed($data)
    {
        $transactionId = $data['transaction_id'];

        // Abonnement
        Abonnement::where('fedapay_transaction_id', $transactionId)
                 ->update(['statut' => 'echoue']);

        // Paiement formation
        PaiementFormation::where('fedapay_transaction_id', $transactionId)
                        ->update(['statut' => 'echoue']);

        Log::warning('Payment failed', ['transaction_id' => $transactionId]);
    }

    // ═══ Traiter paiement en attente ═══
    private function handlePending($data)
    {
        $transactionId = $data['transaction_id'];

        Abonnement::where('fedapay_transaction_id', $transactionId)
                 ->update(['statut' => 'en_attente']);

        PaiementFormation::where('fedapay_transaction_id', $transactionId)
                        ->update(['statut' => 'en_attente']);

        Log::info('Payment pending', ['transaction_id' => $transactionId]);
    }

    // ═══ Webhook d'autres services ═══
    public function mollie(Request $request)
    {
        // Implémenter si besoin
        Log::info('Mollie Webhook', $request->all());
        return response()->json(['success' => true]);
    }

    public function stripe(Request $request)
    {
        // Implémenter si besoin
        Log::info('Stripe Webhook', $request->all());
        return response()->json(['success' => true]);
    }
}
