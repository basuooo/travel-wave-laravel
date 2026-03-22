<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AdminSearchController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DestinationController;
use App\Http\Controllers\Admin\FooterSettingController;
use App\Http\Controllers\Admin\FloatingWhatsappSettingController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\HeaderSettingController;
use App\Http\Controllers\Admin\HomeCountryStripController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\LeadFormController;
use App\Http\Controllers\Admin\MarketingLandingPageController;
use App\Http\Controllers\Admin\MediaLibraryController;
use App\Http\Controllers\Admin\MapSectionController;
use App\Http\Controllers\Admin\MetaConversionApiSettingController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\SeoManagerController;
use App\Http\Controllers\Admin\SeoMetaController;
use App\Http\Controllers\Admin\SeoRedirectController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\TrackingIntegrationController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VisaCategoryController;
use App\Http\Controllers\Admin\VisaCountryController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\SeoPublicController;
use Illuminate\Support\Facades\Route;

Route::get('locale/{locale}', [FrontendController::class, 'switchLocale'])->name('locale.switch');

Route::get('/', [FrontendController::class, 'home'])->name('home');
Route::get('/visas', [FrontendController::class, 'visaIndex'])->name('visas.index');
Route::get('/visas/{category:slug}', [FrontendController::class, 'visaCategory'])->name('visas.category');
Route::get('/visa-country/{country:slug}', [FrontendController::class, 'visaCountry'])->name('visas.country');
Route::get('/domestic-tourism', [FrontendController::class, 'domesticIndex'])->name('destinations.index');
Route::get('/domestic-tourism/{destination:slug}', [FrontendController::class, 'destinationShow'])->name('destinations.show');
Route::get('/flights', [FrontendController::class, 'flights'])->name('flights');
Route::get('/hotels', [FrontendController::class, 'hotels'])->name('hotels');
Route::get('/about', [FrontendController::class, 'about'])->name('about');
Route::get('/blog', [FrontendController::class, 'blogIndex'])->name('blog.index');
Route::get('/blog/{post:slug}', [FrontendController::class, 'blogShow'])->name('blog.show');
Route::get('/contact', [FrontendController::class, 'contact'])->name('contact');
Route::get('/pages/{page:slug}', [FrontendController::class, 'pageShow'])->name('pages.show');
Route::get('/campaigns/{landingPage:slug}', [FrontendController::class, 'marketingLandingPage'])->name('marketing.landing-pages.show');
Route::post('/campaigns/{landingPage:slug}/events', [FrontendController::class, 'trackMarketingLandingPageEvent'])->name('marketing.landing-pages.events.store');
Route::post('/inquiries', [FrontendController::class, 'storeInquiry'])->name('inquiries.store');
Route::post('/tracking/meta/events', [FrontendController::class, 'trackMetaEvent'])->name('tracking.meta.events.store');
Route::get('/robots.txt', [SeoPublicController::class, 'robots'])->name('seo.robots');
Route::get('/sitemap.xml', [SeoPublicController::class, 'sitemapIndex'])->name('seo.sitemap.index');
Route::get('/sitemap-{file}.xml', [SeoPublicController::class, 'sitemapFile'])->where('file', '[A-Za-z0-9\-]+')->name('seo.sitemap.file');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', [DashboardController::class, 'index'])->middleware('permission:dashboard.access')->name('dashboard');
        Route::get('/search', [AdminSearchController::class, 'index'])->middleware('permission:dashboard.access')->name('search');

        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::resource('permissions', PermissionController::class)->except(['show']);

        Route::middleware('permission:settings.manage')->group(function () {
            Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
            Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
            Route::get('/header-settings', [HeaderSettingController::class, 'edit'])->name('header-settings.edit');
            Route::put('/header-settings', [HeaderSettingController::class, 'update'])->name('header-settings.update');
            Route::get('/footer-settings', [FooterSettingController::class, 'edit'])->name('footer-settings.edit');
            Route::put('/footer-settings', [FooterSettingController::class, 'update'])->name('footer-settings.update');
            Route::get('/floating-whatsapp-settings', [FloatingWhatsappSettingController::class, 'edit'])->name('floating-whatsapp-settings.edit');
            Route::put('/floating-whatsapp-settings', [FloatingWhatsappSettingController::class, 'update'])->name('floating-whatsapp-settings.update');
            Route::get('/meta-conversion-api-settings', [MetaConversionApiSettingController::class, 'edit'])->name('meta-conversion-api-settings.edit');
            Route::put('/meta-conversion-api-settings', [MetaConversionApiSettingController::class, 'update'])->name('meta-conversion-api-settings.update');
        });

        Route::middleware('permission:seo.manage')->group(function () {
            Route::get('/seo', [SeoManagerController::class, 'index'])->name('seo.dashboard');
            Route::get('/seo/settings', [SeoManagerController::class, 'settings'])->name('seo.settings');
            Route::put('/seo/settings', [SeoManagerController::class, 'updateSettings'])->name('seo.settings.update');
            Route::post('/seo/sitemap/regenerate', [SeoManagerController::class, 'regenerateSitemap'])->middleware('permission:seo.sitemap.manage')->name('seo.sitemap.regenerate');
            Route::get('/seo/meta', [SeoMetaController::class, 'index'])->middleware('permission:seo.meta.manage')->name('seo.meta.index');
            Route::get('/seo/meta/{targetType}/{targetId}/edit', [SeoMetaController::class, 'edit'])->middleware('permission:seo.meta.manage')->name('seo.meta.edit');
            Route::put('/seo/meta/{targetType}/{targetId}', [SeoMetaController::class, 'update'])->middleware('permission:seo.meta.manage')->name('seo.meta.update');
            Route::resource('/seo/redirects', SeoRedirectController::class)->middleware('permission:seo.redirects.manage')->names('seo.redirects')->parameters(['redirects' => 'redirect']);
        });

        Route::middleware('permission:pages.view')->group(function () {
            Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
            Route::get('/pages/trash', [PageController::class, 'trash'])->middleware('permission:pages.delete')->name('pages.trash');
            Route::get('/pages/create', [PageController::class, 'create'])->middleware('permission:pages.create')->name('pages.create');
            Route::post('/pages', [PageController::class, 'store'])->middleware('permission:pages.create')->name('pages.store');
            Route::post('/pages/{page:key}/duplicate', [PageController::class, 'duplicate'])->middleware('permission:pages.create')->name('pages.duplicate');
            Route::get('/pages/{page:key}/edit', [PageController::class, 'edit'])->middleware('permission:pages.edit')->name('pages.edit');
            Route::put('/pages/{page:key}', [PageController::class, 'update'])->middleware('permission:pages.edit')->name('pages.update');
            Route::delete('/pages/{page:key}', [PageController::class, 'destroy'])->middleware('permission:pages.delete')->name('pages.destroy');
            Route::post('/pages/{page}/restore', [PageController::class, 'restore'])->middleware('permission:pages.delete')->name('pages.restore');
            Route::delete('/pages/{page}/force-delete', [PageController::class, 'forceDestroy'])->middleware('permission:pages.delete')->name('pages.force-destroy');
        });

        Route::middleware('permission:media.manage')->group(function () {
            Route::get('/media-library', [MediaLibraryController::class, 'index'])->name('media-library.index');
            Route::post('/media-library', [MediaLibraryController::class, 'store'])->name('media-library.store');
            Route::get('/media-library/library', [MediaLibraryController::class, 'library'])->name('media-library.library');
            Route::get('/media-library/{media_library}/preview', [MediaLibraryController::class, 'preview'])->name('media-library.preview');
            Route::put('/media-library/{media_library}', [MediaLibraryController::class, 'update'])->name('media-library.update');
            Route::delete('/media-library/{media_library}', [MediaLibraryController::class, 'destroy'])->name('media-library.destroy');
        });

        Route::middleware('permission:destinations.manage')->group(function () {
            Route::get('/visa-categories/trash', [VisaCategoryController::class, 'trash'])->name('visa-categories.trash');
            Route::post('/visa-categories/{visa_category}/duplicate', [VisaCategoryController::class, 'duplicate'])->name('visa-categories.duplicate');
            Route::post('/visa-categories/{visa_category}/restore', [VisaCategoryController::class, 'restore'])->name('visa-categories.restore');
            Route::delete('/visa-categories/{visa_category}/force-delete', [VisaCategoryController::class, 'forceDestroy'])->name('visa-categories.force-destroy');
            Route::resource('visa-categories', VisaCategoryController::class);
            Route::get('/visa-countries/trash', [VisaCountryController::class, 'trash'])->name('visa-countries.trash');
            Route::post('/visa-countries/{visa_country}/duplicate', [VisaCountryController::class, 'duplicate'])->name('visa-countries.duplicate');
            Route::post('/visa-countries/{visa_country}/restore', [VisaCountryController::class, 'restore'])->name('visa-countries.restore');
            Route::delete('/visa-countries/{visa_country}/force-delete', [VisaCountryController::class, 'forceDestroy'])->name('visa-countries.force-destroy');
            Route::resource('visa-countries', VisaCountryController::class);
            Route::get('/destinations/trash', [DestinationController::class, 'trash'])->name('destinations.trash');
            Route::post('/destinations/{destination}/duplicate', [DestinationController::class, 'duplicate'])->name('destinations.duplicate');
            Route::post('/destinations/{destination}/restore', [DestinationController::class, 'restore'])->name('destinations.restore');
            Route::delete('/destinations/{destination}/force-delete', [DestinationController::class, 'forceDestroy'])->name('destinations.force-destroy');
            Route::resource('destinations', DestinationController::class);
        });

        Route::middleware('permission:blog.manage')->group(function () {
            Route::get('/blog-categories/trash', [BlogCategoryController::class, 'trash'])->name('blog-categories.trash');
            Route::post('/blog-categories/{blog_category}/duplicate', [BlogCategoryController::class, 'duplicate'])->name('blog-categories.duplicate');
            Route::post('/blog-categories/{blog_category}/restore', [BlogCategoryController::class, 'restore'])->name('blog-categories.restore');
            Route::delete('/blog-categories/{blog_category}/force-delete', [BlogCategoryController::class, 'forceDestroy'])->name('blog-categories.force-destroy');
            Route::resource('blog-categories', BlogCategoryController::class);
            Route::get('/blog-posts/trash', [BlogPostController::class, 'trash'])->name('blog-posts.trash');
            Route::post('/blog-posts/{blog_post}/duplicate', [BlogPostController::class, 'duplicate'])->name('blog-posts.duplicate');
            Route::post('/blog-posts/{blog_post}/restore', [BlogPostController::class, 'restore'])->name('blog-posts.restore');
            Route::delete('/blog-posts/{blog_post}/force-delete', [BlogPostController::class, 'forceDestroy'])->name('blog-posts.force-destroy');
            Route::resource('blog-posts', BlogPostController::class);
        });

        Route::middleware('permission:testimonials.manage')->group(function () {
            Route::get('/testimonials/trash', [TestimonialController::class, 'trash'])->name('testimonials.trash');
            Route::post('/testimonials/{testimonial}/duplicate', [TestimonialController::class, 'duplicate'])->name('testimonials.duplicate');
            Route::post('/testimonials/{testimonial}/restore', [TestimonialController::class, 'restore'])->name('testimonials.restore');
            Route::delete('/testimonials/{testimonial}/force-delete', [TestimonialController::class, 'forceDestroy'])->name('testimonials.force-destroy');
            Route::resource('testimonials', TestimonialController::class);
        });

        Route::middleware('permission:menu.manage')->group(function () {
            Route::get('/menu-items/trash', [MenuItemController::class, 'trash'])->name('menu-items.trash');
            Route::post('/menu-items/{menu_item}/duplicate', [MenuItemController::class, 'duplicate'])->name('menu-items.duplicate');
            Route::post('/menu-items/{menu_item}/restore', [MenuItemController::class, 'restore'])->name('menu-items.restore');
            Route::delete('/menu-items/{menu_item}/force-delete', [MenuItemController::class, 'forceDestroy'])->name('menu-items.force-destroy');
            Route::resource('menu-items', MenuItemController::class);
        });

        Route::middleware('permission:forms.manage')->group(function () {
            Route::post('/forms/{form}/duplicate', [LeadFormController::class, 'duplicate'])->name('forms.duplicate');
            Route::resource('forms', LeadFormController::class);
        });
        Route::get('/forms/submissions', [InquiryController::class, 'index'])->middleware('permission:forms.submissions.view')->name('forms.submissions');

        Route::middleware('permission:marketing.manage')->group(function () {
            Route::post('/marketing-landing-pages/{marketing_landing_page}/duplicate', [MarketingLandingPageController::class, 'duplicate'])->name('marketing-landing-pages.duplicate');
            Route::resource('marketing-landing-pages', MarketingLandingPageController::class);
        });

        Route::middleware('permission:maps.manage')->group(function () {
            Route::post('/map-sections/{map_section}/duplicate', [MapSectionController::class, 'duplicate'])->name('map-sections.duplicate');
            Route::resource('map-sections', MapSectionController::class);
        });

        Route::middleware('permission:tracking.manage')->group(function () {
            Route::post('/tracking-integrations/{tracking_integration}/duplicate', [TrackingIntegrationController::class, 'duplicate'])->name('tracking-integrations.duplicate');
            Route::resource('tracking-integrations', TrackingIntegrationController::class);
        });

        Route::middleware('permission:pages.edit')->group(function () {
            Route::put('/hero-slides/settings', [HeroSlideController::class, 'updateSettings'])->name('hero-slides.settings');
            Route::resource('hero-slides', HeroSlideController::class);
            Route::put('/home-country-strip/settings', [HomeCountryStripController::class, 'updateSettings'])->name('home-country-strip.settings');
            Route::get('/home-country-strip/trash', [HomeCountryStripController::class, 'trash'])->name('home-country-strip.trash');
            Route::post('/home-country-strip/{home_country_strip}/duplicate', [HomeCountryStripController::class, 'duplicate'])->name('home-country-strip.duplicate');
            Route::post('/home-country-strip/{home_country_strip}/restore', [HomeCountryStripController::class, 'restore'])->name('home-country-strip.restore');
            Route::delete('/home-country-strip/{home_country_strip}/force-delete', [HomeCountryStripController::class, 'forceDestroy'])->name('home-country-strip.force-destroy');
            Route::resource('home-country-strip', HomeCountryStripController::class)->parameters([
                'home-country-strip' => 'home_country_strip',
            ]);
        });

        Route::middleware('permission:leads.view')->group(function () {
            Route::get('/inquiries', [InquiryController::class, 'index'])->name('inquiries.index');
            Route::get('/inquiries/{inquiry}', [InquiryController::class, 'show'])->name('inquiries.show');
        });
        Route::put('/inquiries/{inquiry}', [InquiryController::class, 'update'])->middleware('permission:leads.edit')->name('inquiries.update');
    });
});
