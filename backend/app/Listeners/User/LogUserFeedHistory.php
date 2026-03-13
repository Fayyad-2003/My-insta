<?php

namespace App\Listeners;

use App\Events\User\UserInteractedWithContent;
use App\Jobs\UpdateUserPreferenceJob;
use App\Models\User\UserFeedHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogUserFeedHistory
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
    public function handle(UserInteractedWithContent $event): void
    {
        $exists = UserFeedHistory::where([
            'user_id' => $event->userId,
            'content_type' => $event->contentType,
            'content_id' => $event->contentId,
            'action' => $event->action
        ])
            ->where('created_at', '>', now()->subMinutes(10))
            ->exists();

        if (!$exists) {
            UserFeedHistory::create([
                'user_id' => $event->userId,
                'content_type' => $event->contentType,
                'content_id' => $event->contentId,
                'action' => $event->action
            ]);
        }

        dispatch(new UpdateUserPreferenceJob($event->userId));
    }
}
