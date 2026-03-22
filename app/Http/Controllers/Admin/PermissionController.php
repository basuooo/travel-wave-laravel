<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:permissions.manage');
    }

    public function index()
    {
        return view('admin.permissions.index', [
            'items' => Permission::query()->withCount('roles')->orderBy('module')->orderBy('name')->paginate(40),
        ]);
    }

    public function create()
    {
        return view('admin.permissions.form', [
            'item' => new Permission(),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        Permission::create($data);

        return redirect()->route('admin.permissions.index')->with('success', __('admin.permission_saved'));
    }

    public function edit(Permission $permission)
    {
        return view('admin.permissions.form', [
            'item' => $permission,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Permission $permission)
    {
        $permission->update($this->validatedData($request, $permission));

        return redirect()->route('admin.permissions.index')->with('success', __('admin.permission_updated'));
    }

    public function destroy(Permission $permission)
    {
        if ($permission->roles()->exists()) {
            return back()->withErrors(__('admin.cannot_delete_permission_in_use'));
        }

        $permission->delete();

        return redirect()->route('admin.permissions.index')->with('success', __('admin.permission_deleted'));
    }

    protected function validatedData(Request $request, ?Permission $permission = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('permissions', 'slug')->ignore($permission?->id)],
            'module' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
        ]);

        return [
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?: Str::slug($validated['module'] . '-' . $validated['name'], '.'),
            'module' => Str::slug($validated['module'], '_'),
            'description' => $validated['description'] ?? null,
        ];
    }
}
