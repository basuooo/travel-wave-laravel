<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesCmsData;
use App\Http\Controllers\Controller;
use App\Models\HomeCountryStripItem;
use App\Models\Setting;
use App\Models\VisaCountry;
use Illuminate\Http\Request;

class HomeCountryStripController extends Controller
{
    use HandlesCmsData;

    public function index()
    {
        return view('admin.home-country-strip.index', [
            'items' => HomeCountryStripItem::with('visaCountry')->orderBy('sort_order')->get(),
            'setting' => Setting::query()->firstOrCreate([]),
        ]);
    }

    public function create()
    {
        return view('admin.home-country-strip.form', [
            'item' => new HomeCountryStripItem(),
            'countries' => VisaCountry::query()->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['image_path'] = $this->uploadFile($request, 'image', 'home-country-strip');
        $data['flag_image_path'] = $this->uploadFile($request, 'flag_image', 'home-country-strip');
        $data['is_active'] = $request->boolean('is_active');
        $data['show_on_homepage'] = $request->boolean('show_on_homepage');

        HomeCountryStripItem::create($data);

        return redirect()->route('admin.home-country-strip.index')->with('success', 'Homepage country item created.');
    }

    public function edit(HomeCountryStripItem $home_country_strip)
    {
        return view('admin.home-country-strip.form', [
            'item' => $home_country_strip,
            'countries' => VisaCountry::query()->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, HomeCountryStripItem $home_country_strip)
    {
        $data = $this->validatedData($request);
        $data['image_path'] = $this->uploadFile($request, 'image', 'home-country-strip', $home_country_strip->image_path);
        $data['flag_image_path'] = $this->uploadFile($request, 'flag_image', 'home-country-strip', $home_country_strip->flag_image_path);
        $data['is_active'] = $request->boolean('is_active');
        $data['show_on_homepage'] = $request->boolean('show_on_homepage');

        $home_country_strip->update($data);

        return redirect()->route('admin.home-country-strip.index')->with('success', 'Homepage country item updated.');
    }

    public function destroy(HomeCountryStripItem $home_country_strip)
    {
        $home_country_strip->delete();

        return back()->with('success', 'Homepage country item deleted.');
    }

    public function updateSettings(Request $request)
    {
        $setting = Setting::query()->firstOrCreate([]);

        $data = $request->validate([
            'home_country_strip_title_en' => ['nullable', 'string', 'max:255'],
            'home_country_strip_title_ar' => ['nullable', 'string', 'max:255'],
            'home_country_strip_subtitle_en' => ['nullable', 'string', 'max:255'],
            'home_country_strip_subtitle_ar' => ['nullable', 'string', 'max:255'],
            'home_country_strip_speed' => ['nullable', 'integer', 'min:12', 'max:80'],
            'home_destinations_interval' => ['nullable', 'integer', 'min:1000', 'max:20000'],
            'home_destinations_speed' => ['nullable', 'integer', 'min:100', 'max:5000'],
        ]);

        $data['home_country_strip_autoplay'] = $request->boolean('home_country_strip_autoplay');
        $data['home_country_strip_speed'] = $data['home_country_strip_speed'] ?: ($setting->home_country_strip_speed ?: 32);
        $data['home_destinations_autoplay'] = $request->boolean('home_destinations_autoplay');
        $data['home_destinations_pause_on_hover'] = $request->boolean('home_destinations_pause_on_hover');
        $data['home_destinations_loop'] = $request->boolean('home_destinations_loop');
        $data['home_destinations_interval'] = $data['home_destinations_interval'] ?: ($setting->home_destinations_interval ?: 3200);
        $data['home_destinations_speed'] = $data['home_destinations_speed'] ?: ($setting->home_destinations_speed ?: 500);

        $setting->update($data);

        return back()->with('success', 'Homepage country strip settings updated.');
    }

    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'visa_country_id' => ['nullable', 'exists:visa_countries,id'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'subtitle_en' => ['nullable', 'string', 'max:255'],
            'subtitle_ar' => ['nullable', 'string', 'max:255'],
            'custom_url' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'image' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp'],
            'flag_image' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg'],
        ]);
    }
}
