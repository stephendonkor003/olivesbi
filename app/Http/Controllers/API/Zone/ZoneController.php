<?php

namespace App\Http\Controllers\API\Zone;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    public function index(Request $request)
    {
        $data = Zone::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return response()->json([
            'message' => 'Get all zones',
            'data' => $data,
        ]);
    }

    public function show($uuid)
    {
        $zone = Zone::whereUuidOrId($uuid)->first();

        if (empty($zone)) {
            return response()->json([
                'message' => 'Zone not found',
            ], 422);
        }

        return response()->json([
            'message' => 'Get particular zone',
            'data' => $zone,
        ]);
    }
}
