@extends('layouts.app')

@section('title', $contentPage['page_title'] ?? ($page->localized('meta_title') ?: $page->localized('title')))
@section('meta_description', $page->localized('meta_description') ?: data_get($contentPage, 'hero.subtitle'))

@php
    $hero = $contentPage['hero'] ?? [];
    $story = $contentPage['story'] ?? [];
    $mission = $contentPage['mission'] ?? [];
    $whyChoose = $contentPage['why_choose'] ?? [];
    $services = $contentPage['services'] ?? [];
    $stats = $contentPage['stats'] ?? [];
    $professionalism = $contentPage['professionalism'] ?? [];
    $contactInfo = $contentPage['contact_info'] ?? [];
    $form = $contentPage['form'] ?? [];
    $quickHelp = $contentPage['quick_help'] ?? [];
    $map = $contentPage['map'] ?? [];
    $faq = $contentPage['faq'] ?? [];
    $cta = $contentPage['cta'] ?? [];
@endphp

@section('content')
<div class="tw-brand-page" dir="{{ $contentPage['direction'] ?? 'rtl' }}">
    @include('partials.frontend.form-zone', ['assignments' => $managedForms['top'] ?? [], 'position' => 'top', 'sourcePage' => $page->key])
    @include('partials.frontend.map-zone', ['assignments' => $managedMaps['top'] ?? [], 'position' => 'top'])

    @if($hero['enabled'] ?? false)
        @include('frontend.pages.sections.hero', ['section' => $hero])
    @endif

    @include('partials.frontend.form-zone', ['assignments' => $managedForms['below_hero'] ?? [], 'position' => 'below_hero', 'sourcePage' => $page->key])
    @include('partials.frontend.map-zone', ['assignments' => $managedMaps['below_hero'] ?? [], 'position' => 'below_hero'])

    @if($story['enabled'] ?? false)
        @include('frontend.pages.sections.story', ['section' => $story])
    @endif

    @if($mission['enabled'] ?? false)
        @include('frontend.pages.sections.cards', ['section' => $mission])
    @endif

    @if($whyChoose['enabled'] ?? false)
        @include('frontend.pages.sections.cards', ['section' => $whyChoose])
    @endif

    @if($services['enabled'] ?? false)
        @include('frontend.pages.sections.cards', ['section' => $services])
    @endif

    @if($stats['enabled'] ?? false)
        @include('frontend.pages.sections.stats', ['section' => $stats])
    @endif

    @if($professionalism['enabled'] ?? false)
        @include('frontend.pages.sections.story', ['section' => $professionalism])
    @endif

    @include('partials.frontend.form-zone', ['assignments' => $managedForms['middle'] ?? [], 'position' => 'middle', 'sourcePage' => $page->key])
    @include('partials.frontend.map-zone', ['assignments' => $managedMaps['middle'] ?? [], 'position' => 'middle'])

    @if($contactInfo['enabled'] ?? false)
        @include('frontend.pages.sections.cards', ['section' => $contactInfo])
    @endif

    @if($form['enabled'] ?? false)
        @include('partials.frontend.map-zone', ['assignments' => $managedMaps['above_form'] ?? [], 'position' => 'above_form'])
        @include('frontend.pages.sections.form', ['section' => $form])
        @include('partials.frontend.map-zone', ['assignments' => $managedMaps['below_form'] ?? [], 'position' => 'below_form'])
    @endif

    @if($quickHelp['enabled'] ?? false)
        @include('frontend.pages.sections.cards', ['section' => $quickHelp])
    @endif

    @include('partials.frontend.map-zone', ['assignments' => $managedMaps['before_faq'] ?? [], 'position' => 'before_faq', 'fallbackSection' => $map])
    @include('partials.frontend.form-zone', ['assignments' => $managedForms['before_faq'] ?? [], 'position' => 'before_faq', 'sourcePage' => $page->key])

    @if($faq['enabled'] ?? false)
        @include('frontend.pages.sections.faq', ['section' => $faq])
    @endif

    @include('partials.frontend.form-zone', ['assignments' => $managedForms['after_faq'] ?? [], 'position' => 'after_faq', 'sourcePage' => $page->key])
    @include('partials.frontend.map-zone', ['assignments' => $managedMaps['after_faq'] ?? [], 'position' => 'after_faq'])

    @if($cta['enabled'] ?? false)
        @include('frontend.pages.sections.cta', ['section' => $cta])
    @endif

    @include('partials.frontend.form-zone', ['assignments' => $managedForms['bottom'] ?? [], 'position' => 'bottom', 'sourcePage' => $page->key])
    @include('partials.frontend.map-zone', ['assignments' => $managedMaps['bottom'] ?? [], 'position' => 'bottom'])
</div>
@endsection
