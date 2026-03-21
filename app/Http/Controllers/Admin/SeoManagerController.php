<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\SeoManager;
use Illuminate\Http\Request;

class SeoManagerController extends Controller
{
    public function index(SeoManager $seoManager)
    {
        return view('admin.seo.dashboard', [
            'summary' => $seoManager->dashboardSummary(),
            'settings' => $seoManager->settings(),
            'sitemapUrls' => [
                'index' => url('/sitemap.xml'),
                'pages' => url('/sitemap-pages.xml'),
                'visa' => url('/sitemap-visa-destinations.xml'),
                'destinations' => url('/sitemap-destinations.xml'),
                'blog' => url('/sitemap-blog-posts.xml'),
                'marketing' => url('/sitemap-marketing-pages.xml'),
                'images' => url('/sitemap-images.xml'),
            ],
        ]);
    }

    public function settings(SeoManager $seoManager)
    {
        return view('admin.seo.settings', [
            'settings' => $seoManager->settings(),
        ]);
    }

    public function updateSettings(Request $request, SeoManager $seoManager)
    {
        $settings = $seoManager->settings();

        $data = $request->validate([
            'robots_txt_content' => ['nullable', 'string'],
            'search_console_property' => ['nullable', 'string', 'max:255'],
            'search_console_notes' => ['nullable', 'string'],
            'schema_organization_name' => ['nullable', 'string', 'max:255'],
            'schema_organization_logo' => ['nullable', 'string', 'max:255'],
            'merchant_center_verification_code' => ['nullable', 'string', 'max:255'],
            'merchant_center_notes' => ['nullable', 'string'],
            'default_robots_meta' => ['nullable', 'string', 'max:255'],
        ]);

        $settings->update([
            'sitemap_include_pages' => $request->boolean('sitemap_include_pages', true),
            'sitemap_include_visa_destinations' => $request->boolean('sitemap_include_visa_destinations', true),
            'sitemap_include_destinations' => $request->boolean('sitemap_include_destinations', true),
            'sitemap_include_blog_posts' => $request->boolean('sitemap_include_blog_posts', true),
            'sitemap_include_marketing_pages' => $request->boolean('sitemap_include_marketing_pages', true),
            'sitemap_include_images' => $request->boolean('sitemap_include_images', true),
            'robots_txt_content' => $data['robots_txt_content'] ?? $seoManager->defaultRobotsTxt(),
            'search_console_property' => $data['search_console_property'] ?? null,
            'search_console_notes' => $data['search_console_notes'] ?? null,
            'schema_organization_enabled' => $request->boolean('schema_organization_enabled', true),
            'schema_organization_name' => $data['schema_organization_name'] ?? null,
            'schema_organization_logo' => $data['schema_organization_logo'] ?? null,
            'schema_local_business_enabled' => $request->boolean('schema_local_business_enabled'),
            'schema_breadcrumb_enabled' => $request->boolean('schema_breadcrumb_enabled', true),
            'schema_faq_enabled' => $request->boolean('schema_faq_enabled', true),
            'schema_article_enabled' => $request->boolean('schema_article_enabled', true),
            'schema_destination_enabled' => $request->boolean('schema_destination_enabled', true),
            'merchant_center_enabled' => $request->boolean('merchant_center_enabled'),
            'merchant_center_verification_code' => $data['merchant_center_verification_code'] ?? null,
            'merchant_center_notes' => $data['merchant_center_notes'] ?? null,
            'default_robots_meta' => $data['default_robots_meta'] ?? 'index,follow',
        ]);

        return back()->with('success', __('admin.seo_settings_updated'));
    }

    public function regenerateSitemap(SeoManager $seoManager)
    {
        $seoManager->regenerateSitemaps();

        return back()->with('success', __('admin.seo_sitemap_regenerated'));
    }
}
