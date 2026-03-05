<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserFeed extends Model
{
    protected $fillable = [
        'user_id',
        'content_type',
        'content_id',
        'score',
        'is_ai_generated'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
