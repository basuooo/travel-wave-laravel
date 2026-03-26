<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoalTarget extends Model
{
    use HasFactory;

    public const PERIOD_DAILY = 'daily';
    public const PERIOD_WEEKLY = 'weekly';
    public const PERIOD_MONTHLY = 'monthly';
    public const PERIOD_CUSTOM = 'custom';

    protected $fillable = [
        'user_id',
        'target_type',
        'target_value',
        'period_type',
        'period_start',
        'period_end',
        'note',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'target_value' => 'decimal:2',
        'period_start' => 'date',
        'period_end' => 'date',
        'is_active' => 'boolean',
        'created_by' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function periodTypeOptions(): array
    {
        return [
            self::PERIOD_DAILY => __('admin.goals_targets_ui_period_daily'),
            self::PERIOD_WEEKLY => __('admin.goals_targets_ui_period_weekly'),
            self::PERIOD_MONTHLY => __('admin.goals_targets_ui_period_monthly'),
            self::PERIOD_CUSTOM => __('admin.goals_targets_ui_period_custom'),
        ];
    }

    public function localizedPeriodType(): string
    {
        return self::periodTypeOptions()[$this->period_type] ?? $this->period_type;
    }
}
