<?php
// app/Models/Licence.php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Licence extends Model
{
    use HasFactory;

    protected $fillable = [
        'joueur_id',
        'club_id',
        'numero_licence',
        'fichier_pdf',
        'categorie',
        'date_debut',
        'date_expiration',
        'active',
        'note',
    ];

    protected $casts = [
        'date_debut'       => 'date',
        'date_expiration'  => 'date',
        'active'           => 'boolean',
    ];

    // ═══ Scopes ═══

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }

    public function scopeExpirees(Builder $query): Builder
    {
        return $query->where('date_expiration', '<', now());
    }

    public function scopeExpirentBientot(Builder $query, int $jours = 30): Builder
    {
        return $query->where('date_expiration', '<=', now()->addDays($jours))
                     ->where('date_expiration', '>=', now());
    }

    // ═══ Helpers ═══

    public function estExpiree(): bool
    {
        return $this->date_expiration->isPast();
    }

    public function joursRestants(): int
    {
        return max(0, now()->diffInDays($this->date_expiration, false));
    }

    public function urlPdf(): string
    {
        return route('club.licences.download', $this);
    }

    // ═══ Relations ═══

    public function joueur()
    {
        return $this->belongsTo(Joueur::class);
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}