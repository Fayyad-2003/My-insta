<?php

namespace App\Jobs;

use App\Models\Reel\Reel;
use App\Services\AI\TagSuggestionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GenerateReelAITagsJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $reelId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $reelId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $reel = Reel::find($this->reelId);

        if (!$reel) {
            return;
        }

        try {
            $musicTitle = null;
            $ai = TagSuggestionService::suggestTags($reel->caption ?? '', $musicTitle);

            // Fallback
            if (empty($ai['labels']) && empty($ai['hashtags'])) {
                $ai = $this->fallbackSuggestTags($reel);
            }

            $reel->update([
                'tags' => array_unique(array_merge($reel->tags ?? [], $ai['hashtags'] ?? [])),
                'ai_labels' => $ai['labels']
            ]);

        } catch (\Exception $e) {
            Log::error($e);
            $fallback = $this->fallbackSuggestTags($reel);

            $reel->update([
                'tags' => array_unique(
                    array_merge(
                        $reel->tags ?? [],
                        $fallback['hashtags'] ?? []
                    )
                ),
                'ai_labels' => $fallback['labels']
            ]);
        }
    }

    private function fallbackSuggestTags(Reel $reel)
    {
        if (empty($reel->caption)) {
            return [
                'labels' => [],
                'hashtags' => []
            ];
        }

        $caption = strtolower($reel->caption ?? '');
        $words = collect(preg_split('/\s+/', $caption))
            ->filter(function ($word) {
                return strlen($word) > 3 && !Str::startsWith($word, '#');
            })
            ->take(5)
            ->values()
            ->all();


        $labels = array_map('ucfirst', $words);
        $hashtags = array_map(fn($w) => preg_match('/[^\W]/', '', $w), $words);

        // Add some categroy-based defaults
        if (Str::contains($caption, 'music') || Str::contains($caption, 'song')) {
            $hashtags[] = 'music';
        } else if (Str::contains($caption, 'travel')) {
            $hashtags[] = 'travel';
        } else if (Str::contains($caption, 'food')) {
            $hashtags[] = 'food';
        } else if (Str::contains($caption, 'fashion')) {
            $hashtags[] = 'fashion';
        } else if (Str::contains($caption, 'style')) {
            $hashtags[] = 'style';
        }

        $hashtags = array_unique(array_filter($hashtags));
        $labels = array_unique(array_filter($labels));

        return [
            'labels' => $labels,
            'hashtags' => $hashtags
        ];
    }
}
