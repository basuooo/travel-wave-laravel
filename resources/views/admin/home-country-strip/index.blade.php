@extends('layouts.admin')

@section('page_title', 'Homepage Country Strip')
@section('page_description', 'Manage the country cards shown below the homepage hero plus autoplay and movement speed.')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <form method="post" action="{{ route('admin.home-country-strip.settings') }}" class="card admin-card p-4 h-100">
            @csrf
            @method('PUT')
            <h2 class="h5 mb-3">Strip Settings</h2>
            <div class="row g-3">
                <div class="col-12"><label class="form-label">Section Title EN</label><input class="form-control" name="home_country_strip_title_en" value="{{ old('home_country_strip_title_en', $setting->home_country_strip_title_en) }}"></div>
                <div class="col-12"><label class="form-label">Section Title AR</label><input class="form-control text-end" dir="rtl" name="home_country_strip_title_ar" value="{{ old('home_country_strip_title_ar', $setting->home_country_strip_title_ar) }}"></div>
                <div class="col-12"><label class="form-label">Section Subtitle EN</label><input class="form-control" name="home_country_strip_subtitle_en" value="{{ old('home_country_strip_subtitle_en', $setting->home_country_strip_subtitle_en) }}"></div>
                <div class="col-12"><label class="form-label">Section Subtitle AR</label><input class="form-control text-end" dir="rtl" name="home_country_strip_subtitle_ar" value="{{ old('home_country_strip_subtitle_ar', $setting->home_country_strip_subtitle_ar) }}"></div>
                <div class="col-md-6"><label class="form-label">Movement Speed</label><input class="form-control" type="number" name="home_country_strip_speed" value="{{ old('home_country_strip_speed', $setting->home_country_strip_speed ?: 32) }}"></div>
                <div class="col-md-6 d-flex align-items-end"><div class="form-check pb-2"><input class="form-check-input" type="checkbox" name="home_country_strip_autoplay" value="1" id="home_country_strip_autoplay" @checked(old('home_country_strip_autoplay', $setting->home_country_strip_autoplay ?? true))><label class="form-check-label" for="home_country_strip_autoplay">Enable autoplay</label></div></div>
                <div class="col-12"><hr class="my-1"></div>
                <div class="col-12"><h3 class="h6 mb-0">Carousel Behavior</h3></div>
                <div class="col-md-6 d-flex align-items-end"><div class="form-check pb-2"><input class="form-check-input" type="checkbox" name="home_destinations_autoplay" value="1" id="home_destinations_autoplay" @checked(old('home_destinations_autoplay', $setting->home_destinations_autoplay ?? true))><label class="form-check-label" for="home_destinations_autoplay">Autoplay</label></div></div>
                <div class="col-md-6 d-flex align-items-end"><div class="form-check pb-2"><input class="form-check-input" type="checkbox" name="home_destinations_pause_on_hover" value="1" id="home_destinations_pause_on_hover" @checked(old('home_destinations_pause_on_hover', $setting->home_destinations_pause_on_hover ?? true))><label class="form-check-label" for="home_destinations_pause_on_hover">Pause on hover</label></div></div>
                <div class="col-md-6 d-flex align-items-end"><div class="form-check pb-2"><input class="form-check-input" type="checkbox" name="home_destinations_loop" value="1" id="home_destinations_loop" @checked(old('home_destinations_loop', $setting->home_destinations_loop ?? true))><label class="form-check-label" for="home_destinations_loop">Infinite loop</label></div></div>
                <div class="col-md-6"><label class="form-label">Autoplay Interval (ms)</label><input class="form-control" type="number" name="home_destinations_interval" value="{{ old('home_destinations_interval', $setting->home_destinations_interval ?: 3200) }}"></div>
                <div class="col-md-6"><label class="form-label">Transition Speed (ms)</label><input class="form-control" type="number" name="home_destinations_speed" value="{{ old('home_destinations_speed', $setting->home_destinations_speed ?: 500) }}"></div>
            </div>
            <button class="btn btn-primary mt-4">Save Strip Settings</button>
        </form>
    </div>
    <div class="col-lg-8">
        <div class="card admin-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="h5 mb-1">Country Items</h2>
                    <div class="text-muted small">Add icons/cards for France, Germany, Italy, Spain, UAE, USA, Canada, and other featured destinations.</div>
                </div>
                <a href="{{ route('admin.home-country-strip.create') }}" class="btn btn-primary">Add Country Item</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Preview</th><th>Country</th><th>Subtitle</th><th>Linked Page</th><th>Order</th><th>Status</th><th>Homepage</th><th></th></tr></thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td style="width:100px">
                                    @if($item->displayImagePath())
                                        <img src="{{ asset('storage/' . $item->displayImagePath()) }}" class="img-fluid rounded-3 border" alt="{{ $item->displayName('en') }}" style="max-height: 70px; object-fit: cover;">
                                    @else
                                        <span class="badge text-bg-light">No image</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $item->displayName('en') }}</div>
                                    <div class="text-muted small" dir="rtl">{{ $item->displayName('ar') }}</div>
                                </td>
                                <td>{{ $item->displaySubtitle('en') ?: 'Not set' }}</td>
                                <td>{{ $item->visaCountry?->name_en ?: ($item->custom_url ?: 'Not set') }}</td>
                                <td>{{ $item->sort_order }}</td>
                                <td><span class="badge {{ $item->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $item->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td><span class="badge {{ $item->show_on_homepage ? 'text-bg-primary' : 'text-bg-light' }}">{{ $item->show_on_homepage ? 'Shown' : 'Hidden' }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.home-country-strip.edit', $item) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <form method="post" action="{{ route('admin.home-country-strip.destroy', $item) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this country item?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
