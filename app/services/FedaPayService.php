<?php
// app/Services/FedaPayService.php

namespace App\Services;

use FedaPay\FedaPay;
use FedaPay\Transaction;

class FedaPayService
{
    public function __construct()
    {
        FedaPay::setApiKey(config('services.fedapay.secret_key'));
        FedaPay::setEnvironment(config('services.fedapay.env', 'sandbox'));
    }

    /**
     * Créer une transaction FedaPay et retourner le token de paiement
     */
    public function creerTransaction(array $params): object
    {
        $transactionParams = [
            'description' => $params['description'],
            'amount'      => $params['amount'],
            'currency'    => ['iso' => $params['currency'] ?? 'XOF'],
            'callback_url'=> $params['callback_url'],
            'return_url'  => $params['return_url'],
        ];

        if (!blank($params['customer']['phone'] ?? null)) {
            $transactionParams['customer'] = [
                'firstname'    => $params['customer']['firstname'],
                'lastname'     => $params['customer']['lastname'],
                'email'        => $params['customer']['email'],
                'phone_number' => [
                    'number'  => $params['customer']['phone'] ?? '',
                    'country' => 'BJ',
                ],
            ];
        }

        $transaction = Transaction::create($transactionParams);

        $token = $transaction->generateToken();

        return (object) [
            'transaction_id' => $transaction->id,
            'token'          => $token->token,
            'payment_url'    => $token->url,
        ];
    }
    /**
     * Vérifier le statut d'une transaction
     */
    public function verifierTransaction(string $transactionId): object
    {
        $transaction = Transaction::retrieve($transactionId);

        return (object) [
            'id'     => $transaction->id,
            'statut' => $transaction->status, // approved, declined, cancelled
            'amount' => $transaction->amount,
        ];
    }
}