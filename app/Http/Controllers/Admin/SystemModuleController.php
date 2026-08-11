<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesCmsData;
use App\Http\Controllers\Concerns\InteractsWithSettingColumns;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SystemModuleController extends Controller
{
    use HandlesCmsData;
    use InteractsWithSettingColumns;

    /**
     * Get system modules settings with JSON fallback.
     */
    public static function getModuleSettings()
    {
        return WebsiteSettingController::getWebsiteSettings();
    }

    public function edit()
    {
        $setting = self::getModuleSettings();
        $dbMigrated = Schema::hasColumn('settings', 'site_status');

        return view('admin.modules-control.edit', [
            'setting' => $setting,
            'dbMigrated' => $dbMigrated,
        ]);
    }

    public function update(Request $request)
    {
        $setting = Setting::query()->firstOrCreate([]);
        $currentSettings = WebsiteSettingController::getWebsiteSettings();

        $data = $request->validate([
            'module_website_enabled' => ['nullable', 'boolean'],
            'module_crm_enabled' => ['nullable', 'boolean'],
            'module_accounting_enabled' => ['nullable', 'boolean'],
            'module_marketing_enabled' => ['nullable', 'boolean'],
            'module_chatbot_enabled' => ['nullable', 'boolean'],
            'module_blog_enabled' => ['nullable', 'boolean'],
            'module_destinations_enabled' => ['nullable', 'boolean'],
            'module_forms_enabled' => ['nullable', 'boolean'],
        ]);

        $moduleFlags = [
            'module_website_enabled' => $request->boolean('module_website_enabled'),
            'module_crm_enabled' => $request->boolean('module_crm_enabled'),
            'module_accounting_enabled' => $request->boolean('module_accounting_enabled'),
            'module_marketing_enabled' => $request->boolean('module_marketing_enabled'),
            'module_chatbot_enabled' => $request->boolean('module_chatbot_enabled'),
            'module_blog_enabled' => $request->boolean('module_blog_enabled'),
            'module_destinations_enabled' => $request->boolean('module_destinations_enabled'),
            'module_forms_enabled' => $request->boolean('module_forms_enabled'),
        ];

        // Merge with existing json file data
        $jsonPath = 'website_status.json';
        $existingData = [];
        if (Storage::disk('local')->exists($jsonPath)) {
            try {
                $existingData = json_decode(Storage::disk('local')->get($jsonPath), true) ?: [];
            } catch (\Throwable $e) {
                $existingData = [];
            }
        }

        $mergedData = array_merge($existingData, $moduleFlags);

        // 1. Save to JSON file storage
        Storage::disk('local')->put($jsonPath, json_encode($mergedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // 2. Save to DB if columns exist
        if (Schema::hasColumn('settings', 'site_status')) {
            $setting->update($this->filterExistingSettingColumns($moduleFlags));
        }

        return back()->with('success', 'تم حفظ وإدارة موديولات النظام بنجاح!');
    }
}
