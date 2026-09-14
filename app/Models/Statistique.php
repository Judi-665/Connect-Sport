<?php
// app/Models/Statistique.php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statistique extends Model
{
    use HasFactory;

    protected $fillable = [
        'joueur_id',
        'club_id',
        'evenement_id',
        'saison',
        'matchs_joues',
        'matchs_titulaire',
        'matchs_remplacant',
        'minutes_jouees',
        'buts',
        'passes_decisives',
        'cartons_jaunes',
        'cartons_rouges',
        'note_moyenne',
        'stats_complementaires',
        'valide',
        'valide_at',
         'visibilite',
    ];

    protected $casts = [
        'note_moyenne'           => 'decimal:2',
        'stats_complementaires'  => 'array',       // JSON auto-décodé
        'valide'                 => 'boolean',
        'valide_at'              => 'datetime',
    ];

    // ═══ Scopes ═══

    public function scopeValides(Builder $query): Builder
    {
        return $query->where('valide', 1);
    }

    public function scopeParSaison(Builder $query, string $saison): Builder
    {
        return $query->where('saison', $saison);
    }

    public function scopeSaisonEnCours(Builder $query): Builder
    {
        $saison = date('Y') . '-' . (date('Y') + 1);
        return $query->where('saison', $saison);
    }

    // ═══ Helpers ═══

    public function valider(): void
    {
        $this->update([
            'valide'    => true,
            'valide_at' => now(),
        ]);
    }

    public function moyenneButs(): float
    {
        if ($this->matchs_joues === 0) return 0;
        return round($this->buts / $this->matchs_joues, 2);
    }

    public function saisonEnCours(): string
    {
        return date('Y') . '-' . (date('Y') + 1);
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

    public function evenement()
    {
        return $this->belongsTo(EvenementAgenda::class, 'evenement_id');
    }
}