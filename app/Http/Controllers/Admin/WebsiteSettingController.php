<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesCmsData;
use App\Http\Controllers\Concerns\InteractsWithSettingColumns;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class WebsiteSettingController extends Controller
{
    use HandlesCmsData;
    use InteractsWithSettingColumns;

    public function edit()
    {
        return view('admin.website-settings.edit', [
            'setting' => Setting::query()->firstOrCreate([]),
        ]);
    }

    public function update(Request $request)
    {
        $setting = Setting::query()->firstOrCreate([]);

        $data = $request->validate([
            'site_status' => ['nullable', 'string', 'in:active,maintenance,redirect'],
            'site_redirect_url' => ['nullable', 'string', 'max:500'],
            'maintenance_title_ar' => ['nullable', 'string', 'max:255'],
            'maintenance_title_en' => ['nullable', 'string', 'max:255'],
            'maintenance_message_ar' => ['nullable', 'string'],
            'maintenance_message_en' => ['nullable', 'string'],
            'maintenance_bypass_admin' => ['nullable', 'boolean'],
        ]);

        $data['maintenance_bypass_admin'] = $request->boolean('maintenance_bypass_admin');

        $setting->update($this->filterExistingSettingColumns($data));

        return back()->with('success', __('admin.website_settings_updated') ?? 'تم تحديث إعدادات الموقع بنجاح.');
    }
}
