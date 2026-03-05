<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;

class ParticipantMessage extends Model
{
    protected $fillable = [
        'participant_id',
        'message_id',
        'delivered_at',
        'read_at'
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
        'read_at' => 'datetime'
    ];

    public function participant()
    {
        return $this->belongsTo(ConversationParticipant::class, 'participant_id');
    }

    public function message()
    {
        return $this->belongsTo(Message::class, 'message_id');
    }
}
