<?php

namespace App\Services\Ai\Providers;

use App\Services\Ai\AiProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class GeminiProvider implements AiProviderInterface
{
    protected Client $http;
    protected string $apiKey;
    protected string $model;

    public function __construct(string $apiKey, string $model = 'gemini-1.5-flash')
    {
        $this->apiKey = $apiKey;
        $this->model  = $model;
        $this->http   = new Client([
            'base_uri' => 'https://generativelanguage.googleapis.com/v1beta/',
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
        $contents = [];

        // System prompt and context in Gemini go into a special systemInstruction or as the first user message parts
        // For simplicity with this version, we'll prefix them to the current message or use contents[0]
        
        $promptParts = [];
        if ($systemPrompt !== '') {
            $promptParts[] = ['text' => "Instructions: " . $systemPrompt . "\n\n"];
        }

        if (! empty($context)) {
            $contextText = implode("\n\n---\n\n", $context);
            $promptParts[] = ['text' => "Knowledge Base:\n{$contextText}\n\n"];
        }

        // Add history
        foreach ($history as $msg) {
            $contents[] = [
                'role'  => ($msg['role'] === 'assistant') ? 'model' : 'user',
                'parts' => [['text' => $msg['content'] ?? '']],
            ];
        }

        // Final message
        $promptParts[] = ['text' => $userMessage];
        $contents[] = ['role' => 'user', 'parts' => $promptParts];

        $maxTokens   = $options['max_tokens'] ?? 1000;
        $temperature = $options['temperature'] ?? 0.7;

        try {
            $response = $this->http->post("models/{$this->model}:generateContent", [
                'query' => ['key' => $this->apiKey],
                'json'  => [
                    'contents'         => $contents,
                    'generationConfig' => [
                        'maxOutputTokens' => $maxTokens,
                        'temperature'     => $temperature,
                    ],
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

            return trim((string) $text);
        } catch (GuzzleException $e) {
            Log::error('Gemini API error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function name(): string
    {
        return 'gemini';
    }

    public function isConfigured(): bool
    {
        return filled($this->apiKey);
    }
}
