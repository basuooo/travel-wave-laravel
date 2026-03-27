<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('admin.admin_dashboard'))</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/admin.css') }}" rel="stylesheet">
</head>
<body class="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
@php($adminUser = auth()->user())
@php($currentRoute = request()->route()?->getName())
@php($notificationCenter = app(\App\Support\AdminNotificationCenterService::class))
@php($crmInformationEnabled = $adminUser && \Illuminate\Support\Facades\Schema::hasTable('crm_information') && \Illuminate\Support\Facades\Schema::hasTable('crm_information_recipients'))
@php($crmCustomersEnabled = $adminUser && \Illuminate\Support\Facades\Schema::hasTable('crm_customers'))
@php($crmDocumentsEnabled = $adminUser && \Illuminate\Support\Facades\Schema::hasTable('crm_documents') && \Illuminate\Support\Facades\Schema::hasTable('crm_document_categories'))
@php($pendingInformationCount = $crmInformationEnabled ? \App\Models\CrmInformationRecipient::query()->where('user_id', $adminUser->id)->whereNull('acknowledged_at')->count() : 0)
@php($crmInformationLabel = __('admin.crm_information') . ($pendingInformationCount > 0 ? ' (' . $pendingInformationCount . ')' : ''))
@php($sidebarSections = [
    [
        'title' => __('admin.nav_users_permissions'),
        'items' => array_values(array_filter([
            $adminUser?->hasPermission('users.view') ? ['label' => __('admin.users_management'), 'route' => 'admin.users.index', 'match' => 'admin.users.*'] : null,
            $adminUser?->hasPermission('roles.manage') ? ['label' => __('admin.roles_management'), 'route' => 'admin.roles.index', 'match' => 'admin.roles.*'] : null,
            $adminUser?->hasPermission('permissions.manage') ? ['label' => __('admin.permissions_management'), 'route' => 'admin.permissions.index', 'match' => 'admin.permissions.*'] : null,
        ])),
    ],
    [
        'title' => __('admin.nav_site_settings'),
        'items' => array_values(array_filter([
            $adminUser?->hasPermission('settings.manage') ? ['label' => __('admin.brand_settings'), 'route' => 'admin.settings.edit', 'match' => 'admin.settings.*'] : null,
            $adminUser?->hasPermission('settings.manage') ? ['label' => __('admin.header_settings'), 'route' => 'admin.header-settings.edit', 'match' => 'admin.header-settings.*'] : null,
            $adminUser?->hasPermission('settings.manage') ? ['label' => __('admin.footer_settings'), 'route' => 'admin.footer-settings.edit', 'match' => 'admin.footer-settings.*'] : null,
            $adminUser?->hasPermission('settings.manage') ? ['label' => __('admin.floating_whatsapp'), 'route' => 'admin.floating-whatsapp-settings.edit', 'match' => 'admin.floating-whatsapp-settings.*'] : null,
            $adminUser?->hasPermission('settings.manage') ? ['label' => __('admin.meta_conversion_api'), 'route' => 'admin.meta-conversion-api-settings.edit', 'match' => 'admin.meta-conversion-api-settings.*'] : null,
        ])),
    ],
    [
        'title' => __('admin.nav_content'),
        'items' => array_values(array_filter([
            $adminUser?->hasPermission('pages.view') ? ['label' => __('admin.pages'), 'route' => 'admin.pages.index', 'match' => 'admin.pages.*'] : null,
            $adminUser?->hasPermission('media.manage') ? ['label' => __('admin.media_library'), 'route' => 'admin.media-library.index', 'match' => 'admin.media-library.*'] : null,
            $adminUser?->hasPermission('pages.view') ? ['label' => __('admin.hero_slider'), 'route' => 'admin.hero-slides.index', 'match' => 'admin.hero-slides.*'] : null,
            $adminUser?->hasPermission('pages.view') ? ['label' => __('admin.homepage_country_strip'), 'route' => 'admin.home-country-strip.index', 'match' => 'admin.home-country-strip.*'] : null,
            $adminUser?->hasPermission('destinations.manage') ? ['label' => __('admin.visa_categories'), 'route' => 'admin.visa-categories.index', 'match' => 'admin.visa-categories.*'] : null,
            $adminUser?->hasPermission('destinations.manage') ? ['label' => __('admin.visa_destinations'), 'route' => 'admin.visa-countries.index', 'match' => 'admin.visa-countries.*'] : null,
            $adminUser?->hasPermission('destinations.manage') ? ['label' => __('admin.destinations'), 'route' => 'admin.destinations.index', 'match' => 'admin.destinations.*'] : null,
            $adminUser?->hasPermission('testimonials.manage') ? ['label' => __('admin.testimonials'), 'route' => 'admin.testimonials.index', 'match' => 'admin.testimonials.*'] : null,
            $adminUser?->hasPermission('menu.manage') ? ['label' => __('admin.navigation'), 'route' => 'admin.menu-items.index', 'match' => 'admin.menu-items.*'] : null,
            $adminUser?->hasPermission('maps.manage') ? ['label' => __('admin.maps_manager'), 'route' => 'admin.map-sections.index', 'match' => 'admin.map-sections.*'] : null,
        ])),
    ],
    [
        'title' => __('admin.nav_forms_leads'),
        'items' => array_values(array_filter([
            $adminUser?->hasPermission('forms.manage') ? ['label' => __('admin.forms_manager'), 'route' => 'admin.forms.index', 'match' => 'admin.forms.*'] : null,
            $adminUser?->hasPermission('forms.submissions.view') ? ['label' => __('admin.form_submissions'), 'route' => 'admin.forms.submissions', 'match' => 'admin.forms.submissions'] : null,
            $adminUser?->hasPermission('leads.view') ? ['label' => __('admin.inquiries'), 'route' => 'admin.inquiries.index', 'match' => 'admin.inquiries.*'] : null,
        ])),
    ],
    [
        'title' => __('admin.nav_crm'),
        'items' => array_values(array_filter([
            $adminUser?->hasPermission('leads.view') ? ['label' => __('admin.crm'), 'route' => 'admin.crm.dashboard', 'match' => 'admin.crm.dashboard'] : null,
            $adminUser?->hasPermission('leads.view') ? ['label' => __('admin.crm_leads'), 'route' => 'admin.crm.leads.index', 'match' => 'admin.crm.leads.*'] : null,
            ($crmCustomersEnabled && $adminUser?->hasPermission('customers.view')) ? ['label' => __('admin.crm_customers'), 'route' => 'admin.crm.customers.index', 'match' => 'admin.crm.customers.*'] : null,
            ($crmDocumentsEnabled && $adminUser?->hasPermission('documents.view')) ? ['label' => __('admin.documents'), 'route' => 'admin.documents.index', 'match' => 'admin.documents.*'] : null,
            $adminUser?->hasPermission('leads.view') ? ['label' => __('admin.crm_delayed_leads'), 'route' => 'admin.crm.leads.delayed', 'match' => 'admin.crm.leads.delayed'] : null,
            $adminUser?->hasPermission('leads.view') ? ['label' => __('admin.crm_pipeline'), 'route' => 'admin.crm.pipeline', 'match' => 'admin.crm.pipeline'] : null,
            $adminUser?->hasPermission('leads.view') ? ['label' => __('admin.crm_followups'), 'route' => 'admin.crm.follow-ups', 'match' => 'admin.crm.follow-ups'] : null,
            $adminUser?->hasPermission('leads.view') ? ['label' => __('admin.crm_tasks'), 'route' => 'admin.crm.tasks.index', 'match' => 'admin.crm.tasks.*'] : null,
            $adminUser?->hasPermission('leads.view') ? ['label' => $crmInformationLabel ?? __('admin.crm_information'), 'route' => 'admin.crm.information.index', 'match' => 'admin.crm.information.*'] : null,
            $adminUser?->hasPermission('leads.edit') ? ['label' => __('admin.crm_statuses'), 'route' => 'admin.crm.statuses', 'match' => 'admin.crm.statuses'] : null,
            $adminUser?->hasPermission('leads.view') ? ['label' => __('admin.crm_sources'), 'route' => 'admin.crm.sources', 'match' => 'admin.crm.sources'] : null,
            $adminUser?->hasPermission('leads.view') ? ['label' => __('admin.crm_service_types'), 'route' => 'admin.crm.service-types', 'match' => 'admin.crm.service-types'] : null,
            $adminUser?->hasPermission('leads.delete') ? ['label' => __('admin.crm_deleted_leads'), 'route' => 'admin.crm.leads.trash', 'match' => 'admin.crm.leads.trash'] : null,
            $adminUser?->hasPermission('reports.view') ? ['label' => __('admin.crm_reports'), 'route' => 'admin.crm.reports', 'match' => 'admin.crm.reports'] : null,
            $adminUser?->hasPermission('reports.view') ? ['label' => __('admin.crm_reports2'), 'route' => 'admin.crm.reports2', 'match' => 'admin.crm.reports2'] : null,
        ])),
    ],
    [
        'title' => __('admin.nav_marketing'),
        'items' => array_values(array_filter([
            $adminUser?->hasPermission('marketing.manage') ? ['label' => __('admin.marketing_campaigns'), 'route' => 'admin.marketing-campaigns.index', 'match' => 'admin.marketing-campaigns.*'] : null,
            $adminUser?->hasPermission('marketing.manage') ? ['label' => __('admin.marketing_manager'), 'route' => 'admin.marketing-landing-pages.index', 'match' => 'admin.marketing-landing-pages.*'] : null,
            $adminUser?->hasPermission('tracking.manage') ? ['label' => __('admin.tracking_manager'), 'route' => 'admin.tracking-integrations.index', 'match' => 'admin.tracking-integrations.*'] : null,
            $adminUser?->hasPermission('utm.manage') ? ['label' => __('admin.utm_analytics'), 'route' => 'admin.utm.dashboard', 'match' => 'admin.utm.*'] : null,
            $adminUser?->hasPermission('chatbot.manage') ? ['label' => __('admin.chatbot_manager'), 'route' => 'admin.chatbot-settings.edit', 'match' => 'admin.chatbot-*'] : null,
        ])),
    ],
    [
        'title' => __('admin.nav_accounting'),
        'items' => array_values(array_filter([
            $adminUser?->hasPermission('accounting.view') ? ['label' => __('admin.accounting_dashboard'), 'route' => 'admin.accounting.dashboard', 'match' => 'admin.accounting.dashboard'] : null,
            $adminUser?->hasPermission('accounting.view') ? ['label' => __('admin.accounting_customer_accounts'), 'route' => 'admin.accounting.customers.index', 'match' => 'admin.accounting.customers.*'] : null,
            $adminUser?->hasPermission('accounting.view') ? ['label' => __('admin.accounting_treasuries'), 'route' => 'admin.accounting.treasuries.index', 'match' => 'admin.accounting.treasuries.*'] : null,
            $adminUser?->hasPermission('accounting.view') ? ['label' => __('admin.accounting_general_expenses'), 'route' => 'admin.accounting.general-expenses.index', 'match' => 'admin.accounting.general-expenses.*'] : null,
            $adminUser?->hasPermission('accounting.view') ? ['label' => __('admin.accounting_employees'), 'route' => 'admin.accounting.employees.index', 'match' => 'admin.accounting.employees.*'] : null,
            $adminUser?->hasPermission('accounting.reports.view') ? ['label' => __('admin.accounting_reports'), 'route' => 'admin.accounting.reports', 'match' => 'admin.accounting.reports'] : null,
            $adminUser?->hasPermission('accounting.manage') ? ['label' => __('admin.accounting_settings'), 'route' => 'admin.accounting.settings', 'match' => 'admin.accounting.settings'] : null,
        ])),
    ],
    [
        'title' => 'SEO',
        'items' => array_values(array_filter([
            $adminUser?->hasPermission('seo.manage') ? ['label' => __('admin.seo_manager'), 'route' => 'admin.seo.dashboard', 'match' => 'admin.seo.*'] : null,
        ])),
    ],
    [
        'title' => __('admin.nav_blog'),
        'items' => array_values(array_filter([
            $adminUser?->hasPermission('blog.manage') ? ['label' => __('admin.blog_categories'), 'route' => 'admin.blog-categories.index', 'match' => 'admin.blog-categories.*'] : null,
            $adminUser?->hasPermission('blog.manage') ? ['label' => __('admin.blog_posts'), 'route' => 'admin.blog-posts.index', 'match' => 'admin.blog-posts.*'] : null,
        ])),
    ],
])
@php($notificationsEnabled = $adminUser && \Illuminate\Support\Facades\Schema::hasTable('notifications'))
@php($adminNotifications = $notificationsEnabled ? $notificationCenter->presentNotifications($adminUser->notifications()->latest()->limit(8)->get()) : collect())
@php($unreadNotificationCount = $notificationsEnabled ? $adminUser->unreadNotifications()->count() : 0)
@php($followUpPopupNotifications = $notificationsEnabled ? $adminUser->unreadNotifications()->where('type', \App\Notifications\CrmFollowUpReminderNotification::class)->latest()->take(5)->get()->map(function ($notification) {
    $data = $notification->data ?? [];

    return [
        'id' => $notification->id,
        'title' => $data['title_' . app()->getLocale()] ?? ($data['title_ar'] ?? $data['title_en'] ?? __('admin.notifications')),
        'lead_name' => $data['lead_name'] ?? null,
        'phone' => $data['phone'] ?? null,
        'whatsapp_number' => $data['whatsapp_number'] ?? null,
        'scheduled_at' => $data['scheduled_at'] ?? null,
        'status_reason' => $data['status_reason_' . app()->getLocale()] ?? ($data['status_reason_ar'] ?? $data['status_reason_en'] ?? null),
        'note' => $data['follow_up_note'] ?? null,
        'assigned_user_name' => $data['assigned_user_name'] ?? null,
        'url' => $data['url'] ?? route('admin.crm.dashboard'),
        'follow_up_update_url' => $data['follow_up_update_url'] ?? null,
        'read_url' => route('admin.notifications.read', $notification->id),
    ];
}) : collect())
<div class="container-fluid">
    <div class="row">
        <aside class="col-lg-2 admin-sidebar text-white p-3">
            <div class="admin-brand">
                <div class="admin-brand__mark">TW</div>
                <div>
                    <div class="admin-brand__name">Travel Wave CMS</div>
                    <span class="admin-brand__meta">{{ __('admin.admin_dashboard') }}</span>
                </div>
            </div>
            <nav class="nav flex-column gap-1">
                @if($adminUser?->hasPermission('dashboard.access'))
                    <a class="nav-link rounded px-3 py-2 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">{{ __('admin.dashboard') }}</a>
                    @if($adminUser?->hasPermission('reports.view'))
                        <a class="nav-link rounded px-3 py-2 {{ request()->routeIs('admin.kpi.*') ? 'active' : '' }}" href="{{ route('admin.kpi.dashboard') }}">{{ __('admin.kpi_dashboard') }}</a>
                    @endif
                    @if($adminUser?->hasPermission('knowledge_base.view'))
                        <a class="nav-link rounded px-3 py-2 {{ request()->routeIs('admin.knowledge-base.*') ? 'active' : '' }}" href="{{ route('admin.knowledge-base.index') }}">{{ __('admin.knowledge_base') }}</a>
                    @endif
                    @if($adminUser?->hasPermission('audit_logs.view'))
                        <a class="nav-link rounded px-3 py-2 {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}" href="{{ route('admin.audit-logs.index') }}">{{ __('admin.audit_log') }}</a>
                    @endif
                @if($adminUser?->hasPermission('workflow_automations.view'))
                    <a class="nav-link rounded px-3 py-2 {{ request()->routeIs('admin.workflow-automations.*') ? 'active' : '' }}" href="{{ route('admin.workflow-automations.index') }}">{{ __('admin.workflow_automations') }}</a>
                @endif
                @if($adminUser?->hasPermission('goals_commissions.view'))
                    <a class="nav-link rounded px-3 py-2 {{ request()->routeIs('admin.goals-commissions.*') ? 'active' : '' }}" href="{{ route('admin.goals-commissions.targets.index') }}">{{ __('admin.goals_commissions') }}</a>
                @endif
                <a class="nav-link rounded px-3 py-2 {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}" href="{{ route('admin.notifications.index') }}">
                        {{ __('admin.notifications') }}@if($unreadNotificationCount > 0) ({{ $unreadNotificationCount }})@endif
                    </a>
                @endif
                @foreach($sidebarSections as $section)
                    @continue(empty($section['items']))
                    @php($sectionActive = collect($section['items'])->contains(fn ($item) => request()->routeIs($item['match'])))
                    <details class="admin-sidebar-group" {{ $sectionActive ? 'open' : '' }}>
                        <summary class="admin-sidebar-group-summary">{{ $section['title'] }}</summary>
                        <div class="admin-sidebar-group-links">
                            @foreach($section['items'] as $item)
                                <a class="nav-link rounded px-3 py-2 {{ request()->routeIs($item['match']) ? 'active' : '' }}" href="{{ route($item['route']) }}">{{ $item['label'] }}</a>
                            @endforeach
                        </div>
                    </details>
                @endforeach
                <form method="post" action="{{ route('admin.logout') }}" class="mt-3">
                    @csrf
                    <button class="btn btn-outline-light w-100">{{ __('admin.logout') }}</button>
                </form>
            </nav>
        </aside>
        <div class="col-lg-10 px-0 admin-content-column">
            <div class="admin-topbar px-4 py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="admin-page-title mb-0">@yield('page_title', __('admin.dashboard'))</h1>
                    <div class="admin-page-description small">@yield('page_description')</div>
                </div>
                <div class="admin-topbar-actions">
                    <form method="get" action="{{ route('admin.search') }}" class="admin-search-shell d-none d-md-flex">
                        <input type="text" name="q" class="form-control" placeholder="{{ __('admin.search_placeholder') }}" value="{{ request('q') }}">
                    </form>
                    <button type="button" class="admin-icon-button" aria-label="Notifications">◦</button>
                    <div class="admin-notification-shell" data-admin-notification-shell>
                        <button type="button" class="admin-icon-button admin-notification-toggle" aria-label="{{ __('admin.notifications') }}" data-admin-notification-toggle>
                            <span aria-hidden="true">&#128276;</span>
                            @if($unreadNotificationCount > 0)
                                <span class="admin-notification-badge" data-admin-notification-count>{{ $unreadNotificationCount }}</span>
                            @endif
                        </button>
                        <div class="admin-notification-panel" hidden data-admin-notification-panel>
                            <div class="admin-notification-panel__header">
                                <strong>{{ __('admin.notifications') }}</strong>
                                <div class="admin-notification-panel__header-actions">
                                    @if($unreadNotificationCount > 0)
                                        <span class="badge text-bg-danger" data-admin-notification-count-text>{{ $unreadNotificationCount }}</span>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-notifications-read-all="{{ route('admin.notifications.read-all') }}">{{ __('admin.crm_notifications_read_all') }}</button>
                                </div>
                            </div>
                            <div class="admin-notification-panel__filters">
                                <button type="button" class="btn btn-sm btn-outline-secondary is-active" data-notification-filter="all">{{ __('admin.crm_notifications_filter_all') }}</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-notification-filter="unread">{{ __('admin.crm_notifications_filter_unread') }}</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-notification-filter="read">{{ __('admin.crm_notifications_filter_read') }}</button>
                            </div>
                            <div class="admin-notification-panel__list">
                                @forelse($adminNotifications as $notification)
                                    <div class="admin-notification-item {{ $notification['is_read'] ? 'is-read' : 'is-unread' }}" data-notification-item="{{ $notification['id'] }}" data-notification-state="{{ $notification['is_read'] ? 'read' : 'unread' }}">
                                        <a href="{{ $notification['url'] ?? route('admin.dashboard') }}" class="admin-notification-item__body" data-notification-link data-notification-read-url="{{ route('admin.notifications.read', $notification['id']) }}">
                                            <strong>{{ $notification['title'] }}</strong>
                                            <span class="d-flex align-items-center gap-2 flex-wrap">
                                                <span class="badge text-bg-{{ $notification['severity'] }}">{{ $notification['severity_label'] }}</span>
                                                <span class="badge text-bg-light">{{ $notification['type_label'] }}</span>
                                            </span>
                                            @if(!empty($notification['message']))
                                                <span>{{ $notification['message'] }}</span>
                                            @endif
                                            <small>{{ optional($notification['created_at'])->diffForHumans() }}</small>
                                        </a>
                                        <span class="admin-notification-dot" aria-hidden="true"></span>
                                        @if(! $notification['is_read'])
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-notification-read="{{ route('admin.notifications.read', $notification['id']) }}">{{ __('admin.crm_notification_mark_read') }}</button>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-muted small">{{ __('admin.no_notifications') }}</div>
                                @endforelse
                            </div>
                            <div class="admin-notification-panel__footer">
                                <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-primary w-100">{{ __('admin.view_all') }}</a>
                            </div>
                        </div>
                    </div>
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('locale.switch', 'en') }}">EN</a>
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('locale.switch', 'ar') }}">AR</a>
                    <div class="admin-user-chip">
                        <div class="admin-user-chip__avatar">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($adminUser?->name ?? 'A', 0, 1)) }}</div>
                        <div class="admin-user-chip__meta">
                            <strong>{{ $adminUser?->name ?? 'Admin' }}</strong>
                            <span>{{ __('admin.admin_dashboard') }}</span>
                        </div>
                    </div>
                    <a class="btn btn-primary" href="{{ route('home') }}" target="_blank">{{ __('admin.view_website') }}</a>
                </div>
            </div>
            <div class="admin-main-content p-4">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>
