<?php
// app/Models/EvenementAgenda.php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EvenementAgenda extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'evenements_agenda';     // ⚠️ nom de table explicite

    protected $fillable = [
        'club_id',
        'titre',
        'type',
        'description',
        'lieu',
        'debut_at',
        'fin_at',
        'adversaire_nom',
        'adversaire_logo',
        'domicile_exterieur',
        'score_nous',
        'score_eux',
        'resultat',
        'visibilite',
        'convocation_envoyee',
    ];

    protected $casts = [
        'debut_at'             => 'datetime',
        'fin_at'               => 'datetime',
        'convocation_envoyee'  => 'boolean',
    ];

    // ═══ Scopes ═══

    public function scopeAVenir(Builder $query): Builder
    {
        return $query->where('debut_at', '>', now())
                     ->orderBy('debut_at', 'asc');
    }

    public function scopePasses(Builder $query): Builder
    {
        return $query->where('debut_at', '<', now())
                     ->orderBy('debut_at', 'desc');
    }

    public function scopeMatchs(Builder $query): Builder
    {
        return $query->where('type', 'match');
    }

    public function scopeEntrainements(Builder $query): Builder
    {
        return $query->where('type', 'entrainement');
    }

    public function scopePublics(Builder $query): Builder
    {
        return $query->where('visibilite', 'public');
    }

    // ═══ Helpers ═══

    public function estTermine(): bool
    {
        return $this->debut_at->isPast();
    }

    public function scoreFormate(): ?string
    {
        if (is_null($this->score_nous) || is_null($this->score_eux)) {
            return null;
        }
        return $this->score_nous . ' - ' . $this->score_eux;
    }

    public function dureeEnMinutes(): ?int
    {
        if (!$this->fin_at) return null;
        return $this->debut_at->diffInMinutes($this->fin_at);
    }

    // ═══ Relations ═══

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function medias()
    {
        return $this->hasMany(Media::class, 'evenement_id');
    }

    public function statistiques()
    {
        return $this->hasMany(Statistique::class, 'evenement_id');
    }
}