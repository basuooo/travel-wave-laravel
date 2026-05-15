<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotKnowledgeEntry extends Model
{
    use HasFactory;
    use HasLocalizedContent;

    protected $fillable = [
        'title_en',
        'title_ar',
        'question_en',
        'question_ar',
        'answer_en',
        'answer_ar',
        'keywords_en',
        'keywords_ar',
        'category_en',
        'category_ar',
        'is_active',
        'match_type',
        'priority',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function localizedTitle(?string $locale = null): string
    {
        return (string) $this->localized('title', $locale, '');
    }

    public function localizedQuestion(?string $locale = null): string
    {
        return (string) $this->localized('question', $locale, '');
    }

    public function localizedAnswer(?string $locale = null): string
    {
        return (string) $this->localized('answer', $locale, '');
    }

    public function localizedCategory(?string $locale = null): string
    {
        return (string) $this->localized('category', $locale, '');
    }

    public function localizedKeywords(?string $locale = null): string
    {
        return (string) $this->localized('keywords', $locale, '');
    }
}
