<?php
// app/Models/Abonnement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abonnement extends Model
{
    use HasFactory;

    protected $table = 'abonnements';

    protected $fillable = [
        'club_id' ,
        'joueur_id',
        'subscription_plan_id',
        'plan',
        'montant',
        'devise',
        'fedapay_transaction_id',
        'fedapay_token',
        'fedapay_reference',
        'mode_paiement',
        'numero_telephone_paiement',
        'statut',
        'debut_at',
        'fin_at',
        'renouvellement_auto',
        'renouvele_at',
        'rappel_7j_envoye',
        'rappel_1j_envoye',
        'metadata',
        'note',
    ];

    protected $casts = [
        'montant'             => 'decimal:2',
        'debut_at'            => 'datetime',
        'fin_at'              => 'datetime',
        'renouvele_at'        => 'datetime',
        'renouvellement_auto' => 'boolean',
        'rappel_7j_envoye'    => 'boolean',
        'rappel_1j_envoye'    => 'boolean',
        'metadata'            => 'array',
    ];

    // ═══ Scopes ═══

    public function scopeActif(Builder $query): Builder
    {
        return $query->where('statut', 'actif')
                     ->where(fn($q) => $q->whereNull('fin_at')
                                         ->orWhere('fin_at', '>', now()));
    }

    public function scopeEnAttente(Builder $query): Builder
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeExpire(Builder $query): Builder
    {
        return $query->where('statut', 'expire');
    }

    public function scopeExpirentBientot(Builder $query, int $jours = 7): Builder
    {
        return $query->where('statut', 'actif')
                     ->whereBetween('fin_at', [now(), now()->addDays($jours)]);
    }

    // ═══ Helpers ═══

    public function estActif(): bool
    {
        return $this->statut === 'actif'
            && (!$this->fin_at || $this->fin_at->isFuture());
    }

    public function estExpire(): bool
    {
        return $this->statut === 'expire'
            || ($this->fin_at && $this->fin_at->isPast());
    }

    public function joursRestants(): ?int
    {
        if (!$this->fin_at) return null;
        return max(0, (int) now()->diffInDays($this->fin_at, false));
    }

    public function montantAffiche(): string
    {
        if (!$this->montant) return 'Gratuit';
        return number_format($this->montant, 0, '.', ' ') . ' ' . ($this->devise ?? 'XOF');
    }

    // ═══ Relations ═══

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function joueur()
{
    return $this->belongsTo(Joueur::class);
}

// Le "propriétaire" de l'abonnement, quel qu'il soit
public function abonnable(): Club|Joueur|null
{
    return $this->club_id ? $this->club : $this->joueur;
}

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function subscriptionPlan()
{
    return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
}
}