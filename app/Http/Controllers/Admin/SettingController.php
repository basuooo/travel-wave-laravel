<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesCmsData;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    use HandlesCmsData;

    public function edit()
    {
        return view('admin.settings.edit', [
            'setting' => Setting::query()->firstOrCreate([]),
        ]);
    }

    public function update(Request $request)
    {
        $setting = Setting::query()->firstOrCreate([]);

        $data = $request->validate([
            'site_name_en' => ['nullable', 'string', 'max:255'],
            'site_name_ar' => ['nullable', 'string', 'max:255'],
            'site_tagline_en' => ['nullable', 'string', 'max:255'],
            'site_tagline_ar' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'secondary_phone' => ['nullable', 'string', 'max:255'],
            'whatsapp_number' => ['nullable', 'string', 'max:255'],
            'address_en' => ['nullable', 'string'],
            'address_ar' => ['nullable', 'string'],
            'working_hours_en' => ['nullable', 'string'],
            'working_hours_ar' => ['nullable', 'string'],
            'map_iframe' => ['nullable', 'string'],
            'facebook_url' => ['nullable', 'url'],
            'instagram_url' => ['nullable', 'url'],
            'youtube_url' => ['nullable', 'url'],
            'tiktok_url' => ['nullable', 'url'],
            'footer_text_en' => ['nullable', 'string'],
            'footer_text_ar' => ['nullable', 'string'],
            'copyright_text_en' => ['nullable', 'string'],
            'copyright_text_ar' => ['nullable', 'string'],
            'default_meta_title_en' => ['nullable', 'string', 'max:255'],
            'default_meta_title_ar' => ['nullable', 'string', 'max:255'],
            'default_meta_description_en' => ['nullable', 'string'],
            'default_meta_description_ar' => ['nullable', 'string'],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'secondary_color' => ['nullable', 'string', 'max:20'],
            'global_cta_title_en' => ['nullable', 'string', 'max:255'],
            'global_cta_title_ar' => ['nullable', 'string', 'max:255'],
            'global_cta_text_en' => ['nullable', 'string'],
            'global_cta_text_ar' => ['nullable', 'string'],
            'global_cta_button_en' => ['nullable', 'string', 'max:255'],
            'global_cta_button_ar' => ['nullable', 'string', 'max:255'],
            'global_cta_url' => ['nullable', 'string', 'max:255'],
            'facebook_url' => ['nullable', 'url'],
            'instagram_url' => ['nullable', 'url'],
            'youtube_url' => ['nullable', 'url'],
            'tiktok_url' => ['nullable', 'url'],
            'logo' => ['nullable', 'image'],
            'favicon' => ['nullable', 'image'],
        ]);

        $data['logo_path'] = $this->uploadFile($request, 'logo', 'settings', $setting->logo_path);
        $data['favicon_path'] = $this->uploadFile($request, 'favicon', 'settings', $setting->favicon_path);

        $setting->update($data);

        return back()->with('success', 'Site settings updated successfully.');
    }
}
