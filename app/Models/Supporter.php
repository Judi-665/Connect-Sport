<?php
// app/Models/Supporter.php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supporter extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ville',
        'pays',
        'bio',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
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

    public function clubs()
    {
        return $this->belongsToMany(Club::class, 'club_supporter')
                    ->withPivot('type_abonnement', 'abonnement_expire_at', 'notifications_actives')
                    ->withTimestamps();
    }

    public function clubsPremium()
    {
        return $this->belongsToMany(Club::class, 'club_supporter')
                    ->withPivot('type_abonnement', 'abonnement_expire_at', 'notifications_actives')
                    ->wherePivot('type_abonnement', 'premium')
                    ->withTimestamps();
    }
}