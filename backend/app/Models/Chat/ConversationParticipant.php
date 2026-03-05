<?php

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ConversationParticipant extends Model
{
    //
    protected $fillable = [
        'conversation_id',
        'user_id',
        'role',
        'last_read_at',
        'left_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    public function message()
    {
        return $this->hasMany(ParticipantMessage::class, 'participant_id');
    }


}
