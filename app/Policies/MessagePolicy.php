<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    /**
     * Un utilisateur peut voir un message s'il en est l'expediteur ou le destinataire.
     * L'admin peut tout voir.
     */
    public function view(User $user, Message $message): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $message->expediteur_id
            || $user->id === $message->destinataire_id;
    }

    /**
     * Un utilisateur peut supprimer un message s'il en est l'expediteur ou le destinataire.
     * L'admin peut tout supprimer.
     */
    public function delete(User $user, Message $message): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $message->expediteur_id
            || $user->id === $message->destinataire_id;
    }
}
