<?php

namespace App\Support;

use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class CrmLeadAccess
{
    public static function canViewAll(?User $user): bool
    {
        return $user && $user->hasPermission('leads.view_all');
    }

    public static function applyVisibilityScope(Builder $query, ?User $user): Builder
    {
        if (static::canViewAll($user)) {
            return $query;
        }

        return $query->where('assigned_user_id', $user?->id ?: 0);
    }

    public static function canAccessLead(?User $user, Inquiry $lead): bool
    {
        return static::canViewAll($user) || (int) $lead->assigned_user_id === (int) $user?->id;
    }
}
