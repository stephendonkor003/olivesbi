<?php

namespace App\Http\Controllers\NewBusiness;

use App\Actions\Properties\GeneratePropertyIDAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Business\CreateBusinessRequest;
use App\Http\Requests\Business\UpdateBusinessRequest;
use App\Models\Assembly;
use App\Models\Block;
use App\Models\Business;
use App\Models\Category;
use App\Models\Division;
use App\Models\ItemDescription;
use App\Models\Polygon;
use App\Models\RevenueStream;
use App\Models\RevenueStreamType;
use App\Support\GlobalLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class NewBusinessController extends Controller
{
    public function index(Request $request)
    {
        if (! auth()->user()->can('businesses.view')) {
            abort(403, 'Unauthorized action.');
        }

        $businesses = Business::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            });

        if ($request->expectsJson()) {
            return datatables()->eloquent($businesses)
                ->addIndexColumn()
                ->addColumn('action', function (Business $business) {
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
                                    <a class="dropdown-item" href="'.route('new-businesses.show', $business).'">View</a>
                                    <a class="dropdown-item" href="'.route('new-businesses.edit', $business).'">Edit</a>
                                    <a class="dropdown-item" href="'.route('businesses.assignRevenueStream', $business).'">Assign Revenue Stream</a>
                                </div>
                            </div>
                        </div>
                    ';
                })
                ->editColumn('fullname', function (Business $business) {
                    return $business->customer?->fname.' '.$business->customer?->lname;
                })
                ->editColumn('created_at', function (Business $business) {
                    return $business->created_at?->format('Y-m-d H:i');
                })
                ->rawColumns(['action'])
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('businesses.created_at', $order);
                })
                ->make(true);
        }

        return view('businesses-new.index', compact('businesses'));
    }

    public function create(Request $request)
    {
        if (! auth()->user()->can('businesses.create')) {
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

        return view('businesses-new.create', compact('customers', 'assemblies'));
    }

    public function store(CreateBusinessRequest $request)
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

        $randomNumber = str_pad(mt_rand(0, 9999999), 7, '0', STR_PAD_LEFT);
        $randomNumbers = '';
        $number = str_pad(rand(1, 9999999), 7, '0', STR_PAD_LEFT);
        $random = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 0, 5);

        for ($i = 0; $i < 6; $i++) {
            $randomNumbers .= mt_rand(0, 9);
        }

        do {
            $erms_id = 'ERMS'.$randomNumbers;
        } while (Polygon::where('erms_id', $erms_id)->exists());

        do {
            $account_no = "ERMS/B-000{$number}.{$random}";
        } while (Business::where('account_no', $account_no)->exists());

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

        // Create polygon
        $polygon = Polygon::create($polygonData);

        $globalLocation = GlobalLocation::from(
            $data,
            $data['address'] ?? $data['digitaladdress'] ?? $data['location'] ?? $data['street_name'] ?? null
        );

        $businessData = [
            'erms_id' => $propertyId,
            'account_no' => $account_no,
            'polygon_id' => $polygon->id,
            'business_name' => $data['business_name'] ?? null,
            'registration_no' => $data['registration_no'] ?? null,
            'permit_no' => $data['permit_no'] ?? null,
            'hseno' => $data['hseno'] ?? null,
            'digitaladdress' => $data['digitaladdress'] ?? null,
            'quantity' => 0,
            'location' => $data['location'] ?? null,
            'street_name' => $data['street_name'] ?? null,
            ...$globalLocation,
            'phone_no' => $data['phone_no'] ?? null,
            'address' => $data['address'] ?? null,
            'email' => $data['email'] ?? null,
            'buss_rev_id' => 0,
            'is_bsc_rate' => '{"basic": true, "sanit": true}',
            'owner_id' => $data['owner_id'],
            'validation_id' => 'reclf',
            'generate_inv' => '1',
            'remarks' => '[BOP] - ',
            'created_on' => now(),
            'created_by' => $data['created_by'],
            'assembly_code' => $data['assembly_code'],
        ];

        if (! empty($polygon)) {
            // Create Business
            Business::create($businessData);
        }

        return redirect()->route('new-businesses.index')->with('status', 'Business created successfully!');
    }

    public function show(Business $business)
    {
        return view('businesses-new.show', compact('business'));
    }

    public function edit(Request $request, Business $business)
    {
        if (! auth()->user()->can('businesses.update')) {
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

        $polygon = Polygon::with('block')->find($business->polygon_id);

        $assemblies = Assembly::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        $divisions = Division::orderBy('name', 'ASC')
            ->where('assembly_code', $business->assembly_code)
            ->get();

        $blocks = Block::orderBy('name', 'ASC')
            ->where('division_id', old('division_id', $polygon->block->division_id))
            ->get();

        return view('businesses-new.edit', compact('business', 'customers', 'assemblies', 'divisions', 'blocks'));
    }

    public function update(UpdateBusinessRequest $request, Business $business)
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
            $data['address'] ?? $data['digitaladdress'] ?? $data['location'] ?? $data['street_name'] ?? null
        );

        $businessData = [
            'business_name' => $data['business_name'] ?? null,
            'registration_no' => $data['registration_no'] ?? null,
            'permit_no' => $data['permit_no'] ?? null,
            'hseno' => $data['hseno'] ?? null,
            'digitaladdress' => $data['digitaladdress'] ?? null,
            'quantity' => 0,
            'location' => $data['location'] ?? null,
            'street_name' => $data['street_name'] ?? null,
            ...$globalLocation,
            'phone_no' => $data['phone_no'] ?? null,
            'address' => $data['address'] ?? null,
            'email' => $data['email'] ?? null,
            'buss_rev_id' => 0,
            'is_bsc_rate' => '{"basic": true, "sanit": true}',
            'owner_id' => $data['owner_id'],
            'validation_id' => 'reclf',
            'generate_inv' => '1',
            'remarks' => '[BOP] - ',
            'updated_on' => now(),
            'assembly_code' => $data['assembly_code'],
        ];

        // update polygon & business
        $business->update($businessData);
        $business->polygon->update($polygonData);

        return redirect()->route('new-businesses.index')->with('status', 'Business updated successfully!');
    }

    public function assignRevenueStream(Request $request, Business $business)
    {
        $revenueStreamTypes = RevenueStreamType::orderBy('name', 'ASC')->get();

        $itemDescriptions = ItemDescription::orderBy('name', 'ASC')
            ->where('assembly_code', $business->assembly_code)
            ->get();

        $categories = Category::orderBy('name', 'DESC')
            ->where('assembly_code', $business->assembly_code)
            ->get();

        $assemblies = Assembly::orderBy('created_at', 'DESC')
            ->where('assembly_code', $business->assembly_code)
            ->get();

        $fetchedRevenueStreams = collect();
        if (
            $request->filled('category_id') || $request->filled('description_id') || $request->filled('assembly_code')
        ) {
            $query = RevenueStream::query()
                ->where('assembly_code', $business->assembly_code)
                ->whereHas('revenueStreamType', function ($query) {
                    $query->where('name', 'BOP (Licenses)');
                });

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->filled('description_id')) {
                $query->where('description_id', $request->description_id);
            }

            if ($request->filled('assembly_code')) {
                $query->where('assembly_code', $request->assembly_code);
            }

            $fetchedRevenueStreams = $query->get();
        }

        $revenueStreams = RevenueStream::where('id', $business->buss_rev_id)->get();

        return view('businesses-new.revenue-stream', compact(
            'business',
            'revenueStreams',
            'revenueStreamTypes',
            'itemDescriptions',
            'categories',
            'assemblies',
            'fetchedRevenueStreams'
        ));
    }

    public function assignRevenueStreamStore(Request $request)
    {
        $business = Business::whereUuidOrId($request->query('business_id'))->first();
        $revenueStreamsId = RevenueStream::whereUuidOrId($request->query('revenue_stream_id'))->value('id');

        if ($business) {
            $business->update(['buss_rev_id' => $revenueStreamsId]);
        }

        return redirect()->route('new-businesses.index')->with('status', 'Business revenue stream updated successfully.');
    }

    public function getMapData(Request $request)
    {
        $billsSubquery = DB::table('bills')
            ->select('business_id', DB::raw('SUM(amount) as total_bills'))
            ->groupBy('business_id');

        $paymentsSubquery = DB::table('payments')
            ->join('bills', 'payments.bills_id', '=', 'bills.bills_id')
            ->select('bills.business_id', DB::raw('SUM(CASE
                WHEN payments.payment_mode = \'momo\' AND payments.transaction_status = \'Success\' THEN payments.amount
                WHEN payments.payment_mode != \'momo\' THEN payments.amount
                ELSE 0
            END) as total_payments'))
            ->groupBy('bills.business_id');

        $businesses = DB::table('businesses')
            ->join('assemblies', 'businesses.assembly_code', '=', 'assemblies.assembly_code')
            ->join('polygons', 'businesses.polygon_id', '=', 'polygons.id')
            ->leftJoin('entity_owners', 'businesses.owner_id', '=', 'entity_owners.erms_id')
            ->leftJoinSub($billsSubquery, 'bills_sum', 'businesses.uuid', '=', 'bills_sum.business_id')
            ->leftJoinSub($paymentsSubquery, 'payments_sum', 'businesses.uuid', '=', 'payments_sum.business_id')
            ->select([
                'polygons.cntr_y_coord as latitude',
                'polygons.cntr_x_coord as longitude',
                DB::raw("COALESCE(businesses.erms_id, 'N/A') as business_no"),
                DB::raw("TRIM(CONCAT(COALESCE(entity_owners.fname, ''), ' ', COALESCE(entity_owners.lname, ''))) as owner"),
                DB::raw("COALESCE(businesses.location, 'N/A') as location"),
                DB::raw('COALESCE(bills_sum.total_bills, 0) as total_bills'),
                DB::raw('COALESCE(payments_sum.total_payments, 0) as total_payments'),
            ])
            ->whereNotNull('polygons.cntr_y_coord')
            ->whereNotNull('polygons.cntr_x_coord')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('assemblies.regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), fn ($q) => $q->where('businesses.assembly_code', $request->user()->assembly_code))
            ->when($request->filled('category'), fn ($q) => $q->where('businesses.category', $request->category))
            ->get();

        return response()->json($businesses);
    }
}
