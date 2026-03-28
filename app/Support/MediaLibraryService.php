<?php

namespace App\Support;

use App\Models\BlogPost;
use App\Models\Destination;
use App\Models\HeroSlide;
use App\Models\HomeCountryStripItem;
use App\Models\MapSection;
use App\Models\MarketingLandingPage;
use App\Models\MediaAsset;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\VisaCategory;
use App\Models\VisaCountry;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaLibraryService
{
    public static function normalizePath(string $path): string
    {
        $path = trim(str_replace('\\', '/', $path));

        if ($path === '') {
            return '';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $path = trim((string) parse_url($path, PHP_URL_PATH), '/');
        }

        $path = preg_replace('#^(?:/?storage/)+#', '', $path) ?: $path;
        $path = preg_replace('#^(?:/?public/)+#', '', $path) ?: $path;

        return ltrim($path, '/');
    }

    public static function syncKnownReferences(): void
    {
        if (! Schema::hasTable('media_assets')) {
            return;
        }

        $singleColumns = [
            Setting::class => ['logo_path', 'header_logo_path', 'footer_logo_path', 'favicon_path'],
            HeroSlide::class => ['image_path', 'mobile_image_path'],
            Page::class => ['hero_image'],
            Destination::class => ['hero_image', 'featured_image', 'hero_mobile_image', 'flag_image', 'about_image', 'cta_background_image'],
            VisaCountry::class => ['hero_image', 'hero_mobile_image', 'flag_image', 'intro_image', 'final_cta_background_image', 'og_image'],
            BlogPost::class => ['featured_image'],
            Testimonial::class => ['image'],
            HomeCountryStripItem::class => ['image_path', 'flag_image_path'],
            VisaCategory::class => ['image'],
            User::class => ['profile_image'],
        ];

        foreach ($singleColumns as $modelClass => $columns) {
            $model = new $modelClass();
            if (! Schema::hasTable($model->getTable())) {
                continue;
            }

            foreach ($columns as $column) {
                if (! Schema::hasColumn($model->getTable(), $column)) {
                    continue;
                }

                $modelClass::query()
                    ->whereNotNull($column)
                    ->pluck($column)
                    ->filter()
                    ->unique()
                    ->each(fn ($path) => self::syncExistingFile((string) $path));
            }
        }

        if (Schema::hasTable((new Destination())->getTable())) {
            Destination::query()->whereNotNull('gallery')->get()->each(function (Destination $destination) {
                foreach ((array) $destination->gallery as $path) {
                    if (is_string($path) && trim($path) !== '') {
                        self::syncExistingFile($path, 'destinations/gallery');
                    }
                }
            });

            if (Schema::hasColumn((new Destination())->getTable(), 'highlight_items')) {
                Destination::query()->whereNotNull('highlight_items')->get()->each(function (Destination $destination) {
                    foreach ((array) $destination->highlight_items as $item) {
                        $path = is_array($item) ? ($item['image'] ?? null) : null;
                        if (is_string($path) && trim($path) !== '') {
                            self::syncExistingFile($path, 'destinations/highlights');
                        }
                    }
                });
            }
        }

        if (Schema::hasTable((new VisaCountry())->getTable()) && Schema::hasColumn((new VisaCountry())->getTable(), 'highlights')) {
            VisaCountry::query()->whereNotNull('highlights')->get()->each(function (VisaCountry $country) {
                foreach ((array) $country->highlights as $item) {
                    $path = is_array($item) ? ($item['image'] ?? null) : null;
                    if (is_string($path) && trim($path) !== '') {
                        self::syncExistingFile($path, 'visa-countries/highlights');
                    }
                }
            });
        }

        if (Schema::hasTable((new MarketingLandingPage())->getTable())) {
            MarketingLandingPage::query()->whereNotNull('sections')->get()->each(function (MarketingLandingPage $page) {
                $sections = (array) $page->sections;
                foreach ([
                    data_get($sections, 'hero.background_image'),
                    data_get($sections, 'testimonials.background_image'),
                    data_get($sections, 'cta.background_image'),
                ] as $path) {
                    if (is_string($path) && trim($path) !== '') {
                        self::syncExistingFile($path, 'marketing/landing-pages');
                    }
                }
            });
        }
    }

    public static function usageCounts(array $paths): array
    {
        $normalizedPaths = collect($paths)
            ->map(fn ($path) => self::normalizePath((string) $path))
            ->filter()
            ->unique()
            ->values();

        if ($normalizedPaths->isEmpty()) {
            return [];
        }

        $counts = $normalizedPaths->mapWithKeys(fn ($path) => [$path => 0])->all();

        $singleColumns = [
            Setting::class => ['logo_path', 'header_logo_path', 'footer_logo_path', 'favicon_path'],
            HeroSlide::class => ['image_path', 'mobile_image_path'],
            Page::class => ['hero_image'],
            Destination::class => ['hero_image', 'featured_image', 'hero_mobile_image', 'flag_image', 'about_image', 'cta_background_image'],
            VisaCountry::class => ['hero_image', 'hero_mobile_image', 'flag_image', 'intro_image', 'final_cta_background_image', 'og_image'],
            BlogPost::class => ['featured_image'],
            Testimonial::class => ['image'],
            HomeCountryStripItem::class => ['image_path', 'flag_image_path'],
            VisaCategory::class => ['image'],
            User::class => ['profile_image'],
        ];

        foreach ($singleColumns as $modelClass => $columns) {
            $model = new $modelClass();
            if (! Schema::hasTable($model->getTable())) {
                continue;
            }

            foreach ($columns as $column) {
                if (! Schema::hasColumn($model->getTable(), $column)) {
                    continue;
                }

                $matches = $modelClass::query()
                    ->whereIn($column, $normalizedPaths->all())
                    ->selectRaw($column . ' as matched_path, COUNT(*) as aggregate_count')
                    ->groupBy($column)
                    ->get();

                foreach ($matches as $match) {
                    $path = self::normalizePath((string) $match->matched_path);
                    if ($path !== '' && array_key_exists($path, $counts)) {
                        $counts[$path] += (int) $match->aggregate_count;
                    }
                }
            }
        }

        if (Schema::hasTable((new Destination())->getTable()) && Schema::hasColumn((new Destination())->getTable(), 'gallery')) {
            foreach ($normalizedPaths as $path) {
                $counts[$path] += Destination::query()->where('gallery', 'like', '%' . $path . '%')->count();
            }
        }

        if (Schema::hasTable((new Destination())->getTable()) && Schema::hasColumn((new Destination())->getTable(), 'highlight_items')) {
            foreach ($normalizedPaths as $path) {
                $counts[$path] += Destination::query()->where('highlight_items', 'like', '%' . $path . '%')->count();
            }
        }

        if (Schema::hasTable((new VisaCountry())->getTable()) && Schema::hasColumn((new VisaCountry())->getTable(), 'highlights')) {
            foreach ($normalizedPaths as $path) {
                $counts[$path] += VisaCountry::query()->where('highlights', 'like', '%' . $path . '%')->count();
            }
        }

        if (Schema::hasTable((new MarketingLandingPage())->getTable()) && Schema::hasColumn((new MarketingLandingPage())->getTable(), 'sections')) {
            foreach ($normalizedPaths as $path) {
                $counts[$path] += MarketingLandingPage::query()->where('sections', 'like', '%' . $path . '%')->count();
            }
        }

        return $counts;
    }

    public static function registerUploadedFile(UploadedFile $file, string $path, string $directory, ?string $title = null): ?MediaAsset
    {
        if (! Schema::hasTable('media_assets')) {
            return null;
        }

        $normalizedPath = self::normalizePath($path);
        $dimensions = self::imageDimensions($file);

        return MediaAsset::query()->updateOrCreate(
            ['path' => $normalizedPath],
            [
                'title' => $title ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'disk' => 'public',
                'directory' => $directory,
                'file_name' => basename($normalizedPath),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->extension(),
                'size' => $file->getSize(),
                'width' => $dimensions['width'],
                'height' => $dimensions['height'],
                'uploaded_by' => Auth::id(),
            ]
        );
    }

    public static function syncExistingFile(string $path, string $directory = 'library'): ?MediaAsset
    {
        $normalizedPath = self::normalizePath($path);

        if (! Schema::hasTable('media_assets') || $normalizedPath === '' || ! Storage::disk('public')->exists($normalizedPath)) {
            return null;
        }

        $fullPath = Storage::disk('public')->path($normalizedPath);
        $dimensions = @getimagesize($fullPath) ?: [null, null];

        return MediaAsset::query()->firstOrCreate(
            ['path' => $normalizedPath],
            [
                'title' => pathinfo($normalizedPath, PATHINFO_FILENAME),
                'disk' => 'public',
                'directory' => trim(dirname($normalizedPath), '/.') ?: $directory,
                'file_name' => basename($normalizedPath),
                'mime_type' => Storage::disk('public')->mimeType($normalizedPath) ?: null,
                'extension' => pathinfo($normalizedPath, PATHINFO_EXTENSION) ?: null,
                'size' => Storage::disk('public')->size($normalizedPath) ?: null,
                'width' => $dimensions[0] ?: null,
                'height' => $dimensions[1] ?: null,
                'uploaded_by' => Auth::id(),
            ]
        );
    }

    public static function usageReferences(string $path): array
    {
        self::syncKnownReferences();
        $references = [];

        $singleColumns = [
            Setting::class => ['logo_path', 'header_logo_path', 'footer_logo_path', 'favicon_path'],
            HeroSlide::class => ['image_path', 'mobile_image_path'],
            Page::class => ['hero_image'],
            Destination::class => ['hero_image', 'featured_image', 'hero_mobile_image', 'flag_image', 'about_image', 'cta_background_image'],
            VisaCountry::class => ['hero_image', 'hero_mobile_image', 'flag_image', 'intro_image', 'final_cta_background_image', 'og_image'],
            BlogPost::class => ['featured_image'],
            Testimonial::class => ['image'],
            HomeCountryStripItem::class => ['image_path', 'flag_image_path'],
            VisaCategory::class => ['image'],
            User::class => ['profile_image'],
            MapSection::class => ['embed_code'],
        ];

        foreach ($singleColumns as $modelClass => $columns) {
            if (! Schema::hasTable((new $modelClass())->getTable())) {
                continue;
            }

            foreach ($columns as $column) {
                if (! Schema::hasColumn((new $modelClass())->getTable(), $column)) {
                    continue;
                }

                $matches = $modelClass::query()->where($column, $path)->count();
                if ($matches > 0) {
                    $references[] = [
                        'source' => class_basename($modelClass),
                        'field' => $column,
                        'count' => $matches,
                    ];
                }
            }
        }

        if (Schema::hasTable((new Destination())->getTable())) {
            $galleryMatches = Schema::hasColumn((new Destination())->getTable(), 'gallery')
                ? Destination::query()->where('gallery', 'like', '%' . $path . '%')->count()
                : 0;
            if ($galleryMatches > 0) {
                $references[] = [
                    'source' => 'Destination',
                    'field' => 'gallery',
                    'count' => $galleryMatches,
                ];
            }

            $highlightMatches = Schema::hasColumn((new Destination())->getTable(), 'highlight_items')
                ? Destination::query()->where('highlight_items', 'like', '%' . $path . '%')->count()
                : 0;
            if ($highlightMatches > 0) {
                $references[] = [
                    'source' => 'Destination',
                    'field' => 'highlight_items',
                    'count' => $highlightMatches,
                ];
            }
        }

        if (Schema::hasTable((new VisaCountry())->getTable()) && Schema::hasColumn((new VisaCountry())->getTable(), 'highlights')) {
            $highlightMatches = VisaCountry::query()->where('highlights', 'like', '%' . $path . '%')->count();
            if ($highlightMatches > 0) {
                $references[] = [
                    'source' => 'Visa Country',
                    'field' => 'highlights',
                    'count' => $highlightMatches,
                ];
            }
        }

        if (Schema::hasTable((new MarketingLandingPage())->getTable())) {
            $sectionMatches = Schema::hasColumn((new MarketingLandingPage())->getTable(), 'sections')
                ? MarketingLandingPage::query()->where('sections', 'like', '%' . $path . '%')->count()
                : 0;
            if ($sectionMatches > 0) {
                $references[] = [
                    'source' => 'MarketingLandingPage',
                    'field' => 'sections',
                    'count' => $sectionMatches,
                ];
            }
        }

        return $references;
    }

    public static function usageDetails(string $path): array
    {
        self::syncKnownReferences();

        $path = self::normalizePath($path);
        if ($path === '') {
            return [];
        }

        $details = [];
        $singleColumns = [
            Setting::class => ['logo_path', 'header_logo_path', 'footer_logo_path', 'favicon_path'],
            HeroSlide::class => ['image_path', 'mobile_image_path'],
            Page::class => ['hero_image'],
            Destination::class => ['hero_image', 'featured_image', 'hero_mobile_image', 'flag_image', 'about_image', 'cta_background_image'],
            VisaCountry::class => ['hero_image', 'hero_mobile_image', 'flag_image', 'intro_image', 'final_cta_background_image', 'og_image'],
            BlogPost::class => ['featured_image'],
            Testimonial::class => ['image'],
            HomeCountryStripItem::class => ['image_path', 'flag_image_path'],
            VisaCategory::class => ['image'],
            User::class => ['profile_image'],
            MapSection::class => ['embed_code'],
        ];

        foreach ($singleColumns as $modelClass => $columns) {
            $model = new $modelClass();
            if (! Schema::hasTable($model->getTable())) {
                continue;
            }

            foreach ($columns as $column) {
                if (! Schema::hasColumn($model->getTable(), $column)) {
                    continue;
                }

                $matches = $modelClass::query()->where($column, $path)->get();

                foreach ($matches as $record) {
                    $details[] = self::buildUsageDetail($record, $column);
                }
            }
        }

        if (Schema::hasTable((new Destination())->getTable()) && Schema::hasColumn((new Destination())->getTable(), 'gallery')) {
            Destination::query()->where('gallery', 'like', '%' . $path . '%')->get()->each(function (Destination $destination) use (&$details) {
                $details[] = self::buildUsageDetail($destination, 'gallery');
            });
        }

        if (Schema::hasTable((new Destination())->getTable()) && Schema::hasColumn((new Destination())->getTable(), 'highlight_items')) {
            Destination::query()->where('highlight_items', 'like', '%' . $path . '%')->get()->each(function (Destination $destination) use (&$details) {
                $details[] = self::buildUsageDetail($destination, 'highlight_items');
            });
        }

        if (Schema::hasTable((new VisaCountry())->getTable()) && Schema::hasColumn((new VisaCountry())->getTable(), 'highlights')) {
            VisaCountry::query()->where('highlights', 'like', '%' . $path . '%')->get()->each(function (VisaCountry $country) use (&$details) {
                $details[] = self::buildUsageDetail($country, 'highlights');
            });
        }

        if (Schema::hasTable((new MarketingLandingPage())->getTable()) && Schema::hasColumn((new MarketingLandingPage())->getTable(), 'sections')) {
            MarketingLandingPage::query()->where('sections', 'like', '%' . $path . '%')->get()->each(function (MarketingLandingPage $page) use (&$details) {
                $details[] = self::buildUsageDetail($page, 'sections');
            });
        }

        return collect($details)
            ->unique(fn (array $detail) => implode('|', [
                $detail['source'],
                $detail['field'],
                $detail['record_id'] ?? 'none',
                $detail['label'],
            ]))
            ->values()
            ->all();
    }

    public static function pathInUse(string $path): bool
    {
        return ! empty(self::usageReferences($path));
    }

    protected static function imageDimensions(UploadedFile $file): array
    {
        $dimensions = @getimagesize($file->getPathname()) ?: [null, null];

        return [
            'width' => $dimensions[0] ?: null,
            'height' => $dimensions[1] ?: null,
        ];
    }

    protected static function buildUsageDetail(Model $record, string $field): array
    {
        return [
            'source' => self::usageSourceLabel($record),
            'field' => self::usageFieldLabel($field),
            'record_id' => $record->getKey(),
            'label' => self::usageRecordLabel($record, $field),
            'admin_url' => self::usageAdminUrl($record, $field),
        ];
    }

    protected static function usageSourceLabel(Model $record): string
    {
        return match ($record::class) {
            Setting::class => 'Setting',
            HeroSlide::class => 'Banner',
            Page::class => 'Page',
            Destination::class => 'Destination',
            VisaCountry::class => 'Visa Country',
            BlogPost::class => 'Blog Post',
            Testimonial::class => 'Testimonial',
            HomeCountryStripItem::class => 'Homepage Strip',
            VisaCategory::class => 'Visa Category',
            User::class => 'User',
            MarketingLandingPage::class => 'Landing Page',
            MapSection::class => 'Map Section',
            default => class_basename($record),
        };
    }

    protected static function usageFieldLabel(string $field): string
    {
        return match ($field) {
            'logo_path' => 'Site Logo',
            'header_logo_path' => 'Header Logo',
            'footer_logo_path' => 'Footer Logo',
            'favicon_path' => 'Favicon',
            'image_path' => 'Image',
            'mobile_image_path' => 'Mobile Image',
            'hero_image' => 'Hero Image',
            'hero_mobile_image' => 'Hero Mobile Image',
            'featured_image' => 'Featured Image',
            'flag_image' => 'Flag Image',
            'about_image' => 'About Image',
            'cta_background_image' => 'CTA Background',
            'intro_image' => 'Intro Image',
            'final_cta_background_image' => 'Final CTA Background',
            'og_image' => 'Open Graph Image',
            'image' => 'Image',
            'profile_image' => 'Profile Image',
            'gallery' => 'Gallery',
            'highlights' => 'Highlights',
            'highlight_items' => 'Helpful Guidance Points',
            'sections' => 'Sections',
            default => Str::title(str_replace('_', ' ', $field)),
        };
    }

    protected static function usageRecordLabel(Model $record, string $field): string
    {
        return match ($record::class) {
            Setting::class => self::usageFieldLabel($field),
            HeroSlide::class => $record->localized('headline') ?: 'Slide #' . $record->getKey(),
            Page::class => $record->localized('title') ?: ($record->key ?: 'Page #' . $record->getKey()),
            Destination::class => $record->localized('title') ?: 'Destination #' . $record->getKey(),
            VisaCountry::class => $record->localized('name') ?: 'Visa Country #' . $record->getKey(),
            BlogPost::class => $record->localized('title') ?: 'Blog Post #' . $record->getKey(),
            Testimonial::class => $record->client_name ?: 'Testimonial #' . $record->getKey(),
            HomeCountryStripItem::class => $record->displayName() ?: 'Homepage Strip Item #' . $record->getKey(),
            VisaCategory::class => $record->localized('name') ?: 'Visa Category #' . $record->getKey(),
            User::class => $record->name ?: $record->email ?: 'User #' . $record->getKey(),
            MarketingLandingPage::class => $record->localized('title') ?: $record->internal_name ?: 'Landing Page #' . $record->getKey(),
            MapSection::class => $record->localized('title') ?: $record->name ?: 'Map Section #' . $record->getKey(),
            default => class_basename($record) . ' #' . $record->getKey(),
        };
    }

    protected static function usageAdminUrl(Model $record, string $field): ?string
    {
        return match ($record::class) {
            Setting::class => self::usageSettingAdminUrl($field),
            HeroSlide::class => Route::has('admin.hero-slides.edit') ? route('admin.hero-slides.edit', $record) : null,
            Page::class => Route::has('admin.pages.edit') ? route('admin.pages.edit', $record->key) : null,
            Destination::class => Route::has('admin.destinations.edit') ? route('admin.destinations.edit', $record) : null,
            VisaCountry::class => Route::has('admin.visa-countries.edit') ? route('admin.visa-countries.edit', $record) : null,
            BlogPost::class => Route::has('admin.blog-posts.edit') ? route('admin.blog-posts.edit', $record) : null,
            Testimonial::class => Route::has('admin.testimonials.edit') ? route('admin.testimonials.edit', $record) : null,
            HomeCountryStripItem::class => Route::has('admin.home-country-strip.edit') ? route('admin.home-country-strip.edit', $record) : null,
            VisaCategory::class => Route::has('admin.visa-categories.edit') ? route('admin.visa-categories.edit', $record) : null,
            User::class => Route::has('admin.users.edit') ? route('admin.users.edit', $record) : null,
            MarketingLandingPage::class => Route::has('admin.marketing-landing-pages.edit') ? route('admin.marketing-landing-pages.edit', $record) : null,
            default => null,
        };
    }

    protected static function usageSettingAdminUrl(string $field): ?string
    {
        return match ($field) {
            'header_logo_path' => Route::has('admin.header-settings.edit') ? route('admin.header-settings.edit') : null,
            'footer_logo_path' => Route::has('admin.footer-settings.edit') ? route('admin.footer-settings.edit') : null,
            default => Route::has('admin.settings.edit') ? route('admin.settings.edit') : null,
        };
    }
}
