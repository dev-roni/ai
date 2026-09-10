<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class AiService
{
    protected string $provider;

    public function __construct()
    {
        $this->provider = config('services.ai.provider', 'groq');
    }

    /**
     * মূল ফাংশন — যেকোনো প্রোভাইডার থেকে চ্যাট রেসপন্স পাবেন।
     * $messages ফরম্যাট: [['role' => 'user', 'content' => '...'], ...]
     */
    public function chat(array $messages): string
    {
        return match ($this->provider) {
            'groq'   => $this->askGroq($messages),
            'gemini' => $this->askGemini($messages),
            default  => $this->askOllama($messages),
        };
    }

    // ---------------- OLLAMA (Local, ৮GB RAM friendly) ----------------
    protected function askOllama(array $messages): string
    {
        $response = Http::timeout(120)->post(
            config('services.ollama.base_url') . '/api/chat',
            [
                'model'    => config('services.ollama.model'),
                'messages' => $messages,
                'stream'   => false,
            ]
        );

        if ($response->failed()) {
            throw new Exception('Ollama চলছে না। টার্মিনালে `ollama serve` চালু আছে কিনা চেক করুন।');
        }

        return $response->json('message.content') ?? '';
    }

    // ---------------- GROQ (Free, খুব ফাস্ট) ----------------
    protected function askGroq(array $messages): string
    {
        $response = Http::withToken(config('services.groq.key'))
            ->timeout(60)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'    => config('services.groq.model'),
                'messages' => $messages,
            ]);

        if ($response->failed()) {
            throw new Exception('Groq API এরর: ' . $response->body());
        }

        $content = $response->json('choices.0.message.content');

        // <think>...</think> remove
        $content = preg_replace('/<think>.*?<\/think>/s', '', $content);

        return trim($content);
    }

    // ---------------- GEMINI (Free, ভালো কোয়ালিটি) ----------------
    protected function askGemini(array $messages): string
    {
        // Gemini API ফরম্যাট একটু আলাদা, তাই messages কনভার্ট করছি
        $contents = array_map(function ($msg) {
            return [
                'role'  => $msg['role'] === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $msg['content']]],
            ];
        }, $messages);

        $model = config('services.gemini.model');
        $key = config('services.gemini.key');

        $response = Http::timeout(60)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$key}",
            ['contents' => $contents]
        );

        if ($response->failed()) {
            throw new Exception('Gemini API এরর: ' . $response->body());
        }

        return $response->json('candidates.0.content.parts.0.text') ?? '';
    }
}
