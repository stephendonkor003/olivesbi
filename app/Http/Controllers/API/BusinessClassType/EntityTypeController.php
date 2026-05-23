<?php

namespace App\Http\Controllers\API\BusinessClassType;

use App\Http\Controllers\Controller;
use App\Models\BusinessClassType;

class EntityTypeController extends Controller
{
    public function index()
    {
        $data = BusinessClassType::orderBy('created_at', 'DESC')
            ->get();

        return response()->json([
            'message' => 'Get entity types',
            'data' => $data,
        ]);
    }

    public function show($id)
    {
        $entityType = BusinessClassType::where('id', $id)->first();

        if (empty($entityType)) {
            return response()->json([
                'message' => 'Entity type not found',
            ], 422);
        }

        return response()->json([
            'message' => 'Get particular entity type',
            'data' => $entityType,
        ]);
    }
}
