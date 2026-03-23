<?php

namespace App\Support;

use App\Models\ChatbotInteraction;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SiteChatbotService
{
    public function __construct(
        protected ChatbotKnowledgeManager $knowledgeManager
    ) {
    }

    public function answer(string $question, ?string $locale = null, ?Request $request = null): array
    {
        $request ??= request();
        $settings = Setting::query()->firstOrCreate([]);
        $locale = $this->resolveLocale($locale, $settings);

        $directAnswer = $this->directContactAnswer($question, $locale, $settings);
        $matchedSources = collect();
        $usedHandoff = false;

        if ($directAnswer) {
            $answer = $directAnswer;
            $wasAnswered = true;
        } else {
            $matchedSources = $this->knowledgeManager->search(
                question: $question,
                locale: $locale,
                sources: $settings->chatbotContentSources(),
                limit: 3,
            );

            if ($matchedSources->isEmpty()) {
                $answer = $this->fallbackAnswer($locale, $settings);
                $wasAnswered = false;
                $usedHandoff = true;
            } else {
                $answer = $this->composeKnowledgeAnswer($question, $matchedSources, $locale, $settings);
                $wasAnswered = true;
            }
        }

        $interaction = ChatbotInteraction::query()->create([
            'session_key' => $request?->session()->getId(),
            'locale' => $locale,
            'question' => $question,
            'answer' => $answer,
            'matched_sources' => $matchedSources->map(fn ($item) => [
                'title' => $item->title,
                'url' => $item->url,
                'source_type' => $item->source_type,
            ])->all(),
            'was_answered' => $wasAnswered,
            'used_handoff' => $usedHandoff,
            'ip_address' => $request?->ip(),
            'user_agent' => Str::limit((string) $request?->userAgent(), 1000, ''),
        ]);

        return [
            'answer' => $answer,
            'was_answered' => $wasAnswered,
            'used_handoff' => $usedHandoff,
            'sources' => $matchedSources->map(fn ($item) => [
                'title' => $item->title,
                'url' => $item->url,
            ])->filter(fn ($item) => filled($item['url']))->values()->all(),
            'handoff' => [
                'whatsapp_url' => $settings->chatbot_show_whatsapp_handoff ? $settings->floatingWhatsappUrl() : null,
                'contact_url' => $settings->chatbot_show_contact_handoff ? route('contact') : null,
            ],
            'interaction_id' => $interaction->id,
        ];
    }

    protected function composeKnowledgeAnswer(string $question, Collection $matchedSources, string $locale, Setting $settings): string
    {
        $intro = $locale === 'ar'
            ? 'وجدت لك أقرب المعلومات داخل محتوى Travel Wave:'
            : 'Here is the closest answer I found in Travel Wave content:';

        $lines = [$intro, ''];

        foreach ($matchedSources as $index => $item) {
            $snippet = $this->bestSnippet($item->summary ?: $item->content, $question);
            $prefix = $locale === 'ar' ? '• ' : '- ';
            $line = $prefix . trim($item->title);

            if ($snippet !== '') {
                $line .= ': ' . $snippet;
            }

            $lines[] = $line;

            if ($item->url) {
                $lines[] = ($locale === 'ar' ? 'الرابط: ' : 'Link: ') . $item->url;
            }

            if ($index < $matchedSources->count() - 1) {
                $lines[] = '';
            }
        }

        if (($settings->chatbot_show_whatsapp_handoff || $settings->chatbot_show_contact_handoff) && $matchedSources->count() < 2) {
            $lines[] = '';
            $lines[] = $locale === 'ar'
                ? 'إذا أردت، يمكنني أيضًا توجيهك إلى واتساب أو صفحة التواصل لمتابعة الطلب.'
                : 'If you want, I can also guide you to WhatsApp or the contact page for follow-up.';
        }

        return trim(implode("\n", $lines));
    }

    protected function fallbackAnswer(string $locale, Setting $settings): string
    {
        $lines = [$settings->chatbotFallbackMessage()];

        if ($settings->chatbot_show_whatsapp_handoff && $settings->floatingWhatsappUrl()) {
            $lines[] = '';
            $lines[] = $locale === 'ar'
                ? 'يمكنك المتابعة مباشرة عبر واتساب.'
                : 'You can continue directly on WhatsApp.';
        }

        if ($settings->chatbot_show_contact_handoff) {
            $lines[] = $locale === 'ar'
                ? 'أو استخدم صفحة التواصل لإرسال استفسارك.'
                : 'Or use the contact page to send your inquiry.';
        }

        return trim(implode("\n", $lines));
    }

    protected function directContactAnswer(string $question, string $locale, Setting $settings): ?string
    {
        $normalized = $this->normalize($question);

        $contactTokens = ['contact', 'phone', 'email', 'whatsapp', 'address', 'hours', 'call', 'اتصال', 'هاتف', 'واتساب', 'البريد', 'العنوان', 'مواعيد'];

        if (! collect($contactTokens)->contains(fn ($token) => str_contains($normalized, $token))) {
            return null;
        }

        $lines = [
            $locale === 'ar' ? 'هذه بيانات التواصل الحالية الخاصة بـ Travel Wave:' : 'Here are the current Travel Wave contact details:',
        ];

        if ($settings->phone) {
            $lines[] = ($locale === 'ar' ? 'الهاتف: ' : 'Phone: ') . $settings->phone;
        }

        if ($settings->secondary_phone) {
            $lines[] = ($locale === 'ar' ? 'هاتف إضافي: ' : 'Secondary phone: ') . $settings->secondary_phone;
        }

        if ($settings->whatsapp_number) {
            $lines[] = ($locale === 'ar' ? 'واتساب: ' : 'WhatsApp: ') . $settings->whatsapp_number;
        }

        if ($settings->contact_email) {
            $lines[] = ($locale === 'ar' ? 'البريد الإلكتروني: ' : 'Email: ') . $settings->contact_email;
        }

        if ($settings->localized('address', $locale)) {
            $lines[] = ($locale === 'ar' ? 'العنوان: ' : 'Address: ') . $settings->localized('address', $locale);
        }

        if ($settings->localized('working_hours', $locale)) {
            $lines[] = ($locale === 'ar' ? 'مواعيد العمل: ' : 'Working hours: ') . $settings->localized('working_hours', $locale);
        }

        return trim(implode("\n", $lines));
    }

    protected function bestSnippet(string $content, string $question): string
    {
        $content = trim(preg_replace('/\s+/u', ' ', strip_tags($content)) ?? '');
        if ($content === '') {
            return '';
        }

        $sentences = preg_split('/(?<=[\.\!\?\n])\s+/u', $content) ?: [$content];
        $queryTokens = collect(explode(' ', $this->normalize($question)))
            ->filter(fn ($token) => mb_strlen($token) >= 2)
            ->values();

        $best = collect($sentences)
            ->map(function (string $sentence) use ($queryTokens) {
                $normalized = $this->normalize($sentence);
                $score = $queryTokens->sum(fn ($token) => str_contains($normalized, $token) ? 1 : 0);

                return ['sentence' => trim($sentence), 'score' => $score];
            })
            ->sortByDesc('score')
            ->first();

        return Str::limit($best['sentence'] ?? $content, 220);
    }

    protected function resolveLocale(?string $locale, Setting $settings): string
    {
        $locale = $locale ?: app()->getLocale() ?: $settings->chatbot_primary_language ?: 'ar';

        return in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';
    }

    protected function normalize(string $text): string
    {
        $text = mb_strtolower($text);
        $text = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $text) ?? '';
        $text = preg_replace('/\s+/u', ' ', $text) ?? '';

        return trim($text);
    }
}
