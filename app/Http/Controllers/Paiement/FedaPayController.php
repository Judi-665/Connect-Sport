<?php
// app/Http/Controllers/Paiement/FedaPayController.php

namespace App\Http\Controllers\Paiement;

use App\Http\Controllers\Controller;
use App\Models\Abonnement;
use App\Models\PaiementFormation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FedaPayController extends Controller
{
    protected $fedapayApiKey;
    protected $fedapayApiUrl;

    public function __construct()
    {
        $this->fedapayApiKey = config('services.fedapay.api_key');
        $this->fedapayApiUrl = config('services.fedapay.api_url');
    }

    // ═══ Paiement abonnement club ═══
    public function abonnement(Request $request)
    {
        $user = Auth::user();
        $club = $user->club;

        if (!$club) {
            return redirect()->route('club.profil.create');
        }

        $validated = $request->validate([
            'plan'     => 'required|in:standard,premium',
            'devise'   => 'required|string|max:3',
            'montant'  => 'required|numeric|min:0',
        ]);

        // Créer une transaction FedaPay
        $transaction = $this->createTransaction(
            montant: $validated['montant'],
            devise: $validated['devise'],
            description: "Abonnement {$validated['plan']} - {$club->nom}",
            type: 'abonnement',
            club_id: $club->id,
        );

        // Créer l'enregistrement d'abonnement
        $abonnement = Abonnement::create([
            'club_id'                   => $club->id,
            'plan'                      => $validated['plan'],
            'montant'                   => $validated['montant'],
            'devise'                    => $validated['devise'],
            'fedapay_transaction_id'    => $transaction['id'],
            'fedapay_token'             => $transaction['token'],
            'mode_paiement'             => 'fedapay',
            'statut'                    => 'en_attente',
        ]);

        return redirect()->to($transaction['checkout_url'])
                        ->with('abonnement_id', $abonnement->id);
    }

    // ═══ Paiement formation ═══
    public function formation(Request $request)
    {
        $joueur = Auth::user()->joueur;

        if (!$joueur) {
            return redirect()->route('joueur.profil.create');
        }

        $validated = $request->validate([
            'formation_id' => 'required|exists:formations,id',
            'devise'       => 'required|string|max:3',
            'montant'      => 'required|numeric|min:0',
        ]);

        $transaction = $this->createTransaction(
            montant: $validated['montant'],
            devise: $validated['devise'],
            description: "Formation - {$validated['formation_id']}",
            type: 'formation',
            joueur_id: $joueur->id,
        );

        $paiement = PaiementFormation::create([
            'joueur_id'                 => $joueur->id,
            'formation_id'              => $validated['formation_id'],
            'montant'                   => $validated['montant'],
            'devise'                    => $validated['devise'],
            'fedapay_transaction_id'    => $transaction['id'],
            'fedapay_token'             => $transaction['token'],
            'mode_paiement'             => 'fedapay',
            'statut'                    => 'en_attente',
        ]);

        return redirect()->to($transaction['checkout_url']);
    }

    // ═══ Créer transaction ═══
    private function createTransaction($montant, $devise, $description, $type, $club_id = null, $joueur_id = null)
    {
        try {
            $response = Http::withToken($this->fedapayApiKey)
                            ->post("{$this->fedapayApiUrl}/transactions", [
                                'amount'      => $montant * 100,  // FedaPay en centimes
                                'currency'    => $devise,
                                'description' => $description,
                                'callback_url' => route('paiement.webhook'),
                            ]);

            if (!$response->successful()) {
                Log::error('FedaPay error', $response->json());
                throw new \Exception('Erreur FedaPay');
            }

            $data = $response->json();

            return [
                'id'           => $data['transaction']['id'],
                'token'        => $data['transaction']['token'],
                'checkout_url' => $data['transaction']['links']['checkout_url'],
            ];

        } catch (\Exception $e) {
            Log::error('Transaction creation failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    // ═══ Vérifier statut paiement ═══
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'transaction_id' => 'required|string',
        ]);

        try {
            $response = Http::withToken($this->fedapayApiKey)
                            ->get("{$this->fedapayApiUrl}/transactions/{$validated['transaction_id']}");

            return response()->json($response->json());

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
