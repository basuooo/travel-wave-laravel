<?php

namespace App\Services\LandingPageNew;

use App\Models\LandingPageNew\LpNewLandingPage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ZipArchive;

class LandingPageNewImporter
{
    protected HtmlAnalyzer $analyzer;
    protected AssetScanner $assetScanner;

    public function __construct(HtmlAnalyzer $analyzer, AssetScanner $assetScanner)
    {
        $this->analyzer = $analyzer;
        $this->assetScanner = $assetScanner;
    }

    public function importFromZip(UploadedFile $zipFile, array $overrides = []): LpNewLandingPage
    {
        LpNewLandingPage::ensureTableSchema();

        $tempExtractPath = storage_path('app/temp-lp-import-' . Str::random(10));
        File::makeDirectory($tempExtractPath, 0755, true);

        $zip = new ZipArchive();
        if ($zip->open($zipFile->getRealPath()) !== true) {
            File::deleteDirectory($tempExtractPath);
            throw new \RuntimeException('Failed to open ZIP package.');
        }

        $zip->extractTo($tempExtractPath);
        $zip->close();

        // Find main HTML file (index.html or first .html file)
        $mainHtmlFile = $this->findMainHtmlFile($tempExtractPath);
        if (! $mainHtmlFile) {
            File::deleteDirectory($tempExtractPath);
            throw new \RuntimeException('No index.html or HTML file found in ZIP package.');
        }

        $rawHtml = File::get($mainHtmlFile->getRealPath());

        // Create landing page record placeholder
        $internalName = ! empty($overrides['internal_name']) ? $overrides['internal_name'] : pathinfo($zipFile->getClientOriginalName(), PATHINFO_FILENAME);
        $slugSeed = (! empty($overrides['slug']) && $overrides['slug'] !== 'null') ? $overrides['slug'] : $internalName;
        $slug = LpNewLandingPage::makeUniqueSlug($slugSeed);

        $landingPage = LpNewLandingPage::create([
            'internal_name' => $internalName,
            'title_ar' => $overrides['title_ar'] ?? $internalName,
            'title_en' => $overrides['title_en'] ?? $internalName,
            'slug' => $slug,
            'brand_id' => $overrides['brand_id'] ?? null,
            'assigned_lead_form_id' => $overrides['assigned_lead_form_id'] ?? null,
            'status' => LpNewLandingPage::STATUS_DRAFT,
            'is_active' => true,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        // Scan and extract all assets
        $assetMap = $this->assetScanner->scanAndStoreAssets($tempExtractPath, $landingPage);

        // Rewrite asset references in HTML
        $processedHtml = $this->assetScanner->rewriteReferencesInHtml($rawHtml, $assetMap);
        $processedHtml = $this->unlockNonEditableElements($processedHtml);

        // Analyze HTML structure, dependencies, forms & sections
        $analysis = $this->analyzer->analyze($processedHtml);

        // Read and compile external CSS files from ZIP
        $compiledCss = $this->compileCssFiles($tempExtractPath, $assetMap);
        if (filled($analysis['head_styles'])) {
            $compiledCss .= "\n" . $analysis['head_styles'];
        }

        // Read and compile external JS files from ZIP
        $compiledJs = $this->compileJsFiles($tempExtractPath, $assetMap);
        if (filled($analysis['head_scripts'])) {
            $compiledJs .= "\n" . $analysis['head_scripts'];
        }

        $structure = [
            'html' => $processedHtml,
            'elements' => $analysis['sections'],
            'imported_at' => now()->toIso8601String(),
            'source_zip' => $zipFile->getClientOriginalName(),
        ];

        $landingPage->update([
            'structure' => json_encode($structure, JSON_UNESCAPED_UNICODE),
            'custom_css' => $compiledCss,
            'custom_js' => $compiledJs,
            'dependency_libraries' => $analysis['dependencies'],
        ]);

        // Cleanup temporary folder
        File::deleteDirectory($tempExtractPath);

        return $landingPage;
    }

    protected function findMainHtmlFile(string $dir): ?\Symfony\Component\Finder\SplFileInfo
    {
        $allFiles = File::allFiles($dir);

        foreach ($allFiles as $file) {
            if (strtolower($file->getFilename()) === 'index.html' || strtolower($file->getFilename()) === 'index.htm') {
                return $file;
            }
        }

        foreach ($allFiles as $file) {
            if (strtolower($file->getExtension()) === 'html' || strtolower($file->getExtension()) === 'htm') {
                return $file;
            }
        }

        return null;
    }

    protected function compileCssFiles(string $dir, array $assetMap): string
    {
        $compiled = '';
        $cssFiles = File::allFiles($dir);

        foreach ($cssFiles as $file) {
            if (strtolower($file->getExtension()) === 'css') {
                $content = File::get($file->getRealPath());
                $content = $this->assetScanner->rewriteReferencesInCss($content, $assetMap);
                $compiled .= "/* File: " . $file->getRelativePathname() . " */\n" . $content . "\n\n";
            }
        }

        return $compiled;
    }

    protected function compileJsFiles(string $dir, array $assetMap): string
    {
        $compiled = '';
        $jsFiles = File::allFiles($dir);

        foreach ($jsFiles as $file) {
            if (strtolower($file->getExtension()) === 'js') {
                $content = File::get($file->getRealPath());
                $compiled .= "/* File: " . $file->getRelativePathname() . " */\n" . $content . "\n\n";
            }
        }

        return $compiled;
    }

    protected function unlockNonEditableElements(string $html): string
    {
        $html = preg_replace('/contenteditable=["\']false["\']/i', 'contenteditable="true"', $html);
        $html = preg_replace('/data-gjs-editable=["\']false["\']/i', 'data-gjs-editable="true"', $html);
        $html = preg_replace('/data-gjs-selectable=["\']false["\']/i', 'data-gjs-selectable="true"', $html);
        $html = preg_replace('/data-gjs-hoverable=["\']false["\']/i', 'data-gjs-hoverable="true"', $html);
        $html = preg_replace('/\bnon-editable-area\b/i', 'editable-area', $html);
        $html = preg_replace('/\bnon-editable\b/i', 'is-editable', $html);
        return $html;
    }
}
