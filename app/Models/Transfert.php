<?php
// app/Models/Transfert.php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transfert extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'joueur_id',
        'club_source_id',
        'club_destinataire_id',
        'agent_id',
        'statut',
        'type',
        'montant',
        'devise',
        'montant_confidentiel',
        'note_joueur',
        'note_club_source',
        'note_club_destinataire',
        'agent_note',
        'date_effet',
        'date_fin_pret',
        'finalise_at',
    ];

    protected $casts = [
        'montant'              => 'decimal:2',
        'montant_confidentiel' => 'boolean',
        'date_effet'           => 'date',
        'date_fin_pret'        => 'date',
        'finalise_at'          => 'datetime',
    ];

    // ═══ Scopes ═══

    public function scopeEnAttente(Builder $query): Builder
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeEnNegociation(Builder $query): Builder
    {
        return $query->where('statut', 'en_negociation');
    }

    public function scopeAcceptes(Builder $query): Builder
    {
        return $query->where('statut', 'accepte');
    }

    public function scopeDefinitifs(Builder $query): Builder
    {
        return $query->where('type', 'definitif');
    }

    public function scopePrets(Builder $query): Builder
    {
        return $query->where('type', 'pret');
    }

    // ═══ Helpers ═══

    public function estFinalise(): bool
    {
        return !is_null($this->finalise_at);
    }

    public function estEnCours(): bool
    {
        return in_array($this->statut, ['en_attente', 'en_negociation']);
    }

    public function montantAffiche(): string
    {
        if ($this->montant_confidentiel) {
            return 'Confidentiel';
        }
        return number_format($this->montant, 0, '.', ' ') . ' ' . $this->devise;
    }

    // ═══ Relations ═══

    public function joueur()
    {
        return $this->belongsTo(Joueur::class);
    }

    public function clubSource()
    {
        return $this->belongsTo(Club::class, 'club_source_id');
    }

    public function clubDestinataire()
    {
        return $this->belongsTo(Club::class, 'club_destinataire_id');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function historique()
    {
        return $this->hasMany(TransfertHistory::class)->latest();
    }
}