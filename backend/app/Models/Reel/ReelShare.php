<?php

namespace App\Models\Reel;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ReelShare extends Model
{
    //
    protected $fillable = [
        'reel_id',
        'shared_to',
        'shared_at',
        'shared_by_id',
        'platform'
    ];

    public function reel()
    {
        return $this->belongsTo(Reel::class);
    }

    public function sharedBy()
    {
        return $this->belongsTo(User::class, 'shared_by_id');
    }
}
