<?php

namespace App\Providers;

use App\Models\MenuItem;
use App\Models\Setting;
use App\Support\SeoManager;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFive();

        view()->composer('*', function ($view) {
            $settings = Setting::query()->first();
            $seoManager = app(SeoManager::class);
            $headerMenu = MenuItem::query()
                ->where('location', 'header')
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->with(['children' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
                ->orderBy('sort_order')
                ->get();
            $footerMenu = MenuItem::query()
                ->where('location', 'footer')
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->with(['children' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
                ->orderBy('sort_order')
                ->get();

            $view->with([
                'siteSettings' => $settings,
                'seoSettings' => class_exists(\App\Models\SeoSetting::class) ? $seoManager->settings() : null,
                'seoMetaData' => $seoManager->resolveForRequest(request()),
                'headerMenuItems' => $headerMenu,
                'footerMenuItems' => $footerMenu,
            ]);
        });
    }
}
