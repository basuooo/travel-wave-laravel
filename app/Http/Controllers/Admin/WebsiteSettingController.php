<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesCmsData;
use App\Http\Controllers\Concerns\InteractsWithSettingColumns;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Schema;

class WebsiteSettingController extends Controller
{
    use HandlesCmsData;
    use InteractsWithSettingColumns;

    public function edit()
    {
        $setting = Setting::query()->firstOrCreate([]);
        $dbMigrated = Schema::hasColumn('settings', 'site_status');

        return view('admin.website-settings.edit', [
            'setting' => $setting,
            'dbMigrated' => $dbMigrated,
        ]);
    }

    public function update(Request $request)
    {
        $setting = Setting::query()->firstOrCreate([]);

        if (!Schema::hasColumn('settings', 'site_status')) {
            return back()->with('error', 'عفواً، يجب تشغيل أمر المايجريشن php artisan migrate أولاً لإنشاء حقول إعدادات الموقع في قاعدة البيانات.');
        }

        $data = $request->validate([
            'site_status' => ['nullable', 'string', 'in:active,maintenance,redirect'],
            'site_redirect_url' => ['nullable', 'string', 'max:500'],
            'maintenance_template' => ['nullable', 'string', 'in:glassmorphism,minimal_countdown,agency_hero'],
            'maintenance_title_ar' => ['nullable', 'string', 'max:255'],
            'maintenance_title_en' => ['nullable', 'string', 'max:255'],
            'maintenance_message_ar' => ['nullable', 'string'],
            'maintenance_message_en' => ['nullable', 'string'],
            'maintenance_end_time' => ['nullable', 'date'],
            'maintenance_bypass_admin' => ['nullable', 'boolean'],
        ]);

        $data['maintenance_bypass_admin'] = $request->boolean('maintenance_bypass_admin');

        $setting->update($this->filterExistingSettingColumns($data));

        return back()->with('success', 'تم تحديث إعدادات الموقع وتصميم صفحة الصيانة بنجاح.');
    }
}
