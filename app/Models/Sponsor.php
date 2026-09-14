<?php
// app/Models/Sponsor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sponsor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'club_id',
        'nom',
        'logo',
        'site_web',
        'email_contact',
        'telephone_contact',
        'description',
        'type_visibilite',
        'montant_contrat',
        'devise',
        'montant_confidentiel',
        'debut_partenariat',
        'fin_partenariat',
        'actif',
    ];

    protected $casts = [
        'montant_contrat'      => 'decimal:2',
        'montant_confidentiel' => 'boolean',
        'debut_partenariat'    => 'date',
        'fin_partenariat'      => 'date',
        'actif'                => 'boolean',
    ];

    // ═══ Scopes ═══

    public function scopeActif(Builder $query): Builder
    {
        return $query->where('actif', 1);
    }

    public function scopeExpirentBientot(Builder $query, int $jours = 30): Builder
    {
        return $query->where('fin_partenariat', '<=', now()->addDays($jours))
                     ->where('fin_partenariat', '>=', now());
    }

    public function scopeParVisibilite(Builder $query, string $visibilite): Builder
    {
        return $query->where('type_visibilite', $visibilite)
                     ->orWhere('type_visibilite', 'tous');
    }

    // ═══ Helpers ═══

    public function estExpire(): bool
    {
        return $this->fin_partenariat && $this->fin_partenariat->isPast();
    }

    public function joursRestants(): ?int
    {
        if (!$this->fin_partenariat) return null;
        return max(0, now()->diffInDays($this->fin_partenariat, false));
    }

    public function montantAffiche(): string
    {
        if ($this->montant_confidentiel) return 'Confidentiel';
        if (!$this->montant_contrat) return 'Non précisé';
        return number_format($this->montant_contrat, 0, '.', ' ') . ' ' . $this->devise;
    }

    // ═══ Relations ═══

    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}