</div>
@if($followUpPopupNotifications->isNotEmpty())
    <div class="admin-followup-popup" hidden data-admin-followup-popup data-notifications='@json($followUpPopupNotifications)' data-csrf="{{ csrf_token() }}">
        <div class="admin-followup-popup__backdrop"></div>
        <div class="admin-followup-popup__dialog">
            <div class="admin-followup-popup__eyebrow">{{ __('admin.crm_follow_up_popup_title') }}</div>
            <h3 data-followup-popup-title></h3>
            <div class="admin-followup-popup__grid">
                <div>
                    <span>{{ __('admin.full_name') }}</span>
                    <strong data-followup-popup-lead>-</strong>
                </div>
                <div>
                    <span>{{ __('admin.phone') }}</span>
                    <strong data-followup-popup-phone>-</strong>
                </div>
                <div>
                    <span>{{ __('admin.crm_follow_up_time') }}</span>
                    <strong data-followup-popup-time>-</strong>
                </div>
                <div>
                    <span>{{ __('admin.assigned_to') }}</span>
                    <strong data-followup-popup-seller>-</strong>
                </div>
            </div>
            <div class="admin-followup-popup__note">
                <span>{{ __('admin.crm_follow_up_note') }}</span>
                <p data-followup-popup-note>{{ __('admin.crm_follow_up_popup_empty_note') }}</p>
            </div>
            <div class="admin-followup-popup__quick-actions">
                <a href="#" class="btn btn-outline-secondary" data-followup-popup-call>{{ __('admin.crm_popup_call') }}</a>
                <a href="#" class="btn btn-outline-secondary" target="_blank" rel="noopener noreferrer" data-followup-popup-whatsapp>{{ __('admin.crm_popup_whatsapp') }}</a>
                <button type="button" class="btn btn-outline-secondary" data-followup-popup-open>{{ __('admin.crm_popup_open_lead') }}</button>
            </div>
            <div class="admin-followup-popup__actions">
                <button type="button" class="btn btn-outline-secondary" data-followup-popup-dismiss>{{ __('admin.crm_popup_close') }}</button>
                <div class="admin-followup-popup__snooze">
                    <select class="form-select" data-followup-popup-snooze-minutes>
                        <option value="15">{{ __('admin.crm_15_minutes') }}</option>
                        <option value="30">{{ __('admin.crm_30_minutes') }}</option>
                        <option value="60">{{ __('admin.crm_1_hour') }}</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary" data-followup-popup-snooze>{{ __('admin.crm_popup_snooze') }}</button>
                </div>
                <button type="button" class="btn btn-outline-primary" data-followup-popup-complete>{{ __('admin.mark_completed') }}</button>
                <button type="button" class="btn btn-primary" data-followup-popup-reschedule>{{ __('admin.reschedule') }}</button>
            </div>
            <div class="admin-followup-popup__footer" data-followup-popup-counter></div>
        </div>
    </div>
