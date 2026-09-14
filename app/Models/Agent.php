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
    ];

    protected $casts = [
        'verifie' => 'boolean',
        'actif'   => 'boolean',
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
}