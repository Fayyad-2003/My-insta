<?php

namespace App\Jobs;

use App\Models\Post\Post;
use App\Models\Reel\Reel;
use App\Models\User;
use App\Models\User\UserFeed;
use App\Models\User\UserPreference;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateUserFeedJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public int $userId;

    // Candidate limits
    const MAX_CANDIDATES = 800;
    const MAX_FEED_ITEMS = 800;

    // Feed composition targets
    const TARGET_POSTS = 400;
    const TARGET_REELS = 400;
    const TARGET_USERS = 30;

    // if candidate pool is smaller than target, user all candidates instead (fallback)
    const FALLBACK_THRESHOLD = 40;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::find($this->userId);

        if (!$user) {
            Log::info('User not found: ' . $this->userId);
            return;
        }

        $userPreferences = UserPreference::where('user_id', $user->id)
            ->orderByDesc('score')
            ->pluck('score', 'label')
            ->take(15);

        if (empty($userPreferences)) {
            $userPreferences = collect(['general' => 1]);
        }

        // Exclusion (self + users this user follows)
        $followedUserIds = $user->following()->pluck('following_id')->all();
        $excludedUserIds = collect($followedUserIds)
            ->push($user->id)
            ->unique()
            ->values()
            ->all();

        // Fetch candidate posts and reels
        $posts = collect($this->getPostCandidate($excludedUserIds, $userPreferences)->all());
        $reels = collect($this->getReelCandidate($excludedUserIds, $userPreferences)->all());

        $candidateCount = $posts->count() + $reels->count();
        if ($candidateCount < self::FALLBACK_THRESHOLD) {
            $fallback = $this->getFallbackPopularContent($excludedUserIds, self::FALLBACK_THRESHOLD - $candidateCount);

            $posts = $posts->merge($fallback->where('content_type', 'post'));
            $reels = $reels->merge($fallback->where('content_type', 'reel'));
        }

        if ($posts->isEmpty() && $reels->isEmpty()) {
            Log::info('User ' . $user->id . 'has no candidates');
            return;
        }

        // Merge and score content
        $mergedCandidates = $posts->merge($reels);
        $scoreContent = $this->scoreAndRankContent($mergedCandidates, $userPreferences, $followedUserIds);

        $candidateUsers = $this->getUserCandidates($excludedUserIds, $userPreferences->keys()->all());
        $scoreUsers = $this->scoreAndRankUsers($candidateUsers);

        // Compose the feed
        $finalFeed = $this->blendFeed($scoreContent, $scoreUsers);

        // Ensure at least some items - if none, Log
        if ($finalFeed->isEmpty()) {
            Log::info('User ' . $user->id . 'has no feed items');
            return;
        }

        // Save the feed
        $this->saveFeed($user, $finalFeed);
    }

    private function getPostCandidate(array $excludedUserIds, Collection $userPreferences): Collection
    {
        if ($userPreferences->isEmpty()) {
            return collect();
        }

        $raw = Post::query()
            ->select(['id', 'user_id', 'tags', 'ai_labels', 'created_at'])
            ->withCount('likes', 'view')
            ->whereNotIn('user_id', $excludedUserIds)
            ->where('is_published', true)
            ->where('created_at', now()->subDays(7))
            ->where(function ($query) use ($userPreferences) {
                foreach ($userPreferences->keys as $label) {
                    $query->orWhereJsonContains('tags', $label)
                        ->orWhereJsonContains('ai_labels', $label);
                }
            })->latest('id')->limit(self::MAX_CANDIDATES)->get();

        return collect($raw->map(function ($post) {
            return [
                'content_type' => 'post',
                'model' => $post,
                'content_id' => $post->id
            ];
        })->values()->all());
    }

    private function getReelCandidate(array $excludedUserIds, Collection $userPreferences): Collection
    {
        if ($userPreferences->isEmpty()) {
            return collect();
        }

        $raw = Reel::query()
            ->select(['id', 'user_id', 'tags', 'ai_labels', 'created_at'])
            ->withCount('likes', 'view')
            ->whereNotIn('user_id', $excludedUserIds)
            ->where('is_published', true)
            ->where('created_at', now()->subDays(7))
            ->where(function ($query) use ($userPreferences) {
                foreach ($userPreferences->keys as $label) {
                    $query->orWhereJsonContains('tags', $label)
                        ->orWhereJsonContains('ai_labels', $label);
                }
            })->latest('id')->limit(self::MAX_CANDIDATES)->get();

        return collect($raw->map(function ($reel) {
            return [
                'content_type' => 'reel',
                'model' => $reel,
                'content_id' => $reel->id
            ];
        })->values()->all());
    }

    private function getFallbackPopularContent(array $excludedUserIds, int $needed): Collection
    {
        $limitPerType = max(0, ceil($needed / 2));
        $popularPosts = Post::
            query()
            ->select()
            ->withCount('likes', 'views')
            ->whereNotIn('user_id', $excludedUserIds)
            ->where('is_published', true)
            ->where('created_at', '>', now()->subDays(14))
            ->orderByDesc('likes_count')
            ->limit($limitPerType)
            ->get()
            ->map(fn($post) => [
                'content_type' => 'post',
                'model' => $post
            ])
        ;
        $popularReels = Reel::
            query()
            ->select()
            ->withCount('likes', 'views')
            ->whereNotIn('user_id', $excludedUserIds)
            ->where('is_published', true)
            ->where('created_at', '>', now()->subDays(14))
            ->orderByDesc('likes_count')
            ->limit($limitPerType)
            ->get()
            ->map(fn($reel) => [
                'content_type' => 'reel',
                'model' => $reel
            ])
        ;
        return collect($popularPosts->merge($popularReels)->values()->all());
    }

    private function scoreAndRankContent(Collection $content, Collection $userPreferences, array $followedUserIds): Collection
    {
        if ($content->isEmpty()) {
            return collect();
        }

        // compute maxima safely (models are nested in 'model')
        $maxLikes = max(1, $content->max(function ($i) {
            return $i['model']->likes_count ?? 0;
        }));

        $maxViews = max(1, $content->max(function ($i) {
            return $i['model']->views_count ?? 0;
        }));

        $scored = $content->map(function ($item) use ($userPreferences, $maxLikes, $maxViews, $followedUserIds) {
            $model = $item['model'];
            $tags = array_merge($model->$tags ?? [], $model->ai_labels ?? []);
            $prefScore = collect($tags)
                ->filter(fn($tag) => $userPreferences->has($tag))
                ->sum(fn($tag) => $userPreferences->get($tag) * 2);

            $popularity = 0;
            $popularity += (min(10, $model->likes_count ?? 0) / $maxLikes) * 5;
            $popularity += (min(10, $model->views_count ?? 0) / $maxViews) * 2;

            $hourseAgo = now()->subHours(78);
            $recency = max(0, (78 - $hourseAgo) / 78) * 10;

            // Base score
            $score = $prefScore + $popularity + $recency;
            if ($item['content_type'] === 'post') {

            }

            if ($item['content_type'] === 'reel') {
                $score *= 1.5;
            }

            return [
                'content_type' => $item['content_type'],
                'content_id' => $model->id,
                'score' => $score,
                'is_ai_generated' => true,
                'user_id' => $model->user_id,
                'created_at' => now(),
                'updated_at' => now()
            ];
        });


        return collect();
    }

    private function getUserCandidates(array $excludedUserIds, array $interests): Collection
    {
        return collect();
    }

    private function scoreAndRankUsers(Collection $candidateUsers): Collection
    {
        return collect();
    }

    private function blendFeed(Collection $content, Collection $scoreUsers): Collection
    {
        return collect();
    }

    private function saveFeed(User $user, Collection $feed)
    {
        if ($feed->isEmpty()) {
            Log::info('User ' . $user->id . 'has no feed items');
            return;
        }

        $uniqueFeed = $feed->unique(function ($item) {
            return $item['user_id']
                . '-' . $item['content_type']
                . '-' . $item['content_id']
            ;
        })->values();

        DB::transaction(function () use ($user, $uniqueFeed) {
            UserFeed::where('user_id', $user->id)->delete();
            UserFeed::insert($uniqueFeed->toArray());
        });

    }
}
