<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserFollows extends Model
{
    protected $fillable = [
        'follower_id',
        'followed_id'
    ];
}
