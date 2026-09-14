<?php
// app/Notifications/AbonnementBientotExpireNotification.php

namespace App\Notifications;

use App\Models\Abonnement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AbonnementBientotExpireNotification extends Notification
{
    use Queueable;

    public function __construct(public Abonnement $abonnement) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $jours = max(0, (int) now()->diffInDays($this->abonnement->fin_at, false));
        $route = $this->abonnement->club_id ? 'club.abonnement.choisir' : 'joueur.abonnement.choisir';

        return (new MailMessage)
            ->subject('Votre abonnement expire dans ' . $jours . ' jour(s)')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Votre abonnement **' . ($this->abonnement->subscriptionPlan?->nom ?? $this->abonnement->plan) . '** expire dans **' . $jours . ' jour(s)**.')
            ->action('Renouveler maintenant', route($route))
            ->line('Merci d\'utiliser ConnectSport.');
    }

    public function toArray($notifiable): array
    {
        $jours = max(0, (int) now()->diffInDays($this->abonnement->fin_at, false));
        $route = $this->abonnement->club_id ? 'club.abonnement.choisir' : 'joueur.abonnement.choisir';

        return [
            'type'    => 'abonnement_bientot_expire',
            'message' => 'Votre abonnement expire dans ' . $jours . ' jour(s).',
            'url'     => route($route),
        ];
    }
}