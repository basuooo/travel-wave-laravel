<?php

namespace App\Services\Ai\Providers;

use App\Services\Ai\AiProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class OpenAiProvider implements AiProviderInterface
{
    protected Client $http;
    protected string $apiKey;
    protected string $model;

    public function __construct(string $apiKey, string $model = 'gpt-4o-mini')
    {
        $this->apiKey = $apiKey;
        $this->model  = $model;
        $this->http   = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
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

        if ($systemPrompt !== '') {
            $messages[] = ['role' => 'system', 'content' => $systemPrompt];
        }

        if (! empty($context)) {
            $contextText = implode("\n\n---\n\n", $context);
            $messages[]  = [
                'role'    => 'system',
                'content' => "Use the following knowledge base information to answer the user. If the answer is not in the knowledge base, say you don't know:\n\n{$contextText}",
            ];
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
            Log::error('OpenAI API error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function name(): string
    {
        return 'openai';
    }

    public function isConfigured(): bool
    {
        return filled($this->apiKey);
    }
}
