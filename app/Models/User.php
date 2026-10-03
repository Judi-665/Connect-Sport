<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'prenom',
        'email',
        'password',
        'role',
        'avatar',
        'telephone',
        'actif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'actif'             => 'boolean',
    ];


    // ═══ Helpers rôles ═══

    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function isClub(): bool     { return $this->role === 'club'; }
    public function isJoueur(): bool   { return $this->role === 'joueur'; }
    public function isParent(): bool   { return $this->role === 'parent'; }
    public function isAgent(): bool    { return $this->role === 'agent'; }
    public function isSupporter(): bool{ return $this->role === 'supporter'; }

    // ═══ Relations ═══

    public function club()
    {
        return $this->hasOne(Club::class);
    }

    public function joueur()
    {
        return $this->hasOne(Joueur::class);
    }

    public function agent()
    {
        return $this->hasOne(Agent::class);
    }

    public function supporter()
    {
        return $this->hasOne(Supporter::class);
    }

    public function mediaReactions()
    {
        return $this->hasMany(MediaReaction::class);
    }
    
    public function parentsJoueurs()
{
    return $this->hasMany(ParentJoueur::class);
}

    public function messagesEnvoyes()
    {
        return $this->hasMany(Message::class, 'expediteur_id');
    }

    public function messagesRecus()
    {
        return $this->hasMany(Message::class, 'destinataire_id');
    }

    // Abonnement actif du club
public function abonnement()
{
    return $this->hasOne(ClubSubscription::class, 'club_id')
                ->where('statut', 'actif')
                ->latest();
}

// Récupère le plan actif
public function planActif(): ?SubscriptionPlan
{
    return $this->abonnement?->plan;
}

// Vérifie si le club a au moins un plan donné
public function aPlan(string $slug): bool
{
    $plan = $this->planActif();
    if (!$plan) return $slug === 'gratuit';

    $ordre = ['gratuit' => 0, 'standard' => 1, 'premium' => 2];
    return ($ordre[$plan->slug] ?? 0) >= ($ordre[$slug] ?? 0);
}
}