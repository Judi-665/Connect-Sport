<?php
// app/Models/Joueur.php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Joueur extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'club_id',
        'equipe_id',
        'poste',
        'categorie',
        'date_naissance',
        'nationalite',
        'telephone',
        'ville',
        'pays',
        'bio',
        'sans_club',
        'visible_recruteur',
        'actif',
    ];

    protected $casts = [
        'date_naissance'    => 'date',
        'sans_club'         => 'boolean',
        'visible_recruteur' => 'boolean',
        'actif'             => 'boolean',
    ];

    // ═══ Scopes ═══

    public function scopeActif(Builder $query): Builder
    {
        return $query->where('actif', 1);
    }

    public function scopeSansClub(Builder $query): Builder
    {
        return $query->where('sans_club', 1);
    }

    public function scopeVisibleRecruteur(Builder $query): Builder
    {
        return $query->where('visible_recruteur', 1);
    }

    public function scopeParCategorie(Builder $query, string $categorie): Builder
    {
        return $query->where('categorie', $categorie);
    }

    public function scopeParPoste(Builder $query, string $poste): Builder
    {
        return $query->where('poste', $poste);
    }

    // ═══ Helpers ═══

    public function age(): ?int
    {
        return $this->date_naissance
            ? $this->date_naissance->age
            : null;
    }

    public function nomComplet(): string
    {
        return $this->user->prenom . ' ' . $this->user->name;
    }

    // ═══ Relations ═══

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function equipe()
    {
        return $this->belongsTo(Equipe::class);
    }

    public function licences()
    {
        return $this->hasMany(Licence::class);
    }

    public function licenceActive()
    {
        return $this->hasOne(Licence::class)
                    ->where('active', 1)
                    ->latestOfMany();
    }

    public function statistiques()
    {
        return $this->hasMany(Statistique::class);
    }

    public function difficultes()
    {
        return $this->hasMany(DifficulteVoeu::class)
                    ->where('type', 'difficulte');
    }

    public function voeux()
    {
        return $this->hasMany(DifficulteVoeu::class)
                    ->where('type', 'voeu');
    }

    public function parents()
    {
        return $this->hasMany(ParentJoueur::class);
    }

    public function agents()
    {
        return $this->belongsToMany(Agent::class, 'agent_joueur')
                    ->withPivot('debut_mandat', 'fin_mandat', 'commission_pourcentage', 'statut')
                    ->withTimestamps();
    }

    public function transferts()
    {
        return $this->hasMany(Transfert::class);
    }

    public function paiementsFormations()
    {
        return $this->hasMany(PaiementFormation::class);
    }

    public function abonnements()
    {
        return $this->hasMany(Abonnement::class);
    }

    public function subscriptionActive(): ?Abonnement
    {
        return $this->abonnements()->actif()->latest('fin_at')->first();
    }

    public function candidatures()
{
    return $this->hasMany(Candidature::class);
}

public function carrieres()
{
    return $this->hasMany(Carriere::class);
}

public function carriereActuelle()
{
    return $this->hasOne(Carriere::class)->whereNull('date_fin')->latestOfMany();
}

public function equipes()
{
    return $this->belongsToMany(Equipe::class, 'equipe_joueur')
                ->withPivot('date_debut', 'date_fin', 'actif')
                ->withTimestamps();
}

public function opportunites()
{
    return $this->belongsToMany(Opportunite::class, 'opportunite_joueur')
                ->withPivot('candidature_at', 'statut')
                ->withTimestamps();
}
}