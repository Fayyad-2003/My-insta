<?php

namespace App\Listeners\User;

use App\Events\User\UserLastSeen;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateUserLastSeen
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserLastSeen $event): void
    {
        $event->user->update(['last_seen' => now()]);
    }
}
