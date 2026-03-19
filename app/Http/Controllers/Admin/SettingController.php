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

        $rules = [
            'site_name_en' => ['nullable', 'string', 'max:255'],
            'site_name_ar' => ['nullable', 'string', 'max:255'],
            'site_tagline_en' => ['nullable', 'string', 'max:255'],
            'site_tagline_ar' => ['nullable', 'string', 'max:255'],
            'logo_width' => ['nullable', 'integer', 'min:60', 'max:520'],
            'logo_height' => ['nullable', 'integer', 'min:20', 'max:220'],
            'mobile_logo_width' => ['nullable', 'integer', 'min:50', 'max:320'],
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
            'accent_color' => ['nullable', 'string', 'max:20'],
            'button_color' => ['nullable', 'string', 'max:20'],
            'button_hover_color' => ['nullable', 'string', 'max:20'],
            'link_hover_color' => ['nullable', 'string', 'max:20'],
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
        ];

        foreach (['logo', 'footer_logo', 'favicon'] as $field) {
            if ($request->hasFile($field)) {
                $rules[$field] = ['nullable', 'image', 'mimes:png,jpg,jpeg,webp'];
            }
        }

        $data = $request->validate($rules);

        $data['logo_path'] = $this->uploadFile($request, 'logo', 'settings', $setting->logo_path);
        $data['footer_logo_path'] = $this->uploadFile($request, 'footer_logo', 'settings', $setting->footer_logo_path);
        $data['favicon_path'] = $this->uploadFile($request, 'favicon', 'settings', $setting->favicon_path);
        $data['logo_width'] = $data['logo_width'] ?: ($setting->logo_width ?: 220);
        $data['mobile_logo_width'] = $data['mobile_logo_width'] ?: ($setting->mobile_logo_width ?: 168);

        $setting->update($data);

        return back()->with('success', 'Site settings updated successfully.');
    }
}
