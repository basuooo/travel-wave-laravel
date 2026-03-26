<?php

namespace App\Models;

use App\Support\HasLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    use HasFactory;
    use HasLocalizedContent;

    protected $fillable = [
        'image_path',
        'mobile_image_path',
        'image_framing',
        'headline_en',
        'headline_ar',
        'subtitle_en',
        'subtitle_ar',
        'cta_text_en',
        'cta_text_ar',
        'cta_link',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'image_framing' => 'array',
    ];

    public const FRAME_TARGETS = [
        'desktop_banner',
        'mobile_banner',
    ];

    public function framingFor(string $target): array
    {
        $framing = (array) ($this->image_framing ?? []);
        $values = (array) ($framing[$target] ?? []);

        return [
            'x' => $this->normalizeFrameCoordinate($values['x'] ?? 50),
            'y' => $this->normalizeFrameCoordinate($values['y'] ?? 50),
        ];
    }

    public function framingCssPosition(string $target): string
    {
        $framing = $this->framingFor($target);

        return $framing['x'] . '% ' . $framing['y'] . '%';
    }

    protected function normalizeFrameCoordinate(mixed $value): float
    {
        return max(0, min(100, round((float) $value, 2)));
    }
}
