<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum']);
    }

    public function index()
    {
        if (! auth()->user()->canAny(['roles.view', 'roles.create', 'roles.update'])) {
            abort(403, 'Unauthorized action.');
        }

        $roles = Role::where('name', '!=', 'Customer')->orderBy('created_at', 'DESC')->get();

        return view('roles.index')->with([
            'roles' => $roles,
        ]);
    }

    public function create()
    {
        if (! auth()->user()->can('roles.create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('roles.create', [
            'permissions' => Permission::query()->where('guard_name', 'web')->get(),
        ]);
    }

    public function store(CreateRoleRequest $request)
    {
        if (! auth()->user()->can('roles.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::transaction(function () use ($request) {
                $role = Role::create([
                    ...$request->validated(),
                    'guard_name' => 'web',
                ]);

                $this->createPermissionIfNotExists($request->permissions);

                $role->syncPermissions($request->permissions);

            });

            return redirect()->route('roles.index')->with('status', 'Role created successfully.');
        } catch (\Throwable $ex) {
            report($ex);

            return redirect()->back()->with('status', 'Something went wrong.');
        }
    }

    public function show(Role $role)
    {
        if (! auth()->user()->can('roles.view')) {
            abort(403, 'Unauthorized action.');
        }

        return view('roles.show')->with([
            'role' => $role,
        ]);
    }

    public function edit(Role $role)
    {
        if (! auth()->user()->can('roles.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (auth()->user()->hasRole($role->name)) {
            return redirect()->back()->with('error', 'You cannot update your current role.');
        }

        return view('roles.edit')->with([
            'role' => $role,
            'permissions' => Permission::query()->where('guard_name', 'web')->get(),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        if (! auth()->user()->can('roles.update')) {
            abort(403, 'Unauthorized action.');
        }

        // Prevent users from updating their current role
        if (auth()->user()->hasRole($role->name)) {
            return redirect()->back()->with('error', 'You cannot update your current role.');
        }

        try {
            DB::transaction(function () use ($role, $request) {
                $role->update($request->validated());

                $this->createPermissionIfNotExists($request->permissions);

                $role->syncPermissions($request->permissions);
            });

            return redirect()->route('roles.index')->with('status', 'Role updated successfully.');
        } catch (\Throwable $ex) {
            report($ex);

            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    private function createPermissionIfNotExists($permissions)
    {
        $exising_permissions = Permission::whereIn('name', $permissions)
            ->where('guard_name', 'web')
            ->pluck('name')
            ->toArray();

        $non_existing_permissions = array_diff($permissions, $exising_permissions);

        if (! empty($non_existing_permissions)) {
            foreach ($non_existing_permissions as $new_permission) {
                Permission::firstOrCreate([
                    'name' => $new_permission,
                    'guard_name' => 'web',
                ]);
            }
        }
    }

    public function activate(Role $role)
    {
        if (! auth()->user()->can('roles.update')) {
            abort(403, 'Unauthorized action.');
        }

        // Prevent users from deactivating their current role
        if ($role->active && auth()->user()->hasRole($role->name)) {
            return redirect()->back()->with('error', 'You cannot activate/deactivate your current role.');
        }

        try {
            if ($role->active) {
                $role->update(['active' => false]);
            } else {
                $role->update(['active' => true]);
            }

            return redirect()->route('roles.index')->with('status', "Role '{$role->name}' active status changed successfully.");
        } catch (\Throwable $ex) {
            report($ex);

            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }
}
