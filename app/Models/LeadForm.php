<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadForm extends Model
{
    use HasFactory;
    use HasLocalizedContent;

    protected $fillable = [
        'name',
        'slug',
        'form_category',
        'title_en',
        'title_ar',
        'subtitle_en',
        'subtitle_ar',
        'submit_text_en',
        'submit_text_ar',
        'success_message_en',
        'success_message_ar',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public function fields()
    {
        return $this->hasMany(LeadFormField::class)->orderBy('sort_order')->orderBy('id');
    }

    public function assignments()
    {
        return $this->hasMany(LeadFormAssignment::class)->orderBy('sort_order')->orderBy('id');
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }

    public function getThankYouSetting(string $key, mixed $default = null): mixed
    {
        $settings = $this->settings ?: [];
        return $settings['thank_you'][$key] ?? $default;
    }

    public function thankYouAction(): string
    {
        return $this->getThankYouSetting('action', 'thank_you_page');
    }

    public function thankYouTitle(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        return $this->getThankYouSetting("title_{$locale}")
            ?: ($this->getThankYouSetting('title_ar')
            ?: ($this->getThankYouSetting('title_en')
            ?: 'تم إرسال طلبك بنجاح 🎉'));
    }

    public function thankYouMessage(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        return $this->getThankYouSetting("message_{$locale}")
            ?: ($this->getThankYouSetting('message_ar')
            ?: ($this->getThankYouSetting('message_en')
            ?: 'شكراً لك! تم استلام بياناتك بنجاح وسيقوم فريق المبيعات بالتواصل معك في أقرب وقت.'));
    }

    public function thankYouBgColor(): string
    {
        return $this->getThankYouSetting('bg_color', '#ffffff');
    }

    public function thankYouTextColor(): string
    {
        return $this->getThankYouSetting('text_color', '#212529');
    }

    public function thankYouRedirectUrl(): ?string
    {
        return $this->getThankYouSetting('redirect_url');
    }

    public function thankYouCustomHtml(): ?string
    {
        return $this->getThankYouSetting('custom_html');
    }

    public function thankYouCustomCss(): ?string
    {
        return $this->getThankYouSetting('custom_css');
    }
}
