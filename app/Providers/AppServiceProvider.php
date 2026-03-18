<?php

namespace App\Providers;

use App\Models\MenuItem;
use App\Models\Setting;
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
                'headerMenuItems' => $headerMenu,
                'footerMenuItems' => $footerMenu,
            ]);
        });
    }
}
