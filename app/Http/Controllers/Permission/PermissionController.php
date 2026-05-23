<?php

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum']);
    }

    public function index()
    {
        if (! auth()->user()->can('permissions.view')) {
            abort(403, 'Unauthorized action.');
        }

        $roles = Role::orderBy('name', 'ASC')->get();

        return view('permissions.index')->with([
            'roles' => $roles,
        ]);
    }

    public function create() {}

    public function store() {}

    public function show() {}

    public function edit($id)
    {
        if (! auth()->user()->can('permissions.update')) {
            abort(403, 'Unauthorized action.');
        }

        $role = Role::where('id', $id)->first();

        $role_permissions = Role::findByName($role->name)->permissions->pluck('name')->toArray();

        return view('permissions.edit')->with([
            'role' => $role,
            'role_permissions' => $role_permissions,
        ]);
    }

    public function update(Request $request, $id)
    {
        try {

            $role = Role::where('id', $id)->first();

            $role->name = $request->input('name');

            $role->save();

            $permissions = $request->input('permissions');
            $this->__createPermissionIfNotExists($permissions);

            $perm = Permission::whereIn('name', $permissions)
                ->pluck('id')
                ->toArray();

            $role->permissions()->sync($perm);

            return redirect()->route('permissions.index')->with('status', 'Role updated successfully');
        } catch (\Exception $ex) {
        }
    }

    /**
     * Creates new permission if doesn't exist
     *
     * @param  array  $permissions
     * @return void
     */
    private function __createPermissionIfNotExists($permissions)
    {
        $exising_permissions = Permission::whereIn('name', $permissions)
            ->pluck('name')
            ->toArray();

        $non_existing_permissions = array_diff($permissions, $exising_permissions);

        if (! empty($non_existing_permissions)) {
            foreach ($non_existing_permissions as $new_permission) {
                $perm = Permission::where('name', $new_permission)->first();
                if (! $perm) {
                    $time_stamp = Carbon::now()->toDateTimeString();

                    Permission::create([
                        'name' => $new_permission,
                        'guard_name' => 'web',
                        'created_at' => $time_stamp,
                    ]);
                }
            }
        }
    }
}
