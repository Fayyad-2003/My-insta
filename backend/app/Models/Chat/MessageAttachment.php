<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;

class MessageAttachment extends Model
{
    protected $fillable = [
        'message_id',
        'file_path',
        'file_type',
        'file_size',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}
