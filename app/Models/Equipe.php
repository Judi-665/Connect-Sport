<?php
// app/Models/Equipe.php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_id',
        'nom',
        'genre',
        'categorie',
        'description',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    // ═══ Scopes ═══

    public function scopeActif(Builder $query): Builder
    {
        return $query->where('actif', 1);
    }

    public function scopeParCategorie(Builder $query, string $categorie): Builder
    {
        return $query->where('categorie', $categorie);
    }

    public function scopeParGenre(Builder $query, string $genre): Builder
    {
        return $query->where('genre', $genre);
    }

    // ═══ Relations ═══

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function joueurs()
    {
        return $this->hasMany(Joueur::class);
    }
}