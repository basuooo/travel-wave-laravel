<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class MetaConversionApiSettingController extends Controller
{
    public function edit()
    {
        return view('admin.meta-conversion-api-settings.edit', [
            'setting' => Setting::query()->firstOrCreate([]),
        ]);
    }

    public function update(Request $request)
    {
        $setting = Setting::query()->firstOrCreate([]);

        $data = $request->validate([
            'meta_pixel_id' => ['nullable', 'regex:/^[0-9]+$/'],
            'meta_conversion_api_access_token' => ['nullable', 'string'],
            'meta_conversion_api_test_event_code' => ['nullable', 'string', 'max:255'],
            'meta_conversion_api_default_event_source_url' => ['nullable', 'url', 'max:255'],
        ]);

        $setting->update([
            'meta_pixel_id' => $data['meta_pixel_id'] ?? null,
            'meta_conversion_api_enabled' => $request->boolean('meta_conversion_api_enabled'),
            'meta_conversion_api_access_token' => $data['meta_conversion_api_access_token'] ?? null,
            'meta_conversion_api_test_event_code' => $data['meta_conversion_api_test_event_code'] ?? null,
            'meta_conversion_api_default_event_source_url' => $data['meta_conversion_api_default_event_source_url'] ?? null,
        ]);

        return back()->with('success', __('admin.meta_conversion_api_updated'));
    }
};
