<?php
// app/Models/Media.php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'medias';

    protected $fillable = [
        'club_id',
        'joueur_id',
        'evenement_id',
        'type',
        'chemin',
        'titre',
        'description',
        'miniature',
        'taille_ko',
        'duree_secondes',
        'visibilite',
        'payant',
        'prix',
        'vues',
    ];

    protected $casts = [
        'payant' => 'boolean',
        'prix'   => 'decimal:2',
    ];

    // ═══ Scopes ═══

    public function scopePhotos(Builder $query): Builder
    {
        return $query->where('type', 'photo');
    }

    public function scopeVideos(Builder $query): Builder
    {
        return $query->where('type', 'video');
    }

    public function scopePublics(Builder $query): Builder
    {
        return $query->where('visibilite', 'public');
    }

    public function scopePayants(Builder $query): Builder
    {
        return $query->where('payant', 1);
    }

    public function scopeGratuits(Builder $query): Builder
    {
        return $query->where('payant', 0);
    }

    // ═══ Helpers ═══

    public function url(): string
    {
        return asset('storage/' . $this->chemin);
    }

    public function urlMiniature(): ?string
    {
        return $this->miniature
            ? asset('storage/' . $this->miniature)
            : null;
    }

    public function incrementerVues(): void
    {
        $this->increment('vues');
    }

    public function dureeFormatee(): ?string
    {
        if (!$this->duree_secondes) return null;
        $minutes = intdiv($this->duree_secondes, 60);
        $secondes = $this->duree_secondes % 60;
        return sprintf('%d:%02d', $minutes, $secondes);
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

    public function evenement()
    {
        return $this->belongsTo(EvenementAgenda::class, 'evenement_id');
    }
  
}