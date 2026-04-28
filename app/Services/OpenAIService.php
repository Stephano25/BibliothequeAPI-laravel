<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    public function generateSummary(string $title, string $authorName, int $year): ?string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('openai.api_key'),
                'Content-Type' => 'application/json',
            ])->post(config('openai.base_url') . '/chat/completions', [
                'model' => config('openai.model'),
                'messages' => [
                    ['role' => 'system', 'content' => 'Tu es un expert en littérature. Réponds en français. Maximum 3 phrases.'],
                    ['role' => 'user', 'content' => "Résumé du livre '{$title}' par {$authorName} ({$year})"]
                ],
                'max_tokens' => 200,
            ]);

            return $response->json()['choices'][0]['message']['content'] ?? null;
        } catch (\Exception $e) {
            Log::error('OpenAI Error: ' . $e->getMessage());
            throw new \Exception('Service OpenAI indisponible');
        }
    }

    public function extractKeywords(string $description): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('openai.api_key'),
                'Content-Type' => 'application/json',
            ])->post(config('openai.base_url') . '/chat/completions', [
                'model' => config('openai.model'),
                'messages' => [
                    ['role' => 'system', 'content' => 'Extrait les mots-clés. Réponds uniquement avec un tableau JSON.'],
                    ['role' => 'user', 'content' => "Mots-clés pour: {$description}"]
                ],
                'max_tokens' => 100,
            ]);

            $keywords = json_decode($response->json()['choices'][0]['message']['content'] ?? '[]', true);
            return is_array($keywords) ? $keywords : [];
        } catch (\Exception $e) {
            return [];
        }
    }
}