<?php

namespace App\Models\Reel;

use Illuminate\Database\Eloquent\Model;

class ReelSetting extends Model
{
    //
    protected $fillable = [
        'reel_id',
        'audience',
        'share_to_threads',
        'share_to_facebook',
        'share_to_story',
        'allow_use_template',
        'enable_captions',
        'enable_captions',
        'caption_color',
        'caption_size',
        'allow_mentions',
        'allow_tagging'
    ];

    protected function reel()
    {
        return $this->belongsTo(Reel::class);
    }
}
