<?php
// app/Models/Message.php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'expediteur_id',
        'destinataire_id',
        'club_id',
        'contenu',
        'lu',
        'lu_at',
        'archive_expediteur',
        'archive_destinataire',
    ];

    protected $casts = [
        'lu'                   => 'boolean',
        'lu_at'                => 'datetime',
        'archive_expediteur'   => 'boolean',
        'archive_destinataire' => 'boolean',
    ];

    // ═══ Scopes ═══

    public function scopeNonLus(Builder $query): Builder
    {
        return $query->where('lu', 0);
    }

    public function scopeConversation(Builder $query, int $userId1, int $userId2): Builder
    {
        return $query->where(function ($q) use ($userId1, $userId2) {
            $q->where('expediteur_id', $userId1)
              ->where('destinataire_id', $userId2);
        })->orWhere(function ($q) use ($userId1, $userId2) {
            $q->where('expediteur_id', $userId2)
              ->where('destinataire_id', $userId1);
        })->orderBy('created_at', 'asc');
    }

    public function scopeNonArchive(Builder $query, int $userId): Builder
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('expediteur_id', $userId)
              ->where('archive_expediteur', 0);
        })->orWhere(function ($q) use ($userId) {
            $q->where('destinataire_id', $userId)
              ->where('archive_destinataire', 0);
        });
    }

    // ═══ Helpers ═══

    public function marquerLu(): void
    {
        if (!$this->lu) {
            $this->update([
                'lu'    => true,
                'lu_at' => now(),
            ]);
        }
    }

    // ═══ Relations ═══

    public function expediteur()
    {
        return $this->belongsTo(User::class, 'expediteur_id');
    }

    public function destinataire()
    {
        return $this->belongsTo(User::class, 'destinataire_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}