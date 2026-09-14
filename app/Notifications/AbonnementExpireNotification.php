<?php
// app/Notifications/AbonnementExpireNotification.php

namespace App\Notifications;

use App\Models\Abonnement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AbonnementExpireNotification extends Notification
{
    use Queueable;

    public function __construct(public Abonnement $abonnement) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $route = $this->abonnement->club_id ? 'club.abonnement.choisir' : 'joueur.abonnement.choisir';

        return (new MailMessage)
            ->subject('Votre abonnement ConnectSport a expiré')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Votre abonnement **' . ($this->abonnement->subscriptionPlan?->nom ?? $this->abonnement->plan) . '** a expiré.')
            ->line('Vous êtes automatiquement passé au plan Gratuit.')
            ->action('Renouveler mon abonnement', route($route))
            ->line('Merci d\'utiliser ConnectSport.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type'    => 'abonnement_expire',
            'message' => 'Votre abonnement ' . ($this->abonnement->subscriptionPlan?->nom ?? $this->abonnement->plan) . ' a expiré.',
            'url'     => route($route ?? ($this->abonnement->club_id ? 'club.abonnement.choisir' : 'joueur.abonnement.choisir')),
        ];
    }
}