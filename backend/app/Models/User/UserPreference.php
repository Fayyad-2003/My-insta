<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'label',
        'score'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
