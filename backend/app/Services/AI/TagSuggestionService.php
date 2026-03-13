<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use OpenAI\Laravel\Facades\OpenAI;


class TagSuggestionService
{
    public static function suggestTags(?string $caption = '', ?string $musicTitle = null): array
    {
        if (empty($caption)) {
            return [
                'labels' => [],
                'hastags' => [],
                'music' => $musicTitle ?? null
            ];
        }

        $prompt = <<<PROMPT
            "You are n AI assistant specialized in social media content tagging (like 
                instagram Reels or Tiktok).
                Analyze the text below and return relevant labels (topics) and hashtags (without #)
                Keep them concise and realistic.

                Caption: "{$caption}"
                Music: "{$musicTitle}"

                Retrun valid JSON with structure:
                {
                    "labels": ["..."],
                    "hashtags": ["..."]
                }
            "
        PROMPT;

        try {
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a precise and concinse social media content taggin assistant.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],

                'response_format' => ['type' => 'json_object'],
                'max_tokens' => 250
            ]);

            $data = json_decode($response['choices'][0]['content'] ?? [], true);

            // Normalize output
            $labels = collect($data['labels'] ?? [])
                ->map(fn($v) => Str::limit(trim($v), 40))
                ->unique()
                ->values()
                ->all();

            $hashtags = collect($data['hashtags'] ?? [])
                ->map(fn($v) => Str::slug(trim($v), '_'))
                ->filter()
                ->unique()
                ->all();

            return [
                'labels' => $labels,
                'hashtags' => $hashtags
            ];

        } catch (\Exception $e) {
            Log::warning('Ai tag suggestion failed: ' . $e->getMessage());
        }
        return [];
    }
}