<?php
// app/Models/Agent.php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'numero_accreditation',
        'agence',
        'ville',
        'pays',
        'telephone',
        'bio',
        'site_web',
        'verifie',
        'actif',
        'mise_en_avant',
    ];

    protected $casts = [
        'verifie' => 'boolean',
        'actif'   => 'boolean',
        'mise_en_avant' => 'boolean',
    ];

    // ═══ Scopes ═══

    public function scopeActif(Builder $query): Builder
    {
        return $query->where('actif', 1);
    }

    public function scopeVerifie(Builder $query): Builder
    {
        return $query->where('verifie', 1);
    }

    // ═══ Helpers ═══

    public function nomComplet(): string
    {
        return $this->user->prenom . ' ' . $this->user->name;
    }

    // ═══ Relations ═══

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function joueurs()
    {
        return $this->belongsToMany(Joueur::class, 'agent_joueur')
                    ->withPivot(
                        'debut_mandat',
                        'fin_mandat',
                        'commission_pourcentage',
                        'statut',
                        'note'
                    )
                    ->withTimestamps();
    }

    public function joueursActifs()
    {
        return $this->belongsToMany(Joueur::class, 'agent_joueur')
                    ->withPivot(
                        'debut_mandat',
                        'fin_mandat',
                        'commission_pourcentage',
                        'statut',
                        'note'
                    )
                    ->wherePivot('statut', 'actif')
                    ->withTimestamps();
    }

    public function transferts()
    {
        return $this->hasMany(Transfert::class);
    }

    public function clubsPartenaires()
    {
        return $this->belongsToMany(Club::class, 'agent_club')
                    ->withPivot('statut', 'dernier_contact_at')
                    ->withTimestamps();
    }

    public function scopeAvecMandatsExpirantBientot(Builder $query, int $jours = 30): Builder
{
    return $query; // placeholder si besoin de filtrer une liste d'agents plus tard
}

public function mandatsExpirantBientot(int $jours = 30)
{
    return $this->joueurs()
                ->wherePivot('statut', 'actif')
                ->wherePivot('fin_mandat', '<=', now()->addDays($jours)->format('Y-m-d'))
                ->wherePivot('fin_mandat', '>=', now()->format('Y-m-d'));
}

public function abonnements()
{
    return $this->hasMany(Abonnement::class);
}

public function subscriptionActive(): ?Abonnement
{
    return $this->hasMany(Abonnement::class)
                ->where('statut', 'actif')
                ->where(fn($q) => $q->whereNull('fin_at')->orWhere('fin_at', '>', now()))
                ->with('subscriptionPlan')
                ->latest()
                ->first();
}

public function planActif(): ?SubscriptionPlan
{
    return $this->subscriptionActive()?->subscriptionPlan;
}

public function niveauPlan(): string
{
    $slug = $this->planActif()?->slug ?? 'gratuit';
    return \Illuminate\Support\Str::before($slug, '-');
}

public function estStandardOuPlus(): bool
{
    return in_array($this->niveauPlan(), ['standard', 'premium']);
}

public function estPremium(): bool
{
    return $this->niveauPlan() === 'premium';
}
}