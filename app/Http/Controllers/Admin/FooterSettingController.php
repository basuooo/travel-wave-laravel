<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesCmsData;
use App\Http\Controllers\Concerns\InteractsWithSettingColumns;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class FooterSettingController extends Controller
{
    use HandlesCmsData;
    use InteractsWithSettingColumns;

    public function edit()
    {
        return view('admin.footer-settings.edit', [
            'setting' => Setting::query()->firstOrCreate([]),
        ]);
    }

    public function update(Request $request)
    {
        $setting = Setting::query()->firstOrCreate([]);

        $rules = [
            'footer_background_color' => ['nullable', 'string', 'max:20'],
            'footer_text_color' => ['nullable', 'string', 'max:20'],
            'footer_link_color' => ['nullable', 'string', 'max:20'],
            'footer_hover_color' => ['nullable', 'string', 'max:20'],
            'footer_heading_color' => ['nullable', 'string', 'max:20'],
            'footer_button_color' => ['nullable', 'string', 'max:20'],
            'footer_button_text_color' => ['nullable', 'string', 'max:20'],
            'footer_vertical_padding' => ['nullable', 'integer', 'min:24', 'max:180'],
            'footer_logo_width' => ['nullable', 'integer', 'min:60', 'max:520'],
            'footer_logo_height' => ['nullable', 'integer', 'min:20', 'max:220'],
            'footer_logo_keep_aspect_ratio' => ['nullable', 'boolean'],
            'footer_logo_display_mode' => ['nullable', 'in:original,contain,cover,custom'],
            'footer_text_en' => ['nullable', 'string'],
            'footer_text_ar' => ['nullable', 'string'],
            'copyright_text_en' => ['nullable', 'string'],
            'copyright_text_ar' => ['nullable', 'string'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'secondary_phone' => ['nullable', 'string', 'max:255'],
            'whatsapp_number' => ['nullable', 'string', 'max:255'],
            'address_en' => ['nullable', 'string'],
            'address_ar' => ['nullable', 'string'],
            'facebook_url' => ['nullable', 'url'],
            'instagram_url' => ['nullable', 'url'],
            'twitter_url' => ['nullable', 'url'],
            'youtube_url' => ['nullable', 'url'],
            'linkedin_url' => ['nullable', 'url'],
            'snapchat_url' => ['nullable', 'url'],
            'telegram_url' => ['nullable', 'url'],
            'tiktok_url' => ['nullable', 'url'],
        ];

        if ($request->hasFile('footer_logo')) {
            $rules['footer_logo'] = ['nullable', 'image', 'mimes:png,jpg,jpeg,webp'];
        }

        $data = $request->validate($rules);

        $data['footer_logo_path'] = $this->uploadFile($request, 'footer_logo', 'settings', $setting->footer_logo_path);
        $data['footer_logo_display_mode'] = $this->stringOrFallback($data, 'footer_logo_display_mode', $setting->footer_logo_display_mode ?: 'original');
        $data['footer_logo_width'] = $this->integerOrFallback($data, 'footer_logo_width', $setting->footer_logo_width ?: 200);
        $data['footer_logo_height'] = $this->integerOrFallback($data, 'footer_logo_height', $setting->footer_logo_height);
        $data['footer_logo_keep_aspect_ratio'] = $request->boolean('footer_logo_keep_aspect_ratio');
        $data['footer_quick_links'] = $this->mapFooterLinks($request->input('footer_quick_links', []));
        $data['footer_vertical_padding'] = $this->integerOrFallback($data, 'footer_vertical_padding', $setting->footer_vertical_padding ?: 80);

        $setting->update($this->filterExistingSettingColumns($data));

        return back()->with('success', 'Footer settings updated successfully.');
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

    protected function stringOrFallback(array $data, string $key, string $fallback): string
    {
        if (! array_key_exists($key, $data) || ! is_string($data[$key]) || trim($data[$key]) === '') {
            return $fallback;
        }

        return trim($data[$key]);
    }

    protected function mapFooterLinks(array $links): array
    {
        $mapped = [];

        foreach ($links as $index => $link) {
            if (! is_array($link)) {
                continue;
            }

            $item = [
                'title_en' => trim($link['title_en'] ?? ''),
                'title_ar' => trim($link['title_ar'] ?? ''),
                'url' => trim($link['url'] ?? ''),
            ];

            if (collect($item)->filter()->isEmpty()) {
                continue;
            }

            $mapped[] = $item + ['sort_order' => $index + 1];
        }

        return $mapped;
    }
}
