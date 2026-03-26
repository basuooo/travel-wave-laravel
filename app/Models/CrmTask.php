<?php

namespace App\Models;

use App\Support\CrmLeadAccess;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class CrmTask extends Model
{
    use HasFactory;

    public const TYPE_LEAD = 'lead';
    public const TYPE_GENERAL = 'general';
    public const TYPE_TEAM = 'team';

    public const STATUS_NEW = 'new';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_WAITING = 'waiting';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';

    public const CATEGORY_CUSTOMER_FOLLOWUP = 'customer_followup';
    public const CATEGORY_DOCUMENTS = 'documents';
    public const CATEGORY_BOOKING = 'booking';
    public const CATEGORY_COLLECTION = 'collection';
    public const CATEGORY_INTERNAL = 'internal';
    public const CATEGORY_ACCOUNTING = 'accounting';
    public const CATEGORY_MARKETING = 'marketing';

    protected $fillable = [
        'inquiry_id',
        'assigned_user_id',
        'created_by',
        'title',
        'description',
        'task_type',
        'category',
        'priority',
        'status',
        'due_at',
        'completed_at',
        'last_activity_at',
        'closed_by',
        'closed_note',
        'notes',
    ];

    protected $casts = [
        'inquiry_id' => 'integer',
        'assigned_user_id' => 'integer',
        'created_by' => 'integer',
        'closed_by' => 'integer',
        'due_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function closer()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function activities()
    {
        return $this->hasMany(CrmTaskActivity::class)->latest();
    }

    public function documents()
    {
        return $this->morphMany(CrmDocument::class, 'documentable')->latest('uploaded_at');
    }

    public function scopeVisibleTo($query, User $user)
    {
        if (CrmLeadAccess::canViewAll($user)) {
            return $query;
        }

        return $query->where(function ($builder) use ($user) {
            $builder->where('assigned_user_id', $user->id)
                ->orWhere('created_by', $user->id);
        });
    }

    public function scopeDelayed($query)
    {
        return $query
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('due_at', today());
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_NEW => ['ar' => 'جديد', 'en' => 'New'],
            self::STATUS_IN_PROGRESS => ['ar' => 'جاري العمل', 'en' => 'In Progress'],
            self::STATUS_WAITING => ['ar' => 'بانتظار رد', 'en' => 'Waiting'],
            self::STATUS_COMPLETED => ['ar' => 'مكتمل', 'en' => 'Completed'],
            self::STATUS_CANCELLED => ['ar' => 'ملغي', 'en' => 'Cancelled'],
        ];
    }

    public static function priorityOptions(): array
    {
        return [
            self::PRIORITY_LOW => ['ar' => 'منخفض', 'en' => 'Low'],
            self::PRIORITY_MEDIUM => ['ar' => 'متوسط', 'en' => 'Medium'],
            self::PRIORITY_HIGH => ['ar' => 'عالي', 'en' => 'High'],
            self::PRIORITY_URGENT => ['ar' => 'عاجل', 'en' => 'Urgent'],
        ];
    }

    public static function typeOptions(): array
    {
        return [
            self::TYPE_LEAD => ['ar' => 'مرتبطة بليد', 'en' => 'Lead Task'],
            self::TYPE_GENERAL => ['ar' => 'مهمة داخلية', 'en' => 'General Task'],
            self::TYPE_TEAM => ['ar' => 'مهمة فريق', 'en' => 'Team Task'],
        ];
    }

    public static function categoryOptions(): array
    {
        return [
            self::CATEGORY_CUSTOMER_FOLLOWUP => ['ar' => 'متابعة عميل', 'en' => 'Customer Follow-up'],
            self::CATEGORY_DOCUMENTS => ['ar' => 'مستندات', 'en' => 'Documents'],
            self::CATEGORY_BOOKING => ['ar' => 'حجز', 'en' => 'Booking'],
            self::CATEGORY_COLLECTION => ['ar' => 'تحصيل', 'en' => 'Collection'],
            self::CATEGORY_INTERNAL => ['ar' => 'داخلي', 'en' => 'Internal'],
            self::CATEGORY_ACCOUNTING => ['ar' => 'محاسبة', 'en' => 'Accounting'],
            self::CATEGORY_MARKETING => ['ar' => 'تسويق', 'en' => 'Marketing'],
        ];
    }

    public function isDelayed(): bool
    {
        return ! in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED], true)
            && $this->due_at instanceof Carbon
            && $this->due_at->isPast();
    }

    public function localizedStatus(): string
    {
        if ($this->isDelayed()) {
            return app()->getLocale() === 'ar' ? 'متأخر' : 'Delayed';
        }

        $options = static::statusOptions();

        return $options[$this->status][app()->getLocale() === 'ar' ? 'ar' : 'en']
            ?? ucfirst(str_replace('_', ' ', (string) $this->status));
    }

    public function localizedWorkflowStatus(): string
    {
        $options = static::statusOptions();

        return $options[$this->status][app()->getLocale() === 'ar' ? 'ar' : 'en']
            ?? ucfirst(str_replace('_', ' ', (string) $this->status));
    }

    public function localizedPriority(): string
    {
        $options = static::priorityOptions();

        return $options[$this->priority][app()->getLocale() === 'ar' ? 'ar' : 'en']
            ?? ucfirst((string) $this->priority);
    }

    public function localizedType(): string
    {
        $options = static::typeOptions();

        return $options[$this->task_type][app()->getLocale() === 'ar' ? 'ar' : 'en']
            ?? ucfirst((string) $this->task_type);
    }

    public function localizedCategory(): string
    {
        $options = static::categoryOptions();

        return $options[$this->category][app()->getLocale() === 'ar' ? 'ar' : 'en']
            ?? ucfirst(str_replace('_', ' ', (string) $this->category));
    }

    public function visualStatus(): string
    {
        if ($this->isDelayed()) {
            return 'danger';
        }

        return match ($this->status) {
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELLED => 'secondary',
            self::STATUS_WAITING => 'warning',
            self::STATUS_IN_PROGRESS => 'primary',
            default => 'light',
        };
    }

    public function priorityClass(): string
    {
        return match ($this->priority) {
            self::PRIORITY_URGENT => 'danger',
            self::PRIORITY_HIGH => 'warning',
            self::PRIORITY_MEDIUM => 'primary',
            default => 'secondary',
        };
    }

    public function priorityBadgeStyle(): string
    {
        return match ($this->priority) {
            self::PRIORITY_URGENT => 'background:#dc3545;color:#fff;',
            self::PRIORITY_HIGH => 'background:#fd7e14;color:#fff;',
            self::PRIORITY_MEDIUM => 'background:#0d6efd;color:#fff;',
            default => 'background:#6c757d;color:#fff;',
        };
    }

    public function typeBadgeClass(): string
    {
        return match ($this->task_type) {
            self::TYPE_LEAD => 'primary',
            self::TYPE_TEAM => 'warning',
            default => 'secondary',
        };
    }

    public function categoryBadgeClass(): string
    {
        return match ($this->category) {
            self::CATEGORY_CUSTOMER_FOLLOWUP => 'primary',
            self::CATEGORY_DOCUMENTS => 'info',
            self::CATEGORY_BOOKING => 'success',
            self::CATEGORY_COLLECTION => 'warning',
            self::CATEGORY_ACCOUNTING => 'dark',
            self::CATEGORY_MARKETING => 'danger',
            default => 'secondary',
        };
    }

    public function overdueLabel(): ?string
    {
        if (! $this->isDelayed() || ! $this->due_at instanceof Carbon) {
            return null;
        }

        $days = max(1, $this->due_at->diffInDays(now()));

        if (app()->getLocale() === 'ar') {
            return 'متأخرة منذ ' . $days . ' يوم';
        }

        return 'Overdue by ' . $days . ' day' . ($days === 1 ? '' : 's');
    }
}
