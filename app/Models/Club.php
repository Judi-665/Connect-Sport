<?php
// app/Models/Club.php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Club extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'sport_id',
        'nom',
        'slug',
        'ville',
        'pays',
        'adresse',
        'description',
        'logo',
        'site_web',
        'telephone',
        'abonnement',
        'abonnement_expire_at',
        'actif',
    ];

    protected $casts = [
        'abonnement_expire_at' => 'datetime',
        'actif'                => 'boolean',
    ];

    // ═══ Scopes ═══

    public function scopeActif(Builder $query): Builder
    {
        return $query->where('actif', 1);
    }

    public function scopePremium(Builder $query): Builder
    {
        return $query->where('abonnement', 'premium');
    }

    public function scopeStandard(Builder $query): Builder
    {
        return $query->where('abonnement', 'standard');
    }

    // ═══ Helpers ═══

    public function isPremium(): bool
    {
        return $this->abonnement === 'premium'
            && $this->abonnement_expire_at
            && $this->abonnement_expire_at->isFuture();
    }

    public function isStandard(): bool
    {
        return $this->abonnement === 'standard'
            && $this->abonnement_expire_at
            && $this->abonnement_expire_at->isFuture();
    }

    // ═══ Relations ═══

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function equipes()
    {
        return $this->hasMany(Equipe::class);
    }

    public function joueurs()
    {
        return $this->hasMany(Joueur::class);
    }

    public function licences()
    {
        return $this->hasMany(Licence::class);
    }

    public function evenements()
    {
        return $this->hasMany(EvenementAgenda::class);
    }

    public function medias()
    {
        return $this->hasMany(Media::class);
    }

    public function sponsors()
    {
        return $this->hasMany(Sponsor::class);
    }

    public function supporters()
    {
        return $this->belongsToMany(Supporter::class, 'club_supporter')
                    ->withPivot('type_abonnement', 'abonnement_expire_at', 'notifications_actives')
                    ->withTimestamps();
    }

    public function transfertsRecus()
    {
        return $this->hasMany(Transfert::class, 'club_destinataire_id');
    }

    public function agentsPartenaires()
    {
        return $this->belongsToMany(Agent::class, 'agent_club')
                    ->withPivot('statut', 'dernier_contact_at')
                    ->withTimestamps();
    }

    public function transfertsEmis()
    {
        return $this->hasMany(Transfert::class, 'club_source_id');
    }

    public function abonnements()
    {
        return $this->hasMany(Abonnement::class);
    }

    public function formations()
    {
        return $this->hasMany(Formation::class);
    }

    public function opportunites()
    {
        return $this->hasMany(Opportunite::class);
    }

        public function transferts()
    {
        return $this->hasMany(\App\Models\Transfert::class, 'club_source_id')
                    ->orWhere('club_destinataire_id', $this->id);
    }

    // app/Models/Club.php

public function subscriptions()
{
    return $this->hasMany(ClubSubscription::class);
}

public function subscriptionActive(): ?Abonnement
{
    return $this->hasMany(Abonnement::class)
                ->where('statut', 'actif')
                ->where(function ($q) {
                    $q->whereNull('debut_at')
                      ->orWhere('debut_at', '<=', now());
                })
                ->where(function ($q) {
                    $q->whereNull('fin_at')
                      ->orWhere('fin_at', '>', now());
                })
                ->with('subscriptionPlan')
                ->latest()
                ->first();
}

public function planActif(): ?SubscriptionPlan
{
    return $this->subscriptionActive()?->plan;
}

public function peutUpgraderVers(SubscriptionPlan $plan): bool
{
    $planActif = $this->planActif();
    if (!$planActif) return true;

    $ordre = ['gratuit' => 0, 'standard' => 1, 'premium' => 2];
    return ($ordre[$plan->slug] ?? 0) > ($ordre[$planActif->slug] ?? 0);
}

public function candidatures()
{
    return $this->hasMany(Candidature::class);
}

public function carrieres()
{
    return $this->hasMany(Carriere::class);
}

}