@endif
@if($adminUser?->hasPermission('media.manage'))
    <div class="admin-media-modal" id="admin-media-modal" hidden
         data-list-url="{{ route('admin.media-library.library') }}"
         data-upload-url="{{ route('admin.media-library.store') }}"
         data-usage-url-template="{{ route('admin.media-library.usage', ['media_library' => '__MEDIA__']) }}"
         data-csrf="{{ csrf_token() }}">
        <div class="admin-media-modal__backdrop" data-media-close></div>
        <div class="admin-media-modal__dialog">
            <div class="admin-media-modal__header">
                <div>
                    <h3>{{ __('admin.media_library') }}</h3>
                    <p>{{ __('admin.media_picker_desc') }}</p>
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm" data-media-close>{{ __('admin.cancel') }}</button>
            </div>
            <div class="admin-media-modal__toolbar">
                <input type="text" class="form-control" id="admin-media-search" placeholder="{{ __('admin.media_search_placeholder') }}">
                <label class="btn btn-outline-secondary mb-0">
                    {{ __('admin.upload_new') }}
                    <input type="file" id="admin-media-upload" hidden multiple accept="image/*,.svg">
                </label>
            </div>
            <div class="admin-media-modal__content">
                <div class="admin-media-modal__grid" id="admin-media-grid"></div>
                <aside class="admin-media-modal__details" id="admin-media-details">
                    <div class="text-muted">{{ __('admin.media_select_prompt') }}</div>
                </aside>
            </div>
            <div class="admin-media-modal__footer">
                <div class="d-flex align-items-center gap-2" id="admin-media-pagination">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="admin-media-prev">{{ __('admin.previous') }}</button>
                    <span class="small text-muted" id="admin-media-page-label">{{ __('admin.media_page_label', ['current' => 1, 'last' => 1]) }}</span>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="admin-media-next">{{ __('admin.next') }}</button>
                </div>
                <button type="button" class="btn btn-outline-secondary" data-media-close>{{ __('admin.cancel') }}</button>
                <button type="button" class="btn btn-primary" id="admin-media-confirm">{{ __('admin.confirm_selection') }}</button>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('admin-media-modal');
        if (!modal) return;
        const listUrl = modal.dataset.listUrl;
        const uploadUrl = modal.dataset.uploadUrl;
        const usageUrlTemplate = modal.dataset.usageUrlTemplate;
        const csrf = modal.dataset.csrf;
        const grid = document.getElementById('admin-media-grid');
        const details = document.getElementById('admin-media-details');
        const confirmButton = document.getElementById('admin-media-confirm');
        const searchInput = document.getElementById('admin-media-search');
        const uploadInput = document.getElementById('admin-media-upload');
        const prevPageButton = document.getElementById('admin-media-prev');
        const nextPageButton = document.getElementById('admin-media-next');
        const pageLabel = document.getElementById('admin-media-page-label');
        let pickerState = { input: null, multiple: false, selected: [] };
        let currentItems = [];
        let currentPage = 1;
        let lastPage = 1;
        const usageCache = new Map();

        const selectedText = @json(__('admin.media_select_prompt'));
        const usageLabel = @json(__('admin.usage'));
        const typeLabel = @json(__('admin.type'));
        const sizeLabel = @json(__('admin.file_size'));
        const dimensionsLabel = @json(__('admin.dimensions'));
        const createdLabel = @json(__('admin.created_date'));
        const unusedLabel = @json(__('admin.media_unused'));
        const noMediaText = @json(__('admin.no_media_assets'));
        const selectFromLibraryText = @json(__('admin.select_from_library'));
        const orUploadNewText = @json(__('admin.or_upload_new'));
        const usageLocationsText = @json(__('admin.media_usage_locations'));
        const usageLoadingText = @json(__('admin.media_usage_loading'));
        const usageErrorText = @json(__('admin.media_usage_error'));
        const usageNoneText = @json(__('admin.media_usage_none'));
        const usageOpenText = @json(__('admin.media_usage_open'));
        const usageRecordText = @json(__('admin.media_usage_record'));
        const usageCountTemplate = @json(__('admin.media_used_in', ['count' => '__COUNT__']));
        const pageLabelTemplate = @json(__('admin.media_page_label', ['current' => '__CURRENT__', 'last' => '__LAST__']));

        const escapeHtml = (value) => String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

        const ensurePreviewShell = (input) => {
            if (input.dataset.mediaEnhanced === '1') return input.parentElement.querySelector('.admin-media-picker');
            input.dataset.mediaEnhanced = '1';
            const shell = document.createElement('div');
            shell.className = 'admin-media-picker';
            shell.innerHTML = `<div class="admin-media-picker__actions"><button type="button" class="btn btn-outline-secondary btn-sm js-open-media-library">${selectFromLibraryText}</button><span class="admin-media-picker__hint">${orUploadNewText}</span></div><div class="admin-media-picker__selected"></div>`;
            input.insertAdjacentElement('afterend', shell);
            return shell;
        };

        const clearHiddenFields = (input) => {
            input.form.querySelectorAll(`[data-media-hidden-for="${input.name}"]`).forEach((field) => field.remove());
        };

        const appendHiddenField = (input, name, value) => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = name;
            hidden.value = value;
            hidden.dataset.mediaHiddenFor = input.name;
            input.form.appendChild(hidden);
        };

        const renderInlineSelection = (input, selected) => {
            const shell = ensurePreviewShell(input);
            const selectedBox = shell.querySelector('.admin-media-picker__selected');
            clearHiddenFields(input);

            if (input.multiple) {
                selected.forEach((item) => appendHiddenField(input, `${input.name.replace(/\[\]$/, '')}_existing_paths[]`, item.path));
            } else if (selected[0]) {
                appendHiddenField(input, `${input.name}_existing_path`, selected[0].path);
            }

            selectedBox.innerHTML = selected.map((item) => `<div class="admin-media-picker__item"><img src="${item.url}" alt="${item.title || item.file_name}"><div><strong>${item.title || item.file_name}</strong><span>${item.file_name}</span></div></div>`).join('');
        };

        const renderUsageEntries = (entries) => {
            if (!entries.length) {
                return `<div class="admin-media-usage-empty">${usageNoneText}</div>`;
            }

            return `<div class="admin-media-usage-list">${entries.map((entry) => {
                const action = entry.admin_url
                    ? `<a href="${entry.admin_url}" class="btn btn-outline-secondary btn-sm">${usageOpenText}</a>`
                    : '';

                return `<div class="admin-media-usage-item"><div class="admin-media-usage-item__meta"><strong>${escapeHtml(entry.source)}</strong><span>${escapeHtml(entry.field)}</span><span>${usageRecordText}: ${escapeHtml(entry.label)}</span></div>${action}</div>`;
            }).join('')}</div>`;
        };

        const renderUsagePanel = (item) => {
            if (item.usage_count < 1) {
                return `<div class="mt-3"><strong>${usageLabel}:</strong><br><span class="text-muted">${unusedLabel}</span></div>`;
            }

            const cachedUsage = usageCache.get(item.id);
            let usageContent = '';

            if (cachedUsage === 'loading') {
                usageContent = `<div class="admin-media-usage-empty">${usageLoadingText}</div>`;
            } else if (cachedUsage === 'error') {
                usageContent = `<div class="admin-media-usage-empty">${usageErrorText}</div>`;
            } else if (Array.isArray(cachedUsage)) {
                usageContent = renderUsageEntries(cachedUsage);
            }

            const usageCountText = usageCountTemplate.replace('__COUNT__', item.usage_count);
            return `<div class="mt-3"><strong>${usageLabel}:</strong><br><button type="button" class="btn btn-link p-0 admin-media-usage-trigger" data-media-usage-id="${item.id}">${usageCountText}</button><div class="mt-2"><strong>${usageLocationsText}</strong></div>${usageContent}</div>`;
        };

        const renderDetails = (item) => {
            if (!item) {
                details.innerHTML = `<div class="text-muted">${selectedText}</div>`;
                return;
            }

            const usagePanel = renderUsagePanel(item);
            details.innerHTML = `<div class="admin-media-details-card"><img src="${item.url}" alt="${escapeHtml(item.title || item.file_name)}"><h4>${escapeHtml(item.title || item.file_name)}</h4><div class="small text-muted">${escapeHtml(item.file_name)}</div><div class="small text-muted">${escapeHtml(item.path)}</div><hr><div><strong>${typeLabel}:</strong> ${escapeHtml(item.extension || '—')}</div><div><strong>${sizeLabel}:</strong> ${item.size ? `${(item.size / 1024).toFixed(1)} KB` : '—'}</div><div><strong>${dimensionsLabel}:</strong> ${escapeHtml(item.dimensions || '—')}</div><div><strong>${createdLabel}:</strong> ${escapeHtml(item.uploaded_at || '—')}</div>${usagePanel}</div>`;
        };

        const renderGrid = () => {
            if (!currentItems.length) {
                grid.innerHTML = `<div class="text-muted">${noMediaText}</div>`;
                renderPagination();
                renderDetails(null);
                return;
            }

            grid.innerHTML = currentItems.map((item) => {
                const selected = pickerState.selected.some((selectedItem) => selectedItem.path === item.path);
                return `<button type="button" class="admin-media-modal__item ${selected ? 'is-selected' : ''}" data-media-path="${item.path}"><img src="${item.url}" alt="${item.title || item.file_name}"><strong>${item.title || item.file_name}</strong><span>${item.file_name}</span></button>`;
            }).join('');
        };

        const renderPagination = () => {
            if (!pageLabel || !prevPageButton || !nextPageButton) return;
            pageLabel.textContent = pageLabelTemplate
                .replace('__CURRENT__', currentPage)
                .replace('__LAST__', lastPage);
            prevPageButton.disabled = currentPage <= 1;
            nextPageButton.disabled = currentPage >= lastPage;
        };

        const fetchMedia = async (query = '', page = 1) => {
            const url = new URL(listUrl, window.location.origin);
            if (query) url.searchParams.set('q', query);
            url.searchParams.set('page', page);
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const payload = await response.json();
            currentItems = payload.items || [];
            currentPage = payload.pagination?.current_page || 1;
            lastPage = payload.pagination?.last_page || 1;
            renderGrid();
            renderPagination();
            renderDetails(currentItems[0] || null);
        };

        const fetchUsageDetails = async (item) => {
            if (!item || item.usage_count < 1) return;
            if (Array.isArray(usageCache.get(item.id)) || usageCache.get(item.id) === 'error') {
                renderDetails(item);
                return;
            }

            usageCache.set(item.id, 'loading');
            renderDetails(item);

            try {
                const response = await fetch(usageUrlTemplate.replace('__MEDIA__', item.id), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                const payload = await response.json();
                usageCache.set(item.id, payload.items || []);
            } catch (error) {
                usageCache.set(item.id, 'error');
            }

            const currentItem = currentItems.find((entry) => entry.id === item.id) || item;
            renderDetails(currentItem);
        };

        const openModal = async (input) => {
            pickerState = { input, multiple: input.multiple, selected: [] };
            modal.hidden = false;
            document.body.classList.add('admin-media-modal-open');
            searchInput.value = '';
            await fetchMedia('', 1);
        };

        const closeModal = () => {
            modal.hidden = true;
            document.body.classList.remove('admin-media-modal-open');
        };

        document.querySelectorAll('input[type="file"]').forEach((input) => {
            if (input.id === 'admin-media-upload') return;
            ensurePreviewShell(input);
        });

        document.addEventListener('click', (event) => {
            if (event.target.matches('.js-open-media-library')) {
                const input = event.target.closest('.admin-media-picker')?.previousElementSibling;
                if (input) openModal(input);
            }

            if (event.target.matches('[data-media-close]')) {
                closeModal();
            }

            const usageTrigger = event.target.closest('.admin-media-usage-trigger');
            if (usageTrigger) {
                const item = currentItems.find((entry) => entry.id === Number(usageTrigger.dataset.mediaUsageId));
                if (item) {
                    fetchUsageDetails(item);
                }
                return;
            }

            const card = event.target.closest('.admin-media-modal__item');
            if (!card) return;
            const item = currentItems.find((entry) => entry.path === card.dataset.mediaPath);
            if (!item) return;

            if (pickerState.multiple) {
                const exists = pickerState.selected.some((selected) => selected.path === item.path);
                pickerState.selected = exists ? pickerState.selected.filter((selected) => selected.path !== item.path) : [...pickerState.selected, item];
            } else {
                pickerState.selected = [item];
            }

            renderGrid();
            renderDetails(item);
        });

        confirmButton.addEventListener('click', () => {
            if (!pickerState.input) return closeModal();
            renderInlineSelection(pickerState.input, pickerState.selected);
            closeModal();
        });

        let searchTimer = null;
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => fetchMedia(searchInput.value, 1), 250);
        });

        prevPageButton?.addEventListener('click', () => {
            if (currentPage > 1) {
                fetchMedia(searchInput.value, currentPage - 1);
            }
        });

        nextPageButton?.addEventListener('click', () => {
            if (currentPage < lastPage) {
                fetchMedia(searchInput.value, currentPage + 1);
            }
        });

        uploadInput.addEventListener('change', async () => {
            if (!uploadInput.files.length) return;
            const formData = new FormData();
            Array.from(uploadInput.files).forEach((file) => formData.append('files[]', file));

            const response = await fetch(uploadUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: formData,
            });

            if (response.ok) {
                const payload = await response.json();
                const uploaded = payload.items || [];
                pickerState.selected = pickerState.multiple ? uploaded : uploaded.slice(0, 1);
                await fetchMedia(searchInput.value, 1);
            }

            uploadInput.value = '';
        });
    });
    </script>
