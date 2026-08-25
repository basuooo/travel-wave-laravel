<?php

use App\Models\Permission;
use App\Models\Role;
use App\Support\AccessControl;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        AccessControl::syncPermissionsInDatabase();

        $allPermissionIds = Permission::query()->pluck('id')->all();
        $targetSlugs = [
            'visa_database.view', 'visa_database.create', 'visa_database.edit', 'visa_database.delete', 'visa_database.manage',
            'embassy_appointments.view', 'embassy_appointments.create', 'embassy_appointments.edit', 'embassy_appointments.delete', 'embassy_appointments.manage',
        ];

        $targetIds = Permission::query()
            ->whereIn('slug', $targetSlugs)
            ->pluck('id')
            ->all();

        $admin = Role::query()->where('slug', 'admin')->first();
        $superAdmin = Role::query()->where('slug', 'super-admin')->first();

        if ($superAdmin) {
            $superAdmin->permissions()->sync($allPermissionIds);
        }

        if ($admin) {
            $admin->permissions()->syncWithoutDetaching($targetIds);
        }

        $salesManager = Role::query()->where('slug', 'sales-leads-manager')->first();
        $salesTeamLeader = Role::query()->where('slug', 'sales-team-leader')->first();
        $viewerAnalyst = Role::query()->where('slug', 'viewer-analyst')->first();

        $viewAndEditIds = Permission::query()
            ->whereIn('slug', ['visa_database.view', 'embassy_appointments.view', 'embassy_appointments.edit'])
            ->pluck('id')
            ->all();

        $viewOnlyIds = Permission::query()
            ->whereIn('slug', ['visa_database.view', 'embassy_appointments.view'])
            ->pluck('id')
            ->all();

        if ($salesManager) {
            $salesManager->permissions()->syncWithoutDetaching($viewAndEditIds);
        }
        if ($salesTeamLeader) {
            $salesTeamLeader->permissions()->syncWithoutDetaching($viewAndEditIds);
        }
        if ($viewerAnalyst) {
            $viewerAnalyst->permissions()->syncWithoutDetaching($viewOnlyIds);
        }
    }

    public function down(): void
    {
    }
};
