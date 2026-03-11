<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Http\Resources\Post\PostResource;
use App\Http\Resources\Reel\ReelResource;
use App\Http\Resources\User\UserPreviewResource;
use App\Models\Post\Post;
use App\Models\Reel\Reel;
use App\Models\User;
use App\Models\User\UserFeed;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class FeedController extends Controller
{
    public function index(Request $request)
    {

        $per_page = $request->get('per_page', 20);
        $page = $request->get('page', 20);
        $user_id = Auth::id();

        // Define cache variables
        $isProduction = App::environment('production');
        $cachKey = 'feed-' . $user_id . '-' . $page . '-' . $per_page;
        $cachDuration = 300;

        if ($isProduction && $cachResponse = Cache::get($cachKey)) {
            return response()->json(
                json_decode($cachResponse),
                200
            );
        }

        $feed = UserFeed::where('user_id', $user_id)
            ->orderBy('score')
            ->inRandomOrder()
            ->paginate($per_page);

        // Fallback logic

        if ($feed->total() === 0) {
            // Get Posts, Reels and Users
            $posts = Post::with(['media', '']);
        }

        // Personalized feed
        $feedItems = $feed->getCollection();

        // Collect content Ids for efficient fetching bulk loading
        $postsIds = $feedItems->where('content_type', 'post')->pluck('content_id');
        $reelsIds = $feedItems->where('content_type', 'reel')->pluck('content_id');
        $usersIds = $feedItems->where('content_type', 'user')->pluck('content_id');

        // Bulk load content
        $posts = Post::with(['media', 'user'])
            ->whereIn('id', $postsIds)
            ->inRandomOrder()
            ->where('is_published', true)
            ->take(15)
            ->get()
            ->map(function ($post) {
                return [
                    'type' => 'post',
                    'data' => new PostResource($post)
                ];
            });

        $reels = Reel::with(['user'])
            ->whereIn('id', $reelsIds)
            ->inRandomOrder()
            ->where('is_published', true)
            ->get()
            ->map(function ($reel) {
                return [
                    'type' => 'reel',
                    'data' => new ReelResource($reel)
                ];
            });

        $users = User::where('id', '!=', $user_id)
            ->latest()
            ->inRandomOrder()
            ->take(5)
            ->get();

        $userBlock = null;

        if ($users->isNotEmpty()) {
            $userBlock = [
                'type' => 'users',
                'data' => $this->transformUsers($users),
            ];
        }

        $merged = collect()->merge($posts)->merge($reels);
        if ($userBlock) {
            $merged->push($userBlock);
        }

        $shuffled = $merged->shuffle()->values();
        $total = $shuffled->count();

        // Paginate manually
        $paginated = new LengthAwarePaginator(
            $shuffled->forPage($page, $per_page),
            $total,
            $per_page,
            $page,
            [
                'path' => request()->url,
                'query' => request()->query()
            ]
        );

        // Cach the fallback response
        if ($isProduction) {
            $jsonResponse = $paginated->toJson();
            Cache::put($cachKey, json_encode($jsonResponse), $cachDuration);
        }

        // Transform feed items
        $transformedData = $feedItems->map(function ($item) use ($posts, $reels, $users) {
            $content = null;
            $resource = null;

            switch ($item->content_type) {
                case 'post':
                    if ($posts->has($item->content_id)) {
                        $content = $posts->get($item->content_id);
                        $resource = new PostResource($content);
                    };
                    break;
                case 'reel':
                    if ($reels->has($item->content_id)) {
                        $content = $posts->get($item->content_id);
                        $resource = new ReelResource($content);
                    };
                    break;
                case 'user':
                    if ($users->has($item->content_id)) {
                        $content = $posts->get($item->content_id);
                        return [
                            'type' => $item->content_type,
                            'data' => $this->moreSuggestedUsers($content)
                        ];
                    };
                    break;
            }

            if ($content) {
                return [
                    'type' => $item->content_type,
                    'data' => $resource
                ];
            }

            return null;

        })->filter()->values();

        $feed->setCollection($transformedData);
        if ($isProduction) {
            $jsonResponse = $feed->toJson();
            Cache::put($cachKey, json_encode($jsonResponse), $cachDuration);
        }

        return response()->json($feed, 200);
    }

    private function transformUsers($users)
    {
        return $users->map(function ($user) {
            return new UserPreviewResource($user);
        });

    }
    private function moreSuggestedUsers(User $primaryUser)
    {
        if (!$primaryUser) {
            return [];
        }

        $excludedIIds = [Auth::id(), $primaryUser->id];

        // Exclude current authenticated user
        $suggestedUsers = User::whereNotIn('id', [$excludedIIds])->inRandomOrder()->limit(5)->get();
        $allUsers = collect($suggestedUsers)->merge($primaryUser);

        $allUsers->map(function ($user) {
            return new UserPreviewResource($user);
        });

        return $allUsers;
    }
}
