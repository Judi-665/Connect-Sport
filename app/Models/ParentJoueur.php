<?php
// app/Models/ParentJoueur.php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentJoueur extends Model
{
    use HasFactory;

    protected $table = 'parents';       // ⚠️ nom de table explicite car "parents" != "parent_joueurs"

    protected $fillable = [
        'user_id',
        'joueur_id',
        'lien',
        'acces_stats',
        'acces_agenda',
        'actif',
    ];

    protected $casts = [
        'acces_stats'  => 'boolean',
        'acces_agenda' => 'boolean',
        'actif'        => 'boolean',
    ];

    // ═══ Scopes ═══

    public function scopeActif(Builder $query): Builder
    {
        return $query->where('actif', 1);
    }

    // ═══ Relations ═══

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function joueur()
    {
        return $this->belongsTo(Joueur::class);
    }
}