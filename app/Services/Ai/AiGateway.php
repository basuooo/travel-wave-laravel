<?php

namespace App\Services\Ai;

use App\Models\AiBotConfig;
use App\Services\Ai\Providers\ClaudeProvider;
use App\Services\Ai\Providers\DeepSeekProvider;
use App\Services\Ai\Providers\GeminiProvider;
use App\Services\Ai\Providers\OpenAiProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class AiGateway
{
    protected ?AiBotConfig $config = null;

    public function __construct(?AiBotConfig $config = null)
    {
        $this->config = $config ?? AiBotConfig::getDefault();
    }

    /**
     * Generate an AI reply with automatic failover.
     *
     * @param  string      $userMessage
     * @param  Collection  $knowledgeSnippets  Retrieved items from knowledge base
     * @param  string      $locale
     * @return array{reply: string, provider: string|null, used_ai: bool}
     */
    public function reply(
        string $userMessage,
        Collection $knowledgeSnippets,
        string $locale = 'ar',
        array $history = []
    ): array {
        if (! $this->isEnabled()) {
            return ['reply' => '', 'provider' => null, 'used_ai' => false];
        }

        $systemPrompt = $this->buildSystemPrompt($locale);
        $context      = $this->buildContext($knowledgeSnippets, $locale);

        $options = [
            'max_tokens'  => (int) ($this->config->max_tokens ?? 1000),
            'temperature' => (float) ($this->config->temperature ?? 0.7),
        ];

        // Try default provider first, then fallback chain
        $providers = $this->resolveProviderChain();

        foreach ($providers as $provider) {
            if (! $provider->isConfigured()) {
                continue;
            }

            try {
                $reply = $provider->chat($systemPrompt, $userMessage, $context->all(), $options, $history);

                if (filled($reply)) {
                    return [
                        'reply'    => $reply,
                        'provider' => $provider->name(),
                        'used_ai'  => true,
                    ];
                }
            } catch (Throwable $e) {
                Log::warning("AI provider [{$provider->name()}] failed, trying next.", [
                    'error' => $e->getMessage(),
                ]);
                continue;
            }
        }

        // All providers failed
        return ['reply' => '', 'provider' => null, 'used_ai' => false];
    }

    public function isEnabled(): bool
    {
        return (bool) ($this->config->enabled ?? false);
    }

    /**
     * Build ordered provider chain: default first, then others.
     *
     * @return AiProviderInterface[]
     */
    protected function resolveProviderChain(): array
    {
        $default = $this->config->provider ?? 'openai';

        $all = [
            'openai'   => fn () => new OpenAiProvider(
                (string) ($this->config->openai_api_key ?? ''),
                (string) ($this->config->openai_model ?? 'gpt-4o-mini')
            ),
            'gemini'   => fn () => new GeminiProvider(
                (string) ($this->config->gemini_api_key ?? ''),
                (string) ($this->config->gemini_model ?? 'gemini-1.5-flash')
            ),
            'deepseek' => fn () => new DeepSeekProvider(
                (string) ($this->config->deepseek_api_key ?? ''),
                (string) ($this->config->deepseek_model ?? 'deepseek-chat')
            ),
            'claude'   => fn () => new ClaudeProvider(
                (string) ($this->config->claude_api_key ?? ''),
                (string) ($this->config->claude_model ?? 'claude-3-haiku-20240307')
            ),
        ];

        // Put default first
        $ordered = [];
        if (isset($all[$default])) {
            $ordered[$default] = $all[$default];
        }
        foreach ($all as $key => $factory) {
            if ($key !== $default) {
                $ordered[$key] = $factory;
            }
        }

        return array_map(fn ($factory) => $factory(), $ordered);
    }

    protected function buildSystemPrompt(string $locale): string
    {
        $customPrompt = $locale === 'ar'
            ? ($this->config->system_prompt_ar ?? '')
            : ($this->config->system_prompt_en ?? '');

        if (filled($customPrompt)) {
            return $customPrompt;
        }

        // Default system prompt from main settings
        $company = \App\Models\Setting::query()->first()->localized('site_name', $locale) ?: 'Travel Wave';

        if ($locale === 'ar') {
            return "أنت مساعد ذكي لشركة {$company}، متخصص في خدمات الفيزا والسفر والسياحة. "
                . "أجب بالعربية دائمًا، وكن مختصرًا ومفيدًا. "
                . "استخدم المعلومات المتاحة في قاعدة المعرفة المرفقة فقط. "
                . "إذا لم تجد الإجابة، قل أنك لا تعرف وانصح بالتواصل مع الفريق.";
        }

        return "You are a smart assistant for {$company}, specialized in visa services and tourism. "
            . "Always reply in English, be concise and helpful. "
            . "Use the provided knowledge base. If answer is not found, advise to contact the team.";
    }

    /**
     * Extract text snippets from knowledge items for context injection.
     */
    protected function buildContext(Collection $knowledgeSnippets, string $locale): array
    {
        return $knowledgeSnippets->map(function ($item) use ($locale) {
            // Manual ChatbotKnowledgeEntry
            if (method_exists($item, 'localizedAnswer')) {
                $parts = array_filter([
                    $item->localizedTitle($locale),
                    $item->localizedQuestion($locale),
                    $item->localizedAnswer($locale),
                ]);

                return implode("\n", $parts);
            }

            // ChatbotKnowledgeItem (site content)
            return implode("\n", array_filter([
                $item->title,
                $item->summary,
                $item->content ? \Illuminate\Support\Str::limit($item->content, 500) : null,
            ]));
        })->filter()->values()->all();
    }
}
