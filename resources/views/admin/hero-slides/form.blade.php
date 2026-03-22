@extends('layouts.admin')

@section('page_title', $item->exists ? 'Edit Hero Slide' : 'Create Hero Slide')
@section('page_description', 'Manage desktop and mobile banner images, bilingual content, CTA text, ordering, and activation state.')

@section('content')
@php($desktopPreviewUrl = $item->image_path ? asset('storage/' . $item->image_path) : null)
@php($mobilePreviewUrl = $item->mobile_image_path ? asset('storage/' . $item->mobile_image_path) : $desktopPreviewUrl)
@php($mobileGuideRatio = 3 / 4)
<form method="post" enctype="multipart/form-data" action="{{ $item->exists ? route('admin.hero-slides.update', $item) : route('admin.hero-slides.store') }}">
    @csrf
    @if($item->exists)
        @method('PUT')
    @endif

    <div class="card admin-card p-4">
        <div class="row g-4">
            <div class="col-lg-6">
                <label class="form-label">Desktop Banner Image</label>
                <input type="file" class="form-control" name="image" accept="image/*">
                <div class="form-text">Desktop reference size: 1204 x 800. This image is used on desktop and also as the fallback if no dedicated mobile banner is provided.</div>
                <div class="admin-banner-preview mt-3 {{ $desktopPreviewUrl ? '' : 'is-empty' }}" data-banner-preview="desktop">
                    @if($desktopPreviewUrl)
                        <img src="{{ $desktopPreviewUrl }}" alt="{{ $item->headline_en }}" class="admin-banner-preview__image">
                    @else
                        <div class="admin-banner-preview__empty">No desktop banner selected yet.</div>
                    @endif
                    <div class="admin-banner-preview__meta">
                        <span>Desktop Banner</span>
                        <strong>1204 x 800 reference</strong>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <label class="form-label">Mobile Banner Image</label>
                <input type="file" class="form-control" name="mobile_image" accept="image/*">
                <div class="form-text">Recommended mobile banner size: 900 x 1200 (3:4). If the image already matches this ratio it will appear fully; if it is wider or taller, the safe-area frame below shows the mobile visible area.</div>
                <div class="admin-mobile-banner-preview mt-3 {{ $mobilePreviewUrl ? '' : 'is-empty' }}" data-mobile-banner-preview data-guide-ratio="{{ $mobileGuideRatio }}">
                    <div class="admin-mobile-banner-preview__frame">
                        @if($mobilePreviewUrl)
                            <img src="{{ $mobilePreviewUrl }}" alt="{{ $item->headline_en }}" class="admin-mobile-banner-preview__image" data-mobile-banner-image>
                        @else
                            <div class="admin-mobile-banner-preview__empty">No mobile banner selected yet. Desktop banner will be used as fallback on mobile.</div>
                        @endif
                        <div class="admin-mobile-banner-preview__safe-area" data-mobile-safe-area hidden>
                            <span>Mobile safe area</span>
                        </div>
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
                <input class="form-control" type="number" name="sort_order" value="{{ old('sort_order', $item->sort_order) }}">
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
    const mobileFrame = mobilePreview?.querySelector('.admin-mobile-banner-preview__frame');
    const mobileImage = () => mobilePreview?.querySelector('[data-mobile-banner-image]');
    const mobileSafeArea = mobilePreview?.querySelector('[data-mobile-safe-area]');
    const mobileGuideTitle = mobilePreview?.querySelector('[data-mobile-guide-title]');
    const mobileGuideText = mobilePreview?.querySelector('[data-mobile-guide-text]');
    const mobileGuideRatio = parseFloat(mobilePreview?.dataset.guideRatio || '0.75');
    const storageUrl = (path) => new URL(`/storage/${String(path || '').replace(/^storage\//, '')}`, window.location.origin).toString();

    const ensureImage = (container, className) => {
        let image = container?.querySelector(`.${className}`);
        if (!image && container) {
            image = document.createElement('img');
            image.className = className;
            if (className === 'admin-mobile-banner-preview__image') {
                image.dataset.mobileBannerImage = 'true';
            }
            container.querySelector('.admin-mobile-banner-preview__empty, .admin-banner-preview__empty')?.remove();
            container.prepend(image);
        }

        return image;
    };

    const setDesktopPreview = (src) => {
        if (!desktopPreview) return;
        if (!src) {
            desktopPreview.classList.add('is-empty');
            return;
        }

        desktopPreview.classList.remove('is-empty');
        const img = ensureImage(desktopPreview, 'admin-banner-preview__image');
        if (img) img.src = src;
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
            return;
        }

        mobilePreview.classList.remove('is-empty');
        const img = ensureImage(mobileFrame, 'admin-mobile-banner-preview__image');
        if (!img) return;
        img.onload = () => syncMobileGuide(img, sourceLabel);
        img.src = src;
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
                setMobilePreview(src, 'Mobile Preview (desktop fallback)');
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

    observeHiddenField('image_existing_path', (path) => setDesktopPreview(storageUrl(path)));
    observeHiddenField('mobile_image_existing_path', (path) => setMobilePreview(storageUrl(path), 'Mobile Preview (Media Library)'));

    const initialMobileImage = mobileImage();
    if (initialMobileImage && initialMobileImage.complete) {
        syncMobileGuide(initialMobileImage, '{{ $item->mobile_image_path ? 'Mobile Preview' : 'Mobile Preview (desktop fallback)' }}');
    } else if (initialMobileImage) {
        initialMobileImage.addEventListener('load', () => syncMobileGuide(initialMobileImage, '{{ $item->mobile_image_path ? 'Mobile Preview' : 'Mobile Preview (desktop fallback)' }}'), { once: true });
    }
});
</script>
@endsection
