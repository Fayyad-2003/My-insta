<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Music extends Model
{
    protected $fillable = [
        'title',
        'artist',
        'album',
        'genre',
        'duration',
        'source',
        'cover_url',
        'is_featured',
        'is_active',
        'external_url',
        'file_url'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'duration' => 'integer'
    ];
}
