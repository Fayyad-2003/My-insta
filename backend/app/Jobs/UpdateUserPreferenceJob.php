<?php

namespace App\Jobs;

use App\Models\Post\Post;
use App\Models\Reel\Reel;
use App\Models\User;
use App\Models\User\UserFeedHistory;
use App\Models\User\UserPreference;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateUserPreferenceJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $userId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::find($this->userId);
        if (!$user) {
            return;
        }

        // 1. Define interaction  weights (value of engagement)
        $actionWeights = [
            'like' => 1.5,
            'comment' => 1.0,
            'share' => 1.2,
            'view' => 0.5,
            'skip' => -0.5  // Negative value for skip
        ];

        // Fetch recent high-value interactions (e.g, last 100 entries)
        $histories = UserFeedHistory::where('user_id', $user->id)
            ->latest('id')
            ->take(100)
            ->get();

        $tagsScores = [];

        foreach ($histories as $history) {
            $content = match ($history->content_type) {
                'post' => Post::find($history->content_id),
                'reel' => Reel::find($history->content_id),
                default => null
            };

            if (!$content)
                continue;

            // Get all relevant tags
            $lables = array_merge(
                $content->ai_labels ?? [],
                $content->tags ?? []
            );

            $weight = $actionWeights[$history->action] ?? 0.1;

            foreach ($lables as $label) {
                $label = strtolower($label);

                if (empty($label))
                    continue;

                // Add score weighted by interaction type
                $tagsScores[$label] = ($tagsScores[$label] ?? 0) + $weight;

            }
        }

        // Update user preferences
        foreach ($tagsScores as $label => $newValue) {
            $pref = UserPreference::firstOrNew([
                [
                    'user_id' => $this->userId,
                    'label' => $label
                ],
                ['score' => 0]
            ]);

            $pref->score = ($pref->score * 0.8) + ($newValue * 0.2);
            $pref->save();
        }

        UserPreference::where('user_id', $this->userId)
            ->whereNotIn('label', array_keys($tagsScores))
            ->update([
                'score' => DB::raw('score * 0.9')
            ]);

        Log::info('User preference updated for user {$user->id}');
    }
}
