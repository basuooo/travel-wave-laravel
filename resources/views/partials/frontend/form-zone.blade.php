@if(!empty($assignments))
    @php
        $contextType = $contextData['type'] ?? null;
        $singleLayoutVariant = count($assignments) === 1
            ? ($assignments[0]->form->settings['layout_variant'] ?? 'standard')
            : null;
        $renderAsSplitLayout = count($assignments) === 1
            && (
                ($singleLayoutVariant === 'visa_split' && $contextType === 'visa')
                || $singleLayoutVariant === 'split_details'
            );
        $splitSectionId = $singleLayoutVariant === 'visa_split' && $contextType === 'visa'
            ? 'visa-inquiry'
            : null;
    @endphp

    <section class="container py-4 tw-managed-form-zone tw-managed-form-zone-{{ $position ?? 'default' }}" @if($splitSectionId) id="{{ $splitSectionId }}" @endif>
        @if($renderAsSplitLayout)
            @include('partials.frontend.managed-form', [
                'assignment' => $assignments[0],
                'sourcePage' => $sourcePage ?? request()->path(),
                'contextData' => $contextData ?? null,
            ])
        @else
            <div class="row g-4">
                @foreach($assignments as $assignment)
                    <div class="{{ count($assignments) > 1 ? 'col-lg-6' : 'col-12' }}">
                        @include('partials.frontend.managed-form', [
                            'assignment' => $assignment,
                            'sourcePage' => $sourcePage ?? request()->path(),
                            'contextData' => $contextData ?? null,
                        ])
                    </div>
                @endforeach
            </div>
        @endif
    </section>
@endif