@endif
<script>
document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = @json(csrf_token());
    const notificationShell = document.querySelector('[data-admin-notification-shell]');
    const notificationToggle = document.querySelector('[data-admin-notification-toggle]');
    const notificationPanel = document.querySelector('[data-admin-notification-panel]');
    const readAllButtons = Array.from(document.querySelectorAll('[data-notifications-read-all]'));
    const filterButtons = Array.from(document.querySelectorAll('[data-notification-filter]'));
    const popup = document.querySelector('[data-admin-followup-popup]');

    const updateNotificationCount = (count) => {
        document.querySelectorAll('[data-admin-notification-count]').forEach((badge) => {
            badge.textContent = count;
            badge.hidden = count < 1;
        });

        document.querySelectorAll('[data-admin-notification-count-text]').forEach((badge) => {
            badge.textContent = count;
            badge.hidden = count < 1;
        });
    };

    const markNotificationRead = async (url) => {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            throw new Error(@json(__('admin.admin_notifications_mark_read_error')));
        }

        return response.json();
    };

    const setNotificationReadState = (item, isRead = true) => {
        if (!item) {
            return;
        }

        item.dataset.notificationState = isRead ? 'read' : 'unread';
        item.classList.toggle('is-unread', !isRead);
        item.classList.toggle('is-read', isRead);
        item.querySelector('[data-notification-read]')?.remove();
    };

    const applyNotificationFilter = (filter) => {
        document.querySelectorAll('[data-notification-item]').forEach((item) => {
            const state = item.dataset.notificationState || 'read';
            const matches = filter === 'all' || filter === state;
            item.hidden = !matches;
        });

        filterButtons.forEach((button) => {
            button.classList.toggle('is-active', button.dataset.notificationFilter === filter);
        });
    };

    notificationToggle?.addEventListener('click', () => {
        if (!notificationPanel) {
            return;
        }

        notificationPanel.hidden = !notificationPanel.hidden;
    });

    readAllButtons.forEach((readAllButton) => {
        readAllButton.addEventListener('click', async (event) => {
            event.preventDefault();

            try {
                const payload = await markNotificationRead(readAllButton.dataset.notificationsReadAll);
                document.querySelectorAll('[data-notification-item]').forEach((item) => setNotificationReadState(item, true));
                updateNotificationCount(payload.unread_count ?? 0);
                applyNotificationFilter(document.querySelector('[data-notification-filter].is-active')?.dataset.notificationFilter || 'all');
            } catch (error) {
                console.error(error);
            }
        });
    });

    filterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            applyNotificationFilter(button.dataset.notificationFilter || 'all');
        });
    });

    document.addEventListener('click', async (event) => {
        const readButton = event.target.closest('[data-notification-read]');
        const notificationLink = event.target.closest('[data-notification-link]');

        if (readButton) {
            event.preventDefault();

            try {
                const payload = await markNotificationRead(readButton.dataset.notificationRead);
                setNotificationReadState(readButton.closest('[data-notification-item]'), true);
                updateNotificationCount(payload.unread_count ?? 0);
                applyNotificationFilter(document.querySelector('[data-notification-filter].is-active')?.dataset.notificationFilter || 'all');
            } catch (error) {
                console.error(error);
            }

            return;
        }

        if (notificationLink) {
            const targetUrl = notificationLink.getAttribute('href');

            if (notificationLink.dataset.notificationReadUrl) {
                event.preventDefault();

                try {
                    const payload = await markNotificationRead(notificationLink.dataset.notificationReadUrl);
                    setNotificationReadState(notificationLink.closest('[data-notification-item]'), true);
                    updateNotificationCount(payload.unread_count ?? 0);
                    window.location.href = targetUrl;
                } catch (error) {
                    console.error(error);
                    window.location.href = targetUrl;
                }
            }

            return;
        }

        if (notificationShell && !notificationShell.contains(event.target)) {
            if (notificationPanel) {
                notificationPanel.hidden = true;
            }
        }
    });

    applyNotificationFilter('all');

    if (!popup) {
        return;
    }

    const queue = JSON.parse(popup.dataset.notifications || '[]');
    const popupTitle = popup.querySelector('[data-followup-popup-title]');
    const popupLead = popup.querySelector('[data-followup-popup-lead]');
    const popupPhone = popup.querySelector('[data-followup-popup-phone]');
    const popupTime = popup.querySelector('[data-followup-popup-time]');
    const popupSeller = popup.querySelector('[data-followup-popup-seller]');
    const popupNote = popup.querySelector('[data-followup-popup-note]');
    const popupCounter = popup.querySelector('[data-followup-popup-counter]');
    const dismissButton = popup.querySelector('[data-followup-popup-dismiss]');
    const completeButton = popup.querySelector('[data-followup-popup-complete]');
    const openLeadButton = popup.querySelector('[data-followup-popup-open]');
    const callButton = popup.querySelector('[data-followup-popup-call]');
    const whatsappButton = popup.querySelector('[data-followup-popup-whatsapp]');
    const snoozeButton = popup.querySelector('[data-followup-popup-snooze]');
    const snoozeMinutesField = popup.querySelector('[data-followup-popup-snooze-minutes]');
    const rescheduleButton = popup.querySelector('[data-followup-popup-reschedule]');
    const emptyNoteText = @json(__('admin.crm_follow_up_popup_empty_note'));
    const moreRemindersText = @json(__('admin.crm_follow_up_popup_more'));
    const currentSellerName = @json($adminUser?->name ?: 'Travel Wave');
    const whatsappMessage = @json(__('admin.admin_followup_whatsapp_message', ['name' => $adminUser?->name ?: 'Travel Wave']));
    let currentIndex = 0;

    const currentItem = () => queue[currentIndex] || null;
    const normalizePhone = (value) => (value || '').replace(/[^\d+]/g, '');
    const telLink = (value) => {
        const normalized = normalizePhone(value);
        return normalized ? `tel:${normalized}` : '';
    };
    const whatsappLink = (value) => {
        const normalized = normalizePhone(value).replace(/^\+/, '');
        return normalized ? `https://wa.me/${normalized}?text=${encodeURIComponent(whatsappMessage)}` : '';
    };

    const renderPopup = () => {
        const item = currentItem();

        if (!item) {
            popup.hidden = true;
            return;
        }

        popup.hidden = false;
        popupTitle.textContent = item.title || '';
        popupLead.textContent = item.lead_name || '-';
        popupPhone.textContent = item.phone || '-';
        popupTime.textContent = item.scheduled_at ? new Date(item.scheduled_at).toLocaleString() : '-';
        popupSeller.textContent = item.assigned_user_name || '-';
        popupNote.textContent = item.note || emptyNoteText;
        popupCounter.textContent = queue.length > 1 ? moreRemindersText.replace(':count', String(queue.length - currentIndex)) : '';
        completeButton.hidden = !item.follow_up_update_url;
        callButton.href = telLink(item.phone);
        callButton.classList.toggle('disabled', !callButton.href);
        whatsappButton.href = whatsappLink(item.whatsapp_number || item.phone);
        whatsappButton.classList.toggle('disabled', !whatsappButton.href);
    };

    const advancePopup = () => {
        currentIndex += 1;
        renderPopup();
    };

    dismissButton?.addEventListener('click', async () => {
        const item = currentItem();
        if (!item) {
            return;
        }

        try {
            const payload = await markNotificationRead(item.read_url);
            updateNotificationCount(payload.unread_count ?? 0);
            advancePopup();
        } catch (error) {
            console.error(error);
        }
    });

    openLeadButton?.addEventListener('click', () => {
        const item = currentItem();
        if (!item?.url) {
            return;
        }

        window.location.href = item.url;
    });

    rescheduleButton?.addEventListener('click', () => {
        const item = currentItem();
        if (!item?.url) {
            return;
        }

        window.location.href = item.url;
    });

    completeButton?.addEventListener('click', async () => {
        const item = currentItem();

        if (!item?.follow_up_update_url) {
            return;
        }

        const body = new FormData();
        body.append('_method', 'PUT');
        body.append('action', 'complete');
        body.append('completion_note', @json(__('admin.crm_follow_up_completed_from_popup')));

        try {
            const response = await fetch(item.follow_up_update_url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body,
            });

            if (!response.ok) {
                throw new Error(@json(__('admin.admin_followup_complete_error')));
            }

            const payload = await markNotificationRead(item.read_url);
            updateNotificationCount(payload.unread_count ?? 0);
            advancePopup();
        } catch (error) {
            console.error(error);
            window.location.href = item.url;
        }
    });

    snoozeButton?.addEventListener('click', async () => {
        const item = currentItem();

        if (!item?.follow_up_update_url) {
            return;
        }

        const body = new FormData();
        body.append('_method', 'PUT');
        body.append('action', 'snooze');
        body.append('snooze_minutes', snoozeMinutesField?.value || '15');

        try {
            const response = await fetch(item.follow_up_update_url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body,
            });

            if (!response.ok) {
                throw new Error(@json(__('admin.admin_followup_snooze_error')));
            }

            const payload = await markNotificationRead(item.read_url);
            updateNotificationCount(payload.unread_count ?? 0);
            advancePopup();
        } catch (error) {
            console.error(error);
        }
    });

    renderPopup();
});
</script>
</body>
</html>

