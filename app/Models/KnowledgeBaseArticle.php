<?php

namespace App\Models;

use App\Support\CrmLeadAccess;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeBaseArticle extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    public const SCOPE_ALL_STAFF = 'all_staff';
    public const SCOPE_ADMINS = 'admins';
    public const SCOPE_SELLERS = 'sellers';
    public const SCOPE_ACCOUNTING = 'accounting';

    protected $fillable = [
        'knowledge_base_category_id',
        'title',
        'slug',
        'summary',
        'content',
        'status',
        'visibility_scope',
        'is_featured',
        'sort_order',
        'created_by',
        'updated_by',
        'published_at',
        'meta',
    ];

    protected $casts = [
        'knowledge_base_category_id' => 'integer',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'published_at' => 'datetime',
        'meta' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(KnowledgeBaseCategory::class, 'knowledge_base_category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_DRAFT => __('admin.kb_status_draft'),
            self::STATUS_PUBLISHED => __('admin.kb_status_published'),
            self::STATUS_ARCHIVED => __('admin.kb_status_archived'),
        ];
    }

    public static function visibilityOptions(): array
    {
        return [
            self::SCOPE_ALL_STAFF => __('admin.kb_scope_all_staff'),
            self::SCOPE_ADMINS => __('admin.kb_scope_admins'),
            self::SCOPE_SELLERS => __('admin.kb_scope_sellers'),
            self::SCOPE_ACCOUNTING => __('admin.kb_scope_accounting'),
        ];
    }

    public function localizedStatus(): string
    {
        return static::statusOptions()[$this->status] ?? $this->status;
    }

    public function localizedVisibilityScope(): string
    {
        return static::visibilityOptions()[$this->visibility_scope] ?? $this->visibility_scope;
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_PUBLISHED => 'success',
            self::STATUS_ARCHIVED => 'secondary',
            default => 'warning',
        };
    }

    public function allowsUser(User $user): bool
    {
        return match ($this->visibility_scope) {
            self::SCOPE_ADMINS => CrmLeadAccess::canViewAll($user),
            self::SCOPE_SELLERS => ! CrmLeadAccess::canViewAll($user) && $user->hasPermission('leads.view'),
            self::SCOPE_ACCOUNTING => $user->hasPermission('accounting.view'),
            default => $user->canAccessDashboard(),
        };
    }

    public function scopeReadableBy($query, User $user, bool $includeUnpublished = false)
    {
        if ($includeUnpublished) {
            return $query;
        }

        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->where(function ($builder) use ($user) {
                $builder->where('visibility_scope', self::SCOPE_ALL_STAFF);

                if (CrmLeadAccess::canViewAll($user)) {
                    $builder->orWhere('visibility_scope', self::SCOPE_ADMINS);
                }

                if (! CrmLeadAccess::canViewAll($user) && $user->hasPermission('leads.view')) {
                    $builder->orWhere('visibility_scope', self::SCOPE_SELLERS);
                }

                if ($user->hasPermission('accounting.view')) {
                    $builder->orWhere('visibility_scope', self::SCOPE_ACCOUNTING);
                }
            });
    }
}
