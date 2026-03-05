<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;

class MessageReaction extends Model
{
    protected $fillable = [
        'message_id',
        'sender_id',
        'reaction_type'
    ];

    public function message()
    {
        return $this->belongsTo(Message::class, 'message_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'sender_id');
    }
}
