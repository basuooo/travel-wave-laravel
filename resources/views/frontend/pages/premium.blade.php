@extends('layouts.app')

@section('title', $contentPage['page_title'] ?? ($page->localized('meta_title') ?: $page->localized('title')))
@section('meta_description', $page->localized('meta_description') ?: data_get($contentPage, 'hero.subtitle'))

@php
    $hero = $contentPage['hero'] ?? [];
    $story = $contentPage['story'] ?? [];
    $mission = $contentPage['mission'] ?? [];
    $whyChoose = $contentPage['why_choose'] ?? [];
    $services = $contentPage['services'] ?? [];
    $howItWorks = $contentPage['how_it_works'] ?? [];
    $stats = $contentPage['stats'] ?? [];
    $professionalism = $contentPage['professionalism'] ?? [];
    $contactInfo = $contentPage['contact_info'] ?? [];
    $form = $contentPage['form'] ?? [];
    $quickHelp = $contentPage['quick_help'] ?? [];
    $map = $contentPage['map'] ?? [];
    $faq = $contentPage['faq'] ?? [];
    $cta = $contentPage['cta'] ?? [];
    $allowManagedUtilityZones = !in_array($page->key ?? null, ['about', 'contact'], true);
    $orderedSections = $page->getOrderedSections();
@endphp

@section('content')
<div class="tw-brand-page" dir="{{ $contentPage['direction'] ?? 'rtl' }}">
    @if($allowManagedUtilityZones)
        @include('partials.frontend.form-zone', ['assignments' => $managedForms['top'] ?? [], 'position' => 'top', 'sourcePage' => $page->key])
        @include('partials.frontend.map-zone', ['assignments' => $managedMaps['top'] ?? [], 'position' => 'top'])
    @endif

    @foreach($orderedSections as $sKey => $sMeta)
        @continue(empty($sMeta['enabled']))

        @switch($sKey)
            @case('hero')
                @if($hero['enabled'] ?? false)
                    @include('frontend.pages.sections.hero', ['section' => $hero])
                    @if($allowManagedUtilityZones)
                        @include('partials.frontend.form-zone', ['assignments' => $managedForms['below_hero'] ?? [], 'position' => 'below_hero', 'sourcePage' => $page->key])
                        @include('partials.frontend.map-zone', ['assignments' => $managedMaps['below_hero'] ?? [], 'position' => 'below_hero'])
                    @endif
                @endif
                @break

            @case('story')
                @if($story['enabled'] ?? false)
                    @include('frontend.pages.sections.story', ['section' => $story])
                @endif
                @break

            @case('mission')
                @if($mission['enabled'] ?? false)
                    @include('frontend.pages.sections.cards', ['section' => $mission])
                @endif
                @break

            @case('why_choose')
                @if($whyChoose['enabled'] ?? false)
                    @include('frontend.pages.sections.cards', ['section' => $whyChoose])
                @endif
                @break

            @case('services')
                @if($services['enabled'] ?? false)
                    @include('frontend.pages.sections.cards', ['section' => $services])
                @endif
                @break

            @case('how_it_works')
                @if($howItWorks['enabled'] ?? false)
                    @include('frontend.pages.sections.cards', ['section' => $howItWorks])
                @endif
                @break

            @case('stats')
                @if($stats['enabled'] ?? false)
                    @include('frontend.pages.sections.stats', ['section' => $stats])
                @endif
                @break

            @case('professionalism')
                @if($professionalism['enabled'] ?? false)
                    @include('frontend.pages.sections.story', ['section' => $professionalism])
                @endif
                @break

            @case('contact_info')
                @if($contactInfo['enabled'] ?? false)
                    @include('frontend.pages.sections.cards', ['section' => $contactInfo])
                @endif
                @break

            @case('quick_help')
                @if($quickHelp['enabled'] ?? false)
                    @include('frontend.pages.sections.cards', ['section' => $quickHelp])
                @endif
                @break

            @case('faq')
                @if($faq['enabled'] ?? false)
                    @if($allowManagedUtilityZones)
                        @include('partials.frontend.map-zone', ['assignments' => $managedMaps['before_faq'] ?? [], 'position' => 'before_faq'])
                        @include('partials.frontend.form-zone', ['assignments' => $managedForms['before_faq'] ?? [], 'position' => 'before_faq', 'sourcePage' => $page->key])
                    @endif

                    @include('frontend.pages.sections.faq', ['section' => $faq])

                    @if($allowManagedUtilityZones)
                        @include('partials.frontend.form-zone', ['assignments' => $managedForms['after_faq'] ?? [], 'position' => 'after_faq', 'sourcePage' => $page->key])
                        @include('partials.frontend.map-zone', ['assignments' => $managedMaps['after_faq'] ?? [], 'position' => 'after_faq'])
                    @endif
                @endif
                @break

            @case('cta')
                @if($cta['enabled'] ?? false)
                    @include('frontend.pages.sections.cta', ['section' => $cta])
                @endif
                @break
        @endswitch
    @endforeach

    @if($allowManagedUtilityZones)
        @include('partials.frontend.form-zone', ['assignments' => $managedForms['middle'] ?? [], 'position' => 'middle', 'sourcePage' => $page->key])
        @include('partials.frontend.map-zone', ['assignments' => $managedMaps['middle'] ?? [], 'position' => 'middle'])
        @include('partials.frontend.form-zone', ['assignments' => $managedForms['bottom'] ?? [], 'position' => 'bottom', 'sourcePage' => $page->key])
        @include('partials.frontend.map-zone', ['assignments' => $managedMaps['bottom'] ?? [], 'position' => 'bottom'])
    @endif
</div>
@endsection
