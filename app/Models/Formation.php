<?php
// app/Models/Formation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Formation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'club_id',
        'user_id',
        'titre',
        'description',
        'type',
        'niveau',
        'categorie_cible',
        'sport_cible',
        'duree_estimee',
        'video_url',
        'miniature',
        'prix',
        'devise',
        'gratuit',
        'visibilite',
        'active',
        'mise_en_avant',
        'vues',
        'telechargements',
    ];

    protected $casts = [
        'prix'          => 'decimal:2',
        'gratuit'       => 'boolean',
        'active'        => 'boolean',
        'mise_en_avant' => 'boolean',
    ];

    // ═══ Scopes ═══

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }

    public function scopeGratuites(Builder $query): Builder
    {
        return $query->where('gratuit', 1);
    }

    public function scopePayantes(Builder $query): Builder
    {
        return $query->where('gratuit', 0);
    }

    public function scopeMiseEnAvant(Builder $query): Builder
    {
        return $query->where('mise_en_avant', 1);
    }

    public function scopeParType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeParNiveau(Builder $query, string $niveau): Builder
    {
        return $query->where('niveau', $niveau);
    }

    public function scopePubliques(Builder $query): Builder
    {
        return $query->where('visibilite', 'public');
    }

    // ═══ Helpers ═══

    public function prixAffiche(): string
    {
        if ($this->gratuit) return 'Gratuit';
        return number_format($this->prix, 0, '.', ' ') . ' ' . $this->devise;
    }

    public function incrementerVues(): void
    {
        $this->increment('vues');
    }

    public function incrementerTelechargements(): void
    {
        $this->increment('telechargements');
    }

    public function urlMiniature(): ?string
    {
        return $this->miniature
            ? asset('storage/' . $this->miniature)
            : null;
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

    public function paiements()
    {
        return $this->hasMany(PaiementFormation::class);
    }

    public function joueursPaye()
    {
        return $this->hasManyThrough(
            Joueur::class,
            PaiementFormation::class,
            'formation_id',
            'id',
            'id',
            'joueur_id'
        );
    }
}