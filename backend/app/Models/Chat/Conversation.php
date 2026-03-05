<?php

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Conversation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type',
        'title',
        'created_at',
        'is_draf',
        'is_archived',
        'is_muted'
    ];

    protected $append = [
        'others_user'
    ];

    public function participants()
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function message()
    {
        return $this->hasMany(Message::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function getOtherUserAttribute()
    {
        if (!$this->relationLoaded('participants')) {
            $this->load('participants');
        }

        $authUser = Auth::user();

        if (!$authUser || $this->type != 'private') {
            return null;
        }

        $otherParticipant = $this->participants->first(
            function ($participant) use ($authUser) {
                return $participant->user_id !== $authUser->id;
            }
        );

        return $otherParticipant ? $otherParticipant->user : null;
    }

    public function scopeWhereParticipant($query, $userId)
    {
        return $query->whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }


}
