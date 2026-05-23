<?php

namespace App\Http\Controllers\Businesses;

use App\Http\Controllers\Controller;
use App\Http\Requests\Business\CreateBusinessRequest;
use App\Http\Requests\Business\UpdateBusinessRequest;
use App\Http\Requests\BusinessOwner\CreateBusinessOwnerRequest;
use App\Models\Assembly;
use App\Models\Block;
use App\Models\Business;
use App\Models\BusinessClass;
use App\Models\BusinessOwner;
use App\Models\BusinessType;
use App\Models\Citizen;
use App\Models\Division;
use App\Models\PropertyUser;
use App\Models\ServiceRequest;
use App\Models\Zone;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware(['auth:sanctum']);
    }

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
            })
            ->get();

        return view('businesses.index', compact('businesses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (! auth()->user()->can('businesses.create')) {
            abort(403, 'Unauthorized action.');
        }

        $businessTypes = BusinessType::get();
        $customers = Citizen::query()
            ->get();

        $assemblies = Assembly::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->when($request->display == 'active', function ($query) {
                $query->where('status_of_business', 'Active');
            })
            ->get();

        $zones = Zone::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('businesses.create', compact('businessTypes', 'customers', 'assemblies', 'zones'));
    }

    public function store(CreateBusinessOwnerRequest $businessOwnerRequest, CreateBusinessRequest $businessRequest)
    {
        // Business Owner data
        $data = $businessOwnerRequest->validated();
        $data['created_by'] = $businessOwnerRequest->user()->id;
        $data['business_owner_id'] = $this->generateBusinessOwnerId();

        if ($data['entity_type'] == 'organization') {
            $organizationData = $businessOwnerRequest->input('organization_data');
            $businessOwnerId = $this->generateBusinessOwnerId();
        }

        // Business data
        $businessData = $businessRequest->validated();
        $businessData['created_by'] = $businessRequest->user()->id;

        $mappedData = [];
        if ($data['entity_type'] == 'individual') {
            foreach ($data as $key => $value) {
                if (strpos($key, '_i') !== false && $key !== 'business_owner_id') {
                    $newKey = str_replace('_i', '', $key);
                    $mappedData[$newKey] = $value;
                } else {
                    $mappedData[$key] = $value;
                }
            }
        } else {
            $mappedData = $this->mapData($organizationData);
        }

        if ($data['entity_type'] == 'individual') {
            $businessOwner = BusinessOwner::create($mappedData);
        } else {
            foreach ($mappedData as $key => $value) {
                if (is_numeric($key)) {
                    $businessOwner = BusinessOwner::create([
                        'business_owner_id' => $businessOwnerId,
                        'organization_name' => $value['organization_name'],
                        'email' => $value['email'],
                        'primary_phone' => $value['primary_phone'],
                        'secondary_phone' => $value['secondary_phone'],
                        'house_number' => $value['house_number'],
                        'digital_address' => $value['digital_address'],
                        'residential_address' => $value['residential_address'],
                        'postal_address' => $value['postal_address'],
                        'created_by' => $businessOwnerRequest->user()->id,
                    ]);
                }
            }
        }

        $businessData['business_owner_id'] = $businessOwner->business_owner_id;

        $business = Business::create($businessData);

        $serviceData = [
            'user_id' => $businessRequest->user()->id,
            'service_used' => 'BoP Registration',
            'usage_date' => now(),
            'service_channel' => 'Web Portal',
            'status' => 'Completed',
        ];

        ServiceRequest::create($serviceData);

        return redirect()->route('businesses.index')->with('status', 'Business created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Business $business)
    {
        return view('businesses.show', compact('business'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Business $business)
    {
        if (! auth()->user()->can('businesses.update')) {
            abort(403, 'Unauthorized action.');
        }

        $businessTypes = BusinessType::get();
        $businessClass = BusinessClass::get();
        $customers = Citizen::query()
            ->get();

        $assemblies = Assembly::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        $divisions = Division::orderBy('division_name', 'ASC')
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        $blocks = Block::orderBy('block_name', 'ASC')
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        $zones = Zone::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        $propertyUses = PropertyUser::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->where('zone_id', $business->zone_id)
            ->get();

        return view('businesses.edit', compact(
            'business',
            'businessTypes',
            'businessClass',
            'customers',
            'assemblies',
            'divisions',
            'blocks',
            'zones',
            'propertyUses'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBusinessRequest $request, Business $business)
    {
        $business->update($request->validated());

        return redirect()->route('businesses.index')->with('status', 'Business updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Business $business)
    {
        $business->delete();

        return redirect()->route('businesses.index')->with('success', 'Business deleted successfully!');
    }

    public function businessClass(Request $request)
    {
        try {
            $businessClass = BusinessClass::where('business_type_id', $request->input('business_type'))->get();

            return response()->json([
                'message' => $businessClass,
            ]);
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function mapData(array $data)
    {
        $mappedData = [];

        foreach ($data as $key => $value) {
            if (is_array($value) && is_numeric($key)) {
                // Recursively process nested arrays if the key is numeric
                $mappedData[$key] = $this->mapData($value);
            } else {
                if (strpos($key, '_o') !== false) {
                    $newKey = str_replace('_o', '', $key);
                    $mappedData[$newKey] = $value;
                } else {
                    $mappedData[$key] = $value;
                }
            }
        }

        return $mappedData;
    }

    public function generateBusinessOwnerId()
    {
        $business_owner_id = rand(10000000, 99999999);

        while (BusinessOwner::where('business_owner_id', $business_owner_id)->exists()) {
            $this->generateBusinessOwnerId();
        }

        return $business_owner_id;
    }
}
