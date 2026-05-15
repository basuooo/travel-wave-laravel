@extends('layouts.admin')

@section('page_title', $item->exists ? 'Edit Hero Slide' : 'Create Hero Slide')
@section('page_description', 'Manage desktop and mobile banner images, bilingual content, CTA text, ordering, and activation state.')

@section('content')
@php($desktopPreviewUrl = $item->image_path ? asset('storage/' . $item->image_path) : null)
@php($mobilePreviewUrl = $item->mobile_image_path ? asset('storage/' . $item->mobile_image_path) : $desktopPreviewUrl)
@php($mobileGuideRatio = 3 / 4)
@php($desktopFraming = old('image_framing.desktop_banner', $item->framingFor('desktop_banner')))
@php($mobileFraming = old('image_framing.mobile_banner', $item->framingFor('mobile_banner')))
<form method="post" enctype="multipart/form-data" action="{{ $item->exists ? route('admin.hero-slides.update', $item) : route('admin.hero-slides.store') }}">
    @csrf
    @if($item->exists)
        @method('PUT')
    @endif

    <input type="hidden" name="image_framing[desktop_banner][x]" value="{{ data_get($desktopFraming, 'x', 50) }}" data-frame-input="desktop_banner:x">
    <input type="hidden" name="image_framing[desktop_banner][y]" value="{{ data_get($desktopFraming, 'y', 50) }}" data-frame-input="desktop_banner:y">
    <input type="hidden" name="image_framing[mobile_banner][x]" value="{{ data_get($mobileFraming, 'x', 50) }}" data-frame-input="mobile_banner:x">
    <input type="hidden" name="image_framing[mobile_banner][y]" value="{{ data_get($mobileFraming, 'y', 50) }}" data-frame-input="mobile_banner:y">

    <div class="card admin-card p-4">
        <div class="row g-4">
            <div class="col-lg-6">
                <label class="form-label">Desktop Banner Image</label>
                <input type="file" class="form-control" name="image" accept="image/*">
                <div class="form-text">Desktop reference size: 1204 x 800. This image is used on desktop and also as the fallback if no dedicated mobile banner is provided.</div>
                <div class="admin-banner-preview mt-3 {{ $desktopPreviewUrl ? '' : 'is-empty' }}" data-banner-preview="desktop" data-frame-target="desktop_banner">
                    <div class="admin-banner-preview__frame" data-frame-surface="desktop_banner">
                        @if($desktopPreviewUrl)
                            <img src="{{ $desktopPreviewUrl }}" alt="{{ $item->headline_en }}" class="admin-banner-preview__image" data-frame-image="desktop_banner" style="object-position: {{ data_get($desktopFraming, 'x', 50) }}% {{ data_get($desktopFraming, 'y', 50) }}%;">
                        @else
                            <div class="admin-banner-preview__empty">No desktop banner selected yet.</div>
                        @endif
                        <div class="admin-frame-positioner__badge" data-frame-badge="desktop_banner" @if(!$desktopPreviewUrl) hidden @endif>Drag to position</div>
                    </div>
                    <div class="admin-banner-preview__meta">
                        <span>Desktop Banner</span>
                        <strong>1204 x 800 reference</strong>
                    </div>
                    <div class="admin-frame-positioner__actions">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-frame-reset="desktop_banner" @if(!$desktopPreviewUrl) hidden @endif>Center Image</button>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <label class="form-label">Mobile Banner Image</label>
                <input type="file" class="form-control" name="mobile_image" accept="image/*">
                <div class="form-text">Recommended mobile banner size: 900 x 1200 (3:4). If the image already matches this ratio it will appear fully; if it is wider or taller, the safe-area frame below shows the mobile visible area.</div>
                <div class="admin-mobile-banner-preview mt-3 {{ $mobilePreviewUrl ? '' : 'is-empty' }}" data-mobile-banner-preview data-guide-ratio="{{ $mobileGuideRatio }}" data-frame-target="mobile_banner">
                    <div class="admin-mobile-banner-preview__frame" data-frame-surface="mobile_banner">
                        @if($mobilePreviewUrl)
                            <img src="{{ $mobilePreviewUrl }}" alt="{{ $item->headline_en }}" class="admin-mobile-banner-preview__image" data-mobile-banner-image data-frame-image="mobile_banner" style="object-position: {{ data_get($mobileFraming, 'x', 50) }}% {{ data_get($mobileFraming, 'y', 50) }}%;">
                        @else
                            <div class="admin-mobile-banner-preview__empty">No mobile banner selected yet. Desktop banner will be used as fallback on mobile.</div>
                        @endif
                        <div class="admin-mobile-banner-preview__safe-area" data-mobile-safe-area hidden>
                            <span>Mobile safe area</span>
                        </div>
                        <div class="admin-frame-positioner__badge" data-frame-badge="mobile_banner" @if(!$mobilePreviewUrl) hidden @endif>Drag to position</div>
                    </div>
                    <div class="admin-mobile-banner-preview__footer">
                        <strong data-mobile-guide-title>Mobile Preview</strong>
                        <span data-mobile-guide-text>
                            @if($item->mobile_image_path)
                                Dedicated mobile banner loaded.
                            @elseif($item->image_path)
                                Using desktop banner as current mobile fallback.
                            @else
                                Select a mobile banner or upload one from Media Library.
                            @endif
                        </span>
                    </div>
                    <div class="admin-frame-positioner__actions">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-frame-reset="mobile_banner" @if(!$mobilePreviewUrl) hidden @endif>Center Image</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">English Title</label>
                <input class="form-control" name="headline_en" value="{{ old('headline_en', $item->headline_en) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Arabic Title</label>
                <input class="form-control text-end" dir="rtl" name="headline_ar" value="{{ old('headline_ar', $item->headline_ar) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">English Subtitle</label>
                <textarea class="form-control" name="subtitle_en" rows="4">{{ old('subtitle_en', $item->subtitle_en) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Arabic Subtitle</label>
                <textarea class="form-control text-end" dir="rtl" name="subtitle_ar" rows="4">{{ old('subtitle_ar', $item->subtitle_ar) }}</textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label">English Button Text</label>
                <input class="form-control" name="cta_text_en" value="{{ old('cta_text_en', $item->cta_text_en) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Arabic Button Text</label>
                <input class="form-control text-end" dir="rtl" name="cta_text_ar" value="{{ old('cta_text_ar', $item->cta_text_ar) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Button Link</label>
                <input class="form-control" name="cta_link" value="{{ old('cta_link', $item->cta_link) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sort Order</label>
                <input class="form-control" type="number" name="sort_order" value="{{ old('sort_order', $item->sort_order) }}" required>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="form-check pb-2">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $item->is_active ?? true))>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
        </div>
    </div>
    <button class="btn btn-primary mt-3 px-4">Save Slide</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const desktopInput = document.querySelector('input[name="image"]');
    const mobileInput = document.querySelector('input[name="mobile_image"]');
    const desktopPreview = document.querySelector('[data-banner-preview="desktop"]');
    const mobilePreview = document.querySelector('[data-mobile-banner-preview]');
    const desktopFrame = desktopPreview?.querySelector('[data-frame-surface="desktop_banner"]');
    const mobileFrame = mobilePreview?.querySelector('[data-frame-surface="mobile_banner"]');
    const frameConfigs = {
        desktop_banner: {
            preview: desktopPreview,
            frame: desktopFrame,
            image: () => document.querySelector('[data-frame-image="desktop_banner"]'),
            xInput: document.querySelector('[data-frame-input="desktop_banner:x"]'),
            yInput: document.querySelector('[data-frame-input="desktop_banner:y"]'),
            badge: document.querySelector('[data-frame-badge="desktop_banner"]'),
            resetButton: document.querySelector('[data-frame-reset="desktop_banner"]'),
        },
        mobile_banner: {
            preview: mobilePreview,
            frame: mobileFrame,
            image: () => document.querySelector('[data-frame-image="mobile_banner"]'),
            xInput: document.querySelector('[data-frame-input="mobile_banner:x"]'),
            yInput: document.querySelector('[data-frame-input="mobile_banner:y"]'),
            badge: document.querySelector('[data-frame-badge="mobile_banner"]'),
            resetButton: document.querySelector('[data-frame-reset="mobile_banner"]'),
        },
    };
    const mobileImage = () => frameConfigs.mobile_banner.image();
    const mobileSafeArea = mobilePreview?.querySelector('[data-mobile-safe-area]');
    const mobileGuideTitle = mobilePreview?.querySelector('[data-mobile-guide-title]');
    const mobileGuideText = mobilePreview?.querySelector('[data-mobile-guide-text]');
    const mobileGuideRatio = parseFloat(mobilePreview?.dataset.guideRatio || '0.75');
    const storageUrl = (path) => new URL(`/storage/${String(path || '').replace(/^storage\//, '')}`, window.location.origin).toString();
    const clamp = (value, min, max) => Math.min(max, Math.max(min, value));
    let activeDrag = null;

    const ensureImage = (container, className, targetKey = null) => {
        let image = container?.querySelector(`.${className}`);
        if (!image && container) {
            image = document.createElement('img');
            image.className = className;
            if (className === 'admin-mobile-banner-preview__image') {
                image.dataset.mobileBannerImage = 'true';
            }
            if (targetKey) {
                image.dataset.frameImage = targetKey;
            }
            container.querySelector('.admin-mobile-banner-preview__empty, .admin-banner-preview__empty')?.remove();
            container.prepend(image);
        }

        return image;
    };

    const getFramePosition = (targetKey) => {
        const config = frameConfigs[targetKey];

        return {
            x: parseFloat(config?.xInput?.value || '50'),
            y: parseFloat(config?.yInput?.value || '50'),
        };
    };

    const setFramePosition = (targetKey, x, y) => {
        const config = frameConfigs[targetKey];
        if (!config) return;

        const safeX = clamp(Number.isFinite(x) ? x : 50, 0, 100);
        const safeY = clamp(Number.isFinite(y) ? y : 50, 0, 100);

        if (config.xInput) config.xInput.value = safeX.toFixed(2);
        if (config.yInput) config.yInput.value = safeY.toFixed(2);

        const image = config.image();
        if (image) {
            image.style.objectPosition = `${safeX}% ${safeY}%`;
        }
    };

    const showFrameControls = (targetKey, isVisible) => {
        const config = frameConfigs[targetKey];
        if (!config) return;

        if (config.badge) config.badge.hidden = !isVisible;
        if (config.resetButton) config.resetButton.hidden = !isVisible;
    };

    const beginDrag = (targetKey, event) => {
        const config = frameConfigs[targetKey];
        if (!config?.frame || !config.image()) return;

        event.preventDefault();
        activeDrag = { targetKey };
        config.frame.classList.add('is-positioning');
        updateDragPosition(event);
    };

    const endDrag = () => {
        if (!activeDrag) return;
        const config = frameConfigs[activeDrag.targetKey];
        config?.frame?.classList.remove('is-positioning');
        activeDrag = null;
    };

    const updateDragPosition = (event) => {
        if (!activeDrag) return;
        if (event.cancelable) {
            event.preventDefault();
        }

        const config = frameConfigs[activeDrag.targetKey];
        if (!config?.frame) return;

        const rect = config.frame.getBoundingClientRect();
        const point = event.touches?.[0] || event;
        const x = ((point.clientX - rect.left) / rect.width) * 100;
        const y = ((point.clientY - rect.top) / rect.height) * 100;

        setFramePosition(activeDrag.targetKey, x, y);
    };

    const setDesktopPreview = (src) => {
        if (!desktopPreview) return;
        if (!src) {
            desktopPreview.classList.add('is-empty');
            showFrameControls('desktop_banner', false);
            return;
        }

        desktopPreview.classList.remove('is-empty');
        const img = ensureImage(desktopFrame, 'admin-banner-preview__image', 'desktop_banner');
        if (img) {
            img.src = src;
            setFramePosition('desktop_banner', 50, 50);
        }
        showFrameControls('desktop_banner', true);
    };

    const syncMobileGuide = (img, sourceLabel) => {
        if (!img || !mobileSafeArea || !mobileGuideText || !mobileGuideTitle) return;

        const naturalWidth = img.naturalWidth || 0;
        const naturalHeight = img.naturalHeight || 0;
        if (!naturalWidth || !naturalHeight) return;

        const ratio = naturalWidth / naturalHeight;
        const matchesRatio = Math.abs(ratio - mobileGuideRatio) < 0.03;

        mobileGuideTitle.textContent = sourceLabel;

        if (matchesRatio) {
            mobileSafeArea.hidden = true;
            mobileGuideText.textContent = `This image matches the recommended 3:4 mobile ratio (${naturalWidth} x ${naturalHeight}) and will display fully.`;
            return;
        }

        mobileSafeArea.hidden = false;
        mobileGuideText.textContent = `Visible mobile area guide shown. Current image ratio is ${naturalWidth} x ${naturalHeight}; the highlighted frame approximates what will appear on mobile.`;
    };

    const setMobilePreview = (src, sourceLabel) => {
        if (!mobilePreview || !mobileFrame || !mobileGuideText) return;
        if (!src) {
            mobilePreview.classList.add('is-empty');
            if (mobileSafeArea) mobileSafeArea.hidden = true;
            mobileGuideTitle.textContent = 'Mobile Preview';
            mobileGuideText.textContent = 'Select a mobile banner or upload one from Media Library.';
            showFrameControls('mobile_banner', false);
            return;
        }

        mobilePreview.classList.remove('is-empty');
        const img = ensureImage(mobileFrame, 'admin-mobile-banner-preview__image', 'mobile_banner');
        if (!img) return;
        img.onload = () => syncMobileGuide(img, sourceLabel);
        img.src = src;
        setFramePosition('mobile_banner', 50, 50);
        showFrameControls('mobile_banner', true);
    };

    const readFilePreview = (input, callback) => {
        const file = input?.files?.[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (event) => callback(event.target?.result || '');
        reader.readAsDataURL(file);
    };

    desktopInput?.addEventListener('change', () => {
        readFilePreview(desktopInput, (src) => {
            setDesktopPreview(src);
            if (!document.querySelector('input[name="mobile_image_existing_path"]') && !mobileInput?.files?.length) {
                const mobilePosition = getFramePosition('mobile_banner');
                setMobilePreview(src, 'Mobile Preview (desktop fallback)');
                setFramePosition('mobile_banner', mobilePosition.x, mobilePosition.y);
            }
        });
    });

    mobileInput?.addEventListener('change', () => {
        readFilePreview(mobileInput, (src) => setMobilePreview(src, 'Mobile Preview (selected file)'));
    });

    const observeHiddenField = (fieldName, onChange) => {
        const form = document.querySelector('form');
        if (!form) return;

        const apply = () => {
            const hidden = form.querySelector(`input[name="${fieldName}"]`);
            if (!hidden || !hidden.value) return;
            onChange(hidden.value);
        };

        const observer = new MutationObserver(apply);
        observer.observe(form, { childList: true, subtree: true });
        apply();
    };

    observeHiddenField('image_existing_path', (path) => {
        const src = storageUrl(path);
        setDesktopPreview(src);

        if (!document.querySelector('input[name="mobile_image_existing_path"]') && !mobileInput?.files?.length) {
            const mobilePosition = getFramePosition('mobile_banner');
            setMobilePreview(src, 'Mobile Preview (desktop fallback)');
            setFramePosition('mobile_banner', mobilePosition.x, mobilePosition.y);
        }
    });
    observeHiddenField('mobile_image_existing_path', (path) => setMobilePreview(storageUrl(path), 'Mobile Preview (Media Library)'));

    Object.entries(frameConfigs).forEach(([targetKey, config]) => {
        setFramePosition(targetKey, getFramePosition(targetKey).x, getFramePosition(targetKey).y);

        config.frame?.addEventListener('pointerdown', (event) => beginDrag(targetKey, event));
        config.frame?.addEventListener('touchstart', (event) => beginDrag(targetKey, event), { passive: false });
        config.resetButton?.addEventListener('click', () => setFramePosition(targetKey, 50, 50));
        showFrameControls(targetKey, !!config.image());
    });

    window.addEventListener('pointermove', updateDragPosition);
    window.addEventListener('pointerup', endDrag);
    window.addEventListener('pointercancel', endDrag);
    window.addEventListener('touchmove', updateDragPosition, { passive: false });
    window.addEventListener('touchend', endDrag);

    const initialMobileImage = mobileImage();
    if (initialMobileImage && initialMobileImage.complete) {
        syncMobileGuide(initialMobileImage, '{{ $item->mobile_image_path ? 'Mobile Preview' : 'Mobile Preview (desktop fallback)' }}');
    } else if (initialMobileImage) {
        initialMobileImage.addEventListener('load', () => syncMobileGuide(initialMobileImage, '{{ $item->mobile_image_path ? 'Mobile Preview' : 'Mobile Preview (desktop fallback)' }}'), { once: true });
    }

    const initialDesktopImage = frameConfigs.desktop_banner.image();
    if (initialDesktopImage) {
        setFramePosition('desktop_banner', getFramePosition('desktop_banner').x, getFramePosition('desktop_banner').y);
    }
    if (initialMobileImage) {
        setFramePosition('mobile_banner', getFramePosition('mobile_banner').x, getFramePosition('mobile_banner').y);
    }
});
</script>
@endsection
