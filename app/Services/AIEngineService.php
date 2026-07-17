<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AIEngineService
{
    protected SettingService $settings;

    public function __construct(SettingService $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Generate content from the selected AI provider.
     */
    public function generate(string $prompt, ?string $provider = null, ?string $systemPrompt = null): array
    {
        if (!$provider) {
            $provider = $this->settings->get('default_ai_provider', 'openai');
        }

        if ($provider === 'openai') {
            return $this->queryOpenAI($prompt, $systemPrompt);
        }

        return $this->queryGemini($prompt, $systemPrompt);
    }

    /**
     * Generate chat completion from history array.
     * History format: [['role' => 'user|assistant', 'content' => '...']]
     */
    public function generateChat(array $history, ?string $provider = null): array
    {
        if (!$provider) {
            $provider = $this->settings->get('default_ai_provider', 'openai');
        }

        if ($provider === 'openai') {
            return $this->queryOpenAIChat($history);
        }

        return $this->queryGeminiChat($history);
    }

    /**
     * Query OpenAI Chat Completion API.
     */
    protected function queryOpenAI(string $prompt, ?string $systemPrompt): array
    {
        $apiKey = $this->settings->get('openai_key');

        if (empty($apiKey)) {
            return [
                'text' => '',
                'word_count' => 0,
                'success' => false,
                'error' => 'OpenAI API key is not configured in System Settings.',
            ];
        }

        $messages = [];
        if ($systemPrompt) {
            $messages[] = ['role' => 'system', 'content' => $systemPrompt];
        }
        $messages[] = ['role' => 'user', 'content' => $prompt];

        $url = 'https://api.openai.com/v1/chat/completions';
        $model = 'gpt-4o-mini';

        if (str_starts_with($apiKey, 'gsk_') || str_starts_with($apiKey, 'gsk')) {
            $url = 'https://api.groq.com/openai/v1/chat/completions';
            $model = 'llama-3.3-70b-versatile';
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($url, [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.7,
            ]);

            if ($response->failed()) {
                $errorMsg = $response->json('error.message') ?? 'Unknown Error';
                $providerName = (str_starts_with($apiKey, 'gsk_') || str_starts_with($apiKey, 'gsk')) ? 'Groq' : 'OpenAI';
                return [
                    'text' => '',
                    'word_count' => 0,
                    'success' => false,
                    'error' => $providerName . ' API Error: ' . $errorMsg,
                ];
            }

            $text = $response->json('choices.0.message.content') ?? '';
            return [
                'text' => $text,
                'word_count' => $this->countWords($text),
                'success' => true,
                'error' => null,
            ];
        } catch (\Exception $e) {
            return [
                'text' => '',
                'word_count' => 0,
                'success' => false,
                'error' => 'Connection to OpenAI failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Query Google Gemini generateContent API.
     */
    protected function queryGemini(string $prompt, ?string $systemPrompt): array
    {
        $apiKey = $this->settings->get('gemini_key');

        if (empty($apiKey)) {
            return [
                'text' => '',
                'word_count' => 0,
                'success' => false,
                'error' => 'Gemini API key is not configured in System Settings.',
            ];
        }

        // We target gemini-2.0-flash which is the latest standard model
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $apiKey;

        $body = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
            ]
        ];

        if ($systemPrompt) {
            $body['systemInstruction'] = [
                'parts' => [
                    ['text' => $systemPrompt]
                ]
            ];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($url, $body);

            if ($response->failed()) {
                $errorMsg = $response->json('error.message') ?? 'Unknown Gemini Error';
                return [
                    'text' => '',
                    'word_count' => 0,
                    'success' => false,
                    'error' => 'Gemini API Error: ' . $errorMsg,
                ];
            }

            $text = $response->json('candidates.0.content.parts.0.text') ?? '';
            return [
                'text' => $text,
                'word_count' => $this->countWords($text),
                'success' => true,
                'error' => null,
            ];
        } catch (\Exception $e) {
            return [
                'text' => '',
                'word_count' => 0,
                'success' => false,
                'error' => 'Connection to Gemini failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Helper to count words in string.
     */
    public function countWords(string $text): int
    {
        return str_word_count(strip_tags($text));
    }

    /**
     * Query OpenAI Chat API with message history.
     */
    protected function queryOpenAIChat(array $history): array
    {
        $apiKey = $this->settings->get('openai_key');

        if (empty($apiKey)) {
            return [
                'text' => '',
                'word_count' => 0,
                'success' => false,
                'error' => 'OpenAI API key is not configured.',
            ];
        }

        // Format history for OpenAI
        $messages = array_map(function($msg) {
            return [
                'role' => $msg['role'] === 'assistant' ? 'assistant' : 'user',
                'content' => $msg['content']
            ];
        }, $history);

        // Prepend general helpful system prompt
        array_unshift($messages, [
            'role' => 'system',
            'content' => 'You are QuillNova AI, a helpful, professional, and friendly AI content assistant.'
        ]);

        $url = 'https://api.openai.com/v1/chat/completions';
        $model = 'gpt-4o-mini';

        if (str_starts_with($apiKey, 'gsk_') || str_starts_with($apiKey, 'gsk')) {
            $url = 'https://api.groq.com/openai/v1/chat/completions';
            $model = 'llama-3.3-70b-versatile';
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($url, [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.7,
            ]);

            if ($response->failed()) {
                $errorMsg = $response->json('error.message') ?? 'Unknown Error';
                $providerName = (str_starts_with($apiKey, 'gsk_') || str_starts_with($apiKey, 'gsk')) ? 'Groq' : 'OpenAI';
                return [
                    'text' => '',
                    'word_count' => 0,
                    'success' => false,
                    'error' => $providerName . ' API Error: ' . $errorMsg,
                ];
            }

            $text = $response->json('choices.0.message.content') ?? '';
            return [
                'text' => $text,
                'word_count' => $this->countWords($text),
                'success' => true,
                'error' => null,
            ];
        } catch (\Exception $e) {
            return [
                'text' => '',
                'word_count' => 0,
                'success' => false,
                'error' => 'OpenAI connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Query Google Gemini Chat API with message history.
     */
    protected function queryGeminiChat(array $history): array
    {
        $apiKey = $this->settings->get('gemini_key');

        if (empty($apiKey)) {
            return [
                'text' => '',
                'word_count' => 0,
                'success' => false,
                'error' => 'Gemini API key is not configured.',
            ];
        }

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $apiKey;

        // Format history for Gemini (mapping 'assistant' role to 'model')
        $contents = array_map(function($msg) {
            return [
                'role' => $msg['role'] === 'assistant' ? 'model' : 'user',
                'parts' => [
                    ['text' => $msg['content']]
                ]
            ];
        }, $history);

        $body = [
            'contents' => $contents,
            'systemInstruction' => [
                'parts' => [
                    ['text' => 'You are QuillNova AI, a helpful, professional, and friendly AI content assistant. Output Markdown response when requested.']
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($url, $body);

            if ($response->failed()) {
                $errorMsg = $response->json('error.message') ?? 'Unknown Gemini Error';
                return [
                    'text' => '',
                    'word_count' => 0,
                    'success' => false,
                    'error' => 'Gemini API Error: ' . $errorMsg,
                ];
            }

            $text = $response->json('candidates.0.content.parts.0.text') ?? '';
            return [
                'text' => $text,
                'word_count' => $this->countWords($text),
                'success' => true,
                'error' => null,
            ];
        } catch (\Exception $e) {
            return [
                'text' => '',
                'word_count' => 0,
                'success' => false,
                'error' => 'Gemini connection failed: ' . $e->getMessage(),
            ];
        }
    }
}
