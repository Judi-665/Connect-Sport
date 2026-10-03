<?php

namespace App\Policies;

use App\Models\Sponsor;
use App\Models\User;

class SponsorPolicy
{
    public function view(User $user, Sponsor $sponsor): bool
    {
        return $user->club?->id === $sponsor->club_id;
    }

    public function update(User $user, Sponsor $sponsor): bool
    {
        return $this->view($user, $sponsor);
    }

    public function delete(User $user, Sponsor $sponsor): bool
    {
        return $this->view($user, $sponsor);
    }
}