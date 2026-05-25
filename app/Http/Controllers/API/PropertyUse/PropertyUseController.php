<?php

namespace App\Http\Controllers\API\PropertyUse;

use App\Http\Controllers\Controller;
use App\Models\PropertyUser;
use App\Models\Zone;
use Illuminate\Http\Request;

class PropertyUseController extends Controller
{
    public function index(Request $request, $zone)
    {
        $zone = Zone::whereUuidOrId($zone)->firstOrFail();

        $data = PropertyUser::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->with(['zone'])
            ->where('zone_id', $zone->id)
            ->get();

        return response()->json([
            'message' => 'Get all property use',
            'data' => $data,
        ]);
    }

    public function show($uuid)
    {
        $propertyUse = PropertyUser::whereUuidOrId($uuid)
            ->with(['zone'])
            ->first();

        if (empty($propertyUse)) {
            return response()->json([
                'message' => 'Property use not found',
            ], 422);
        }

        return response()->json([
            'message' => 'Get particalar property use',
            'data' => $propertyUse,
        ]);
    }
}
