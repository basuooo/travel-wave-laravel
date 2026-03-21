<?php

namespace App\Support;

use App\Models\BlogPost;
use App\Models\Destination;
use App\Models\MarketingLandingPage;
use App\Models\Page;
use App\Models\SeoMetaEntry;
use App\Models\SeoRedirect;
use App\Models\SeoSetting;
use App\Models\Setting;
use App\Models\VisaCountry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SeoManager
{
    public function settings(): SeoSetting
    {
        if (! Schema::hasTable('seo_settings')) {
            return new SeoSetting([
                'robots_txt_content' => $this->defaultRobotsTxt(),
                'schema_organization_name' => Setting::query()->first()?->localized('site_name') ?: 'Travel Wave',
                'default_robots_meta' => 'index,follow',
            ]);
        }

        return SeoSetting::query()->firstOrCreate([], [
            'robots_txt_content' => $this->defaultRobotsTxt(),
            'schema_organization_name' => Setting::query()->first()?->localized('site_name') ?: 'Travel Wave',
            'default_robots_meta' => 'index,follow',
        ]);
    }

    public function targets(): Collection
    {
        if (! Schema::hasTable('seo_meta_entries')) {
            return collect();
        }

        return collect()
            ->merge($this->pageTargets())
            ->merge($this->visaTargets())
            ->merge($this->destinationTargets())
            ->merge($this->blogTargets())
            ->merge($this->marketingTargets())
            ->values();
    }

    public function metaTargetOptions(): array
    {
        return $this->targets()->mapWithKeys(fn (array $target) => [
            $target['type'] . ':' . $target['id'] => $target['label'],
        ])->all();
    }

    public function entryForTarget(string $type, int $id): SeoMetaEntry
    {
        if (! Schema::hasTable('seo_meta_entries')) {
            return new SeoMetaEntry([
                'target_type' => $type,
                'target_id' => $id,
                'is_active' => true,
                'schema_enabled' => true,
            ]);
        }

        return SeoMetaEntry::query()->firstOrCreate([
            'target_type' => $type,
            'target_id' => $id,
        ], [
            'is_active' => true,
            'schema_enabled' => true,
        ]);
    }

    public function resolveForRequest(Request $request): array
    {
        $settings = $this->settings();
        [$type, $id, $model] = $this->targetFromRequest($request);
        $entry = $type && $id ? SeoMetaEntry::query()
            ->where('target_type', $type)
            ->where('target_id', $id)
            ->where('is_active', true)
            ->first() : null;

        $title = $entry?->localized('meta_title') ?: $this->fallbackModelValue($model, 'meta_title') ?: $this->fallbackModelValue($model, 'title');
        $description = $entry?->localized('meta_description') ?: $this->fallbackModelValue($model, 'meta_description') ?: $this->fallbackModelValue($model, 'excerpt');
        $canonical = $entry?->canonical_url ?: $request->fullUrl();
        $robots = $entry?->robots_meta ?: ($settings->default_robots_meta ?: 'index,follow');
        $ogTitle = $entry?->localized('og_title') ?: $title;
        $ogDescription = $entry?->localized('og_description') ?: $description;
        $ogImage = $entry?->og_image ?: $this->fallbackImage($model);
        $twitterTitle = $entry?->localized('twitter_title') ?: $ogTitle;
        $twitterDescription = $entry?->localized('twitter_description') ?: $ogDescription;
        $twitterImage = $entry?->twitter_image ?: $ogImage;

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'robots' => $robots,
            'og_title' => $ogTitle,
            'og_description' => $ogDescription,
            'og_image' => $ogImage,
            'twitter_title' => $twitterTitle,
            'twitter_description' => $twitterDescription,
            'twitter_image' => $twitterImage,
            'hreflang_en_url' => $entry?->hreflang_en_url,
            'hreflang_ar_url' => $entry?->hreflang_ar_url,
            'schema' => $this->schemaForRequest($request, $settings, $entry, $model, $title, $description, $canonical),
        ];
    }

    public function dashboardSummary(): array
    {
        $audit = $this->healthAudit();

        return [
            'indexed_ready_pages' => $this->targets()->count(),
            'missing_meta_title' => $audit['missing_meta_title'],
            'missing_meta_description' => $audit['missing_meta_description'],
            'missing_canonical' => $audit['missing_canonical'],
            'missing_schema' => $audit['missing_schema'],
            'redirects_count' => SeoRedirect::query()->count(),
            'active_redirects_count' => SeoRedirect::query()->where('is_active', true)->count(),
            'sitemap_last_generated_at' => $this->settings()->sitemap_last_generated_at,
            'latest_issues' => $audit['issues'],
        ];
    }

    public function healthAudit(): array
    {
        $targets = $this->targets();
        $entryMap = Schema::hasTable('seo_meta_entries')
            ? SeoMetaEntry::query()->get()->keyBy(fn (SeoMetaEntry $entry) => $entry->target_type . ':' . $entry->target_id)
            : collect();

        $missingTitle = 0;
        $missingDescription = 0;
        $missingCanonical = 0;
        $missingSchema = 0;
        $issues = [];

        foreach ($targets as $target) {
            $entry = $entryMap->get($target['type'] . ':' . $target['id']);
            $model = $target['model'];
            $label = $target['label'];

            $hasTitle = filled($entry?->meta_title_en) || filled($entry?->meta_title_ar)
                || filled($this->fallbackModelValue($model, 'meta_title'));
            $hasDescription = filled($entry?->meta_description_en) || filled($entry?->meta_description_ar)
                || filled($this->fallbackModelValue($model, 'meta_description'));
            $hasCanonical = filled($entry?->canonical_url);
            $hasSchema = $entry ? $entry->schema_enabled : true;

            if (! $hasTitle) {
                $missingTitle++;
                $issues[] = ['type' => 'missing_meta_title', 'label' => $label];
            }

            if (! $hasDescription) {
                $missingDescription++;
                $issues[] = ['type' => 'missing_meta_description', 'label' => $label];
            }

            if (! $hasCanonical) {
                $missingCanonical++;
            }

            if (! $hasSchema) {
                $missingSchema++;
            }
        }

        return [
            'missing_meta_title' => $missingTitle,
            'missing_meta_description' => $missingDescription,
            'missing_canonical' => $missingCanonical,
            'missing_schema' => $missingSchema,
            'issues' => collect($issues)->take(10)->all(),
        ];
    }

    public function defaultRobotsTxt(): string
    {
        return "User-agent: *\nAllow: /\n\nSitemap: " . url('/sitemap.xml');
    }

    public function regenerateSitemaps(): array
    {
        if (! Schema::hasTable('seo_settings')) {
            return [
                'index' => 'sitemap.xml',
                'files' => [],
                'generated_at' => now(),
            ];
        }

        $settings = $this->settings();
        $directory = storage_path('app/seo');
        File::ensureDirectoryExists($directory);

        $files = [];
        $sitemaps = [];

        foreach ($this->sitemapCollections($settings) as $name => $items) {
            if ($items->isEmpty()) {
                continue;
            }

            $content = $name === 'images'
                ? $this->buildImageSitemap($items)
                : $this->buildUrlSet($items);

            File::put($directory . DIRECTORY_SEPARATOR . "sitemap-{$name}.xml", $content);
            $files[$name] = "sitemap-{$name}.xml";
            $sitemaps[] = [
                'loc' => url("/sitemap-{$name}.xml"),
                'lastmod' => now()->toAtomString(),
            ];
        }

        $index = $this->buildSitemapIndex(collect($sitemaps));
        File::put($directory . DIRECTORY_SEPARATOR . 'sitemap.xml', $index);

        $settings->update([
            'sitemap_last_generated_at' => now(),
        ]);

        return [
            'index' => 'sitemap.xml',
            'files' => $files,
            'generated_at' => now(),
        ];
    }

    public function readSitemap(string $file = 'sitemap.xml'): string
    {
        if (! Schema::hasTable('seo_settings')) {
            return $this->buildSitemapIndex(collect());
        }

        $path = storage_path('app/seo/' . $file);

        if (! File::exists($path)) {
            $this->regenerateSitemaps();
        }

        return File::exists($path) ? File::get($path) : $this->buildSitemapIndex(collect());
    }

    protected function sitemapCollections(SeoSetting $settings): array
    {
        $collections = [];

        if ($settings->sitemap_include_pages) {
            $collections['pages'] = $this->pageTargets()->map(fn (array $item) => $this->sitemapItem($item['url'], $item['updated_at'] ?? null, $item['images'] ?? []));
        }

        if ($settings->sitemap_include_visa_destinations) {
            $collections['visa-destinations'] = $this->visaTargets()->map(fn (array $item) => $this->sitemapItem($item['url'], $item['updated_at'] ?? null, $item['images'] ?? []));
        }

        if ($settings->sitemap_include_destinations) {
            $collections['destinations'] = $this->destinationTargets()->map(fn (array $item) => $this->sitemapItem($item['url'], $item['updated_at'] ?? null, $item['images'] ?? []));
        }

        if ($settings->sitemap_include_blog_posts) {
            $collections['blog-posts'] = $this->blogTargets()->map(fn (array $item) => $this->sitemapItem($item['url'], $item['updated_at'] ?? null, $item['images'] ?? []));
        }

        if ($settings->sitemap_include_marketing_pages) {
            $collections['marketing-pages'] = $this->marketingTargets()->map(fn (array $item) => $this->sitemapItem($item['url'], $item['updated_at'] ?? null, $item['images'] ?? []));
        }

        if ($settings->sitemap_include_images) {
            $collections['images'] = collect($collections)
                ->flatten(1)
                ->flatMap(fn (array $item) => collect($item['images'] ?? [])->map(fn ($image) => [
                    'loc' => $item['loc'],
                    'image' => $image,
                ]))
                ->unique(fn (array $item) => $item['loc'] . '|' . $item['image'])
                ->values();
        }

        return $collections;
    }

    protected function sitemapItem(string $url, mixed $updatedAt = null, array $images = []): array
    {
        return [
            'loc' => $url,
            'lastmod' => optional($updatedAt)->toAtomString() ?: now()->toAtomString(),
            'images' => collect($images)->filter()->values()->all(),
        ];
    }

    protected function buildSitemapIndex(Collection $items): string
    {
        $xml = ['<?xml version="1.0" encoding="UTF-8"?>', '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'];

        foreach ($items as $item) {
            $xml[] = '<sitemap><loc>' . e($item['loc']) . '</loc><lastmod>' . e($item['lastmod']) . '</lastmod></sitemap>';
        }

        $xml[] = '</sitemapindex>';

        return implode('', $xml);
    }

    protected function buildUrlSet(Collection $items): string
    {
        $xml = ['<?xml version="1.0" encoding="UTF-8"?>', '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'];

        foreach ($items as $item) {
            $xml[] = '<url><loc>' . e($item['loc']) . '</loc><lastmod>' . e($item['lastmod']) . '</lastmod></url>';
        }

        $xml[] = '</urlset>';

        return implode('', $xml);
    }

    protected function buildImageSitemap(Collection $items): string
    {
        $xml = [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">',
        ];

        foreach ($items as $item) {
            $xml[] = '<url><loc>' . e($item['loc']) . '</loc><image:image><image:loc>' . e($item['image']) . '</image:loc></image:image></url>';
        }

        $xml[] = '</urlset>';

        return implode('', $xml);
    }

    protected function pageTargets(): Collection
    {
        $routeMap = [
            'home' => 'home',
            'visas' => 'visas.index',
            'domestic' => 'destinations.index',
            'flights' => 'flights',
            'hotels' => 'hotels',
            'about' => 'about',
            'contact' => 'contact',
            'blog' => 'blog.index',
        ];

        return Page::query()
            ->where('is_active', true)
            ->get()
            ->filter(fn (Page $page) => isset($routeMap[$page->key]))
            ->map(fn (Page $page) => [
                'type' => 'page',
                'id' => $page->id,
                'label' => $page->localized('title') ?: $page->key,
                'url' => route($routeMap[$page->key]),
                'updated_at' => $page->updated_at,
                'images' => collect([$page->hero_image])->filter()->map(fn ($path) => asset('storage/' . ltrim($path, '/')))->all(),
                'model' => $page,
            ]);
    }

    protected function visaTargets(): Collection
    {
        return VisaCountry::query()
            ->where('is_active', true)
            ->get()
            ->map(fn (VisaCountry $country) => [
                'type' => 'visa_country',
                'id' => $country->id,
                'label' => $country->localized('name') ?: $country->slug,
                'url' => route('visas.country', $country),
                'updated_at' => $country->updated_at,
                'images' => collect([$country->hero_image, $country->flag_image, $country->og_image])->filter()->map(fn ($path) => asset('storage/' . ltrim($path, '/')))->all(),
                'model' => $country,
            ]);
    }

    protected function destinationTargets(): Collection
    {
        return Destination::query()
            ->where('is_active', true)
            ->get()
            ->map(fn (Destination $destination) => [
                'type' => 'destination',
                'id' => $destination->id,
                'label' => $destination->localized('title') ?: $destination->slug,
                'url' => route('destinations.show', $destination),
                'updated_at' => $destination->updated_at,
                'images' => collect([$destination->hero_image, $destination->featured_image, $destination->flag_image])->filter()->map(fn ($path) => asset('storage/' . ltrim($path, '/')))->all(),
                'model' => $destination,
            ]);
    }

    protected function blogTargets(): Collection
    {
        return BlogPost::query()
            ->where('is_published', true)
            ->get()
            ->map(fn (BlogPost $post) => [
                'type' => 'blog_post',
                'id' => $post->id,
                'label' => $post->localized('title') ?: $post->slug,
                'url' => route('blog.show', $post),
                'updated_at' => $post->updated_at,
                'images' => collect([$post->featured_image])->filter()->map(fn ($path) => asset('storage/' . ltrim($path, '/')))->all(),
                'model' => $post,
            ]);
    }

    protected function marketingTargets(): Collection
    {
        if (! Schema::hasTable('marketing_landing_pages')) {
            return collect();
        }

        return MarketingLandingPage::query()
            ->where('status', MarketingLandingPage::STATUS_PUBLISHED)
            ->get()
            ->map(fn (MarketingLandingPage $page) => [
                'type' => 'marketing_landing_page',
                'id' => $page->id,
                'label' => $page->localized('title') ?: $page->internal_name,
                'url' => route('marketing.landing-pages.show', $page),
                'updated_at' => $page->updated_at,
                'images' => collect([data_get($page->sections, 'hero.background_image')])->filter()->all(),
                'model' => $page,
            ]);
    }

    protected function targetFromRequest(Request $request): array
    {
        $route = $request->route();
        $routeName = $route?->getName();

        if ($country = $route?->parameter('country')) {
            return ['visa_country', $country->id, $country];
        }

        if ($destination = $route?->parameter('destination')) {
            return ['destination', $destination->id, $destination];
        }

        if ($post = $route?->parameter('post')) {
            return ['blog_post', $post->id, $post];
        }

        if ($landingPage = $route?->parameter('landingPage')) {
            return ['marketing_landing_page', $landingPage->id, $landingPage];
        }

        $pageKey = match ($routeName) {
            'home' => 'home',
            'visas.index' => 'visas',
            'destinations.index' => 'domestic',
            'flights' => 'flights',
            'hotels' => 'hotels',
            'about' => 'about',
            'contact' => 'contact',
            'blog.index' => 'blog',
            default => null,
        };

        if (! $pageKey) {
            return [null, null, null];
        }

        $page = Page::query()->where('key', $pageKey)->first();

        return $page ? ['page', $page->id, $page] : [null, null, null];
    }

    protected function fallbackModelValue(?Model $model, string $field): ?string
    {
        if (! $model) {
            return null;
        }

        if (method_exists($model, 'localized')) {
            return $model->localized($field);
        }

        return $model->{$field} ?? null;
    }

    protected function fallbackImage(?Model $model): ?string
    {
        if (! $model) {
            return null;
        }

        foreach (['og_image', 'featured_image', 'hero_image', 'featured_image', 'hero_image', 'flag_image'] as $field) {
            $value = $model->{$field} ?? null;
            if (filled($value)) {
                return asset('storage/' . ltrim((string) $value, '/'));
            }
        }

        return null;
    }

    protected function schemaForRequest(Request $request, SeoSetting $settings, ?SeoMetaEntry $entry, ?Model $model, ?string $title, ?string $description, ?string $canonical): array
    {
        $schema = [];

        if ($settings->schema_organization_enabled) {
            $schema[] = array_filter([
                '@context' => 'https://schema.org',
                '@type' => $settings->schema_local_business_enabled ? 'LocalBusiness' : 'Organization',
                'name' => $settings->schema_organization_name ?: 'Travel Wave',
                'url' => url('/'),
                'logo' => filled($settings->schema_organization_logo) ? asset('storage/' . ltrim($settings->schema_organization_logo, '/')) : null,
            ]);
        }

        if ($settings->schema_article_enabled && $model instanceof BlogPost) {
            $schema[] = array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => $title,
                'description' => $description,
                'datePublished' => optional($model->published_at)->toAtomString(),
                'image' => $this->fallbackImage($model),
                'mainEntityOfPage' => $canonical,
            ]);
        }

        if ($entry?->schema_enabled && filled($entry->schema_type)) {
            $schema[] = array_filter([
                '@context' => 'https://schema.org',
                '@type' => $entry->schema_type,
                'name' => $title,
                'description' => $description,
                'url' => $canonical,
            ]);
        }

        return $schema;
    }
}
