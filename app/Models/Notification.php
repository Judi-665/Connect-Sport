<?php
// app/Models/Notification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory, HasUuids;       // ⚠️ UUID comme clé primaire

    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'canal',
        'vue',
        'read_at',
    ];

    protected $casts = [
        'data'     => 'array',      // JSON auto-décodé
        'vue'      => 'boolean',
        'read_at'  => 'datetime',
    ];

    // ═══ Scopes ═══

    public function scopeNonLues(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function scopeNonVues(Builder $query): Builder
    {
        return $query->where('vue', 0);
    }

    public function scopeParCanal(Builder $query, string $canal): Builder
    {
        return $query->where('canal', $canal);
    }

    // ═══ Helpers ═══

    public function marquerLue(): void
    {
        if (!$this->read_at) {
            $this->update([
                'read_at' => now(),
                'vue'     => true,
            ]);
        }
    }

    // ═══ Relations ═══

    public function notifiable()
    {
        return $this->morphTo();
    }
}