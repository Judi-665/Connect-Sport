<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carriere extends Model
{
    use HasFactory;

    protected $fillable = [
        'joueur_id', 'club_id', 'poste', 'date_debut', 'date_fin', 'origine', 'origine_id',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin'   => 'date',
    ];

    // ═══ Scopes ═══

    public function scopeActuelles(Builder $query): Builder
    {
        return $query->whereNull('date_fin');
    }

    // ═══ Helpers ═══

    public function estActuelle(): bool
    {
        return is_null($this->date_fin);
    }

    public function cloturer(?\DateTimeInterface $date = null): void
    {
        $this->update(['date_fin' => $date ?? now()]);
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