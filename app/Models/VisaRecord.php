<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisaRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'visa_country_id',
        'visa_type',
        'visa_type_slug',
        'price',
        'currency',
        'working_days',
        'proposed_duration',
        'stay_duration',
        'entries_count',
        'required_documents',
        'embassy_fee',
        'embassy_fee_currency',
        'embassy_fee_payment_method',
        'application_center',
        'is_biometrics_required',
        'is_interview_required',
        'notes',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'application_center' => 'array',
        'is_biometrics_required' => 'boolean',
        'is_interview_required' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(VisaCountry::class, 'visa_country_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(VisaActivityLog::class, 'visa_record_id')->latest();
    }

    public function getApplicationCentersListAttribute(): array
    {
        if (is_array($this->application_center)) {
            return $this->application_center;
        }

        if (filled($this->application_center)) {
            return array_map('trim', explode(',', $this->application_center));
        }

        return [];
    }

    public function getApplicationCentersFormattedAttribute(): string
    {
        $centers = $this->application_centers_list;

        return ! empty($centers) ? implode('، ', $centers) : 'غير محدد';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'نشطة',
            'temporarily_unavailable' => 'متوقفة مؤقتاً',
            'inactive' => 'غير متاحة',
            default => 'نشطة',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'active' => 'bg-success',
            'temporarily_unavailable' => 'bg-warning text-dark',
            'inactive' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function toFormattedShareText(): string
    {
        $countryName = $this->country ? ($this->country->name_ar ?: $this->country->name_en) : 'الدولة';
        $categories = $this->country && $this->country->categories->count() > 0
            ? $this->country->categories->pluck('name_ar')->implode('، ')
            : ($this->country?->category?->name_ar ?: 'عام');

        $priceStr = $this->price !== null ? number_format($this->price, 0) . ' ' . $this->currency : 'غير محدد';
        $embassyFeeStr = $this->embassy_fee ? $this->embassy_fee . ' ' . $this->embassy_fee_currency : 'غير محدد';

        if (filled($this->embassy_fee_payment_method)) {
            $embassyFeeStr .= ' (' . $this->embassy_fee_payment_method . ')';
        }

        $text = "🌍 *تفاصيل تأشيرة {$countryName}*\n";
        $text .= "-----------------------------------\n";
        $text .= "📌 *التصنيف:* {$categories}\n";
        $text .= "📋 *نوع التأشيرة:* {$this->visa_type}\n";
        $text .= "💵 *سعر التأشيرة للعميل:* {$priceStr}\n";
        $text .= "🏛️ *رسوم السفيرة:* {$embassyFeeStr}\n";
        $text .= "⏱️ *عدد أيام العمل:* " . ($this->working_days ?: 'غير محدد') . "\n";
        $text .= "⏳ *مدة التأشيرة المقترحة:* " . ($this->proposed_duration ?: 'غير محدد') . "\n";

        if (filled($this->stay_duration)) {
            $text .= "📅 *مدة الإقامة:* {$this->stay_duration}\n";
        }

        if (filled($this->entries_count)) {
            $text .= "🚪 *عدد مرات الدخول:* {$this->entries_count}\n";
        }

        $text .= "📍 *مكان التقديم:* {$this->application_centers_formatted}\n";
        $text .= "☝️ *البصمة مطلوبة:* " . ($this->is_biometrics_required ? 'نعم' : 'لا') . "\n";
        $text .= "🗣️ *المقابلة مطلوبة:* " . ($this->is_interview_required ? 'نعم' : 'لا') . "\n";

        if (filled($this->required_documents)) {
            $text .= "\n📄 *الأوراق المطلوبة:*\n" . trim($this->required_documents) . "\n";
        }

        if (filled($this->notes)) {
            $text .= "\n💡 *ملاحظات هامة:*\n" . trim($this->notes) . "\n";
        }

        $text .= "\n--- \nTravel Wave ✈️";

        return $text;
    }
}
