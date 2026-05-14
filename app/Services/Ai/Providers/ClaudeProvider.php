<?php

namespace App\Services\Ai\Providers;

use App\Services\Ai\AiProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class ClaudeProvider implements AiProviderInterface
{
    protected Client $http;
    protected string $apiKey;
    protected string $model;

    public function __construct(string $apiKey, string $model = 'claude-3-haiku-20240307')
    {
        $this->apiKey = $apiKey;
        $this->model  = $model;
        $this->http   = new Client([
            'base_uri' => 'https://api.anthropic.com/v1/',
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
        $fullSystem = $systemPrompt;
        if (! empty($context)) {
            $contextText = implode("\n\n---\n\n", $context);
            $fullSystem .= "\n\nKnowledge Base:\n{$contextText}";
        }

        $messages = [];
        // Add history
        foreach ($history as $msg) {
            $messages[] = [
                'role'    => $msg['role'] ?? 'user',
                'content' => $msg['content'] ?? '',
            ];
        }
        // Final message
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        $maxTokens = $options['max_tokens'] ?? 1000;

        try {
            $payload = [
                'model'      => $this->model,
                'max_tokens' => $maxTokens,
                'messages'   => $messages,
            ];

            if (filled($fullSystem)) {
                $payload['system'] = $fullSystem;
            }

            $response = $this->http->post('messages', [
                'headers' => [
                    'x-api-key'         => $this->apiKey,
                    'anthropic-version'  => '2023-06-01',
                    'Content-Type'       => 'application/json',
                ],
                'json' => $payload,
            ]);

            $data = json_decode((string) $response->getBody(), true);

            return trim((string) ($data['content'][0]['text'] ?? ''));
        } catch (GuzzleException $e) {
            Log::error('Claude API error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function name(): string
    {
        return 'claude';
    }

    public function isConfigured(): bool
    {
        return filled($this->apiKey);
    }
}
