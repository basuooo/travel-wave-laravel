<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesCmsData;
use App\Http\Controllers\Concerns\InteractsWithSettingColumns;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class WebsiteSettingController extends Controller
{
    use HandlesCmsData;
    use InteractsWithSettingColumns;

    /**
     * Get website settings with JSON fallback.
     */
    public static function getWebsiteSettings()
    {
        $setting = Setting::query()->firstOrCreate([]);
        $fileData = [];

        if (Storage::disk('local')->exists('website_status.json')) {
            try {
                $fileData = json_decode(Storage::disk('local')->get('website_status.json'), true) ?: [];
            } catch (\Throwable $e) {
                $fileData = [];
            }
        }

        foreach ($fileData as $key => $value) {
            if (!empty($value) || is_bool($value)) {
                $setting->{$key} = $value;
            }
        }

        return $setting;
    }

    public function edit()
    {
        $setting = self::getWebsiteSettings();
        $dbMigrated = Schema::hasColumn('settings', 'site_status');

        return view('admin.website-settings.edit', [
            'setting' => $setting,
            'dbMigrated' => $dbMigrated,
        ]);
    }

    public function update(Request $request)
    {
        $setting = Setting::query()->firstOrCreate([]);

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

        // 1. Save to JSON file storage (Works immediately without requiring DB migration)
        Storage::disk('local')->put('website_status.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // 2. Save to DB if columns exist
        if (Schema::hasColumn('settings', 'site_status')) {
            $setting->update($this->filterExistingSettingColumns($data));
        }

        return back()->with('success', 'تم حفظ وتطبيق إعدادات الموقع وتصميم الصيانة بنجاح!');
    }
}
