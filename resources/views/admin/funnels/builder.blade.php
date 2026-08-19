<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>⚡ Builder | {{ $funnel->name }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap 5 & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>

    <style>
        :root {
            --fb-topbar-h: 60px;
            --fb-sidebar-w: 320px;
            --fb-inspector-w: 360px;
            --fb-bg-dark: #0f172a;
            --fb-panel-bg: #1e293b;
            --fb-border: #334155;
            --fb-accent: #3b82f6;
        }

        * { box-sizing: border-box; }
        body, html { height: 100%; margin: 0; padding: 0; overflow: hidden; font-family: system-ui, -apple-system, sans-serif; background: #090d16; color: #f8fafc; }

        /* Topbar */
        .fb-topbar {
            height: var(--fb-topbar-h);
            background: #030712;
            border-bottom: 1px solid var(--fb-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            z-index: 100;
        }

        /* Main Workspace Layout */
        .fb-workspace {
            display: flex;
            height: calc(100vh - var(--fb-topbar-h));
        }

        /* Left Panel */
        .fb-sidebar {
            width: var(--fb-sidebar-w);
            background: var(--fb-panel-bg);
            border-right: 1px solid var(--fb-border);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        /* Canvas Center */
        .fb-canvas-container {
            flex: 1;
            background: #090d16;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 2rem;
            overflow-y: auto;
        }

        .fb-canvas {
            width: 100%;
            max-width: 680px;
            background: #ffffff;
            color: #0f172a;
            border-radius: 16px;
            min-height: 520px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            transition: all 0.3s ease;
        }

        /* Right Panel Inspector */
        .fb-inspector {
            width: var(--fb-inspector-w);
            background: var(--fb-panel-bg);
            border-left: 1px solid var(--fb-border);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        /* UI Components */
        .fb-step-item {
            padding: 0.75rem 1rem;
            background: #1e293b;
            border: 1px solid var(--fb-border);
            border-radius: 8px;
            margin-bottom: 0.5rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s;
        }
        .fb-step-item.active {
            border-color: var(--fb-accent);
            background: #2563eb22;
        }

        .fb-element-badge {
            background: #334155;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            cursor: grab;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            font-size: 0.85rem;
        }
        .fb-element-badge:hover {
            background: #475569;
        }

        .canvas-element-item {
            border: 2px dashed transparent;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            position: relative;
            cursor: pointer;
        }
        .canvas-element-item:hover, .canvas-element-item.selected {
            border-color: var(--fb-accent);
            background: #eff6ff22;
        }
    </style>
</head>
<body>

<!-- TOPBAR -->
<header class="fb-topbar">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.funnels.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            ← Dashboard
        </a>
        <div>
            <h6 class="mb-0 fw-bold text-white" id="display_funnel_name">{{ $funnel->name }}</h6>
            <small class="text-muted">/f/{{ $funnel->slug }}</small>
        </div>
    </div>

    <!-- Viewport Switcher -->
    <div class="btn-group btn-group-sm">
        <button type="button" class="btn btn-outline-secondary active" onclick="setDevice('desktop')">🖥️ Desktop</button>
        <button type="button" class="btn btn-outline-secondary" onclick="setDevice('mobile')">📱 Mobile</button>
    </div>

    <!-- Actions -->
    <div class="d-flex align-items-center gap-2">
        <a href="{{ $funnel->publicUrl() }}?preview=1" target="_blank" class="btn btn-outline-light btn-sm">
            👁️ Preview
        </a>
        <button type="button" class="btn btn-primary btn-sm fw-bold px-3" onclick="saveFunnel()">
            💾 Save Funnel
        </button>
        @if($funnel->status === 'published')
            <span class="badge bg-success">Published</span>
        @else
            <form action="{{ route('admin.funnels.publish', $funnel) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success btn-sm fw-bold">🚀 Publish</button>
            </form>
        @endif
    </div>
</header>

<!-- WORKSPACE -->
<div class="fb-workspace">
    <!-- LEFT PANEL: Steps & Drag Elements -->
    <aside class="fb-sidebar p-3">
        <h6 class="text-uppercase text-muted fw-bold small mb-3">📌 Funnel Steps</h6>
        <div id="steps_list_wrapper">
            @foreach($funnel->steps as $step)
                <div class="fb-step-item {{ $loop->first ? 'active' : '' }}" data-step-id="{{ $step->id }}" onclick="selectStep({{ $step->id }})">
                    <span class="fw-bold small">{{ $step->title }}</span>
                    <span class="badge bg-dark text-muted">{{ $step->step_type }}</span>
                </div>
            @endforeach
        </div>
        <button type="button" class="btn btn-outline-primary btn-sm w-100 mt-2 mb-4" onclick="addNewStep()">
            ➕ Add Step
        </button>

        <h6 class="text-uppercase text-muted fw-bold small mb-3">🧩 Add Elements</h6>
        <div class="row g-2">
            <div class="col-6">
                <div class="fb-element-badge" onclick="addElementToCurrentStep('single_choice')">
                    🔘 Single Choice
                </div>
            </div>
            <div class="col-6">
                <div class="fb-element-badge" onclick="addElementToCurrentStep('multiple_choice')">
                    ☑️ Multi Choice
                </div>
            </div>
            <div class="col-6">
                <div class="fb-element-badge" onclick="addElementToCurrentStep('text')">
                    📝 Heading/Text
                </div>
            </div>
            <div class="col-6">
                <div class="fb-element-badge" onclick="addElementToCurrentStep('contact_form')">
                    📋 Lead Form
                </div>
            </div>
            <div class="col-6">
                <div class="fb-element-badge" onclick="addElementToCurrentStep('rating')">
                    ⭐ Rating
                </div>
            </div>
            <div class="col-6">
                <div class="fb-element-badge" onclick="addElementToCurrentStep('button')">
                    🔘 Button
                </div>
            </div>
        </div>
    </aside>

    <!-- CENTER CANVAS -->
    <main class="fb-canvas-container">
        <div class="fb-canvas" id="canvas_container">
            <div class="text-center text-muted py-5">
                <h4>Loading canvas...</h4>
            </div>
        </div>
    </main>

    <!-- RIGHT PANEL: Properties Inspector -->
    <aside class="fb-inspector p-3">
        <!-- Tabs -->
        <ul class="nav nav-pills nav-justified mb-3" id="inspector_tabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active btn-sm" data-bs-toggle="tab" data-bs-target="#tab_element">Element</button>
            </li>
            <li class="nav-item">
                <button class="nav-link btn-sm" data-bs-toggle="tab" data-bs-target="#tab_crm">CRM</button>
            </li>
            <li class="nav-item">
                <button class="nav-link btn-sm" data-bs-toggle="tab" data-bs-target="#tab_tracking">Pixels</button>
            </li>
        </ul>

        <div class="tab-content" id="inspector_tab_content">
            <!-- Element Tab -->
            <div class="tab-pane fade show active" id="tab_element">
                <div id="element_inspector_wrapper">
                    <p class="text-muted small">Select an element on canvas to edit properties.</p>
                </div>
            </div>

            <!-- CRM Tab -->
            <div class="tab-pane fade" id="tab_crm">
                <h6 class="fw-bold mb-3">Travel Wave CRM Integration</h6>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="crm_enabled_checkbox" {{ ($funnel->crm_settings['enabled'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold" for="crm_enabled_checkbox">Send Leads to CRM</label>
                </div>

                <div class="mb-3">
                    <label class="form-label small">CRM Lead Source</label>
                    <select class="form-select btn-sm" id="crm_source_select">
                        <option value="">Auto Detect (Interactive Funnel)</option>
                        @foreach($leadSources as $ls)
                            <option value="{{ $ls->id }}">{{ $ls->name_en }} / {{ $ls->name_ar }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small">CRM Service Type</label>
                    <select class="form-select btn-sm" id="crm_service_type_select">
                        <option value="">Select Service Type</option>
                        @foreach($serviceTypes as $st)
                            <option value="{{ $st->id }}">{{ $st->name_en }} / {{ $st->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Tracking Pixels Tab -->
            <div class="tab-pane fade" id="tab_tracking">
                <h6 class="fw-bold mb-3">Pixel & Event Tracking</h6>
                <div class="mb-3">
                    <label class="form-label small">Meta (Facebook) Pixel ID</label>
                    <input type="text" class="form-control form-control-sm" id="meta_pixel_input" value="{{ $funnel->tracking_settings['meta_pixel_id'] ?? '' }}" placeholder="e.g. 1234567890">
                </div>

                <div class="mb-3">
                    <label class="form-label small">Google Analytics 4 (GA4) ID</label>
                    <input type="text" class="form-control form-control-sm" id="ga4_input" value="{{ $funnel->tracking_settings['ga4_id'] ?? '' }}" placeholder="e.g. G-XXXXXXXXXX">
                </div>

                <div class="mb-3">
                    <label class="form-label small">TikTok Pixel ID</label>
                    <input type="text" class="form-control form-control-sm" id="tiktok_input" value="{{ $funnel->tracking_settings['tiktok_pixel_id'] ?? '' }}" placeholder="e.g. CXXXXXXXXXX">
                </div>

                <div class="mb-3">
                    <label class="form-label small">Snapchat Pixel ID</label>
                    <input type="text" class="form-control form-control-sm" id="snap_input" value="{{ $funnel->tracking_settings['snap_pixel_id'] ?? '' }}" placeholder="e.g. xxxxxxxx-xxxx">
                </div>
            </div>
        </div>
    </aside>
</div>

<!-- Bootstrap JS & Builder Engine JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const funnelData = @json($funnel);
    let activeStepId = funnelData.steps.length > 0 ? funnelData.steps[0].id : null;

    function renderCanvas() {
        const canvas = document.getElementById('canvas_container');
        const currentStep = funnelData.steps.find(s => s.id === activeStepId);

        if (!currentStep) {
            canvas.innerHTML = '<div class="text-center text-muted py-5">No step selected</div>';
            return;
        }

        let html = `<h2 class="fw-bold mb-1">${currentStep.title}</h2>`;
        if (currentStep.subtitle) {
            html += `<p class="text-muted mb-4">${currentStep.subtitle}</p>`;
        }

        if (currentStep.elements && currentStep.elements.length > 0) {
            currentStep.elements.forEach(el => {
                html += `<div class="canvas-element-item" onclick="inspectElement(${el.id})">`;
                html += `<label class="fw-bold mb-2">${el.label || 'Question'}</label>`;

                if (el.element_type === 'single_choice' && el.properties?.options) {
                    html += '<div class="d-flex flex-column gap-2">';
                    el.properties.options.forEach(opt => {
                        html += `<button type="button" class="btn btn-outline-primary text-start">${opt.label || opt.value}</button>`;
                    });
                    html += '</div>';
                } else if (el.element_type === 'contact_form') {
                    html += '<input type="text" class="form-control mb-2" placeholder="Full Name">';
                    html += '<input type="text" class="form-control mb-2" placeholder="Phone Number">';
                } else {
                    html += '<input type="text" class="form-control" placeholder="Answer here...">';
                }
                html += '</div>';
            });
        } else {
            html += '<div class="alert alert-light border text-center my-4">Step is empty. Click elements on left sidebar to add questions.</div>';
        }

        canvas.innerHTML = html;
    }

    function selectStep(stepId) {
        activeStepId = stepId;
        document.querySelectorAll('.fb-step-item').forEach(el => {
            el.classList.toggle('active', parseInt(el.dataset.stepId) === stepId);
        });
        renderCanvas();
    }

    function saveFunnel() {
        const payload = {
            name: funnelData.name,
            slug: funnelData.slug,
            design_settings: funnelData.design_settings,
            crm_settings: {
                enabled: document.getElementById('crm_enabled_checkbox').checked,
                source_id: document.getElementById('crm_source_select').value,
                service_type_id: document.getElementById('crm_service_type_select').value,
            },
            tracking_settings: {
                meta_pixel_id: document.getElementById('meta_pixel_input').value,
                ga4_id: document.getElementById('ga4_input').value,
                tiktok_pixel_id: document.getElementById('tiktok_input').value,
                snap_pixel_id: document.getElementById('snap_input').value,
            },
            steps: funnelData.steps,
            results: funnelData.results,
        };

        fetch(`{{ route('admin.funnels.builder.update', $funnel) }}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Funnel saved successfully! 🎉');
            } else {
                alert('Save failed: ' + (data.message || 'Error'));
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderCanvas();
    });
</script>
</body>
</html>
