<?php

namespace App\Listeners;

use App\Events\User\UserRegister;
use App\Jobs\GenerateUserFeedJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GenerateInitialFeed
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
    public function handle(UserRegister $event): void
    {
        $userId = $event->user->id;
        GenerateUserFeedJob::dispatch($userId);
    }
}
