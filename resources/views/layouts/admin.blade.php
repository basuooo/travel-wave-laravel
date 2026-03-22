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
        'title' => __('admin.nav_marketing'),
        'items' => array_values(array_filter([
            $adminUser?->hasPermission('marketing.manage') ? ['label' => __('admin.marketing_manager'), 'route' => 'admin.marketing-landing-pages.index', 'match' => 'admin.marketing-landing-pages.*'] : null,
            $adminUser?->hasPermission('tracking.manage') ? ['label' => __('admin.tracking_manager'), 'route' => 'admin.tracking-integrations.index', 'match' => 'admin.tracking-integrations.*'] : null,
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
@if($adminUser?->hasPermission('media.manage'))
    <div class="admin-media-modal" id="admin-media-modal" hidden
         data-list-url="{{ route('admin.media-library.library') }}"
         data-upload-url="{{ route('admin.media-library.store') }}"
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
        const csrf = modal.dataset.csrf;
        const grid = document.getElementById('admin-media-grid');
        const details = document.getElementById('admin-media-details');
        const confirmButton = document.getElementById('admin-media-confirm');
        const searchInput = document.getElementById('admin-media-search');
        const uploadInput = document.getElementById('admin-media-upload');
        let pickerState = { input: null, multiple: false, selected: [] };
        let currentItems = [];

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

        const renderDetails = (item) => {
            if (!item) {
                details.innerHTML = `<div class="text-muted">${selectedText}</div>`;
                return;
            }

            const usageText = (item.usage || []).length ? item.usage.map((entry) => `${entry.source} · ${entry.field} (${entry.count})`).join('<br>') : unusedLabel;
            details.innerHTML = `<div class="admin-media-details-card"><img src="${item.url}" alt="${item.title || item.file_name}"><h4>${item.title || item.file_name}</h4><div class="small text-muted">${item.file_name}</div><div class="small text-muted">${item.path}</div><hr><div><strong>${typeLabel}:</strong> ${item.extension || '—'}</div><div><strong>${sizeLabel}:</strong> ${item.size ? (item.size / 1024).toFixed(1) + ' KB' : '—'}</div><div><strong>${dimensionsLabel}:</strong> ${item.dimensions || '—'}</div><div><strong>${createdLabel}:</strong> ${item.uploaded_at || '—'}</div><div class="mt-3"><strong>${usageLabel}:</strong><br>${usageText}</div></div>`;
        };

        const renderGrid = () => {
            if (!currentItems.length) {
                grid.innerHTML = `<div class="text-muted">${noMediaText}</div>`;
                renderDetails(null);
                return;
            }

            grid.innerHTML = currentItems.map((item) => {
                const selected = pickerState.selected.some((selectedItem) => selectedItem.path === item.path);
                return `<button type="button" class="admin-media-modal__item ${selected ? 'is-selected' : ''}" data-media-path="${item.path}"><img src="${item.url}" alt="${item.title || item.file_name}"><strong>${item.title || item.file_name}</strong><span>${item.file_name}</span></button>`;
            }).join('');
        };

        const fetchMedia = async (query = '') => {
            const url = new URL(listUrl, window.location.origin);
            if (query) url.searchParams.set('q', query);
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const payload = await response.json();
            currentItems = payload.items || [];
            renderGrid();
            renderDetails(currentItems[0] || null);
        };

        const openModal = async (input) => {
            pickerState = { input, multiple: input.multiple, selected: [] };
            modal.hidden = false;
            document.body.classList.add('admin-media-modal-open');
            searchInput.value = '';
            await fetchMedia();
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
            searchTimer = setTimeout(() => fetchMedia(searchInput.value), 250);
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
                await fetchMedia(searchInput.value);
            }

            uploadInput.value = '';
        });
    });
    </script>
@endif
</body>
</html>
