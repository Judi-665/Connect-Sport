<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidature extends Model
{
    use HasFactory;

    protected $fillable = [
        'joueur_id', 'club_id', 'poste_propose', 'message',
        'statut', 'note_club', 'repondu_par', 'repondu_at',
    ];

    protected $casts = [
        'repondu_at' => 'datetime',
    ];

    // ═══ Scopes ═══

    public function scopeEnAttente(Builder $query): Builder
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeAcceptees(Builder $query): Builder
    {
        return $query->where('statut', 'acceptee');
    }

    // ═══ Helpers ═══

    public function estEnAttente(): bool
    {
        return $this->statut === 'en_attente';
    }

    // Le club accepte : rattache le joueur + ouvre une entrée carrière
    public function accepter(int $responsableId): void
    {
        $this->update([
            'statut'      => 'acceptee',
            'repondu_par' => $responsableId,
            'repondu_at'  => now(),
        ]);

        $this->joueur->update([
            'club_id'   => $this->club_id,
            'sans_club' => false,
        ]);

        Carriere::create([
            'joueur_id'  => $this->joueur_id,
            'club_id'    => $this->club_id,
            'poste'      => $this->poste_propose,
            'date_debut' => now(),
            'origine'    => 'candidature',
            'origine_id' => $this->id,
        ]);
    }

    public function refuser(int $responsableId, ?string $note = null): void
    {
        $this->update([
            'statut'      => 'refusee',
            'repondu_par' => $responsableId,
            'repondu_at'  => now(),
            'note_club'   => $note,
        ]);
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

    public function repondant()
    {
        return $this->belongsTo(User::class, 'repondu_par');
    }
}