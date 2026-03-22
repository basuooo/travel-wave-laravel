<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'profile_image',
        'password',
        'is_admin',
        'is_active',
        'preferred_language',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function permissionOverrides()
    {
        return $this->belongsToMany(Permission::class, 'user_permission_overrides')
            ->withPivot('is_allowed')
            ->withTimestamps();
    }

    public function directAllowedPermissions(): Collection
    {
        return $this->permissionOverrides->where('pivot.is_allowed', true)->values();
    }

    public function directDeniedPermissions(): Collection
    {
        return $this->permissionOverrides->where('pivot.is_allowed', false)->values();
    }

    public function effectivePermissionSlugs(): Collection
    {
        if (! Schema::hasTable('roles') || ! Schema::hasTable('permissions')) {
            return collect($this->is_admin ? ['*'] : []);
        }

        if ($this->is_admin && ! $this->roles()->exists()) {
            return collect(['*']);
        }

        if ($this->roles->contains(fn (Role $role) => $role->slug === 'super-admin')) {
            return collect(['*']);
        }

        $granted = $this->roles
            ->loadMissing('permissions')
            ->flatMap(fn (Role $role) => $role->permissions->pluck('slug'))
            ->unique()
            ->values();

        $granted = $granted->merge($this->directAllowedPermissions()->pluck('slug'))->unique()->values();
        $denied = $this->directDeniedPermissions()->pluck('slug');

        return $granted->reject(fn (string $slug) => $denied->contains($slug))->values();
    }

    public function hasPermission(string $slug): bool
    {
        if ($this->is_admin && ! Schema::hasTable('roles')) {
            return true;
        }

        $permissions = $this->effectivePermissionSlugs();

        return $permissions->contains('*') || $permissions->contains($slug);
    }

    public function canAccessDashboard(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->is_admin && (! Schema::hasTable('roles') || ! $this->roles()->exists())) {
            return true;
        }

        return $this->hasPermission('dashboard.access');
    }

    public function syncPermissionOverrides(array $allowed = [], array $denied = []): void
    {
        if (! Schema::hasTable('user_permission_overrides')) {
            return;
        }

        $payload = collect($allowed)->filter()->mapWithKeys(fn ($id) => [(int) $id => ['is_allowed' => true]])->all();
        $payload = array_replace(
            $payload,
            collect($denied)->filter()->mapWithKeys(fn ($id) => [(int) $id => ['is_allowed' => false]])->all()
        );

        $this->permissionOverrides()->sync($payload);
    }
}
