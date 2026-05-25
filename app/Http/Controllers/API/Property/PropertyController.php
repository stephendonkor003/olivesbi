<?php

namespace App\Http\Controllers\API\Property;

use App\Actions\Properties\GeneratePropertyIDAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Property\CreatePropertyRequest;
use App\Http\Requests\API\Property\UpdatePropertyRequest;
use App\Jobs\Property\SendPropertyOwnerSMS;
use App\Models\Assembly;
use App\Models\Block;
use App\Models\Division;
use App\Models\Owner;
use App\Models\Polygon;
use App\Models\Property;
use App\Support\GlobalLocation;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        ini_set('memory_limit', '512M');

        $data = Property::orderBy('created_at', 'DESC')
            ->with([
                'customer',
                'entityType',
                'assembly',
                'zone',
                'division',
                'block',
                'bills.payments',
                'polygon.block.division',
                'polygon:id,cntr_x_coord,cntr_y_coord,block_id',
            ])
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->paginate(20);

        return response()->json([
            'message' => 'Get all properties',
            'data' => $data,
        ]);
    }

    public function show($uuid)
    {
        $property = Property::where(function ($query) use ($uuid) {
                $query->whereUuidOrId($uuid)
                    ->orWhere('erms_id', $uuid)
                    ->orWhere('account_no', $uuid);
            })
            ->with([
                'customer',
                'entityType',
                'assembly',
                'zone',
                'division',
                'block',
                'bills.payments',
                'polygon.block.division',
                'polygon:id,cntr_x_coord,cntr_y_coord,block_id',
            ])
            ->get();

        if (count($property) == 0) {
            return response()->json([
                'message' => 'Customer property not found',
            ], 422);
        }

        return response()->json([
            'message' => 'Get particular customer property',
            'data' => $property,
        ]);
    }

    public function customerProperty($uuid)
    {
        $matchingOwnerIds = Owner::where(function ($query) use ($uuid) {
            $query->whereUuidOrId($uuid)
                ->orWhere('erms_id', $uuid)
                ->orWhere('tin_no', $uuid)
                ->orWhere('fname', 'LIKE', "%$uuid%")
                ->orWhere('lname', 'LIKE', "%$uuid%")
                ->orWhere('phone_no_primary', $uuid)
                ->orWhere('digital_address', $uuid);
        })
            ->pluck('erms_id');

        $property = Property::with([
            'customer:id,erms_id,fname,lname,tin_no,phone_no_primary,digital_address',
            'bills.payments',
            'assembly',
            'polygon.block.division',
            'polygon:id,cntr_x_coord,cntr_y_coord,block_id',
        ])
            ->where(function ($query) use ($uuid, $matchingOwnerIds) {
                $query->whereUuidOrId($uuid)
                    ->orWhere('erms_id', $uuid)
                    ->orWhere('account_no', $uuid)
                    ->orWhere('digitaladdress', $uuid)
                    ->orWhere('streetname', 'LIKE', "%$uuid%")
                    ->orWhere('location', 'LIKE', "%$uuid%");

                if ($matchingOwnerIds->isNotEmpty()) {
                    $query->orWhereIn('owner_id', $matchingOwnerIds);
                }
            })
            ->get();

        if ($property->isEmpty()) {
            return response()->json([
                'message' => 'No record found for this customer/property',
            ], 422);
        }

        return response()->json([
            'message' => 'Get customer property(s)',
            'data' => $property,
            'total_property' => count($property),
        ]);
    }

    public function store(CreatePropertyRequest $request)
    {
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
            $account_no = "ERMS/C-000{$number}.{$random}";
        } while (Property::where('account_no', $account_no)->exists());

        $data = $request->validated();

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
        } catch (InvalidArgumentException $th) {
            throw ValidationException::withMessages(['block_id' => $th->getMessage()]);
        }

        $data['created_by'] = $request->user()->id ?? '';

        $cntr_geom = 'POINT ('.$data['longitude'].' '.$data['latitude'].')';

        $polygonData = [
            'erms_id' => $erms_id,
            'building' => $block->geofence ?? null,
            'block_id' => $data['block_id'],
            'building_id' => $randomNumber,
            'created_on' => now(),
            'created_by' => $data['created_by'],
            'cntr_geom' => $cntr_geom ?? null,
            'cntr_x_coord' => $data['longitude'],
            'cntr_y_coord' => $data['latitude'],
            'assembly_code' => $data['assembly_code'],
        ];

        // Create polygon
        $polygon = Polygon::create($polygonData);

        $propertyData = [
            'erms_id' => $propertyId,
            'account_no' => $account_no,
            'polygon_id' => $polygon->id,
            'hseno' => $data['hseno'] ?? null,
            'digitaladdress' => $data['digitaladdress'] ?? null,
            'location' => $data['location'] ?? null,
            'streetname' => $data['streetname'] ?? null,
            ...GlobalLocation::from(
                $data,
                $data['digitaladdress'] ?? $data['location'] ?? $data['streetname'] ?? null
            ),
            'prop_rev_id' => null,
            'is_bsc_rate' => '{"basic": true, "sanit": true}',
            'owner_id' => $data['owner_id'],
            'is_rated' => $data['is_rated'] === 'Yes' ? '1' : '0',
            'rateable_value' => $data['rateable_value'] ?? '0.00',
            'validation_id' => 'sucfl',
            'generate_inv' => '1',
            'remarks' => '[PRR] - ',
            'created_on' => now(),
            'created_by' => $data['created_by'],
            'assembly_code' => $data['assembly_code'],
        ];

        if (! empty($polygon)) {
            // Create Property
            $property = Property::create($propertyData);
        }

        if (! empty($data['owner_id'])) {
            // dispatch(new SendPropertyOwnerSMS($property->load('customer')));
        }

        return response()->json([
            'message' => 'Customer property created successfully',
            'data' => $property,
        ]);
    }

    public function update(UpdatePropertyRequest $request, $uuid)
    {
        $property = Property::query()
            ->where(function ($query) use ($uuid) {
                $query->whereUuidOrId($uuid)
                    ->orWhere('erms_id', $uuid)
                    ->orWhere('account_no', $uuid);
            })
            ->first();

        if (empty($property)) {
            return response()->json([
                'message' => 'Customer property not found',
            ], 422);
        }

        $data = $request->validated();
        $block = Block::whereUuidOrId($data['block_id'])->first();

        if (! $block) {
            throw ValidationException::withMessages(['block_id' => 'The selected block id is invalid']);
        }

        $data['block_id'] = $block->id;
        $cntr_geom = 'POINT ('.$data['longitude'].' '.$data['latitude'].')';

        $polygonData = [
            'building' => $block->geofence ?? null,
            'block_id' => $data['block_id'],
            'updated_on' => now(),
            'cntr_geom' => $cntr_geom ?? null,
            'cntr_x_coord' => $data['longitude'],
            'cntr_y_coord' => $data['latitude'],
            'assembly_code' => $data['assembly_code'],
        ];

        $propertyData = [
            'hseno' => $data['hseno'] ?? null,
            'digitaladdress' => $data['digitaladdress'] ?? null,
            'location' => $data['location'] ?? null,
            'streetname' => $data['streetname'] ?? null,
            ...GlobalLocation::from(
                $data,
                $data['digitaladdress'] ?? $data['location'] ?? $data['streetname'] ?? null
            ),
            'is_bsc_rate' => '{"basic": true, "sanit": true}',
            'owner_id' => $data['owner_id'],
            'is_rated' => $data['is_rated'] === 'Yes' ? '1' : '0',
            'rateable_value' => $data['rateable_value'] ?? '0.00',
            'validation_id' => 'sucfl',
            'generate_inv' => '1',
            'remarks' => '[PRR] - ',
            'updated_on' => now(),
            'assembly_code' => $data['assembly_code'],
        ];

        // update polygon & property
        $property->update($propertyData);
        $property->polygon->update($polygonData);

        // if ($confirmSendSMS) {
        //     dispatch(new SendPropertyOwnerSMS($property->load('customer')));
        // }

        return response()->json([
            'message' => 'Customer property updated successfully',
        ]);
    }

    public function getNearbyProperties($latitude, $longitude)
    {
        $radius = 25; // km

        $properties = Property::with([
            'customer',
            'entityType',
            'assembly',
            'zone',
            'division',
            'block',
            'bills.payments',
            'polygon.block.division',
            'polygon:id,cntr_x_coord,cntr_y_coord,block_id',
        ])
            ->join('polygons', 'properties.polygon_id', '=', 'polygons.id')
            ->select('properties.*')
            ->selectRaw(
                '6371 * ACOS(
                    COS(RADIANS(?)) * COS(RADIANS(polygons.cntr_y_coord)) * COS(RADIANS(?) - RADIANS(polygons.cntr_x_coord)) +
                    SIN(RADIANS(?)) * SIN(RADIANS(polygons.cntr_y_coord))
                ) AS distance',
                [$latitude, $longitude, $latitude]
            )
            ->having('distance', '<=', $radius)
            ->orderBy('distance', 'asc')
            ->paginate(20);

        return response()->json([
            'message' => 'Get nearby properites',
            'data' => $properties,
        ]);
    }
}
