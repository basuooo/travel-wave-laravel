<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DestinationController;
use App\Http\Controllers\Admin\FooterSettingController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\HeaderSettingController;
use App\Http\Controllers\Admin\HomeCountryStripController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\VisaCategoryController;
use App\Http\Controllers\Admin\VisaCountryController;
use App\Http\Controllers\FrontendController;
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
Route::post('/inquiries', [FrontendController::class, 'storeInquiry'])->name('inquiries.store');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::get('/header-settings', [HeaderSettingController::class, 'edit'])->name('header-settings.edit');
        Route::put('/header-settings', [HeaderSettingController::class, 'update'])->name('header-settings.update');
        Route::get('/footer-settings', [FooterSettingController::class, 'edit'])->name('footer-settings.edit');
        Route::put('/footer-settings', [FooterSettingController::class, 'update'])->name('footer-settings.update');
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
