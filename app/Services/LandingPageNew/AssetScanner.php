<?php

namespace App\Services\LandingPageNew;

use App\Models\LandingPageNew\LpNewAsset;
use App\Models\LandingPageNew\LpNewLandingPage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AssetScanner
{
    public function scanAndStoreAssets(string $extractPath, LpNewLandingPage $landingPage): array
    {
        $targetDir = storage_path("app/public/landing-pages-new/{$landingPage->id}");
        if (! File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        $allFiles = File::allFiles($extractPath);
        $assetMap = [];

        foreach ($allFiles as $file) {
            $relativePath = str_replace('\\', '/', $file->getRelativePathname());
            $extension = strtolower($file->getExtension());

            // Skip index.html root file as asset
            if ($relativePath === 'index.html' || $relativePath === 'index.htm') {
                continue;
            }

            $assetType = $this->classifyAssetType($extension);
            $filename = $file->getFilename();
            $uniqueFilename = Str::slug(pathinfo($filename, PATHINFO_FILENAME)) . '-' . Str::random(6) . '.' . $extension;
            $storageSubPath = "landing-pages-new/{$landingPage->id}/{$uniqueFilename}";
            $destinationFullPath = storage_path("app/public/{$storageSubPath}");

            File::copy($file->getRealPath(), $destinationFullPath);

            $assetRecord = LpNewAsset::create([
                'landing_page_id' => $landingPage->id,
                'filename' => $filename,
                'original_path' => $relativePath,
                'storage_path' => $storageSubPath,
                'mime_type' => File::mimeType($destinationFullPath) ?: 'application/octet-stream',
                'file_size' => $file->getSize(),
                'asset_type' => $assetType,
                'usage_count' => 1,
            ]);

            $publicUrl = asset('storage/' . $storageSubPath);

            $assetMap[$relativePath] = [
                'record_id' => $assetRecord->id,
                'original' => $relativePath,
                'storage_path' => $storageSubPath,
                'public_url' => $publicUrl,
                'type' => $assetType,
            ];
        }

        return $assetMap;
    }

    public function rewriteReferencesInHtml(string $html, array $assetMap): string
    {
        foreach ($assetMap as $relativePath => $meta) {
            $publicUrl = $meta['public_url'];

            // Replace various relative path formats
            $html = str_replace('src="' . $relativePath . '"', 'src="' . $publicUrl . '"', $html);
            $html = str_replace("src='" . $relativePath . "'", "src='" . $publicUrl . "'", $html);

            $html = str_replace('href="' . $relativePath . '"', 'href="' . $publicUrl . '"', $html);
            $html = str_replace("href='" . $relativePath . "'", "href='" . $publicUrl . "'", $html);

            // Also check ./relativePath
            $dotSlash = './' . $relativePath;
            $html = str_replace('src="' . $dotSlash . '"', 'src="' . $publicUrl . '"', $html);
            $html = str_replace("src='" . $dotSlash . "'", "src='" . $publicUrl . "'", $html);
            $html = str_replace('href="' . $dotSlash . '"', 'href="' . $publicUrl . '"', $html);
            $html = str_replace("href='" . $dotSlash . "'", "href='" . $publicUrl . "'", $html);
        }

        return $html;
    }

    public function rewriteReferencesInCss(string $css, array $assetMap): string
    {
        foreach ($assetMap as $relativePath => $meta) {
            $publicUrl = $meta['public_url'];

            $css = str_replace('url(' . $relativePath . ')', 'url(' . $publicUrl . ')', $css);
            $css = str_replace('url("' . $relativePath . '")', 'url("' . $publicUrl . '")', $css);
            $css = str_replace("url('" . $relativePath . "')", "url('" . $publicUrl . "')", $css);
        }

        return $css;
    }

    protected function classifyAssetType(string $extension): string
    {
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'bmp', 'ico'])) {
            return 'image';
        }
        if ($extension === 'svg') {
            return 'svg';
        }
        if (in_array($extension, ['woff', 'woff2', 'ttf', 'eot', 'otf'])) {
            return 'font';
        }
        if ($extension === 'css') {
            return 'css';
        }
        if ($extension === 'js') {
            return 'js';
        }

        return 'other';
    }
}
