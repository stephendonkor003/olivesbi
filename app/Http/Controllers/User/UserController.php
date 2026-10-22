<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Assembly;
use App\Models\Division;
use App\Models\GhanaRegion;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum']);
    }

    public function index(Request $request)
    {
        if (! \App\Support\UserAccountAccess::hasManagementPermission($request->user(), 'users.view')) {
            abort(403, 'Unauthorized action.');
        }

        $userQuery = \App\Support\UserAccountAccess::visibleQuery(User::query(), $request->user())
            ->orderBy('created_at', 'DESC')
            ->whereNotIn('access_level', ['Rate Payer', 'Customer'])
            ->when($request->display == 'agent', function ($query) {
                $query->where('access_level', 'Assembly_Agent');
            })
            ->when($request->display == 'active', function ($query) {
                $query->where('access_level', 'Assembly_Agent')
                    ->where('status', 'Active');
            })
            ->when($request->display == 'inactive', function ($query) {
                $query->where('access_level', 'Assembly_Agent')
                    ->where('status', 'InActive');
            });

        $users = $userQuery->get();

        $roles = Role::query()->active()->orderBy('name', 'ASC')->get();

        $totalActiveUsers = (clone $userQuery)->where('status', 'Active')->count();
        $totalInactiveUsers = (clone $userQuery)->where('status', 'InActive')->count();
        $totalMaleUsers = (clone $userQuery)->where('gender', 'Male')->count();
        $totalFemaleUsers = (clone $userQuery)->where('gender', 'Female')->count();

        foreach ($users as $user) {
            if (stripos($user->email, 'ERMS') === 0 && strpos($user->email, '@') === false) {
                $user->email .= '@erms.com';
            }
        }

        $total = [
            'totalActiveUsers' => isset($totalActiveUsers) ? $totalActiveUsers : 0,
            'totalInactiveUsers' => isset($totalInactiveUsers) ? $totalInactiveUsers : 0,
            'totalMaleUsers' => isset($totalMaleUsers) ? $totalMaleUsers : 0,
            'totalFemaleUsers' => isset($totalFemaleUsers) ? $totalFemaleUsers : 0,
        ];

        return view('users.index', compact('users', 'roles', 'total'));
    }

    public function create(Request $request)
    {
        if (! \App\Support\UserAccountAccess::hasManagementPermission($request->user(), 'users.create')) {
            abort(403, 'Unauthorized action.');
        }

        $assemblies = Assembly::query()
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->when(empty($request->user()->assembly_code) && old('regional_code'), function ($query) {
                $query->where('regional_code', old('regional_code'));
            })
            ->get();

        $divisions = Division::query()
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        // Derive regional_code from assembly if user has assembly but no region
        $derivedRegionalCode = null;
        if (! empty($request->user()->assembly_code) && empty($request->user()->regional_code)) {
            $userAssembly = Assembly::where('assembly_code', $request->user()->assembly_code)->first();
            $derivedRegionalCode = $userAssembly?->regional_code;
        }

        $regions = GhanaRegion::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($derivedRegionalCode), function ($query) use ($derivedRegionalCode) {
                $query->where('regional_code', $derivedRegionalCode);
            })
            ->get();

        if ($request->user()->access_level === 'Super_User') {
            $roles = Role::query()
                ->active()
                ->whereNot('name', 'Customer')
                ->orderBy('name', 'ASC')
                ->get();
        } else {
            $roles = Role::active()
                ->whereRaw('LOWER(name) NOT IN (?, ?, ?)', ['administrator', 'gra', 'gog'])
                ->whereNot('name', 'Customer')
                ->orderBy('name', 'ASC')
                ->get();
        }

        return view('users.create', compact('assemblies', 'divisions', 'roles', 'regions', 'derivedRegionalCode'));
    }

    public function store(CreateUserRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $data['password'] = Hash::make($data['password']);

        // Auto-assign assembly_code from authenticated user if not provided
        if (! empty($request->user()->assembly_code)) {
            $data['assembly_code'] = $request->user()->assembly_code;
        }

        // Auto-assign regional_code from authenticated user if not provided
        if (! empty($request->user()->regional_code)) {
            $data['regional_code'] = $request->user()->regional_code;
        }

        // Auto-assign division_code from authenticated user if not provided
        if (! empty($request->user()->division_code)) {
            $data['division_code'] = $request->user()->division_code;
        }

        $user = User::create($data);

        $user->roles()->sync($request->validated('role'));

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        abort_unless(\App\Support\UserAccountAccess::mayView(auth()->user(), $user), 403);

        return view('users.show')->with([
            'user' => $user,
        ]);
    }

    public function edit(User $user, Request $request)
    {
        if (! \App\Support\UserAccountAccess::mayUpdate($request->user(), $user, $request)) {
            abort(403, 'Unauthorized action.');
        }

        if (in_array($user->access_level, ['rate payer', 'customer'])) {
            abort(403, 'You cannot edit rate payer information here. Use Rate Payer Management for that.');
        }

        if ($request->user()->access_level === 'Super_User') {
            $roles = Role::query()
                ->active()
                ->orderBy('name', 'ASC')
                ->get();
        } else {
            $roles = Role::active()
                ->whereRaw('LOWER(name) NOT IN (?, ?, ?)', ['administrator', 'gra', 'gog'])
                ->orderBy('name', 'ASC')
                ->get();
        }

        $assemblies = Assembly::query()
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->when(empty($request->user()->assembly_code) && old('regional_code', $user->regional_code), function ($query) use ($user) {
                $query->where('regional_code', old('regional_code', $user->regional_code));
            })
            ->get();

        $divisions = Division::query()
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        // Derive regional_code from assembly if user has assembly but no region
        $derivedRegionalCode = null;
        if (! empty($request->user()->assembly_code) && empty($request->user()->regional_code)) {
            $userAssembly = Assembly::where('assembly_code', $request->user()->assembly_code)->first();
            $derivedRegionalCode = $userAssembly?->regional_code;
        }

        $regions = GhanaRegion::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($derivedRegionalCode), function ($query) use ($derivedRegionalCode) {
                $query->where('regional_code', $derivedRegionalCode);
            })
            ->get();

        return view('users.edit')->with([
            'user' => $user,
            'roles' => $roles,
            'assemblies' => $assemblies,
            'divisions' => $divisions,
            'userRole' => $user->roles()->pluck('id')->toArray(),
            'regions' => $regions,
            'derivedRegionalCode' => $derivedRegionalCode,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $data = $request->validated();

            if (empty($request->validated('password'))) {
                $data['password'] = $user->password;
            } else {
                $data['password'] = Hash::make($data['password']);
            }

            // Auto-assign assembly_code from authenticated user if not provided
            if (! empty($request->user()->assembly_code)) {
                $data['assembly_code'] = $request->user()->assembly_code;
            }

            // Auto-assign regional_code from authenticated user if not provided
            if (! empty($request->user()->regional_code)) {
                $data['regional_code'] = $request->user()->regional_code;
            }

            // Auto-assign division_code from authenticated user if not provided
            if (! empty($request->user()->division_code)) {
                $data['division_code'] = $request->user()->division_code;
            }

            $user->update($data);

            $user->roles()->sync($request->validated('role'));

            return redirect()->route('users.index')->with('status', 'User updated successfully.');
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }
}
