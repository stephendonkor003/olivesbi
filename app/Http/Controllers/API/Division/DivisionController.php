<?php

namespace App\Http\Controllers\API\Division;

use App\Http\Controllers\Controller;
use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index(Request $request)
    {
        $data = Division::orderBy('created_at', 'DESC')
            ->with(['assembly'])
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return response()->json([
            'message' => 'Get all divisions',
            'data' => $data,
        ]);
    }

    public function show($id)
    {
        $division = Division::where('id', $id)
            ->with(['assembly'])
            ->first();

        if (empty($division)) {
            return response()->json([
                'message' => 'Division not found',
            ], 422);
        }

        return response()->json([
            'message' => 'Get particular division',
            'data' => $division,
        ]);
    }

    public function assemblyDivision($code)
    {
        $divisions = Division::where('assembly_code', $code)
            ->with(['assembly'])
            ->get();

        if (count($divisions) == 0) {
            return response()->json([
                'message' => 'Division not found',
            ], 422);
        }

        return response()->json([
            'message' => 'Get divisions under assembly',
            'data' => $divisions,
        ]);
    }
}
