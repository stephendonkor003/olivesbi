<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\CreateOwnerRequest;
use App\Http\Requests\Owner\UpdateOwnerRequest;
use App\Jobs\Registration\SendRegistrationReminder;
use App\Models\Assembly;
use App\Models\Owner;
use App\Models\User;
use App\Support\NewAccountSetup;
use App\Support\OwnerRecordAccess;
use App\Support\UserAccountAccess;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OwnerController extends Controller
{
    public function index(Request $request)
    {
        ini_set('memory_limit', '512M');

        if (! UserAccountAccess::hasManagementPermission($request->user(), 'customers.view')) {
            abort(403, 'Unauthorized action.');
        }

        $owners = OwnerRecordAccess::visibleOwners(Owner::query(), $request->user())->orderBy('created_at', 'DESC')
            ->with(['properties' => fn ($query) => OwnerRecordAccess::visibleAssets($query, $request->user()),
                'businesses' => fn ($query) => OwnerRecordAccess::visibleAssets($query, $request->user())])
//            ->when($request->filled('country_of_citizenship'), function ($query) use ($request) {
//                $query->where('country_of_citizenship', $request->country_of_citizenship);
//            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('gender'), function ($query) use ($request) {
                $query->where('gender', $request->gender);
            })
            ->when($request->filled('from_date'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->from_date);
            })
            ->when($request->filled('to_date'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->to_date);
            });

        $totalCitizens = (clone $owners)->count();
        $male = (clone $owners)->where('gender', 'M')->count();
        $female = (clone $owners)->where('gender', 'F')->count();

        $malePercentage = $totalCitizens > 0 ? ($male / $totalCitizens) * 100 : 0;
        $femalePercentage = $totalCitizens > 0 ? ($female / $totalCitizens) * 100 : 0;

        $customerTypes = (clone $owners)->get()->groupBy('customer_type');
        $customerTypeCounts = $customerTypes->map(function ($group) {
            return $group->count();
        });

        $customerTypePercentages = $customerTypeCounts->map(function ($count) use ($totalCitizens) {
            return $totalCitizens > 0 ? ($count / $totalCitizens) * 100 : 0;
        });

        $genderCustomerStatus = (clone $owners)->groupBy(['customer_type', 'gender', 'status']);

        if (! is_iterable($genderCustomerStatus)) {
            $genderCustomerStatus = collect();
        }

        $heatMapData = [];
        foreach ($genderCustomerStatus as $customerType => $genderGroups) {
            foreach ($genderGroups as $gender => $statusGroups) {
                foreach ($statusGroups as $status => $groupedItems) {
                    $heatMapData[] = [
                        'customer_type' => $customerType,
                        'gender' => ucfirst(strtolower($gender)),
                        'status' => ucfirst(strtolower($status)),
                        'count' => count($groupedItems),
                    ];
                }
            }
        }

        $totalActive = (clone $owners)->where('status', 'Active')->count();
        $inActive = (clone $owners)->where('status', 'InActive')->count();

        $totals = [
            'male' => $male,
            'female' => $female,
            'male_percentage' => round($malePercentage, 2),
            'female_percentage' => round($femalePercentage, 2),
            'customer_type_counts' => $customerTypeCounts,
            'customer_type_percentages' => $customerTypePercentages,
            'total_active' => $totalActive,
            'inactive' => $inActive,
        ];

        if ($request->expectsJson()) {
            return datatables()->eloquent($owners)
                ->addIndexColumn()
                ->addColumn('action', function (Owner $owner) {
                    return '
                        <div class="dropdown">
                            <div class="btn-link" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11 12C11 12.5523 11.4477 13 12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12Z"
                                        stroke="#737B8B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M18 12C18 12.5523 18.4477 13 19 13C19.5523 13 20 12.5523 20 12C20 11.4477 19.5523 11 19 11C18.4477 11 18 11.4477 18 12Z"
                                        stroke="#737B8B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M4 12C4 12.5523 4.44772 13 5 13C5.55228 13 6 12.5523 6 12C6 11.4477 5.55228 11 5 11C4.44772 11 4 11.4477 4 12Z"
                                        stroke="#737B8B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="dropdown-menu dropdown-menu-end">
                                <div class="py-2">
                                    <a class="dropdown-item" href="'.route('owners.show', $owner).'">View Rate Payer Record</a>
                                    <a class="dropdown-item" href="'.route('owners.edit', $owner).'">Edit Rate Payer Record</a>
                                </div>
                            </div>
                        </div>
                    ';
                })
                ->editColumn('fname', function (Owner $owner) {
                    return $owner->fname.' '.$owner->lname;
                })
                ->editColumn('gender', function (Owner $owner) {
                    return $owner->gender === 'M' ? 'Male' : ($owner->gender === 'F' ? 'Female' : 'Other');
                })
                ->editColumn('created_at', function (Owner $owner) {
                    return $owner->created_at?->format('Y-m-d H:i');
                })
                ->rawColumns(['action'])
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy($query->getModel()->qualifyColumn('created_at'), $order);
                })
                ->make(true);
        }

        return view('owners.index', compact('totals', 'heatMapData'));
    }

    public function create(Request $request)
    {
        abort_unless(UserAccountAccess::hasManagementPermission($request->user(), 'customers.create'), 403);
        $assemblies = Assembly::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('owners.create', compact('assemblies'));
    }

    public function store(CreateOwnerRequest $request)
    {
        $data = $request->validated();

        $randomNumbers = '';
        for ($i = 0; $i < 6; $i++) {
            $randomNumbers .= mt_rand(0, 9);
        }

        do {
            $erms_id = 'ERMS'.$randomNumbers;
        } while (Owner::where('erms_id', $erms_id)->exists());

        $data['erms_id'] = $erms_id;
        $data['created_by'] = $request->user()->id;
        $data['fname'] = $request->input('first_name');
        $data['lname'] = $request->input('last_name');

        $userLoginData = [
            'name' => $data['first_name'].' '.$data['last_name'],
            'email' => $data['email'] ?? $erms_id.'@erms.com',
            'phone' => $data['phone_no_primary'],
            'password' => NewAccountSetup::passwordHash(),
            'access_level' => 'customer',
            'status' => 'Active',
            'erms_id' => $data['erms_id'],
            'change_password' => true,
        ];

        // Auto-assign assembly_code from authenticated user if not provided
        if (! empty($request->user()->assembly_code)) {
            $data['assembly_code'] = $request->user()->assembly_code;
        }

        // Auto-assign regional_code from authenticated user if not provided
        if (! empty($request->user()->regional_code)) {
            $data['regional_code'] = $request->user()->regional_code;
        }

        $user = User::where('phone', $data['phone_no_primary'])->first();

        if (! empty($user)) {
            throw ValidationException::withMessages([
                'phone_no_primary' => 'Phone number for user account already exist!',
            ]);
        }

        $userData = User::create($userLoginData);

        if (! empty($userData)) {
            $owner = Owner::create($data);

            SendRegistrationReminder::dispatch($owner)->afterCommit();

        }

        return redirect()->route('owners.index')->with('status', 'Rate Payer created successfully!');
    }

    public function show(Request $request, Owner $owner)
    {
        abort_unless(OwnerRecordAccess::visibleOwners(Owner::query(), $request->user())->whereKey($owner->id)->exists(), 403);

        return view('owners.show', compact('owner'));
    }

    public function edit(Request $request, Owner $owner)
    {
        abort_unless(OwnerRecordAccess::mayUpdate($request->user(), $owner, $request), 403);
        $assemblies = Assembly::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('owners.edit', compact('owner', 'assemblies'));
    }

    public function update(UpdateOwnerRequest $request, Owner $owner)
    {
        $data = $request->validated();
        $data['fname'] = $request->input('first_name');
        $data['lname'] = $request->input('last_name');

        $owner->update($data);

        return redirect()->route('owners.index')->with('status', 'Rate Payer updated successfully!');
    }
}
