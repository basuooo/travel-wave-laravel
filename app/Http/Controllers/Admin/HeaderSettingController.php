<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesCmsData;
use App\Http\Controllers\Concerns\InteractsWithSettingColumns;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class HeaderSettingController extends Controller
{
    use HandlesCmsData;
    use InteractsWithSettingColumns;

    public function edit()
    {
        return view('admin.header-settings.edit', [
            'setting' => Setting::query()->firstOrCreate([]),
        ]);
    }

    public function update(Request $request)
    {
        $setting = Setting::query()->firstOrCreate([]);
        $logoField = $request->hasFile('header_logo') ? 'header_logo' : ($request->hasFile('logo') ? 'logo' : 'header_logo');

        if (! $request->filled('header_logo_existing_path') && $request->filled('logo_existing_path')) {
            $request->merge([
                'header_logo_existing_path' => $request->input('logo_existing_path'),
            ]);
        }

        $rules = [
            'header_background_color' => ['nullable', 'string', 'max:20'],
            'header_text_color' => ['nullable', 'string', 'max:20'],
            'header_link_color' => ['nullable', 'string', 'max:20'],
            'header_hover_color' => ['nullable', 'string', 'max:20'],
            'header_active_link_color' => ['nullable', 'string', 'max:20'],
            'header_button_color' => ['nullable', 'string', 'max:20'],
            'header_button_text_color' => ['nullable', 'string', 'max:20'],
            'header_logo_width' => ['nullable', 'integer', 'min:60', 'max:520'],
            'header_logo_height' => ['nullable', 'integer', 'min:20', 'max:220'],
            'header_mobile_logo_width' => ['nullable', 'integer', 'min:50', 'max:320'],
            'header_logo_keep_aspect_ratio' => ['nullable', 'boolean'],
            'header_logo_display_mode' => ['nullable', 'in:original,contain,cover,custom'],
            'header_logo_position_en' => ['nullable', 'in:left,right'],
            'header_logo_position_ar' => ['nullable', 'in:left,right'],
            'header_menu_position_en' => ['nullable', 'in:left,right'],
            'header_menu_position_ar' => ['nullable', 'in:left,right'],
            'logo_width' => ['nullable', 'integer', 'min:60', 'max:520'],
            'logo_height' => ['nullable', 'integer', 'min:20', 'max:220'],
            'mobile_logo_width' => ['nullable', 'integer', 'min:50', 'max:320'],
            'logo_keep_aspect_ratio' => ['nullable', 'boolean'],
            'header_vertical_padding' => ['nullable', 'integer', 'min:0', 'max:40'],
        ];

        if ($request->hasFile('header_logo') || $request->hasFile('logo')) {
            $rules[$logoField] = ['nullable', 'image', 'mimes:png,jpg,jpeg,webp'];
        }

        $data = $request->validate($rules);

        $headerLogoPath = $this->uploadFile($request, $logoField, 'settings', $setting->header_logo_path ?: $setting->logo_path);
        $headerLogoWidth = $this->firstIntegerFrom($data, ['header_logo_width', 'logo_width'], $setting->header_logo_width ?: $setting->logo_width ?: 220);
        $headerLogoHeight = $this->firstIntegerFrom($data, ['header_logo_height', 'logo_height'], $setting->header_logo_height ?: $setting->logo_height);
        $headerMobileLogoWidth = $this->firstIntegerFrom($data, ['header_mobile_logo_width', 'mobile_logo_width'], $setting->header_mobile_logo_width ?: $setting->mobile_logo_width ?: 168);
        $headerKeepAspectRatio = $this->booleanFromRequest($request, ['header_logo_keep_aspect_ratio', 'logo_keep_aspect_ratio'], $setting->header_logo_keep_aspect_ratio ?? $setting->logo_keep_aspect_ratio ?? true);
        $headerDisplayMode = $this->firstStringFrom($data, ['header_logo_display_mode'], $setting->header_logo_display_mode ?: 'original');

        $data['header_logo_enabled'] = $this->booleanFromRequest($request, ['header_logo_enabled'], $setting->header_logo_enabled ?? true);
        $data['header_is_sticky'] = $this->booleanFromRequest($request, ['header_is_sticky'], $setting->header_is_sticky ?? true);
        $data['header_logo_keep_aspect_ratio'] = $headerKeepAspectRatio;
        $data['header_logo_display_mode'] = $headerDisplayMode;
        $data['header_logo_path'] = $headerLogoPath;
        $data['header_logo_width'] = $headerLogoWidth;
        $data['header_logo_height'] = $headerLogoHeight;
        $data['header_mobile_logo_width'] = $headerMobileLogoWidth;
        $data['header_vertical_padding'] = $data['header_vertical_padding'] ?? ($setting->header_vertical_padding ?: 8);
        $data['header_logo_position_en'] = $this->firstStringFrom($data, ['header_logo_position_en'], $setting->header_logo_position_en ?: 'left');
        $data['header_logo_position_ar'] = $this->firstStringFrom($data, ['header_logo_position_ar'], $setting->header_logo_position_ar ?: 'right');
        $data['header_menu_position_en'] = $this->firstStringFrom($data, ['header_menu_position_en'], $setting->header_menu_position_en ?: 'left');
        $data['header_menu_position_ar'] = $this->firstStringFrom($data, ['header_menu_position_ar'], $setting->header_menu_position_ar ?: 'right');

        // Keep legacy columns in sync for older environments and existing reads.
        $data['logo_path'] = $headerLogoPath;
        $data['logo_width'] = $headerLogoWidth;
        $data['logo_height'] = $headerLogoHeight;
        $data['mobile_logo_width'] = $headerMobileLogoWidth;
        $data['logo_keep_aspect_ratio'] = $headerKeepAspectRatio;

        $setting->update($this->filterExistingSettingColumns($data));

        return back()->with('success', 'Header settings updated successfully.');
    }

    protected function integerOrFallback(array $data, string $key, ?int $fallback = null): ?int
    {
        if (! array_key_exists($key, $data)) {
            return $fallback;
        }

        if ($data[$key] === null || $data[$key] === '') {
            return $fallback;
        }

        return (int) $data[$key];
    }

    protected function firstIntegerFrom(array $data, array $keys, ?int $fallback = null): ?int
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $data)) {
                return $this->integerOrFallback($data, $key, $fallback);
            }
        }

        return $fallback;
    }

    protected function booleanFromRequest(Request $request, array $keys, bool $fallback = false): bool
    {
        foreach ($keys as $key) {
            if ($request->exists($key)) {
                return $request->boolean($key);
            }
        }

        return $fallback;
    }

    protected function firstStringFrom(array $data, array $keys, string $fallback): string
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $data) && is_string($data[$key]) && trim($data[$key]) !== '') {
                return trim($data[$key]);
            }
        }

        return $fallback;
    }
}
