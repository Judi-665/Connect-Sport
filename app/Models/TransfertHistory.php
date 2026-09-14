<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransfertHistory extends Model
{
    protected $fillable = [
        'transfert_id',
        'user_id',
        'ancien_statut',
        'nouveau_statut',
        'note',
    ];

    public function transfert()
    {
        return $this->belongsTo(Transfert::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
