<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaReaction extends Model
{
    protected $fillable = [
        'media_id',
        'user_id',
        'type',
    ];

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}