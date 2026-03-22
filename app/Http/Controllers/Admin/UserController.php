<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesCmsData;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Support\AccessControl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use HandlesCmsData;

    public function __construct()
    {
        $this->middleware('permission:users.view')->only(['index']);
        $this->middleware('permission:users.create')->only(['create', 'store']);
        $this->middleware('permission:users.edit')->only(['edit', 'update']);
        $this->middleware('permission:users.delete')->only(['destroy']);
    }

    public function index()
    {
        return view('admin.users.index', [
            'items' => User::query()->with('roles')->latest()->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.users.form', $this->formViewData(new User([
            'is_active' => true,
            'preferred_language' => 'ar',
        ])));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $user = User::create($data['user']);
        $user->roles()->sync($data['role_ids']);
        $user->syncPermissionOverrides($data['allowed_permissions'], $data['denied_permissions']);

        return redirect()->route('admin.users.index')->with('success', __('admin.user_saved'));
    }

    public function edit(User $user)
    {
        $user->load(['roles', 'permissionOverrides']);

        return view('admin.users.form', $this->formViewData($user));
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validatedData($request, $user);

        $user->update($data['user']);
        $user->roles()->sync($data['role_ids']);
        $user->syncPermissionOverrides($data['allowed_permissions'], $data['denied_permissions']);

        return redirect()->route('admin.users.index')->with('success', __('admin.user_updated'));
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(__('admin.cannot_delete_current_user'));
        }

        if ($user->roles()->where('slug', 'super-admin')->exists() && ! auth()->user()?->hasPermission('security.manage')) {
            return back()->withErrors(__('admin.cannot_delete_super_admin'));
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', __('admin.user_deleted'));
    }

    protected function validatedData(Request $request, ?User $user = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'phone' => ['nullable', 'string', 'max:255'],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
            'preferred_language' => ['nullable', Rule::in(['ar', 'en'])],
            'roles' => ['array'],
            'roles.*' => ['integer', 'exists:roles,id'],
            'allowed_permissions' => ['array'],
            'allowed_permissions.*' => ['integer', 'exists:permissions,id'],
            'denied_permissions' => ['array'],
            'denied_permissions.*' => ['integer', 'exists:permissions,id'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp'],
        ]);

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'preferred_language' => $validated['preferred_language'] ?? 'ar',
            'profile_image' => $this->uploadFile($request, 'profile_image', 'users', $user?->profile_image),
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        return [
            'user' => $payload,
            'role_ids' => $validated['roles'] ?? [],
            'allowed_permissions' => $validated['allowed_permissions'] ?? [],
            'denied_permissions' => $validated['denied_permissions'] ?? [],
        ];
    }

    protected function formViewData(User $user): array
    {
        $user->loadMissing(['roles', 'permissionOverrides']);

        $overrideIds = $user->permissionOverrides->mapWithKeys(fn (Permission $permission) => [
            $permission->id => (bool) $permission->pivot->is_allowed,
        ]);

        return [
            'item' => $user,
            'isEdit' => $user->exists,
            'roles' => Role::query()->withCount('permissions')->orderBy('name')->get(),
            'permissionGroups' => collect(AccessControl::permissionGroups())->map(function (array $definitions, string $module) {
                $slugs = collect($definitions)->pluck('slug')->all();

                return Permission::query()
                    ->whereIn('slug', $slugs)
                    ->orderBy('name')
                    ->get();
            }),
            'selectedRoleIds' => $user->roles->pluck('id')->all(),
            'allowedPermissionIds' => $overrideIds->filter(fn (bool $isAllowed) => $isAllowed)->keys()->all(),
            'deniedPermissionIds' => $overrideIds->filter(fn (bool $isAllowed) => ! $isAllowed)->keys()->all(),
        ];
    }
}
