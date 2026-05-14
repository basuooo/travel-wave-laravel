<?php

namespace App\Services\Ai\Providers;

use App\Services\Ai\AiProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class DeepSeekProvider implements AiProviderInterface
{
    protected Client $http;
    protected string $apiKey;
    protected string $model;

    public function __construct(string $apiKey, string $model = 'deepseek-chat')
    {
        $this->apiKey = $apiKey;
        $this->model  = $model;
        $this->http   = new Client([
            'base_uri' => 'https://api.deepseek.com/v1/',
            'timeout'  => 30,
        ]);
    }

    public function chat(
        string $systemPrompt,
        string $userMessage,
        array $context = [],
        array $options = [],
        array $history = []
    ): string {
        $messages = [];

        $fullSystem = $systemPrompt;
        if (! empty($context)) {
            $contextText = implode("\n\n---\n\n", $context);
            $fullSystem .= "\n\nKnowledge Base:\n{$contextText}";
        }

        if (filled($fullSystem)) {
            $messages[] = ['role' => 'system', 'content' => $fullSystem];
        }

        // Add history
        foreach ($history as $msg) {
            $messages[] = [
                'role'    => $msg['role'] ?? 'user',
                'content' => $msg['content'] ?? '',
            ];
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        try {
            $response = $this->http->post('chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'model'       => $this->model,
                    'messages'    => $messages,
                    'max_tokens'  => $options['max_tokens'] ?? 800,
                    'temperature' => $options['temperature'] ?? 0.7,
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);

            return trim((string) ($data['choices'][0]['message']['content'] ?? ''));
        } catch (GuzzleException $e) {
            Log::error('DeepSeek API error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function name(): string
    {
        return 'deepseek';
    }

    public function isConfigured(): bool
    {
        return filled($this->apiKey);
    }
}
