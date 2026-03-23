<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\ChatbotInteraction;
use App\Models\Destination;
use App\Models\Inquiry;
use App\Models\LeadForm;
use App\Models\MarketingLandingPage;
use App\Models\MapSection;
use App\Models\MediaAsset;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Permission;
use App\Models\Role;
use App\Models\TrackingIntegration;
use App\Models\User;
use App\Models\VisaCategory;
use App\Models\VisaCountry;
use App\Support\CrmLeadAccess;
use Illuminate\Http\Request;

class AdminSearchController extends Controller
{
    public function index(Request $request)
    {
        $query = trim((string) $request->query('q', ''));

        return view('admin.search.index', [
            'query' => $query,
            'results' => $query === '' ? [] : $this->search($query),
        ]);
    }

    protected function search(string $query): array
    {
        $like = '%' . $query . '%';

        return array_filter([
            __('admin.pages') => $this->pages($like),
            __('admin.visa_destinations') => $this->visaCountries($like),
            __('admin.destinations') => $this->destinations($like),
            __('admin.users_management') => $this->users($like),
            __('admin.roles_management') => $this->roles($like),
            __('admin.permissions_management') => $this->permissions($like),
            __('admin.forms_manager') => $this->forms($like),
            __('admin.crm') => $this->crmLeads($like),
            __('admin.marketing_manager') => $this->marketingLandingPages($like),
            __('admin.media_library') => $this->mediaAssets($like),
            __('admin.maps_manager') => $this->maps($like),
            __('admin.tracking_manager') => $this->tracking($like),
            __('admin.chatbot_manager') => $this->chatbot($query),
            __('admin.seo_manager') => $this->seo($query),
            __('admin.blog_posts') => $this->blogPosts($like),
            __('admin.navigation') => $this->menuItems($like),
            __('admin.settings') => $this->settingsShortcuts($query),
            __('admin.visa_categories') => $this->visaCategories($like),
        ], fn ($items) => ! empty($items));
    }

    protected function pages(string $like): array
    {
        return Page::query()
            ->where(fn ($query) => $query
                ->where('key', 'like', $like)
                ->orWhere('slug', 'like', $like)
                ->orWhere('title_en', 'like', $like)
                ->orWhere('title_ar', 'like', $like))
            ->limit(8)
            ->get()
            ->map(fn (Page $page) => [
                'title' => $page->title_en . ' / ' . $page->title_ar,
                'meta' => $page->slug,
                'url' => route('admin.pages.edit', $page),
            ])->all();
    }

    protected function visaCountries(string $like): array
    {
        return VisaCountry::query()
            ->where(fn ($query) => $query
                ->where('slug', 'like', $like)
                ->orWhere('name_en', 'like', $like)
                ->orWhere('name_ar', 'like', $like))
            ->limit(8)
            ->get()
            ->map(fn (VisaCountry $item) => [
                'title' => $item->name_en . ' / ' . $item->name_ar,
                'meta' => $item->slug,
                'url' => route('admin.visa-countries.edit', $item),
            ])->all();
    }

    protected function destinations(string $like): array
    {
        return Destination::query()
            ->where(fn ($query) => $query
                ->where('slug', 'like', $like)
                ->orWhere('title_en', 'like', $like)
                ->orWhere('title_ar', 'like', $like))
            ->limit(8)
            ->get()
            ->map(fn (Destination $item) => [
                'title' => $item->title_en . ' / ' . $item->title_ar,
                'meta' => $item->slug,
                'url' => route('admin.destinations.edit', $item),
            ])->all();
    }

    protected function forms(string $like): array
    {
        return LeadForm::query()
            ->where(fn ($query) => $query
                ->where('name', 'like', $like)
                ->orWhere('slug', 'like', $like))
            ->limit(8)
            ->get()
            ->map(fn (LeadForm $item) => [
                'title' => $item->name,
                'meta' => $item->slug,
                'url' => route('admin.forms.edit', $item),
            ])->all();
    }

