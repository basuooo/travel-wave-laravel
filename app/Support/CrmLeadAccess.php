<?php

namespace App\Support;

use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class CrmLeadAccess
{
    public static function canViewAll(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->is_admin && ! $user->roles()->exists()) {
            return true;
        }

        $user->loadMissing('roles');

        return $user->roles->contains(fn ($role) => in_array($role->slug, ['super-admin', 'admin'], true));
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
