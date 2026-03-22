<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Support\AccessControl;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:roles.manage');
    }

    public function index()
    {
        return view('admin.roles.index', [
            'items' => Role::query()->withCount(['users', 'permissions'])->orderBy('name')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.roles.form', $this->formViewData(new Role([
            'is_system' => false,
        ])));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $role = Role::create($data['role']);
        $role->permissions()->sync($data['permission_ids']);

        return redirect()->route('admin.roles.index')->with('success', __('admin.role_saved'));
    }

    public function edit(Role $role)
    {
        $role->load('permissions');

        return view('admin.roles.form', $this->formViewData($role));
    }

    public function update(Request $request, Role $role)
    {
        $data = $this->validatedData($request, $role);

        if ($role->is_system) {
            unset($data['role']['slug']);
        }

        $role->update($data['role']);
        $role->permissions()->sync($data['permission_ids']);

        return redirect()->route('admin.roles.index')->with('success', __('admin.role_updated'));
    }

    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return back()->withErrors(__('admin.cannot_delete_system_role'));
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', __('admin.role_deleted'));
    }

    protected function validatedData(Request $request, ?Role $role = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('roles', 'slug')->ignore($role?->id)],
            'description' => ['nullable', 'string'],
            'permissions' => ['array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        return [
            'role' => [
                'name' => $validated['name'],
                'slug' => $role && $role->is_system ? $role->slug : ($validated['slug'] ?: Str::slug($validated['name'])),
                'description' => $validated['description'] ?? null,
            ],
            'permission_ids' => $validated['permissions'] ?? [],
        ];
    }

    protected function formViewData(Role $role): array
    {
        return [
            'item' => $role,
            'isEdit' => $role->exists,
            'selectedPermissionIds' => $role->permissions->pluck('id')->all(),
            'permissionGroups' => collect(AccessControl::permissionGroups())->map(function (array $definitions) {
                return Permission::query()
                    ->whereIn('slug', collect($definitions)->pluck('slug')->all())
                    ->orderBy('name')
                    ->get();
            }),
        ];
    }
}
