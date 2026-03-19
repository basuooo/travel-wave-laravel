<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesCmsData;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class HeaderSettingController extends Controller
{
    use HandlesCmsData;

    public function edit()
    {
        return view('admin.header-settings.edit', [
            'setting' => Setting::query()->firstOrCreate([]),
        ]);
    }

    public function update(Request $request)
    {
        $setting = Setting::query()->firstOrCreate([]);

        $rules = [
            'header_background_color' => ['nullable', 'string', 'max:20'],
            'header_text_color' => ['nullable', 'string', 'max:20'],
            'header_link_color' => ['nullable', 'string', 'max:20'],
            'header_hover_color' => ['nullable', 'string', 'max:20'],
            'header_active_link_color' => ['nullable', 'string', 'max:20'],
            'header_button_color' => ['nullable', 'string', 'max:20'],
            'header_button_text_color' => ['nullable', 'string', 'max:20'],
            'logo_width' => ['nullable', 'integer', 'min:60', 'max:520'],
            'logo_height' => ['nullable', 'integer', 'min:20', 'max:220'],
            'mobile_logo_width' => ['nullable', 'integer', 'min:50', 'max:320'],
            'header_vertical_padding' => ['nullable', 'integer', 'min:0', 'max:40'],
        ];

        if ($request->hasFile('logo')) {
            $rules['logo'] = ['nullable', 'image', 'mimes:png,jpg,jpeg,webp'];
        }

        $data = $request->validate($rules);

        $data['header_logo_enabled'] = $request->boolean('header_logo_enabled');
        $data['header_is_sticky'] = $request->boolean('header_is_sticky');
        $data['logo_path'] = $this->uploadFile($request, 'logo', 'settings', $setting->logo_path);
        $data['logo_width'] = $data['logo_width'] ?: ($setting->logo_width ?: 220);
        $data['mobile_logo_width'] = $data['mobile_logo_width'] ?: ($setting->mobile_logo_width ?: 168);
        $data['header_vertical_padding'] = $data['header_vertical_padding'] ?? ($setting->header_vertical_padding ?: 8);

        $setting->update($data);

        return back()->with('success', 'Header settings updated successfully.');
    }
}
