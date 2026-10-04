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

    public function stream(array $messages, callable $onChunk): string
    {
        return match ($this->provider) {
            'groq' => $this->streamGroq($messages, $onChunk),
            default => $this->streamOllama($messages, $onChunk),
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
    //SSE streming ollama
    public function streamOllama(array $messages, callable $onChunk): string
    {
        $fullReply = '';

        $response = Http::withOptions(['stream' => true])
            ->timeout(120)
            ->post(config('services.ollama.base_url') . '/api/chat', [
                'model' => config('services.ollama.model'),
                'messages' => $messages,
                'stream' => true,
            ]);

        $body = $response->toPsrResponse()->getBody();
        $buffer = '';

        while (!$body->eof()) {
            $buffer .= $body->read(8);

            while (($pos = strpos($buffer, "\n")) !== false) {
                $line = substr($buffer, 0, $pos);
                $buffer = substr($buffer, $pos + 1);

                if (trim($line) === '') continue;

                $json = json_decode($line, true);
                if (isset($json['message']['content'])) {
                    $chunk = $json['message']['content'];
                    $fullReply .= $chunk;
                    $onChunk($chunk);
                }
            }
        }

        return $fullReply;
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

    //SSE streming groq
    public function streamGroq(array $messages, callable $onChunk): string
    {
        $fullReply = '';

        $response = Http::withToken(config('services.groq.key'))
            ->withOptions(['stream' => true])
            ->timeout(60)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => config('services.groq.model'),
                'messages' => $messages,
                'stream' => true,
            ]);

        $body = $response->toPsrResponse()->getBody();
        $buffer = '';

        while (!$body->eof()) {
            $buffer .= $body->read(64);

            while (($pos = strpos($buffer, "\n")) !== false) {
                $line = trim(substr($buffer, 0, $pos));
                $buffer = substr($buffer, $pos + 1);

                if ($line === '' || !str_starts_with($line, 'data: ')) continue;

                $data = substr($line, 6); // "data: " প্রিফিক্সটা বাদ দিচ্ছি

                if ($data === '[DONE]') break 2; // স্ট্রিম শেষ, দুই লুপ থেকেই বের হও

                $json = json_decode($data, true);
                $chunk = $json['choices'][0]['delta']['content'] ?? null;

                if ($chunk !== null) {
                    $fullReply .= $chunk;
                    $onChunk($chunk);
                }
            }
        }

        return $fullReply;
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