    protected function crmLeads(string $like): array
    {
        if (! auth()->user()?->hasPermission('leads.view')) {
            return [];
        }

        return CrmLeadAccess::applyVisibilityScope(Inquiry::query(), auth()->user())
            ->where(fn ($query) => $query
                ->where('full_name', 'like', $like)
                ->orWhere('phone', 'like', $like)
                ->orWhere('whatsapp_number', 'like', $like)
                ->orWhere('country', 'like', $like)
                ->orWhere('campaign_name', 'like', $like)
                ->orWhere('lead_source', 'like', $like))
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn (Inquiry $item) => [
                'title' => $item->full_name ?: __('admin.crm_leads'),
                'meta' => $item->phone ?: ($item->country ?: ($item->campaign_name ?: $item->lead_source)),
                'url' => route('admin.crm.leads.show', $item),
            ])->all();
    }

    protected function users(string $like): array
    {
        if (! auth()->user()?->hasPermission('users.view')) {
            return [];
        }

        return User::query()
            ->where(fn ($query) => $query
                ->where('name', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhere('phone', 'like', $like))
            ->limit(8)
            ->get()
            ->map(fn (User $item) => [
                'title' => $item->name,
                'meta' => $item->email,
                'url' => route('admin.users.edit', $item),
            ])->all();
    }

    protected function roles(string $like): array
    {
        if (! auth()->user()?->hasPermission('roles.manage')) {
            return [];
        }

        return Role::query()
            ->where(fn ($query) => $query
                ->where('name', 'like', $like)
                ->orWhere('slug', 'like', $like)
                ->orWhere('description', 'like', $like))
            ->limit(8)
            ->get()
            ->map(fn (Role $item) => [
                'title' => $item->name,
                'meta' => $item->slug,
                'url' => route('admin.roles.edit', $item),
            ])->all();
    }

    protected function permissions(string $like): array
    {
        if (! auth()->user()?->hasPermission('permissions.manage')) {
            return [];
        }

        return Permission::query()
            ->where(fn ($query) => $query
                ->where('name', 'like', $like)
                ->orWhere('slug', 'like', $like)
                ->orWhere('module', 'like', $like))
            ->limit(8)
            ->get()
            ->map(fn (Permission $item) => [
                'title' => $item->name,
                'meta' => $item->slug,
                'url' => route('admin.permissions.edit', $item),
            ])->all();
    }

    protected function maps(string $like): array
    {
        return MapSection::query()
            ->where(fn ($query) => $query
                ->where('name', 'like', $like)
                ->orWhere('slug', 'like', $like)
                ->orWhere('title_en', 'like', $like)
                ->orWhere('title_ar', 'like', $like))
            ->limit(8)
            ->get()
            ->map(fn (MapSection $item) => [
                'title' => $item->name,
                'meta' => $item->slug,
                'url' => route('admin.map-sections.edit', $item),
            ])->all();
    }

    protected function mediaAssets(string $like): array
    {
        if (! auth()->user()?->hasPermission('media.manage')) {
            return [];
        }

        return MediaAsset::query()
            ->where(fn ($query) => $query
                ->where('title', 'like', $like)
                ->orWhere('file_name', 'like', $like)
                ->orWhere('path', 'like', $like))
            ->limit(8)
            ->get()
            ->map(fn (MediaAsset $item) => [
                'title' => $item->title ?: $item->file_name,
                'meta' => $item->path,
                'url' => route('admin.media-library.index', ['q' => $item->file_name]),
            ])->all();
    }

    protected function marketingLandingPages(string $like): array
    {
        return MarketingLandingPage::query()
            ->where(fn ($query) => $query
                ->where('internal_name', 'like', $like)
                ->orWhere('slug', 'like', $like)
                ->orWhere('campaign_name', 'like', $like)
                ->orWhere('title_en', 'like', $like)
                ->orWhere('title_ar', 'like', $like))
            ->limit(8)
            ->get()
            ->map(fn (MarketingLandingPage $item) => [
                'title' => $item->internal_name,
                'meta' => $item->campaign_name ?: $item->slug,
                'url' => route('admin.marketing-landing-pages.edit', $item),
            ])->all();
    }

    protected function tracking(string $like): array
    {
        return TrackingIntegration::query()
            ->where(fn ($query) => $query
                ->where('name', 'like', $like)
                ->orWhere('slug', 'like', $like)
                ->orWhere('platform', 'like', $like)
                ->orWhere('tracking_code', 'like', $like))
            ->limit(8)
            ->get()
            ->map(fn (TrackingIntegration $item) => [
                'title' => $item->name,
                'meta' => $item->platform ?: $item->integration_type,
                'url' => route('admin.tracking-integrations.edit', $item),
            ])->all();
    }

    protected function blogPosts(string $like): array
    {
        return BlogPost::query()
            ->where(fn ($query) => $query
                ->where('slug', 'like', $like)
                ->orWhere('title_en', 'like', $like)
                ->orWhere('title_ar', 'like', $like))
            ->limit(8)
            ->get()
            ->map(fn (BlogPost $item) => [
                'title' => $item->title_en . ' / ' . $item->title_ar,
                'meta' => $item->slug,
                'url' => route('admin.blog-posts.edit', $item),
            ])->all();
    }

    protected function menuItems(string $like): array
    {
        return MenuItem::query()
            ->where(fn ($query) => $query
                ->where('title_en', 'like', $like)
                ->orWhere('title_ar', 'like', $like)
                ->orWhere('route_name', 'like', $like)
                ->orWhere('url', 'like', $like))
            ->limit(8)
            ->get()
            ->map(fn (MenuItem $item) => [
                'title' => $item->title_en . ' / ' . $item->title_ar,
                'meta' => $item->route_name ?: $item->url,
                'url' => route('admin.menu-items.edit', $item),
            ])->all();
    }

    protected function visaCategories(string $like): array
    {
        return VisaCategory::query()
            ->where(fn ($query) => $query
                ->where('slug', 'like', $like)
                ->orWhere('name_en', 'like', $like)
                ->orWhere('name_ar', 'like', $like))
            ->limit(8)
            ->get()
            ->map(fn (VisaCategory $item) => [
                'title' => $item->name_en . ' / ' . $item->name_ar,
                'meta' => $item->slug,
                'url' => route('admin.visa-categories.edit', $item),
            ])->all();
    }

    protected function settingsShortcuts(string $query): array
    {
        $items = collect([
            ['title' => __('admin.brand_settings'), 'keywords' => ['settings', 'brand', 'branding', 'الاعدادات', 'العلامة'], 'url' => route('admin.settings.edit')],
            ['title' => __('admin.header_settings'), 'keywords' => ['header', 'الهيدر'], 'url' => route('admin.header-settings.edit')],
            ['title' => __('admin.footer_settings'), 'keywords' => ['footer', 'الفوتر'], 'url' => route('admin.footer-settings.edit')],
            ['title' => __('admin.floating_whatsapp'), 'keywords' => ['whatsapp', 'واتساب'], 'url' => route('admin.floating-whatsapp-settings.edit')],
            ['title' => __('admin.hero_slider'), 'keywords' => ['hero', 'slider', 'banner', 'البانر'], 'url' => route('admin.hero-slides.index')],
            ['title' => __('admin.homepage_country_strip'), 'keywords' => ['countries', 'destinations', 'الوجهات'], 'url' => route('admin.home-country-strip.index')],
            ['title' => __('admin.tracking_manager'), 'keywords' => ['tracking', 'analytics', 'pixel', 'gtm', 'ga4', 'التتبع'], 'url' => route('admin.tracking-integrations.index')],
            ['title' => __('admin.chatbot_manager'), 'keywords' => ['chatbot', 'assistant', 'ai', 'bot', 'روبوت', 'مساعد', 'شات'], 'url' => route('admin.chatbot-settings.edit')],
            ['title' => __('admin.seo_manager'), 'keywords' => ['seo', 'sitemap', 'robots', 'schema', 'redirect', 'سيو', 'خريطة الموقع', 'روبوتس'], 'url' => route('admin.seo.dashboard')],
            ['title' => __('admin.maps_manager'), 'keywords' => ['maps', 'map', 'الخريطة'], 'url' => route('admin.map-sections.index')],
        ]);

        $needle = mb_strtolower($query);

        return $items
            ->filter(function (array $item) use ($needle) {
                return str_contains(mb_strtolower($item['title']), $needle)
                    || collect($item['keywords'])->contains(fn ($keyword) => str_contains(mb_strtolower($keyword), $needle));
            })
            ->map(fn (array $item) => [
                'title' => $item['title'],
                'meta' => __('admin.settings'),
                'url' => $item['url'],
            ])->values()->all();
    }

    protected function seo(string $query): array
    {
        $needle = mb_strtolower($query);

        return collect([
            ['title' => __('admin.seo_manager'), 'meta' => __('admin.seo_dashboard'), 'url' => route('admin.seo.dashboard')],
            ['title' => __('admin.seo_global_settings'), 'meta' => __('admin.settings'), 'url' => route('admin.seo.settings')],
            ['title' => __('admin.seo_meta_manager'), 'meta' => __('admin.seo_meta_manager'), 'url' => route('admin.seo.meta.index')],
            ['title' => __('admin.seo_redirects_manager'), 'meta' => __('admin.seo_redirects_manager'), 'url' => route('admin.seo.redirects.index')],
        ])->filter(fn (array $item) => str_contains(mb_strtolower($item['title']), $needle))
            ->values()
            ->all();
    }

    protected function chatbot(string $query): array
    {
        $needle = mb_strtolower($query);
        $unansweredCount = ChatbotInteraction::query()->where('was_answered', false)->count();

        return collect([
            ['title' => __('admin.chatbot_manager'), 'meta' => __('admin.settings'), 'url' => route('admin.chatbot-settings.edit')],
            ['title' => __('admin.chatbot_logs'), 'meta' => $unansweredCount > 0 ? __('admin.unanswered_questions_count', ['count' => $unansweredCount]) : __('admin.chatbot_manager'), 'url' => route('admin.chatbot-settings.edit') . '#chatbot-logs'],
        ])->filter(fn (array $item) => str_contains(mb_strtolower($item['title']), $needle))
            ->values()
            ->all();
    }
}
