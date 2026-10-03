<?php
// app/Models/Opportunite.php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opportunite extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'club_id',
        'user_id',
        'titre',
        'type',
        'description',
        'lieu',
        'pays',
        'budget',
        'devise',
        'categorie_cible',
        'poste_cible',
        'sport_cible',
        'places_disponibles',
        'date_limite',
        'active',
        'mise_en_avant',
        'vues',
    ];

    protected $casts = [
        'budget'      => 'decimal:2',
        'date_limite' => 'date',
        'active'      => 'boolean',
        'mise_en_avant' => 'boolean',
    ];

    // ═══ Scopes ═══

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }

    public function scopeMiseEnAvant(Builder $query): Builder
    {
        return $query->where('mise_en_avant', 1);
    }

    public function scopeParType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeParCategorie(Builder $query, string $categorie): Builder
    {
        return $query->where('categorie_cible', $categorie)
                     ->orWhere('categorie_cible', 'tous');
    }

    public function scopeNonExpirees(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('date_limite')
              ->orWhere('date_limite', '>=', now());
        });
    }

    public function scopeInternationales(Builder $query): Builder
    {
        return $query->where('type', 'international');
    }

    // ═══ Helpers ═══

    public function estExpiree(): bool
    {
        return $this->date_limite && $this->date_limite->isPast();
    }

    public function incrementerVues(): void
    {
        $this->increment('vues');
    }

    public function budgetAffiche(): string
    {
        if (!$this->budget) return 'Non précisé';
        return number_format($this->budget, 0, '.', ' ') . ' ' . $this->devise;
    }

    // ═══ Relations ═══

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function candidatures()
    {
        return $this->belongsToMany(Joueur::class, 'opportunite_joueur')
                    ->withPivot('candidature_at', 'statut')
                    ->withTimestamps();
    }
}