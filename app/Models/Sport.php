<?php
// app/Models/Sport.php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'slug',
        'icone',
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

    public function clubs()
    {
        return $this->hasMany(Club::class);
    }
}