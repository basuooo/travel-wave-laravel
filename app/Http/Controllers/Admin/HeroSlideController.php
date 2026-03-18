<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesCmsData;
use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Models\Setting;
use Illuminate\Http\Request;

class HeroSlideController extends Controller
{
    use HandlesCmsData;

    public function index()
    {
        return view('admin.hero-slides.index', [
            'items' => HeroSlide::orderBy('sort_order')->get(),
            'setting' => Setting::query()->firstOrCreate([]),
        ]);
    }

    public function create()
    {
        return view('admin.hero-slides.form', ['item' => new HeroSlide()]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['image_path'] = $this->uploadFile($request, 'image', 'hero-slides');
        $data['is_active'] = $request->boolean('is_active');

        HeroSlide::create($data);

        return redirect()->route('admin.hero-slides.index')->with('success', 'Hero slide created.');
    }

    public function show(HeroSlide $heroSlide)
    {
        return redirect()->route('admin.hero-slides.edit', $heroSlide);
    }

    public function edit(HeroSlide $hero_slide)
    {
        return view('admin.hero-slides.form', ['item' => $hero_slide]);
    }

    public function update(Request $request, HeroSlide $hero_slide)
    {
        $data = $this->validatedData($request, false);
        $data['image_path'] = $this->uploadFile($request, 'image', 'hero-slides', $hero_slide->image_path);
        $data['is_active'] = $request->boolean('is_active');

        $hero_slide->update($data);

        return redirect()->route('admin.hero-slides.index')->with('success', 'Hero slide updated.');
    }

    public function destroy(HeroSlide $hero_slide)
    {
        $hero_slide->delete();

        return back()->with('success', 'Hero slide deleted.');
    }

    public function updateSettings(Request $request)
    {
        $setting = Setting::query()->firstOrCreate([]);
        $data = $request->validate([
            'hero_slider_interval' => ['required', 'integer', 'min:1000', 'max:30000'],
            'hero_slider_overlay_opacity' => ['required', 'numeric', 'min:0', 'max:0.9'],
            'hero_slider_content_alignment' => ['required', 'in:start,center,end'],
            'hero_slider_layout_mode' => ['required', 'in:full-width,custom-1408,large-hero,medium-hero,compact-banner,fullscreen-hero'],
        ]);

        $data['hero_slider_autoplay'] = $request->boolean('hero_slider_autoplay');
        $data['hero_slider_show_dots'] = $request->boolean('hero_slider_show_dots');
        $data['hero_slider_show_arrows'] = $request->boolean('hero_slider_show_arrows');

        $setting->update($data);

        return back()->with('success', 'Hero slider settings updated.');
    }

    protected function validatedData(Request $request, bool $requireImage = true): array
    {
        $imageRules = $requireImage ? ['required', 'image'] : ['nullable', 'image'];

        return $request->validate([
            'headline_en' => ['nullable', 'string', 'max:255'],
            'headline_ar' => ['nullable', 'string', 'max:255'],
            'subtitle_en' => ['nullable', 'string'],
            'subtitle_ar' => ['nullable', 'string'],
            'cta_text_en' => ['nullable', 'string', 'max:255'],
            'cta_text_ar' => ['nullable', 'string', 'max:255'],
            'cta_link' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'image' => $imageRules,
        ]);
    }
}
