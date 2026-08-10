<?php

namespace App\Services\LandingPageNew;

use App\Models\LandingPageNew\LpNewLandingPage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ZipArchive;

class LandingPageNewExporter
{
    public function exportToZip(LpNewLandingPage $landingPage, bool $includeIntegrations = true): string
    {
        $tempDir = storage_path('app/temp-lp-export-' . Str::random(10));
        File::makeDirectory($tempDir, 0755, true);
        File::makeDirectory($tempDir . '/css', 0755, true);
        File::makeDirectory($tempDir . '/js', 0755, true);
        File::makeDirectory($tempDir . '/images', 0755, true);

        // Copy page assets to images/ or assets/
        $assets = $landingPage->assets;
        $assetRewrites = [];

        foreach ($assets as $asset) {
            $sourcePath = storage_path('app/public/' . $asset->storage_path);
            if (File::exists($sourcePath)) {
                $subFolder = $asset->asset_type === 'image' || $asset->asset_type === 'svg' ? 'images/' : 'assets/';
                File::makeDirectory($tempDir . '/' . $subFolder, 0755, true, true);

                $targetRelPath = $subFolder . $asset->filename;
                File::copy($sourcePath, $tempDir . '/' . $targetRelPath);

                $publicUrl = $asset->storageUrl();
                $assetRewrites[$publicUrl] = $targetRelPath;
            }
        }

        // Render HTML content
        $htmlContent = $this->renderHtml($landingPage, $assetRewrites, $includeIntegrations);
        File::put($tempDir . '/index.html', $htmlContent);

        // Write custom CSS file if exists
        if (filled($landingPage->custom_css)) {
            $cssContent = $landingPage->custom_css;
            foreach ($assetRewrites as $url => $relPath) {
                $cssContent = str_replace($url, '../' . $relPath, $cssContent);
            }
            File::put($tempDir . '/css/custom.css', $cssContent);
        }

        // Write custom JS file if exists
        if (filled($landingPage->custom_js)) {
            File::put($tempDir . '/js/custom.js', $landingPage->custom_js);
        }

        // Create Zip Archive
        $zipFileName = 'landing-page-' . $landingPage->slug . '-' . now()->format('Ymd-His') . '.zip';
        $zipPath = storage_path('app/public/exports/' . $zipFileName);
        File::makeDirectory(storage_path('app/public/exports'), 0755, true, true);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $files = File::allFiles($tempDir);
            foreach ($files as $file) {
                $relativePath = str_replace('\\', '/', $file->getRelativePathname());
                $zip->addFile($file->getRealPath(), $relativePath);
            }
            $zip->close();
        }

        File::deleteDirectory($tempDir);

        return $zipPath;
    }

    protected function renderHtml(LpNewLandingPage $page, array $assetRewrites, bool $includeIntegrations): string
    {
        $structure = is_string($page->structure) ? json_decode($page->structure, true) : ($page->structure ?? []);
        $elements = $structure['elements'] ?? [];

        $sectionsHtml = '';
        foreach ($elements as $el) {
            $sectionsHtml .= ($el['html'] ?? '') . "\n";
        }

        foreach ($assetRewrites as $url => $relPath) {
            $sectionsHtml = str_replace($url, $relPath, $sectionsHtml);
        }

        $title = $page->seo_title_ar ?: ($page->title_ar ?: $page->internal_name);
        $description = $page->seo_description_ar ?: '';

        $cssLinks = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">';
        if (File::exists(storage_path('app/public/exports/custom.css')) || filled($page->custom_css)) {
            $cssLinks .= "\n" . '<link rel="stylesheet" href="css/custom.css">';
        }

        $jsScripts = '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>';
        if (filled($page->custom_js)) {
            $jsScripts .= "\n" . '<script src="js/custom.js"></script>';
        }

        $customHead = $page->custom_html_head ?? '';

        return <<<HTML
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <meta name="description" content="{$description}">
    {$cssLinks}
    {$customHead}
</head>
<body>
    {$sectionsHtml}
    {$jsScripts}
</body>
</html>
HTML;
    }
}
