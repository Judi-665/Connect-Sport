<?php
// app/Models/AgentJoueur.php
// Model pour la table pivot agent_joueur (pivot enrichi avec colonnes supplémentaires)

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AgentJoueur extends Pivot
{
    protected $table = 'agent_joueur';

    protected $fillable = [
        'agent_id',
        'joueur_id',
        'debut_mandat',
        'fin_mandat',
        'commission_pourcentage',
        'statut',
        'note',
    ];

    protected $casts = [
        'debut_mandat'           => 'date',
        'fin_mandat'             => 'date',
        'commission_pourcentage' => 'decimal:2',
    ];

    // ═══ Helpers ═══

    public function estActif(): bool
    {
        return $this->statut === 'actif';
    }

    public function estExpire(): bool
    {
        return $this->fin_mandat && $this->fin_mandat->isPast();
    }

    // ═══ Relations ═══

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function joueur()
    {
        return $this->belongsTo(Joueur::class);
    }
}