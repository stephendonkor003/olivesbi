<?php

namespace App\Http\Controllers\API\Block;

use App\Http\Controllers\Controller;
use App\Models\Block;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function index(Request $request)
    {
        $data = Block::orderBy('created_at', 'DESC')
            ->with(['assembly', 'division'])
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return response()->json([
            'message' => 'Get all blocks',
            'data' => $data,
        ]);
    }

    public function show($id)
    {
        $block = Block::where('id', $id)
            ->with(['assembly', 'division'])
            ->first();

        if (empty($block)) {
            return response()->json([
                'message' => 'Block not found',
            ], 422);
        }

        return response()->json([
            'message' => 'Get particular block',
            'data' => $block,
        ]);
    }
}
