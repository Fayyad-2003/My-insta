<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class BlockedUser extends Model
{
    protected $fillable = [
        'user_id',
        'blocked_user_id'
    ];

    public function blocker()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function blockedUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
