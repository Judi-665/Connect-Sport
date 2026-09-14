<?php
// app/Models/PaiementFormation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaiementFormation extends Model
{
    use HasFactory;

    protected $table = 'paiements_formations';  // ⚠️ nom de table explicite

    protected $fillable = [
        'joueur_id',
        'formation_id',
        'fedapay_transaction_id',
        'fedapay_token',
        'fedapay_reference',
        'montant',
        'devise',
        'statut',
        'mode_paiement',
        'numero_telephone_paiement',
        'metadata',
        'paye_at',
        'rembourse_at',
        'note',
    ];

    protected $casts = [
        'montant'      => 'decimal:2',
        'metadata'     => 'array',              // JSON auto-décodé
        'paye_at'      => 'datetime',
        'rembourse_at' => 'datetime',
    ];

    // ═══ Scopes ═══

    public function scopeReussis(Builder $query): Builder
    {
        return $query->where('statut', 'reussi');
    }

    public function scopeEnAttente(Builder $query): Builder
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeEchoues(Builder $query): Builder
    {
        return $query->where('statut', 'echoue');
    }

    public function scopeParModePaiement(Builder $query, string $mode): Builder
    {
        return $query->where('mode_paiement', $mode);
    }

    public function scopeMobileMoney(Builder $query): Builder
    {
        return $query->where('mode_paiement', 'mobile_money');
    }

    // ═══ Helpers ═══

    public function estReussi(): bool
    {
        return $this->statut === 'reussi';
    }

    public function estEnAttente(): bool
    {
        return $this->statut === 'en_attente';
    }

    public function marquerReussi(array $metadata = []): void
    {
        $this->update([
            'statut'   => 'reussi',
            'paye_at'  => now(),
            'metadata' => $metadata,
        ]);
    }

    public function marquerEchoue(string $note = null): void
    {
        $this->update([
            'statut' => 'echoue',
            'note'   => $note,
        ]);
    }

    public function montantAffiche(): string
    {
        return number_format($this->montant, 0, '.', ' ') . ' ' . $this->devise;
    }

    // ═══ Relations ═══

    public function joueur()
    {
        return $this->belongsTo(Joueur::class);
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }
}