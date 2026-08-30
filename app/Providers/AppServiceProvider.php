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

        // Purge compiled view cache files dynamically to force instant view compilation on live server
        try {
            $viewsPath = storage_path('framework/views');
            if (is_dir($viewsPath)) {
                foreach (glob($viewsPath . '/*.php') as $file) {
                    @unlink($file);
                }
            }
        } catch (\Throwable $e) {
            // Ignore if storage permissions restrict deletion
        }

        $this->ensureSchemaOnly();
        $this->applyDynamicMailConfig();

        // ⚡ Interactive Funnels — auto-create tables on first access (no artisan migrate needed)
        try {
            \App\Services\Funnels\FunnelAutoMigrationService::ensureTablesExist();
        } catch (\Throwable $e) {
            report($e);
        }

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

    protected function ensureSchemaOnly(): void
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

    protected function applyDynamicMailConfig(): void
    {
        try {
            if (! $this->safeHasTable('settings')) {
                return;
            }

            $columns = Schema::getColumnListing('settings');
            if (! in_array('mail_host', $columns, true)) {
                return;
            }

            $setting = Setting::query()->first();
            if (! $setting) {
                return;
            }

            if (filled($setting->mail_host)) {
                config([
                    'mail.default' => $setting->mail_mailer ?: env('MAIL_MAILER', 'smtp'),
                    'mail.mailers.smtp.transport' => 'smtp',
                    'mail.mailers.smtp.host' => $setting->mail_host,
                    'mail.mailers.smtp.port' => (int) ($setting->mail_port ?: env('MAIL_PORT', 587)),
                    'mail.mailers.smtp.username' => $setting->mail_username,
                    'mail.mailers.smtp.password' => $setting->mail_password,
                    'mail.mailers.smtp.encryption' => $setting->mail_encryption ?: env('MAIL_ENCRYPTION', 'tls'),
                ]);
            }

            if (filled($setting->mail_from_address)) {
                config([
                    'mail.from.address' => $setting->mail_from_address,
                    'mail.from.name' => $setting->mail_from_name ?: config('app.name', 'Travel Wave'),
                ]);
            }
        } catch (Throwable $e) {
            report($e);
        }
    }
}
