<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\Setting;
use App\Models\VisaCategory;
use App\Models\VisaCountry;
use App\Support\LeadFormManager;
use Illuminate\Http\Request;

class FloatingWhatsappSettingController extends Controller
{
    public function edit()
    {
        return view('admin.floating-whatsapp-settings.edit', [
            'setting' => Setting::query()->firstOrCreate([]),
            'visibilityTargets' => $this->visibilityTargetOptions(),
            'visibilityModes' => $this->visibilityModes(),
            'positionOptions' => $this->positionOptions(),
            'animationOptions' => $this->animationOptions(),
        ]);
    }

    public function update(Request $request)
    {
        $setting = Setting::query()->firstOrCreate([]);

        $data = $request->validate([
            'floating_whatsapp_number' => ['nullable', 'string', 'max:40'],
            'floating_whatsapp_message_en' => ['nullable', 'string'],
            'floating_whatsapp_message_ar' => ['nullable', 'string'],
            'floating_whatsapp_button_text_en' => ['nullable', 'string', 'max:255'],
            'floating_whatsapp_button_text_ar' => ['nullable', 'string', 'max:255'],
            'floating_whatsapp_position' => ['nullable', 'in:bottom_right,bottom_left'],
            'floating_whatsapp_animation_style' => ['nullable', 'in:pulse,float,bounce,glow'],
            'floating_whatsapp_animation_speed' => ['nullable', 'integer', 'min:1000', 'max:10000'],
            'floating_whatsapp_background_color' => ['nullable', 'string', 'max:20'],
            'floating_whatsapp_visibility_mode' => ['nullable', 'in:all,exclude_selected,only_selected'],
            'floating_whatsapp_visibility_targets' => ['array'],
            'floating_whatsapp_visibility_targets.*' => ['nullable', 'string', 'max:255'],
        ]);

        $setting->update([
            'floating_whatsapp_enabled' => $request->boolean('floating_whatsapp_enabled', true),
            'floating_whatsapp_number' => $data['floating_whatsapp_number'] ?? null,
            'floating_whatsapp_message_en' => $data['floating_whatsapp_message_en'] ?? null,
            'floating_whatsapp_message_ar' => $data['floating_whatsapp_message_ar'] ?? null,
            'floating_whatsapp_button_text_en' => $data['floating_whatsapp_button_text_en'] ?? null,
            'floating_whatsapp_button_text_ar' => $data['floating_whatsapp_button_text_ar'] ?? null,
            'floating_whatsapp_show_icon' => $request->boolean('floating_whatsapp_show_icon', true),
            'floating_whatsapp_position' => $data['floating_whatsapp_position'] ?? 'bottom_right',
            'floating_whatsapp_animation_style' => $data['floating_whatsapp_animation_style'] ?? 'pulse',
            'floating_whatsapp_animation_speed' => $data['floating_whatsapp_animation_speed'] ?? 3200,
            'floating_whatsapp_show_desktop' => $request->boolean('floating_whatsapp_show_desktop', true),
            'floating_whatsapp_show_mobile' => $request->boolean('floating_whatsapp_show_mobile', true),
            'floating_whatsapp_background_color' => $data['floating_whatsapp_background_color'] ?? null,
            'floating_whatsapp_visibility_mode' => $data['floating_whatsapp_visibility_mode'] ?? 'all',
            'floating_whatsapp_visibility_targets' => collect($request->input('floating_whatsapp_visibility_targets', []))
                ->filter(fn ($item) => filled($item))
                ->values()
                ->all(),
        ]);

        return back()->with('success', 'Floating WhatsApp settings updated successfully.');
    }

    protected function visibilityModes(): array
    {
        return [
            'all' => 'Show on all pages',
            'exclude_selected' => 'Show everywhere except selected pages',
            'only_selected' => 'Show only on selected pages',
        ];
    }

    protected function positionOptions(): array
    {
        return [
            'bottom_right' => 'Bottom right',
            'bottom_left' => 'Bottom left',
        ];
    }

    protected function animationOptions(): array
    {
        return [
            'pulse' => 'Pulse',
            'float' => 'Floating motion',
            'bounce' => 'Soft bounce',
            'glow' => 'Glow',
        ];
    }

    protected function visibilityTargetOptions(): array
    {
        return [
            'Specific pages' => collect(LeadFormManager::pageKeyOptions())
                ->mapWithKeys(fn ($label, $key) => ['page_key|' . $key => $label])
                ->all(),
            'Page groups' => collect(LeadFormManager::pageGroupOptions())
                ->mapWithKeys(fn ($label, $key) => ['page_group|' . $key => $label])
                ->all(),
            'Visa destinations' => VisaCountry::query()->orderBy('sort_order')->get()
                ->mapWithKeys(fn (VisaCountry $country) => ['visa_country|' . $country->id => $country->name_en . ' / ' . $country->name_ar])
                ->all(),
            'Visa categories' => VisaCategory::query()->orderBy('sort_order')->get()
                ->mapWithKeys(fn (VisaCategory $category) => ['visa_category|' . $category->id => $category->name_en . ' / ' . $category->name_ar])
                ->all(),
            'Domestic destinations' => Destination::query()->orderBy('sort_order')->get()
                ->mapWithKeys(fn (Destination $destination) => ['destination|' . $destination->id => $destination->title_en . ' / ' . $destination->title_ar])
                ->all(),
            'Destination types' => collect(LeadFormManager::destinationTypeOptions())
                ->mapWithKeys(fn ($label, $key) => ['destination_type|' . $key => $label])
                ->all(),
        ];
    }
}
