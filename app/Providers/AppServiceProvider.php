<?php

namespace App\Providers;

use App\Models\MenuItem;
use App\Models\Setting;
use App\Support\SeoManager;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Throwable;

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
            $payload = [
                'siteSettings' => null,
                'seoSettings' => null,
                'seoMetaData' => [],
                'headerMenuItems' => collect(),
                'footerMenuItems' => collect(),
                'adminNotifications' => collect(),
                'adminUnreadNotificationsCount' => 0,
            ];

            try {
                $seoManager = app(SeoManager::class);

                if ($this->safeHasTable('settings')) {
                    $payload['siteSettings'] = Setting::query()->first();
                }

                if ($this->safeHasTable('menu_items')) {
                    $payload['headerMenuItems'] = $this->menuItemsForLocation('header');
                    $payload['footerMenuItems'] = $this->menuItemsForLocation('footer');
                }

                $payload['seoSettings'] = $seoManager->settings();
                $payload['seoMetaData'] = $seoManager->resolveForRequest(request());

                if (auth()->check() && $this->safeHasTable('notifications')) {
                    $payload['adminNotifications'] = auth()->user()->notifications()->latest()->limit(6)->get();
                    $payload['adminUnreadNotificationsCount'] = auth()->user()->unreadNotifications()->count();
                }
            } catch (Throwable $exception) {
                report($exception);
            }

            $view->with($payload);
        });
    }

    protected function safeHasTable(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }

    protected function menuItemsForLocation(string $location): Collection
    {
        return MenuItem::query()
            ->where('location', $location)
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->with(['children' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();
    }
}
