<?php
// app/Models/ClubSubscription.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ClubSubscription extends Model
{
    protected $fillable = [
        'club_id',
        'subscription_plan_id',
        'statut',
        'debut_le',
        'expire_le',
        'reference_paiement',
    ];

    protected $casts = [
        'debut_le'  => 'datetime',
        'expire_le' => 'datetime',
    ];

    // Relations
    public function club()
{
    return $this->belongsTo(Club::class, 'club_id');
}

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    // Helpers
    public function estActif(): bool
    {
        if ($this->statut !== 'actif') return false;
        if (is_null($this->expire_le)) return true; // gratuit illimité
        return $this->expire_le->isFuture();
    }

    public function estExpire(): bool
    {
        if (is_null($this->expire_le)) return false;
        return $this->expire_le->isPast();
    }

    public function joursRestants(): int
    {
        if (is_null($this->expire_le)) return 9999;
        return max(0, (int) now()->diffInDays($this->expire_le, false));
    }
}