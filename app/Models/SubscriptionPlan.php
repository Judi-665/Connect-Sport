<?php
// app/Models/SubscriptionPlan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'nom',
        'slug',
        'prix',
        'frequence',
        'max_equipes',
        'multi_equipes',
        'gestion_licences',
        'agenda',
        'stats_avancees',
        'stockage_etendu',
        'mise_en_avant',
        'outils_marketing',
        'galerie_media',
        'notifications_ciblees',
        'actif',
    ];

    protected $casts = [
        'multi_equipes'         => 'boolean',
        'gestion_licences'      => 'boolean',
        'agenda'                => 'boolean',
        'stats_avancees'        => 'boolean',
        'stockage_etendu'       => 'boolean',
        'mise_en_avant'         => 'boolean',
        'outils_marketing'      => 'boolean',
        'galerie_media'         => 'boolean',
        'notifications_ciblees' => 'boolean',
        'actif'                 => 'boolean',
    ];

    // Tous les abonnements liés à ce plan
    public function abonnements()
    {
        return $this->hasMany(ClubSubscription::class);
    }

    // Helpers
    public function estGratuit(): bool
    {
        return $this->slug === 'gratuit';
    }

    public function estStandard(): bool
    {
        return $this->slug === 'standard';
    }

    public function estPremium(): bool
    {
        return $this->slug === 'premium';
    }

    public function prixFormate(): string
    {
        return $this->prix === 0 ? 'Gratuit' : number_format($this->prix, 0, ',', ' ') . ' FCFA';
    }

    public function frequenceLabel(): string
    {
        return match($this->frequence) {
            'mensuel'  => '/ mois',
            'annuel'   => '/ an',
            default    => '',
        };
    }
}