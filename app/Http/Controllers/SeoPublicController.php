<?php

namespace App\Http\Controllers;

use App\Support\SeoManager;

class SeoPublicController extends Controller
{
    public function sitemapIndex(SeoManager $seoManager)
    {
        return response($seoManager->readSitemap('sitemap.xml'), 200, ['Content-Type' => 'application/xml']);
    }

    public function sitemapFile(string $file, SeoManager $seoManager)
    {
        return response($seoManager->readSitemap("sitemap-{$file}.xml"), 200, ['Content-Type' => 'application/xml']);
    }

    public function robots(SeoManager $seoManager)
    {
        return response($seoManager->settings()->robots_txt_content ?: $seoManager->defaultRobotsTxt(), 200, ['Content-Type' => 'text/plain']);
    }
}
