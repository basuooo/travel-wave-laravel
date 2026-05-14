<?php

namespace App\Services\Ai;

interface AiProviderInterface
{
    /**
     * Generate a reply for the given conversation context.
     *
     * @param  string  $systemPrompt  Instructions for the AI
     * @param  string  $userMessage   The user's latest message
     * @param  array   $context       Retrieved knowledge snippets to inject
     * @param  array   $options       max_tokens, temperature, etc.
     * @return string  The AI-generated reply
     */
    public function chat(
        string $systemPrompt,
        string $userMessage,
        array $context = [],
        array $options = [],
        array $history = []
    ): string;

    /**
     * Return the provider identifier (openai, gemini, deepseek, claude).
     */
    public function name(): string;

    /**
     * Return true if the provider is configured (has API key).
     */
    public function isConfigured(): bool;
}
