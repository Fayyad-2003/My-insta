<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'phone',
        'address',
        'city',
        'is_primary',
        'country',
        'zip_code',
        'is_public'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
