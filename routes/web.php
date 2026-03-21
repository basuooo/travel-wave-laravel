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
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/search', [AdminSearchController::class, 'index'])->name('search');
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
        Route::get('/seo', [SeoManagerController::class, 'index'])->name('seo.dashboard');
        Route::get('/seo/settings', [SeoManagerController::class, 'settings'])->name('seo.settings');
        Route::put('/seo/settings', [SeoManagerController::class, 'updateSettings'])->name('seo.settings.update');
        Route::post('/seo/sitemap/regenerate', [SeoManagerController::class, 'regenerateSitemap'])->name('seo.sitemap.regenerate');
        Route::get('/seo/meta', [SeoMetaController::class, 'index'])->name('seo.meta.index');
        Route::get('/seo/meta/{targetType}/{targetId}/edit', [SeoMetaController::class, 'edit'])->name('seo.meta.edit');
        Route::put('/seo/meta/{targetType}/{targetId}', [SeoMetaController::class, 'update'])->name('seo.meta.update');
        Route::resource('/seo/redirects', SeoRedirectController::class)->names('seo.redirects')->parameters(['redirects' => 'redirect']);
        Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
        Route::get('/pages/{page:key}/edit', [PageController::class, 'edit'])->name('pages.edit');
        Route::put('/pages/{page:key}', [PageController::class, 'update'])->name('pages.update');
        Route::resource('visa-categories', VisaCategoryController::class);
        Route::resource('visa-countries', VisaCountryController::class);
        Route::resource('destinations', DestinationController::class);
        Route::resource('blog-categories', BlogCategoryController::class);
        Route::resource('blog-posts', BlogPostController::class);
        Route::resource('testimonials', TestimonialController::class);
        Route::resource('menu-items', MenuItemController::class);
        Route::post('/forms/{form}/duplicate', [LeadFormController::class, 'duplicate'])->name('forms.duplicate');
        Route::get('/forms/submissions', [InquiryController::class, 'index'])->name('forms.submissions');
        Route::resource('forms', LeadFormController::class);
        Route::post('/marketing-landing-pages/{marketing_landing_page}/duplicate', [MarketingLandingPageController::class, 'duplicate'])->name('marketing-landing-pages.duplicate');
        Route::resource('marketing-landing-pages', MarketingLandingPageController::class);
        Route::post('/map-sections/{map_section}/duplicate', [MapSectionController::class, 'duplicate'])->name('map-sections.duplicate');
        Route::resource('map-sections', MapSectionController::class);
        Route::post('/tracking-integrations/{tracking_integration}/duplicate', [TrackingIntegrationController::class, 'duplicate'])->name('tracking-integrations.duplicate');
        Route::resource('tracking-integrations', TrackingIntegrationController::class);
        Route::put('/hero-slides/settings', [HeroSlideController::class, 'updateSettings'])->name('hero-slides.settings');
        Route::resource('hero-slides', HeroSlideController::class);
        Route::put('/home-country-strip/settings', [HomeCountryStripController::class, 'updateSettings'])->name('home-country-strip.settings');
        Route::resource('home-country-strip', HomeCountryStripController::class)->parameters([
            'home-country-strip' => 'home_country_strip',
        ]);
        Route::get('/inquiries', [InquiryController::class, 'index'])->name('inquiries.index');
        Route::get('/inquiries/{inquiry}', [InquiryController::class, 'show'])->name('inquiries.show');
        Route::put('/inquiries/{inquiry}', [InquiryController::class, 'update'])->name('inquiries.update');
    });
});
