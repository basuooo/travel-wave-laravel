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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

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
}
