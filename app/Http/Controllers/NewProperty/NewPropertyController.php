<?php

namespace App\Http\Controllers\NewProperty;

use App\Actions\Properties\GeneratePropertyIDAction;
use App\Constants\PropertyConstants;
use App\Http\Controllers\Controller;
use App\Http\Requests\Property\CreatePropertyRequest;
use App\Http\Requests\Property\UpdatePropertyRequest;
use App\Models\Assembly;
use App\Models\Bill;
use App\Models\Block;
use App\Models\Building;
use App\Models\Business;
use App\Models\BusinessClassType;
use App\Models\Division;
use App\Models\Owner;
use App\Models\Payment;
use App\Models\Polygon;
use App\Models\Property;
use App\Models\PropertyClasses;
use App\Models\PropertyType;
use App\Models\RevenueStream;
use App\Models\RevenueStreamType;
use App\Models\User;
use App\Models\ValuationList;
use App\Models\Zone;
use App\Services\PropertyValidationService;
use App\Support\GlobalLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class NewPropertyController extends Controller
{
    public function index(Request $request)
    {
        ini_set('memory_limit', '512M');

        if (! auth()->user()->can('properties.view')) {
            abort(403, 'Unauthorized action.');
        }

        $assemblies = Assembly::orderBy('created_at', 'DESC')->get();
        $entityTypes = BusinessClassType::orderBy('created_at', 'DESC')->get();

        $properties = Property::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
//            ->when($request->filled('entity_type'), function ($query) use ($request) {
//                $query->where('entity_type', $request->entity_type);
//            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('category', $request->category);
            })
            ->when($request->filled('rated'), function ($query) use ($request) {
                $query->where('rated', $request->rated);
            })
            ->when($request->filled('validated'), function ($query) use ($request) {
                $query->where('validated', $request->validated);
            })
            ->when($request->filled('assembly'), function ($query) use ($request) {
                $query->where('assembly_code', $request->assembly);
            })
            ->when($request->filled('division'), function ($query) use ($request) {
                $query->where('division_code', $request->division);
            })
            ->when($request->filled('property_use'), function ($query) use ($request) {
                $query->where('property_use', $request->property_use);
            });

        if ($request->expectsJson()) {
            return datatables()->eloquent($properties)
                ->addIndexColumn()
                ->addColumn('action', function (Property $property) {
                    return '
                        <div class="dropdown">
                            <div class="btn-link" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11 12C11 12.5523 11.4477 13 12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12Z"
                                        stroke="#737B8B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M18 12C18 12.5523 18.4477 13 19 13C19.5523 13 20 12.5523 20 12C20 11.4477 19.5523 11 19 11C18.4477 11 18 11.4477 18 12Z"
                                        stroke="#737B8B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M4 12C4 12.5523 4.44772 13 5 13C5.55228 13 6 12.5523 6 12C6 11.4477 5.55228 11 5 11C4.44772 11 4 11.4477 4 12Z"
                                        stroke="#737B8B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </div>
                            <div class="dropdown-menu dropdown-menu-end">
                                <div class="py-2">
                                    <a class="dropdown-item" href="'.route('new-properties.show', $property).'">View</a>
                                    <a class="dropdown-item" href="'.route('new-properties.edit', $property).'">Edit</a>
                                    <a class="dropdown-item" href="'.route('properties.assignRevenueStream', $property).'">Assign Revenue Stream</a>
                                </div>
                            </div>
                        </div>
                    ';
                })
                ->editColumn('fullname', function (Property $property) {
                    $firstname = $property->customer->fname ?? '';
                    $lastname = $property->customer->lname ?? '';
                    $fullname = $firstname.' '.$lastname;

                    return $fullname ?? 'N/A';
                })
                ->editColumn('digitaladdress', function (Property $property) {
                    return $property->digitaladdress ?? 'N/A';
                })
                ->editColumn('location', function (Property $property) {
                    return $property->location ?? 'N/A';
                })
                ->editColumn('created_at', function (Property $property) {
                    return $property->created_at?->format('Y-m-d H:i');
                })
                ->rawColumns(['action'])
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('properties.created_at', $order);
                })
                ->make(true);
        }

        $data = [
            'properties' => $properties->get(),
            'entityTypes' => $entityTypes,
            'assemblies' => $assemblies,
        ];

        return view('properties-new.index', compact('data'));
    }

    public function create(Request $request)
    {
        if (! auth()->user()->can('properties.create')) {
            abort(403, 'Unauthorized action.');
        }

        $customers = DB::table('entity_owners')->orderBy('created_at')
            ->select('id', 'fname', 'lname', 'email', 'phone_no_primary', 'erms_id')
            ->where('status', 'Active')
            ->cursor()
            ->map(function ($customer) {
                return [
                    'id' => $customer->erms_id,
                    'fname' => $customer->fname ?? '',
                    'lname' => $customer->lname ?? '',
                    'full_name' => trim(($customer->fname ?? '').' '.($customer->lname ?? '')),
                    'email' => $customer->email ?? '',
                    'phone' => $customer->phone_no_primary ?? '',
                    'erms_id' => $customer->erms_id,
                ];
            });

        $assemblies = Assembly::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        $zones = Zone::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        $valuationNumbers = ValuationList::limit(100)
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $assemblyName = Assembly::where('assembly_code', $request->user()->assembly_code)
                    ->value('name');

                if ($assemblyName) {
                    $query->where('assembly_name', 'LIKE', '%'.$assemblyName.'%');
                }
            })
            ->pluck('Valuation_Number')
            ->toArray();

        return view('properties-new.create', compact('customers', 'assemblies', 'zones', 'valuationNumbers'));
    }

    public function store(CreatePropertyRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id ?? '';

        // Auto-assign assembly_code from authenticated user if not provided
        if (! empty($request->user()->assembly_code)) {
            $data['assembly_code'] = $request->user()->assembly_code;
        }

        $assembly = Assembly::where('assembly_code', $data['assembly_code'])->first();

        if (! $assembly) {
            throw ValidationException::withMessages(['assembly_code' => 'The selected assembly code is invalid']);
        }

        $division = Division::where('assembly_code', $data['assembly_code'])
            ->whereUuidOrId($data['division_id'])
            ->first();

        if (! $division) {
            throw ValidationException::withMessages(['division_id' => 'The selected division id is invalid']);
        }

        $data['division_id'] = $division->id;

        $block = Block::query()
            ->where('assembly_code', $data['assembly_code'])
            ->where('division_id', $data['division_id'])
            ->whereUuidOrId($data['block_id'])
            ->first();

        if (! $block) {
            throw ValidationException::withMessages(['block_id' => 'The selected block id is invalid']);
        }

        $data['block_id'] = $block->id;

        try {
            $propertyId = app(GeneratePropertyIDAction::class)
                ->execute($data['assembly_code'], $division->code, $block->code);
        } catch (\InvalidArgumentException $th) {
            throw ValidationException::withMessages(['block_id' => $th->getMessage()]);
        }

        $randomNumber = str_pad(
            mt_rand(PropertyConstants::BUILDING_ID_MIN, PropertyConstants::BUILDING_ID_MAX),
            PropertyConstants::BUILDING_ID_LENGTH,
            '0',
            STR_PAD_LEFT
        );

        // Generate unique ERMS ID for Polygon
        do {
            $randomNumbers = implode('', array_map(
                fn () => mt_rand(PropertyConstants::ERMS_ID_DIGIT_MIN, PropertyConstants::ERMS_ID_DIGIT_MAX),
                range(1, PropertyConstants::ERMS_ID_LENGTH)
            ));
            $erms_id = PropertyConstants::ERMS_ID_PREFIX.$randomNumbers;
        } while (Polygon::where('erms_id', $erms_id)->exists());

        // Generate unique account number
        do {
            $number = str_pad(
                rand(PropertyConstants::ACCOUNT_NUMBER_MIN, PropertyConstants::ACCOUNT_NUMBER_MAX),
                PropertyConstants::ACCOUNT_NUMBER_LENGTH,
                '0',
                STR_PAD_LEFT
            );
            $random = substr(
                str_shuffle(PropertyConstants::ACCOUNT_SUFFIX_CHARACTERS),
                0,
                PropertyConstants::ACCOUNT_SUFFIX_LENGTH
            );
            $account_no = PropertyConstants::ACCOUNT_NUMBER_PREFIX."{$number}.{$random}";
        } while (Property::where('account_no', $account_no)->exists());

        $polygonData = [
            'erms_id' => $erms_id,
            'building' => json_decode($request->geofence, true),
            'block_id' => $data['block_id'],
            'building_id' => $randomNumber,
            'created_on' => now(),
            'created_by' => $data['created_by'],
            'cntr_geom' => $request->cntr_geom,
            'cntr_x_coord' => $data['longitude'],
            'cntr_y_coord' => $data['latitude'],
            'assembly_code' => $data['assembly_code'],
        ];

        try {
            DB::beginTransaction();

            // Create polygon
            $polygon = Polygon::create($polygonData);

            $globalLocation = GlobalLocation::from(
                $data,
                $data['digitaladdress'] ?? $data['location'] ?? $data['streetname'] ?? null
            );

            $propertyData = [
                'erms_id' => $propertyId,
                'account_no' => $account_no,
                'polygon_id' => $polygon->id,
                'hseno' => $data['hseno'] ?? null,
                'digitaladdress' => $data['digitaladdress'] ?? null,
                'location' => $data['location'] ?? null,
                'streetname' => $data['streetname'] ?? null,
                ...$globalLocation,
                'prop_rev_id' => null,
                'is_bsc_rate' => PropertyConstants::DEFAULT_RATE_JSON,
                'owner_id' => $data['owner_id'],
                'is_rated' => $data['is_rated'] ?? '',
                'rateable_value' => $data['rateable_value'] ?? PropertyConstants::DEFAULT_RATEABLE_VALUE,
                'validation_id' => PropertyConstants::DEFAULT_VALIDATION_ID,
                'generate_inv' => PropertyConstants::DEFAULT_GENERATE_INV,
                'remarks' => PropertyConstants::DEFAULT_REMARKS_PREFIX,
                'created_on' => now(),
                'created_by' => $data['created_by'],
                'assembly_code' => $data['assembly_code'],
            ];

            // Create Property
            Property::create($propertyData);

            DB::commit();

            return redirect()->route('new-properties.index')->with('status', 'Property created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create property. Please try again.');
        }
    }

    public function show(Property $property)
    {
        $citizen = Owner::find(optional($property->customer)->id);

        if ($citizen && $property) {
            $bills = Bill::orderBy('created_at', 'DESC')
                ->with(['property'])
                ->whereHas('property', function ($query) use ($citizen) {
                    $query->where('owner_id', $citizen->erms_id);
                })
                ->where('property_id', $property->uuid)
                ->whereNotNull('property_id')
                ->whereNull('business_id')
                ->get();

            $payments = Payment::orderBy('created_at', 'DESC')
                ->whereHas('bill', function ($query) use ($property) {
                    $query->whereNotNull('property_id')
                        ->where('property_id', $property->uuid);
                })
                ->where('transaction_status', 'Success')
                ->get();

            $properties = Property::orderBy('created_at', 'DESC')
                ->where('owner_id', $citizen->erms_id)
                ->get();

            $businesses = Business::orderBy('created_at', 'DESC')
                ->where('owner_id', $citizen->erms_id)
                ->get();
        } else {
            $bills = collect();
            $payments = collect();
            $properties = collect();
            $businesses = collect();
        }

        $totalArrears = $bills->sum('arrears');
        $totalAmount = $bills->sum('amount');

        $customerData = [
            'properties' => isset($properties) ? $properties : [],
            'businesses' => isset($businesses) ? $businesses : [],
            'total' => isset($properties) ? number_format(collect($properties)->sum('ratable_value'), 2) : 0,
            'bills' => isset($bills) ? $bills : [],
            'payments' => isset($payments) ? $payments : [],
            'totalArrears' => isset($totalArrears) ? number_format($totalArrears, 2) : 0,
            'totalAmount' => isset($totalAmount) ? number_format($totalAmount, 2) : 0,
            'totalDue' => isset($totalArrears) && isset($totalAmount) ? number_format($totalArrears + $totalAmount, 2) : 0,
            'paymentTotal' => isset($payments) ? number_format(collect($payments)->sum('amount'), 2) : 0,
        ];

        return view('properties-new.show', compact('property', 'customerData'));
    }

    public function edit(Request $request, Property $property)
    {
        if (! auth()->user()->can('properties.update')) {
            abort(403, 'Unauthorized action.');
        }

        $customers = DB::table('entity_owners')->orderBy('created_at')
            ->select('id', 'fname', 'lname', 'email', 'phone_no_primary', 'erms_id')
            ->where('status', 'Active')
            ->cursor()
            ->map(function ($customer) {
                return [
                    'id' => $customer->erms_id,
                    'fname' => $customer->fname ?? '',
                    'lname' => $customer->lname ?? '',
                    'full_name' => trim(($customer->fname ?? '').' '.($customer->lname ?? '')),
                    'email' => $customer->email ?? '',
                    'phone' => $customer->phone_no_primary ?? '',
                    'erms_id' => $customer->erms_id,
                ];
            });

        $assemblies = Assembly::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        $divisions = Division::orderBy('name', 'ASC')
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        $blocks = Block::orderBy('name', 'ASC')
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        $valuationNumbers = ValuationList::limit(100)
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $assemblyName = Assembly::where('assembly_code', $request->user()->assembly_code)
                    ->value('name');
                if ($assemblyName) {
                    $query->where('assembly_name', 'LIKE', '%'.$assemblyName.'%');
                }
            })
            ->pluck('Valuation_Number')
            ->toArray();

        return view('properties-new.edit', compact('property', 'customers', 'assemblies', 'divisions', 'blocks', 'valuationNumbers'));
    }

    public function update(UpdatePropertyRequest $request, Property $property)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id ?? '';

        // Auto-assign assembly_code from authenticated user if not provided
        if (empty($data['assembly_code']) && ! empty($request->user()->assembly_code)) {
            $data['assembly_code'] = $request->user()->assembly_code;
        }

        $division = Division::where('assembly_code', $data['assembly_code'])
            ->whereUuidOrId($data['division_id'])
            ->first();

        if (! $division) {
            throw ValidationException::withMessages(['division_id' => 'The selected division id is invalid']);
        }

        $data['division_id'] = $division->id;

        $block = Block::query()
            ->where('assembly_code', $data['assembly_code'])
            ->where('division_id', $data['division_id'])
            ->whereUuidOrId($data['block_id'])
            ->first();

        if (! $block) {
            throw ValidationException::withMessages(['block_id' => 'The selected block id is invalid']);
        }

        $data['block_id'] = $block->id;

        $polygonData = [
            'building' => json_decode($request->geofence, true),
            'block_id' => $data['block_id'],
            'updated_on' => now(),
            'cntr_geom' => $request->cntr_geom,
            'cntr_x_coord' => $data['longitude'],
            'cntr_y_coord' => $data['latitude'],
            'assembly_code' => $data['assembly_code'],
        ];

        $globalLocation = GlobalLocation::from(
            $data,
            $data['digitaladdress'] ?? $data['location'] ?? $data['streetname'] ?? null
        );

        $propertyData = [
            'hseno' => $data['hseno'] ?? null,
            'digitaladdress' => $data['digitaladdress'] ?? null,
            'location' => $data['location'] ?? null,
            'streetname' => $data['streetname'] ?? null,
            ...$globalLocation,
            'is_bsc_rate' => PropertyConstants::DEFAULT_RATE_JSON,
            'owner_id' => $data['owner_id'],
            'is_rated' => $data['is_rated'],
            'rateable_value' => $data['rateable_value'] ?? PropertyConstants::DEFAULT_RATEABLE_VALUE,
            'validation_id' => PropertyConstants::DEFAULT_VALIDATION_ID,
            'generate_inv' => PropertyConstants::DEFAULT_GENERATE_INV,
            'remarks' => PropertyConstants::DEFAULT_REMARKS_PREFIX,
            'updated_on' => now(),
            'assembly_code' => $data['assembly_code'],
        ];

        try {
            DB::beginTransaction();

            // update polygon & property
            $property->update($propertyData);
            $property->polygon->update($polygonData);

            DB::commit();

            return redirect()->route('new-properties.index')->with('status', 'Property updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update property. Please try again.');
        }
    }

    public function assignRevenueStream(Request $request, Property $property)
    {
        $zoneTypes = Zone::query()
            ->where('assembly_code', $property->assembly_code)
            ->orderBy('name', 'ASC')
            ->get();

        $propertyClasses = PropertyClasses::query()
            ->where('assembly_code', $property->assembly_code)
            ->orderBy('name', 'ASC')
            ->get();

        $propertyTypes = PropertyType::query()
            ->where('assembly_code', $property->assembly_code)
            ->orderBy('name', 'ASC')
            ->get();

        $revenueStreamTypes = RevenueStreamType::orderBy('name', 'ASC')->get();

        $assemblies = Assembly::orderBy('created_at', 'DESC')
            ->where('assembly_code', $property->assembly_code)
            ->get();

        $fetchedRevenueStreams = collect();
        if (
            $request->filled('is_rated') ||
            $request->filled('zone_type_id') ||
            $request->filled('property_classes_id') ||
            ($request->filled('property_type_id') && $request->is_rated == 'N')
        ) {
            $query = RevenueStream::query()
                ->where('assembly_code', $property->assembly_code)
                ->whereHas('revenueStreamType', function ($query) {
                    $query->where('name', 'Property Rate');
                });

            if ($request->filled('is_rated')) {
                $query->where('is_rated', $request->is_rated);
            }

            if ($request->filled('zone_type_id')) {
                $query->where('zone_type_id', $request->zone_type_id);
            }

            if ($request->filled('property_classes_id')) {
                $query->where('property_classes_id', $request->property_classes_id);
            }

            if ($request->filled('property_type_id') && $request->is_rated == 'N') {
                $query->where('property_type_id', $request->property_type_id);
            }

            $fetchedRevenueStreams = $query->get();
        }

        $revenueStreams = RevenueStream::where('id', $property->prop_rev_id)->get();

        return view('properties-new.revenue-stream', compact(
            'property',
            'revenueStreams',
            'revenueStreamTypes',
            'zoneTypes',
            'propertyClasses',
            'propertyTypes',
            'assemblies',
            'fetchedRevenueStreams'
        ));
    }

    public function assignRevenueStreamStore(Request $request)
    {
        $property = Property::whereUuidOrId($request->query('property_id'))->first();
        $revenueStreamsId = RevenueStream::whereUuidOrId($request->query('revenue_stream_id'))->value('id');

        $property?->update(['prop_rev_id' => $revenueStreamsId]);

        return redirect()->route('new-properties.index')->with('status', 'Property revenue stream updated successfully.');
    }

    public function getMapData(Request $request)
    {
        $billsSubquery = DB::table('bills')
            ->select('property_id', DB::raw('SUM(amount) as total_bills'))
            ->groupBy('property_id');

        $paymentsSubquery = DB::table('payments')
            ->join('bills', 'payments.bills_id', '=', 'bills.bills_id')
            ->select('bills.property_id', DB::raw('SUM(CASE
                WHEN payments.payment_mode = \'momo\' AND payments.transaction_status = \'Success\' THEN payments.amount
                WHEN payments.payment_mode != \'momo\' THEN payments.amount
                ELSE 0
            END) as total_payments'))
            ->groupBy('bills.property_id');

        $properties = DB::table('properties')
            ->join('assemblies', 'properties.assembly_code', '=', 'assemblies.assembly_code')
            ->leftJoin('polygons', 'properties.polygon_id', '=', 'polygons.id')
            ->leftJoin('entity_owners', 'properties.owner_id', '=', 'entity_owners.erms_id')
            ->leftJoinSub($billsSubquery, 'bills_sum', 'properties.uuid', '=', 'bills_sum.property_id')
            ->leftJoinSub($paymentsSubquery, 'payments_sum', 'properties.uuid', '=', 'payments_sum.property_id')
            ->select([
                'polygons.cntr_y_coord as latitude',
                'polygons.cntr_x_coord as longitude',
                DB::raw("COALESCE(properties.erms_id, 'N/A') as property_no"),
                DB::raw("TRIM(CONCAT(COALESCE(entity_owners.fname, ''), ' ', COALESCE(entity_owners.lname, ''))) as owner"),
                DB::raw("COALESCE(properties.location, 'N/A') as location"),
                DB::raw('COALESCE(bills_sum.total_bills, 0) as total_bills'),
                DB::raw('COALESCE(payments_sum.total_payments, 0) as total_payments'),
            ])
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('assemblies.regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('properties.assembly_code', $request->user()->assembly_code);
            })
            ->when($request->filled('entity_type'), fn ($q) => $q->where('properties.entity_type', $request->entity_type))
            ->when($request->filled('category'), fn ($q) => $q->where('properties.category', $request->category))
            ->when($request->filled('rated'), fn ($q) => $q->where('properties.rated', $request->rated))
            ->when($request->filled('validated'), fn ($q) => $q->where('properties.validated', $request->validated))
            ->when($request->filled('assembly'), fn ($q) => $q->where('properties.assembly_code', $request->assembly))
            ->when($request->filled('division'), fn ($q) => $q->where('properties.division_code', $request->division))
            ->when($request->filled('property_use'), fn ($q) => $q->where('properties.property_use', $request->property_use))
            ->get();

        return response()->json($properties);
    }

    public function polygons()
    {
        ini_set('memory_limit', '512M');
        // $blocks = PolygonBlock::select('id', 'name', 'boundary', 'block_number')->get()->map(function ($block) {
        //     return [
        //         'id' => $block->id,
        //         'name' => $block->name,
        //         'block_number' => $block->block_number,
        //         'boundary' => json_decode($block->boundary),
        //     ];
        // });
        $blocks = Block::select('id', 'name', 'geofence', 'erms_id')->get()->map(function ($block) {
            return [
                'id' => $block->id,
                'name' => $block->name,
                'block_number' => $block->erms_id,
                'boundary' => json_decode($block->geofence),
            ];
        });

        $buildings = Building::select('id', 'name', 'boundary', 'block_id')->get()->map(function ($building) {
            return [
                'id' => $building->id,
                'name' => $building->name,
                'block_id' => $building->block_id,
                'boundary' => json_decode($building->boundary),
            ];
        });
        // $buildings = Polygon::select('id', 'erms_id', 'cntr_geom', 'block_id')->get()->map(function ($building) {
        //     $coordinates = null;

        //     if (Str::startsWith($building->cntr_geom, 'POINT')) {
        //         preg_match('/POINT\s*\(([-0-9.]+)\s+([-0-9.]+)\)/', $building->cntr_geom, $matches);
        //         if (count($matches) === 3) {
        //             $coordinates = [
        //                 'lng' => (float) $matches[1],
        //                 'lat' => (float) $matches[2],
        //             ];
        //         }
        //     }

        //     return [
        //         'id' => $building->id,
        //         'name' => $building->erms_id,
        //         'block_id' => $building->block_id,
        //         'boundary' => $coordinates,
        //     ];
        // });

        return $buildings;

        return response()->json([
            'blocks' => $blocks,
            'buildings' => $buildings,
        ]);
    }

    public function map(Request $request)
    {
        $agents = User::orderBy('name', 'ASC')
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->where('access_level', 'Assembly_Agent')
            ->where('status', 'Active')
            ->get();

        return view('properties-new.map', compact('agents'));
    }

    public function allocations()
    {
        return response()->json([
            'status' => 'ok',
            'code' => '00',
            'message' => 'Data Fetched Successfully',
            'data' => [
                'jobName' => 'Hello there',
                'block' => '1234',
                'assignedTo' => 'Samuel Otu 123',
            ],
        ]);
    }

    public function ratedProperties(Request $request)
    {
        $assemblyCode = $request->user()->assembly_code;

        if (! $assemblyCode && $request->filled('assembly_code')) {
            $assemblyCode = $request->assembly_code;
        }

        $query = ValuationList::query()
            ->select('Owner_Name', 'Digital_Address', 'Latitude', 'Longitude', 'Valuation_Number', 'Rateable_Value')
            ->when($assemblyCode, function ($query) use ($assemblyCode) {
                return $query->where('Assembly_Code', $assemblyCode);
            });

        // Get properties with valid coordinates
        $properties = $query->get();

        // Filter by block geofence if block_id is provided
        if ($request->filled('block_id')) {
            $block = Block::query()
                ->where('assembly_code', $assemblyCode)
                ->find($request->block_id);

            if ($block && $block->geofence) {
                $properties = $properties->filter(function ($property) use ($request) {
                    $validationService = new PropertyValidationService;
                    $lat = floatval($property->Latitude);
                    $lng = floatval($property->Longitude);

                    if ($lat == 0 || $lng == 0) {
                        return false;
                    }

                    return $validationService->validateCoordinatesInBlock($lat, $lng, $request->block_id);
                })->values();
            }
        }

        return response()->json($properties);
    }

    public function searchRatedProperty(Request $request)
    {
        $valuationNumber = $request->input('valuation_number');

        $assemblyCode = $request->user()->assembly_code;

        if (! $assemblyCode && $request->filled('assembly_code')) {
            $assemblyCode = $request->assembly_code;
        }

        if (! $assemblyCode) {
            return response()->json(['error' => 'Select an assembly'], 400);
        }

        if (! $valuationNumber) {
            return response()->json(['error' => 'Valuation number is required'], 400);
        }

        $property = DB::table('valuation_lists')
            ->where('Assembly_Code', $assemblyCode)
            ->where('Valuation_Number', $valuationNumber)
            ->first();

        if (! $property) {
            return response()->json(['error' => 'Property not found'], 404);
        }

        return response()->json($property);
    }
}
