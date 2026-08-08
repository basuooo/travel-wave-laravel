<?php

namespace App\Providers;

use App\Models\MenuItem;
use App\Models\Page;
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

        $this->ensureSchemaAndDataAutoSync();

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

    protected function ensureSchemaAndDataAutoSync(): void
    {
        try {
            if ($this->safeHasTable('menu_items')) {
                Schema::table('menu_items', function ($table) {
                    if (! Schema::hasColumn('menu_items', 'type')) {
                        $table->string('type')->default('custom')->nullable();
                    }
                    if (! Schema::hasColumn('menu_items', 'page_id')) {
                        $table->unsignedBigInteger('page_id')->nullable();
                    }
                    if (! Schema::hasColumn('menu_items', 'icon')) {
                        $table->string('icon')->nullable();
                    }
                });
            }

            if ($this->safeHasTable('pages')) {
                if (! Page::where('key', 'umrah')->exists() || ! Page::where('key', 'hajj')->exists()) {
                    (new \Database\Seeders\UmrahHajjPagesSeeder())->run();
                }
            }

            if ($this->safeHasTable('menu_items')) {
                if (MenuItem::where('location', 'header')->count() === 0) {
                    (new \Database\Seeders\MainMenuSeeder())->run();
                }
            }
        } catch (Throwable $e) {
            report($e);
        }
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
            ->with(['page', 'children' => fn ($q) => $q->where('is_active', true)->with('page')->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();
    }
}
