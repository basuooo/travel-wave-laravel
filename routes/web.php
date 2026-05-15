<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AdminSearchController;
use App\Http\Controllers\Admin\AccountingController;
use App\Http\Controllers\Admin\AccountingTreasuryController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\AiBotController;
use App\Http\Controllers\Admin\ChatbotSettingController;
use App\Http\Controllers\Admin\ChatbotKnowledgeController;
use App\Http\Controllers\Admin\CrmController;
use App\Http\Controllers\Admin\CrmInformationController;
use App\Http\Controllers\Admin\CrmLeadController;
use App\Http\Controllers\Admin\CrmCustomerController;
use App\Http\Controllers\Admin\CrmDocumentController;
use App\Http\Controllers\Admin\CrmDocumentCategoryController;
use App\Http\Controllers\Admin\CrmTaskController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DestinationController;
use App\Http\Controllers\Admin\FooterSettingController;
use App\Http\Controllers\Admin\FloatingWhatsappSettingController;
use App\Http\Controllers\Admin\GoalsCommissionController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\HeaderSettingController;
use App\Http\Controllers\Admin\HomeCountryStripController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\KpiDashboardController;
use App\Http\Controllers\Admin\KnowledgeBaseCategoryController;
use App\Http\Controllers\Admin\KnowledgeBaseController;
use App\Http\Controllers\Admin\LeadFormController;
use App\Http\Controllers\Admin\MarketingLandingPageController;
use App\Http\Controllers\Admin\MediaLibraryController;
use App\Http\Controllers\Admin\MapSectionController;
use App\Http\Controllers\Admin\MarketingCampaignController;
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
use App\Http\Controllers\Admin\UtmController;
use App\Http\Controllers\Admin\WorkflowAutomationController;
use App\Http\Controllers\Admin\VisaCategoryController;
use App\Http\Controllers\Admin\VisaCountryController;
use App\Http\Controllers\ChatbotController;
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
Route::post('/chatbot/ask', [ChatbotController::class, 'ask'])->name('chatbot.ask');
Route::get('/robots.txt', [SeoPublicController::class, 'robots'])->name('seo.robots');
Route::get('/sitemap.xml', [SeoPublicController::class, 'sitemapIndex'])->name('seo.sitemap.index');
Route::get('/sitemap-{file}.xml', [SeoPublicController::class, 'sitemapFile'])->where('file', '[A-Za-z0-9\-]+')->name('seo.sitemap.file');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    });

    Route::middleware(['auth', 'admin', 'crm.followups.dispatch'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', [DashboardController::class, 'index'])->middleware('permission:dashboard.access')->name('dashboard');
        Route::get('/kpi-dashboard', [KpiDashboardController::class, 'index'])->middleware('permission:reports.view')->name('kpi.dashboard');
        Route::get('/search', [AdminSearchController::class, 'index'])->middleware('permission:dashboard.access')->name('search');
        Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read-all', [AdminNotificationController::class, 'readAll'])->name('notifications.read-all');
        Route::post('/notifications/{notification}/read', [AdminNotificationController::class, 'read'])->name('notifications.read');
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->middleware('permission:audit_logs.view')->name('audit-logs.index');
        Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->middleware('permission:audit_logs.view')->name('audit-logs.show');
        Route::middleware('permission:goals_commissions.view')->prefix('goals-commissions')->name('goals-commissions.')->group(function () {
            Route::get('/targets', [GoalsCommissionController::class, 'targetsIndex'])->name('targets.index');
            Route::get('/targets/create', [GoalsCommissionController::class, 'targetsCreate'])->middleware('permission:goals_commissions.manage')->name('targets.create');
            Route::post('/targets', [GoalsCommissionController::class, 'targetsStore'])->middleware('permission:goals_commissions.manage')->name('targets.store');
            Route::get('/targets/{goalTarget}', [GoalsCommissionController::class, 'targetsShow'])->name('targets.show');
            Route::get('/targets/{goalTarget}/edit', [GoalsCommissionController::class, 'targetsEdit'])->middleware('permission:goals_commissions.manage')->name('targets.edit');
            Route::put('/targets/{goalTarget}', [GoalsCommissionController::class, 'targetsUpdate'])->middleware('permission:goals_commissions.manage')->name('targets.update');

            Route::get('/commissions', [GoalsCommissionController::class, 'commissionsIndex'])->name('commissions.index');
            Route::post('/commissions', [GoalsCommissionController::class, 'commissionsStore'])->middleware('permission:goals_commissions.manage')->name('commissions.store');
            Route::get('/commissions/{commissionStatement}', [GoalsCommissionController::class, 'commissionsShow'])->name('commissions.show');
        });
        Route::middleware('permission:workflow_automations.view')->prefix('workflow-automations')->name('workflow-automations.')->group(function () {
            Route::get('/', [WorkflowAutomationController::class, 'index'])->name('index');
            Route::get('/logs', [WorkflowAutomationController::class, 'executionLogs'])->name('logs');
            Route::get('/create', [WorkflowAutomationController::class, 'create'])->middleware('permission:workflow_automations.manage')->name('create');
            Route::post('/', [WorkflowAutomationController::class, 'store'])->middleware('permission:workflow_automations.manage')->name('store');
            Route::get('/{workflowAutomation}', [WorkflowAutomationController::class, 'show'])->name('show');
            Route::get('/{workflowAutomation}/edit', [WorkflowAutomationController::class, 'edit'])->middleware('permission:workflow_automations.manage')->name('edit');
            Route::put('/{workflowAutomation}', [WorkflowAutomationController::class, 'update'])->middleware('permission:workflow_automations.manage')->name('update');
            Route::patch('/{workflowAutomation}/toggle', [WorkflowAutomationController::class, 'toggle'])->middleware('permission:workflow_automations.manage')->name('toggle');
        });
        Route::middleware('permission:knowledge_base.view')->prefix('knowledge-base')->name('knowledge-base.')->group(function () {
            Route::get('/', [KnowledgeBaseController::class, 'index'])->name('index');
            Route::get('/create', [KnowledgeBaseController::class, 'create'])->middleware('permission:knowledge_base.manage')->name('create');
            Route::post('/', [KnowledgeBaseController::class, 'store'])->middleware('permission:knowledge_base.manage')->name('store');
            Route::get('/categories', [KnowledgeBaseCategoryController::class, 'index'])->middleware('permission:knowledge_base.categories.manage')->name('categories.index');
            Route::post('/categories', [KnowledgeBaseCategoryController::class, 'store'])->middleware('permission:knowledge_base.categories.manage')->name('categories.store');
            Route::put('/categories/{category}', [KnowledgeBaseCategoryController::class, 'update'])->middleware('permission:knowledge_base.categories.manage')->name('categories.update');
            Route::delete('/categories/{category}', [KnowledgeBaseCategoryController::class, 'destroy'])->middleware('permission:knowledge_base.categories.manage')->name('categories.destroy');
            Route::get('/{article}', [KnowledgeBaseController::class, 'show'])->name('show');
            Route::get('/{article}/edit', [KnowledgeBaseController::class, 'edit'])->middleware('permission:knowledge_base.manage')->name('edit');
            Route::put('/{article}', [KnowledgeBaseController::class, 'update'])->middleware('permission:knowledge_base.manage')->name('update');
        });

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

        Route::middleware('permission:chatbot.manage')->group(function () {
            Route::get('/chatbot-settings', [ChatbotSettingController::class, 'edit'])->name('chatbot-settings.edit');
            Route::put('/chatbot-settings', [ChatbotSettingController::class, 'update'])->name('chatbot-settings.update');
            Route::post('/chatbot-settings/rebuild-knowledge', [ChatbotSettingController::class, 'rebuildKnowledge'])->name('chatbot-settings.rebuild');
            Route::post('/chatbot-settings/clear-knowledge', [ChatbotSettingController::class, 'clearKnowledge'])->name('chatbot-settings.clear');
            Route::get('/chatbot-knowledge', [ChatbotKnowledgeController::class, 'index'])->name('chatbot-knowledge.index');
            Route::get('/chatbot-knowledge/create', [ChatbotKnowledgeController::class, 'create'])->name('chatbot-knowledge.create');
            Route::post('/chatbot-knowledge', [ChatbotKnowledgeController::class, 'store'])->name('chatbot-knowledge.store');
            Route::get('/chatbot-knowledge/{chatbotKnowledge}/edit', [ChatbotKnowledgeController::class, 'edit'])->name('chatbot-knowledge.edit');
            Route::put('/chatbot-knowledge/{chatbotKnowledge}', [ChatbotKnowledgeController::class, 'update'])->name('chatbot-knowledge.update');
            Route::delete('/chatbot-knowledge/{chatbotKnowledge}', [ChatbotKnowledgeController::class, 'destroy'])->name('chatbot-knowledge.destroy');
            // AI Bots Management
            Route::resource('ai-bots', AiBotController::class);
            Route::patch('ai-bots/{ai_bot}/toggle', [AiBotController::class, 'toggle'])->name('ai-bots.toggle');

            // WhatsApp Conversations
            Route::prefix('whatsapp')->name('whatsapp.')->group(function () {
                Route::get('/conversations', [\App\Http\Controllers\Admin\WhatsAppConversationController::class, 'index'])->name('conversations.index');
                Route::get('/conversations/{conversation}', [\App\Http\Controllers\Admin\WhatsAppConversationController::class, 'show'])->name('conversations.show');
                Route::post('/conversations/{conversation}/toggle-ai', [\App\Http\Controllers\Admin\WhatsAppConversationController::class, 'toggleAi'])->name('conversations.toggle-ai');
                Route::post('/conversations/{conversation}/send', [\App\Http\Controllers\Admin\WhatsAppConversationController::class, 'sendMessage'])->name('conversations.send');
                Route::post('/conversations/{conversation}/assign', [\App\Http\Controllers\Admin\WhatsAppConversationController::class, 'assign'])->name('conversations.assign');
                Route::post('/conversations/{conversation}/clear', [\App\Http\Controllers\Admin\WhatsAppConversationController::class, 'clearHistory'])->name('conversations.clear');
            });
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
            Route::get('/media-library/{media_library}/usage', [MediaLibraryController::class, 'usage'])->name('media-library.usage');
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

        Route::get('/forms/submissions', [InquiryController::class, 'index'])->middleware('permission:forms.submissions.view')->name('forms.submissions');

        Route::middleware('permission:forms.manage')->group(function () {
            Route::post('/forms/{form}/duplicate', [LeadFormController::class, 'duplicate'])->name('forms.duplicate');
            Route::resource('forms', LeadFormController::class);
        });

        Route::middleware('permission:marketing.manage')->group(function () {
            Route::post('/marketing-landing-pages/{marketing_landing_page}/duplicate', [MarketingLandingPageController::class, 'duplicate'])->name('marketing-landing-pages.duplicate');
            Route::resource('marketing-landing-pages', MarketingLandingPageController::class);
            Route::prefix('marketing-campaigns')->name('marketing-campaigns.')->group(function () {
                Route::get('/', [MarketingCampaignController::class, 'index'])->name('index');
                Route::get('/create', [MarketingCampaignController::class, 'create'])->name('create');
                Route::post('/', [MarketingCampaignController::class, 'store'])->name('store');
                Route::get('/{marketingCampaign}', [MarketingCampaignController::class, 'show'])->name('show');
                Route::get('/{marketingCampaign}/edit', [MarketingCampaignController::class, 'edit'])->name('edit');
                Route::put('/{marketingCampaign}', [MarketingCampaignController::class, 'update'])->name('update');
            });
        });

        Route::middleware('permission:maps.manage')->group(function () {
            Route::post('/map-sections/{map_section}/duplicate', [MapSectionController::class, 'duplicate'])->name('map-sections.duplicate');
            Route::resource('map-sections', MapSectionController::class);
        });

        Route::middleware('permission:tracking.manage')->group(function () {
            Route::post('/tracking-integrations/{tracking_integration}/duplicate', [TrackingIntegrationController::class, 'duplicate'])->name('tracking-integrations.duplicate');
            Route::resource('tracking-integrations', TrackingIntegrationController::class);
        });

        Route::middleware('permission:utm.manage')->prefix('utm')->name('utm.')->group(function () {
            Route::get('/', [UtmController::class, 'dashboard'])->name('dashboard');
            Route::get('/campaigns', [UtmController::class, 'index'])->name('index');
            Route::get('/campaigns/create', [UtmController::class, 'create'])->name('create');
            Route::post('/campaigns', [UtmController::class, 'store'])->name('store');
            Route::get('/campaigns/{campaign}/edit', [UtmController::class, 'edit'])->name('edit');
            Route::put('/campaigns/{campaign}', [UtmController::class, 'update'])->name('update');
        });

        Route::middleware('permission:settings.manage')->prefix('integrations')->name('integrations.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\IntegrationController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\IntegrationController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\IntegrationController::class, 'store'])->name('store');
            Route::get('/{integration}/edit', [\App\Http\Controllers\Admin\IntegrationController::class, 'edit'])->name('edit');
            Route::put('/{integration}', [\App\Http\Controllers\Admin\IntegrationController::class, 'update'])->name('update');
            Route::post('/{integration}/test', [\App\Http\Controllers\Admin\IntegrationController::class, 'testConnection'])->name('test');
            Route::get('/logs', [\App\Http\Controllers\Admin\IntegrationController::class, 'logs'])->name('logs');
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
            Route::prefix('crm')->name('crm.')->group(function () {
                Route::get('/', [CrmController::class, 'dashboard'])->name('dashboard');
                Route::get('/leads', [CrmLeadController::class, 'index'])->name('leads.index');
                Route::get('/customers', [CrmCustomerController::class, 'index'])->middleware('permission:customers.view')->name('customers.index');
                Route::get('/customers/create', [CrmCustomerController::class, 'create'])->middleware('permission:customers.manage')->name('customers.create');
                Route::get('/customers/{customer}', [CrmCustomerController::class, 'show'])->middleware('permission:customers.view')->name('customers.show');
                Route::get('/customers/{customer}/edit', [CrmCustomerController::class, 'edit'])->middleware('permission:customers.manage')->name('customers.edit');
                Route::get('/information', [CrmInformationController::class, 'index'])->name('information.index');
                Route::get('/information/create', [CrmInformationController::class, 'create'])->middleware('permission:information.manage')->name('information.create');
                Route::get('/information/{information}', [CrmInformationController::class, 'show'])->name('information.show');
                Route::post('/information/{information}/acknowledge', [CrmInformationController::class, 'acknowledge'])->name('information.acknowledge');
                Route::get('/leads/delayed', [CrmLeadController::class, 'delayed'])->name('leads.delayed');
                Route::get('/leads/trash', [CrmLeadController::class, 'trash'])->middleware('permission:leads.delete')->name('leads.trash');
                Route::get('/leads/transfer', [CrmLeadController::class, 'transfer'])->name('leads.transfer');
                Route::get('/leads/create', [CrmLeadController::class, 'create'])->middleware('permission:leads.create')->name('leads.create');
                Route::post('/leads', [CrmLeadController::class, 'store'])->middleware('permission:leads.create')->name('leads.store');
                Route::get('/leads/{lead}', [CrmLeadController::class, 'show'])->name('leads.show');
                Route::get('/pipeline', [CrmController::class, 'pipeline'])->name('pipeline');
                Route::get('/follow-ups', [CrmController::class, 'followUps'])->name('follow-ups');
                Route::get('/tasks', [CrmTaskController::class, 'index'])->name('tasks.index');
                Route::get('/tasks/list', [CrmTaskController::class, 'list'])->name('tasks.list');
                Route::get('/tasks/my', [CrmTaskController::class, 'myTasks'])->name('tasks.my');
                Route::get('/tasks/today', [CrmTaskController::class, 'today'])->name('tasks.today');
                Route::get('/tasks/delayed', [CrmTaskController::class, 'delayed'])->name('tasks.delayed');
                Route::get('/tasks/completed', [CrmTaskController::class, 'completed'])->name('tasks.completed');
                Route::get('/tasks/board', [CrmTaskController::class, 'board'])->name('tasks.board');
                Route::get('/tasks/reports', [CrmTaskController::class, 'reports'])->middleware('permission:reports.view')->name('tasks.reports');
                Route::get('/tasks/create', [CrmTaskController::class, 'create'])->name('tasks.create');
                Route::get('/tasks/{task}', [CrmTaskController::class, 'show'])->name('tasks.show');
                Route::get('/tasks/{task}/edit', [CrmTaskController::class, 'edit'])->middleware('permission:leads.edit')->name('tasks.edit');
                Route::get('/statuses', [CrmController::class, 'statuses'])->name('statuses');
                Route::get('/sources', [CrmController::class, 'sources'])->name('sources');
                Route::get('/service-types', [CrmController::class, 'serviceTypes'])->name('service-types');
                Route::get('/reports', [CrmController::class, 'reports'])->middleware('permission:reports.view')->name('reports');
                Route::get('/reports-2', [CrmController::class, 'reports2'])->middleware('permission:reports.view')->name('reports2');
            });
        });
        Route::middleware('permission:documents.view')->prefix('documents')->name('documents.')->group(function () {
            Route::get('/', [CrmDocumentController::class, 'index'])->name('index');
            Route::get('/create', [CrmDocumentController::class, 'create'])->middleware('permission:documents.manage')->name('create');
            Route::get('/categories', [CrmDocumentCategoryController::class, 'index'])->middleware('permission:documents.categories.manage')->name('categories.index');
            Route::get('/{document}', [CrmDocumentController::class, 'show'])->name('show');
            Route::get('/{document}/preview', [CrmDocumentController::class, 'preview'])->name('preview');
            Route::get('/{document}/download', [CrmDocumentController::class, 'download'])->name('download');
        });
        Route::put('/inquiries/{inquiry}', [InquiryController::class, 'update'])->middleware('permission:leads.edit')->name('inquiries.update');
        Route::middleware('permission:leads.edit')->prefix('crm')->name('crm.')->group(function () {
            Route::post('/customers', [CrmCustomerController::class, 'store'])->middleware('permission:customers.manage')->name('customers.store');
            Route::put('/customers/{customer}', [CrmCustomerController::class, 'update'])->middleware('permission:customers.manage')->name('customers.update');
            Route::post('/information', [CrmInformationController::class, 'store'])->middleware('permission:information.manage')->name('information.store');
        });
        Route::prefix('crm')->name('crm.')->group(function () {
            Route::post('/leads/import/preview', [CrmLeadController::class, 'previewImport'])->name('leads.import.preview');
            Route::post('/leads/import', [CrmLeadController::class, 'import'])->name('leads.import');
            Route::get('/leads/import/template', [CrmLeadController::class, 'downloadTemplate'])->name('leads.import.template');
            Route::get('/leads/import/report/{report}', [CrmLeadController::class, 'downloadImportReport'])->name('leads.import.report');
        });
        Route::middleware('permission:leads.edit')->prefix('crm')->name('crm.')->group(function () {
            Route::put('/leads/{lead}', [CrmLeadController::class, 'update'])->name('leads.update');
            Route::post('/leads/bulk', [CrmLeadController::class, 'bulkUpdate'])->name('leads.bulk-update');
            Route::post('/leads/{lead}/notes', [CrmLeadController::class, 'storeNote'])->name('leads.notes.store');
            Route::post('/leads/{lead}/tasks', [CrmLeadController::class, 'storeTask'])->name('leads.tasks.store');
            Route::put('/follow-ups/{followUp}', [CrmLeadController::class, 'updateFollowUp'])->name('follow-ups.update');
            Route::post('/tasks', [CrmTaskController::class, 'store'])->name('tasks.store');
            Route::put('/tasks/{task}', [CrmTaskController::class, 'update'])->name('tasks.update');
            Route::patch('/tasks/{task}/status', [CrmTaskController::class, 'updateStatus'])->name('tasks.status');
            Route::post('/statuses', [CrmController::class, 'storeStatus'])->name('statuses.store');
            Route::put('/statuses/{status}', [CrmController::class, 'updateStatus'])->name('statuses.update');
            Route::post('/sources', [CrmController::class, 'storeSource'])->name('sources.store');
            Route::put('/sources/{source}', [CrmController::class, 'updateSource'])->name('sources.update');
            Route::post('/service-types', [CrmController::class, 'storeServiceType'])->name('service-types.store');
            Route::put('/service-types/{serviceType}', [CrmController::class, 'updateServiceType'])->name('service-types.update');
            Route::delete('/service-types/{serviceType}', [CrmController::class, 'destroyServiceType'])->name('service-types.destroy');
            Route::post('/service-subtypes', [CrmController::class, 'storeServiceSubtype'])->name('service-subtypes.store');
            Route::put('/service-subtypes/{serviceSubtype}', [CrmController::class, 'updateServiceSubtype'])->name('service-subtypes.update');
            Route::delete('/service-subtypes/{serviceSubtype}', [CrmController::class, 'destroyServiceSubtype'])->name('service-subtypes.destroy');
        });
        Route::middleware('permission:leads.export')->prefix('crm')->name('crm.')->group(function () {
            Route::post('/leads/export', [CrmLeadController::class, 'export'])->name('leads.export');
        });
        Route::middleware('permission:leads.delete')->prefix('crm')->name('crm.')->group(function () {
            Route::delete('/leads/{lead}', [CrmLeadController::class, 'destroy'])->name('leads.destroy');
            Route::post('/leads/{lead}/restore', [CrmLeadController::class, 'restore'])->name('leads.restore');
            Route::delete('/leads/{lead}/force-delete', [CrmLeadController::class, 'forceDestroy'])->name('leads.force-destroy');
        });
        Route::middleware('permission:documents.manage')->prefix('documents')->name('documents.')->group(function () {
            Route::post('/', [CrmDocumentController::class, 'store'])->name('store');
            Route::delete('/{document}', [CrmDocumentController::class, 'destroy'])->name('destroy');
        });
        Route::middleware('permission:documents.categories.manage')->prefix('documents/categories')->name('documents.categories.')->group(function () {
            Route::post('/', [CrmDocumentCategoryController::class, 'store'])->name('store');
            Route::put('/{category}', [CrmDocumentCategoryController::class, 'update'])->name('update');
            Route::delete('/{category}', [CrmDocumentCategoryController::class, 'destroy'])->name('destroy');
        });

        Route::middleware('permission:accounting.view')->prefix('accounting')->name('accounting.')->group(function () {
            Route::get('/', [AccountingController::class, 'dashboard'])->name('dashboard');
            Route::get('/customers', [AccountingController::class, 'customers'])->name('customers.index');
            Route::get('/customers/{account}', [AccountingController::class, 'customer'])->name('customers.show');
            Route::get('/treasuries', [AccountingTreasuryController::class, 'index'])->name('treasuries.index');
            Route::get('/general-expenses', [AccountingController::class, 'generalExpenses'])->name('general-expenses.index');
            Route::get('/employees', [AccountingController::class, 'employees'])->name('employees.index');
            Route::get('/settings', [AccountingController::class, 'settings'])->middleware('permission:accounting.manage')->name('settings');
            Route::get('/reports', [AccountingController::class, 'reports'])->middleware('permission:accounting.reports.view')->name('reports');
        });

        Route::middleware('permission:accounting.manage')->prefix('accounting')->name('accounting.')->group(function () {
            Route::get('/treasuries/create', [AccountingTreasuryController::class, 'create'])->name('treasuries.create');
            Route::post('/treasuries', [AccountingTreasuryController::class, 'store'])->name('treasuries.store');
            Route::get('/treasuries/{treasury}/edit', [AccountingTreasuryController::class, 'edit'])->whereNumber('treasury')->name('treasuries.edit');
            Route::put('/treasuries/{treasury}', [AccountingTreasuryController::class, 'update'])->whereNumber('treasury')->name('treasuries.update');
            Route::post('/customers/{account}/payments', [AccountingController::class, 'storePayment'])->name('customers.payments.store');
            Route::post('/customers/{account}/expenses', [AccountingController::class, 'storeCustomerExpense'])->name('customers.expenses.store');
            Route::delete('/customer-expenses/{expense}', [AccountingController::class, 'destroyCustomerExpense'])->name('customers.expenses.destroy');
            Route::post('/general-expenses', [AccountingController::class, 'storeGeneralExpense'])->name('general-expenses.store');
            Route::put('/general-expenses/{expense}', [AccountingController::class, 'updateGeneralExpense'])->name('general-expenses.update');
            Route::delete('/general-expenses/{expense}', [AccountingController::class, 'destroyGeneralExpense'])->name('general-expenses.destroy');
            Route::post('/employees/transactions', [AccountingController::class, 'storeEmployeeTransaction'])->name('employees.transactions.store');
            Route::put('/employees/transactions/{transaction}', [AccountingController::class, 'updateEmployeeTransaction'])->name('employees.transactions.update');
            Route::delete('/employees/transactions/{transaction}', [AccountingController::class, 'destroyEmployeeTransaction'])->name('employees.transactions.destroy');
            Route::post('/settings/customer-categories', [AccountingController::class, 'storeExpenseCategory'])->name('settings.customer-categories.store');
            Route::put('/settings/customer-categories/{category}', [AccountingController::class, 'updateExpenseCategory'])->name('settings.customer-categories.update');
            Route::delete('/settings/customer-categories/{category}', [AccountingController::class, 'destroyExpenseCategory'])->name('settings.customer-categories.destroy');
            Route::post('/settings/customer-subcategories', [AccountingController::class, 'storeExpenseSubcategory'])->name('settings.customer-subcategories.store');
            Route::put('/settings/customer-subcategories/{subcategory}', [AccountingController::class, 'updateExpenseSubcategory'])->name('settings.customer-subcategories.update');
            Route::delete('/settings/customer-subcategories/{subcategory}', [AccountingController::class, 'destroyExpenseSubcategory'])->name('settings.customer-subcategories.destroy');
            Route::post('/settings/general-categories', [AccountingController::class, 'storeGeneralExpenseCategory'])->name('settings.general-categories.store');
            Route::put('/settings/general-categories/{category}', [AccountingController::class, 'updateGeneralExpenseCategory'])->name('settings.general-categories.update');
            Route::delete('/settings/general-categories/{category}', [AccountingController::class, 'destroyGeneralExpenseCategory'])->name('settings.general-categories.destroy');
        });

        Route::middleware('permission:accounting.view')->prefix('accounting')->name('accounting.')->group(function () {
            Route::get('/treasuries/{treasury}', [AccountingTreasuryController::class, 'show'])->whereNumber('treasury')->name('treasuries.show');
        });
    });
});
// Cache Clearing Route for Production Debugging
Route::get('/clear-all-cache', function() {
    try {
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        return "<h1>Success!</h1><p>All caches have been cleared. Please refresh the Integrations page now.</p>";
    } catch (\Exception $e) {
        return "<h1>Error</h1><p>" . $e->getMessage() . "</p>";
    }
});

Route::get('/migrate-db', function() {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return "<h1>Success!</h1><pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";
    } catch (\Exception $e) {
        return "<h1>Error</h1><p>" . $e->getMessage() . "</p>";
    }
});
