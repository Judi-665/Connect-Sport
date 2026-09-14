<?php
// app/Models/DifficulteVoeu.php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DifficulteVoeu extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'difficultes_voeux';     // ⚠️ nom de table explicite

    protected $fillable = [
        'joueur_id',
        'type',
        'categorie',
        'titre',
        'contenu',
        'priorite',
        'resolu',
        'resolu_at',
        'note_resolution',
        'visible_club',
        'visible_agent',
    ];

    protected $casts = [
        'resolu'        => 'boolean',
        'resolu_at'     => 'datetime',
        'visible_club'  => 'boolean',
        'visible_agent' => 'boolean',
    ];

    // ═══ Scopes ═══

    public function scopeDifficultes(Builder $query): Builder
    {
        return $query->where('type', 'difficulte');
    }

    public function scopeVoeux(Builder $query): Builder
    {
        return $query->where('type', 'voeu');
    }

    public function scopeNonResolus(Builder $query): Builder
    {
        return $query->where('resolu', 0);
    }

    public function scopeResolus(Builder $query): Builder
    {
        return $query->where('resolu', 1);
    }

    public function scopeParPriorite(Builder $query, string $priorite): Builder
    {
        return $query->where('priorite', $priorite);
    }

    public function scopeVisiblesClub(Builder $query): Builder
    {
        return $query->where('visible_club', 1);
    }

    public function scopeHaute(Builder $query): Builder
    {
        return $query->where('priorite', 'haute');
    }

    // ═══ Helpers ═══

    public function marquerResolu(string $note = null): void
    {
        $this->update([
            'resolu'          => true,
            'resolu_at'       => now(),
            'note_resolution' => $note,
        ]);
    }

    public function estDifficulte(): bool
    {
        return $this->type === 'difficulte';
    }

    public function estVoeu(): bool
    {
        return $this->type === 'voeu';
    }

    // ═══ Relations ═══

    public function joueur()
    {
        return $this->belongsTo(Joueur::class);
    }
}