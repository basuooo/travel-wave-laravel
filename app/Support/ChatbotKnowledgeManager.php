<?php

namespace App\Support;

use App\Http\Controllers\FrontendController;
use App\Models\BlogPost;
use App\Models\ChatbotKnowledgeItem;
use App\Models\Destination;
use App\Models\Page;
use App\Models\Setting;
use App\Models\VisaCountry;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ChatbotKnowledgeManager
{
    public function sourceOptions(): array
    {
        return [
            'pages' => 'Pages',
            'service_pages' => 'Service Pages',
            'visa_countries' => 'Visa Destinations',
            'destinations' => 'Domestic Destinations',
            'faqs' => 'FAQs',
            'blog_posts' => 'Blog Posts',
            'contact_details' => 'Contact Details',
        ];
    }

    public function rebuild(?Setting $settings = null): int
    {
        $settings ??= Setting::query()->firstOrCreate([]);

        ChatbotKnowledgeItem::query()->delete();

        $items = collect()
            ->merge($this->pageItems())
            ->merge($this->servicePageItems())
            ->merge($this->visaCountryItems())
            ->merge($this->destinationItems())
            ->merge($this->blogItems())
            ->merge($this->contactItems($settings))
            ->values();

        if ($items->isEmpty()) {
            return 0;
        }

        ChatbotKnowledgeItem::query()->insert(
            $items->map(function (array $item, int $index) {
                return array_merge($item, [
                    'metadata' => isset($item['metadata']) ? json_encode($item['metadata'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
                    'sort_order' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            })->all()
        );

        return $items->count();
    }

    public function search(string $question, string $locale = 'ar', array $sources = [], int $limit = 4): Collection
    {
        $tokens = $this->tokens($question);
        $needle = $this->normalizeText($question);

        if ($tokens->isEmpty() && $needle === '') {
            return collect();
        }

        $query = ChatbotKnowledgeItem::query()
            ->where('locale', $locale);

        if (! empty($sources)) {
            $query->whereIn('source_type', $sources);
        }

        return $query->get()
            ->map(function (ChatbotKnowledgeItem $item) use ($tokens, $needle) {
                $haystack = $this->normalizeText(implode(' ', [
                    $item->title,
                    $item->summary,
                    $item->content,
                    implode(' ', Arr::flatten((array) $item->metadata)),
                ]));

                $title = $this->normalizeText($item->title);
                $summary = $this->normalizeText((string) $item->summary);

                $score = 0;

                if ($needle !== '' && str_contains($haystack, $needle)) {
                    $score += 12;
                }

                foreach ($tokens as $token) {
                    if (str_contains($title, $token)) {
                        $score += 6;
                    }

                    if (str_contains($summary, $token)) {
                        $score += 4;
                    }

                    if (str_contains($haystack, $token)) {
                        $score += 2;
                    }
                }

                return ['item' => $item, 'score' => $score];
            })
            ->filter(fn (array $result) => $result['score'] > 0)
            ->sortByDesc('score')
            ->take($limit)
            ->values()
            ->map(fn (array $result) => $result['item']);
    }

    protected function pageItems(): Collection
    {
        return Page::query()
            ->where('is_active', true)
            ->get()
            ->flatMap(fn (Page $page) => $this->localizedItemsFromModel(
                sourceType: 'pages',
                sourceId: $page->id,
                sourceKey: $page->key ?: $page->slug,
                url: $page->frontendUrl(),
                locales: ['en', 'ar'],
                callback: function (string $locale) use ($page) {
                    $title = $page->localized('title', $locale) ?: $page->key;
                    $summary = $page->localized('hero_subtitle', $locale) ?: $page->localized('intro_body', $locale);
                    $content = $this->flattenToText([
                        $page->localized('hero_badge', $locale),
                        $page->localized('hero_title', $locale),
                        $page->localized('hero_subtitle', $locale),
                        $page->localized('intro_title', $locale),
                        $page->localized('intro_body', $locale),
                        $page->sections,
                    ]);

                    return compact('title', 'summary', 'content');
                }
            ));
    }

    protected function servicePageItems(): Collection
    {
        $definitions = [
            'visas' => 'visaIndex',
            'domestic' => 'domesticIndex',
            'flights' => 'flights',
            'hotels' => 'hotels',
            'about' => 'about',
            'contact' => 'contact',
        ];

        return collect($definitions)->flatMap(function (string $method, string $key) {
            $controller = app(FrontendController::class);
            $view = $controller->{$method}();

            if (! $view instanceof View) {
                return [];
            }

            $data = $view->getData();
            $payload = $data['servicePage'] ?? $data['contentPage'] ?? null;

            if (! is_array($payload)) {
                return [];
            }

            return collect(['en', 'ar'])->flatMap(function (string $locale) use ($key, $payload) {
                $title = $this->resolveLocalizedValue($payload, ['title', 'hero.title'], $locale) ?: Str::headline($key);
                $summary = $this->resolveLocalizedValue($payload, ['hero.text', 'hero.subtitle', 'intro.text', 'text'], $locale);
                $content = $this->flattenToText($payload, $locale);

                $items = [[
                    'source_type' => 'service_pages',
                    'source_id' => null,
                    'source_key' => $key,
                    'locale' => $locale,
                    'title' => $title,
                    'summary' => $summary,
                    'content' => $content,
                    'url' => $this->servicePageUrl($key),
                    'metadata' => ['page_key' => $key],
                ]];

                foreach ($this->extractFaqEntries($payload, $locale) as $faqIndex => $faq) {
                    $items[] = [
                        'source_type' => 'faqs',
                        'source_id' => null,
                        'source_key' => $key . '-faq-' . $faqIndex,
                        'locale' => $locale,
                        'title' => $faq['question'],
                        'summary' => $faq['answer'],
                        'content' => $faq['question'] . "\n" . $faq['answer'],
                        'url' => $this->servicePageUrl($key),
                        'metadata' => ['page_key' => $key, 'faq' => true],
                    ];
                }

                return $items;
            });
        });
    }

    protected function visaCountryItems(): Collection
    {
        return VisaCountry::query()
            ->where('is_active', true)
            ->get()
            ->flatMap(function (VisaCountry $country) {
                $pageData = DestinationPageData::fromVisaCountry($country);

                return collect(['en', 'ar'])->flatMap(function (string $locale) use ($country, $pageData) {
                    $title = $country->localized('name', $locale);
                    $summary = $country->localized('excerpt', $locale) ?: $country->localized('overview', $locale);
                    $content = $this->flattenToText($pageData, $locale);

                    $items = [[
                        'source_type' => 'visa_countries',
                        'source_id' => $country->id,
                        'source_key' => $country->slug,
                        'locale' => $locale,
                        'title' => $title,
                        'summary' => $summary,
                        'content' => $content,
                        'url' => $country->frontendUrl(),
                        'metadata' => [
                            'slug' => $country->slug,
                            'category' => $country->category?->localized('name', $locale),
                        ],
                    ]];

                    foreach ($this->extractFaqEntries($pageData, $locale) as $faqIndex => $faq) {
                        $items[] = [
                            'source_type' => 'faqs',
                            'source_id' => $country->id,
                            'source_key' => $country->slug . '-faq-' . $faqIndex,
                            'locale' => $locale,
                            'title' => $faq['question'],
                            'summary' => $faq['answer'],
                            'content' => $faq['question'] . "\n" . $faq['answer'],
                            'url' => $country->frontendUrl(),
                            'metadata' => ['slug' => $country->slug, 'faq' => true],
                        ];
                    }

                    return $items;
                });
            });
    }

    protected function destinationItems(): Collection
    {
        return Destination::query()
            ->where('is_active', true)
            ->get()
            ->flatMap(function (Destination $destination) {
                $pageData = DestinationPageData::fromDestination($destination);

                return collect(['en', 'ar'])->flatMap(function (string $locale) use ($destination, $pageData) {
                    $title = $destination->localized('title', $locale);
                    $summary = $destination->localized('subtitle', $locale) ?: $destination->localized('excerpt', $locale);
                    $content = $this->flattenToText($pageData, $locale);

                    $items = [[
                        'source_type' => 'destinations',
                        'source_id' => $destination->id,
                        'source_key' => $destination->slug,
                        'locale' => $locale,
                        'title' => $title,
                        'summary' => $summary,
                        'content' => $content,
                        'url' => $destination->frontendUrl(),
                        'metadata' => [
                            'slug' => $destination->slug,
                            'destination_type' => $destination->destination_type,
                        ],
                    ]];

                    foreach ($this->extractFaqEntries($pageData, $locale) as $faqIndex => $faq) {
                        $items[] = [
                            'source_type' => 'faqs',
                            'source_id' => $destination->id,
                            'source_key' => $destination->slug . '-faq-' . $faqIndex,
                            'locale' => $locale,
                            'title' => $faq['question'],
                            'summary' => $faq['answer'],
                            'content' => $faq['question'] . "\n" . $faq['answer'],
                            'url' => $destination->frontendUrl(),
                            'metadata' => ['slug' => $destination->slug, 'faq' => true],
                        ];
                    }

                    return $items;
                });
            });
    }

    protected function blogItems(): Collection
    {
        return BlogPost::query()
            ->where('is_published', true)
            ->get()
            ->flatMap(fn (BlogPost $post) => $this->localizedItemsFromModel(
                sourceType: 'blog_posts',
                sourceId: $post->id,
                sourceKey: $post->slug,
                url: $post->frontendUrl(),
                locales: ['en', 'ar'],
                callback: function (string $locale) use ($post) {
                    $title = $post->localized('title', $locale);
                    $summary = $post->localized('excerpt', $locale);
                    $content = $this->flattenToText([
                        $post->localized('title', $locale),
                        $post->localized('excerpt', $locale),
                        $post->localized('content', $locale),
                        $post->category?->localized('name', $locale),
                    ]);

                    return compact('title', 'summary', 'content');
                }
            ));
    }

    protected function contactItems(Setting $settings): Collection
    {
        return collect(['en', 'ar'])->map(function (string $locale) use ($settings) {
            $title = $locale === 'ar' ? 'بيانات التواصل مع Travel Wave' : 'Travel Wave Contact Details';
            $summary = $locale === 'ar'
                ? 'هاتف وواتساب وبريد إلكتروني وعنوان ومواعيد العمل.'
                : 'Phone, WhatsApp, email, address, and working hours.';
            $content = $this->flattenToText([
                $settings->contact_email,
                $settings->phone,
                $settings->secondary_phone,
                $settings->whatsapp_number,
                $settings->localized('address', $locale),
                $settings->localized('working_hours', $locale),
            ]);

            return [
                'source_type' => 'contact_details',
                'source_id' => null,
                'source_key' => 'site-contact',
                'locale' => $locale,
                'title' => $title,
                'summary' => $summary,
                'content' => $content,
                'url' => route('contact'),
                'metadata' => [
                    'email' => $settings->contact_email,
                    'phone' => $settings->phone,
                    'whatsapp' => $settings->whatsapp_number,
                ],
            ];
        });
    }

    protected function localizedItemsFromModel(string $sourceType, ?int $sourceId, ?string $sourceKey, ?string $url, array $locales, callable $callback): Collection
    {
        return collect($locales)->map(function (string $locale) use ($sourceType, $sourceId, $sourceKey, $url, $callback) {
            $payload = $callback($locale);

            return [
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'source_key' => $sourceKey,
                'locale' => $locale,
                'title' => $payload['title'] ?: $sourceKey ?: $sourceType,
                'summary' => $payload['summary'] ?: null,
                'content' => $payload['content'] ?: ($payload['summary'] ?: $payload['title']),
                'url' => $url,
                'metadata' => $payload['metadata'] ?? null,
            ];
        });
    }

    protected function extractFaqEntries(array $payload, string $locale): array
    {
        $faqItems = data_get($payload, 'faq.items', []);

        if (! is_array($faqItems)) {
            return [];
        }

        return collect($faqItems)
            ->map(function ($item) use ($locale) {
                $question = is_array($item)
                    ? ($item['question'] ?? $item['question_' . $locale] ?? $item['q'] ?? null)
                    : null;
                $answer = is_array($item)
                    ? ($item['answer'] ?? $item['answer_' . $locale] ?? $item['a'] ?? null)
                    : null;

                if (! filled($question) || ! filled($answer)) {
                    return null;
                }

                return ['question' => strip_tags((string) $question), 'answer' => strip_tags((string) $answer)];
            })
            ->filter()
            ->values()
            ->all();
    }

    protected function resolveLocalizedValue(array $payload, array $paths, string $locale): ?string
    {
        foreach ($paths as $path) {
            $value = data_get($payload, $path);

            if (is_array($value)) {
                $value = $value[$locale]
                    ?? $value[config('app.fallback_locale', 'en')]
                    ?? null;
            }

            if (filled($value)) {
                return strip_tags((string) $value);
            }
        }

        return null;
    }

    protected function flattenToText(mixed $value, ?string $locale = null): string
    {
        $segments = [];
        $locale ??= app()->getLocale();

        $walker = function (mixed $node) use (&$walker, &$segments, $locale): void {
            if (is_string($node) || is_numeric($node)) {
                $text = trim(strip_tags((string) $node));
                if ($text !== '') {
                    $segments[] = $text;
                }

                return;
            }

            if (! is_array($node)) {
                return;
            }

            foreach ($node as $key => $child) {
                if (is_array($child) && (isset($child[$locale]) || isset($child[config('app.fallback_locale', 'en')]))) {
                    $walker($child[$locale] ?? $child[config('app.fallback_locale', 'en')] ?? null);
                    continue;
                }

                if (is_string($key) && preg_match('/_(en|ar)$/', $key, $matches)) {
                    if ($matches[1] !== $locale) {
                        continue;
                    }
                }

                $walker($child);
            }
        };

        $walker($value);

        return Str::limit(implode("\n", array_values(array_unique($segments))), 6000, '');
    }

    protected function normalizeText(string $text): string
    {
        $text = mb_strtolower($text);
        $text = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $text) ?? '';
        $text = preg_replace('/\s+/u', ' ', $text) ?? '';

        return trim($text);
    }

    protected function tokens(string $text): Collection
    {
        return collect(explode(' ', $this->normalizeText($text)))
            ->filter(fn ($token) => mb_strlen($token) >= 2)
            ->values();
    }

    protected function servicePageUrl(string $key): ?string
    {
        return match ($key) {
            'visas' => route('visas.index'),
            'domestic' => route('destinations.index'),
            'flights' => route('flights'),
            'hotels' => route('hotels'),
            'about' => route('about'),
            'contact' => route('contact'),
            default => null,
        };
    }
}
