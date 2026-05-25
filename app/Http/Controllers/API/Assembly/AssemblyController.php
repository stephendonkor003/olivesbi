<?php

namespace App\Http\Controllers\API\Assembly;

use App\Http\Controllers\Controller;
use App\Models\Assembly;

class AssemblyController extends Controller
{
    public function index()
    {
        $data = Assembly::orderBy('created_at', 'DESC')
            ->with(['region'])
            ->get();

        return response()->json([
            'message' => 'Gell all assemblies',
            'data' => $data,
        ]);
    }

    public function show($uuid)
    {
        $assembly = Assembly::whereUuidOrId($uuid)
            ->with(['region'])
            ->first();

        if (empty($assembly)) {
            return response()->json([
                'message' => 'Assembly not found',
            ], 422);
        }

        return response()->json([
            'message' => 'Get particular assembly',
            'data' => $assembly,
        ]);
    }
}
