<?php

namespace App\Http\Controllers\Blocks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Block\CreateBlockRequest;
use App\Http\Requests\Block\UpdateBlockRequest;
use App\Models\Assembly;
use App\Models\Block;
use App\Models\Division;
use App\Models\Polygon;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum']);
    }

    public function index(Request $request)
    {
        if (! auth()->user()->can('blocks.view')) {
            abort(403, 'Unauthorized action.');
        }

        $blocks = Block::with('division')
            ->orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('blocks.index', compact('blocks'));
    }

    public function create(Request $request)
    {
        if (! auth()->user()->can('blocks.create')) {
            abort(403, 'Unauthorized action.');
        }

        $assemblies = Assembly::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('blocks.create', compact('assemblies'));
    }

    public function store(CreateBlockRequest $request)
    {
        try {
            $data = $request->validated();

            // Auto-assign assembly_code from authenticated user if not provided
            if (! empty($request->user()->assembly_code)) {
                $data['assembly_code'] = $request->user()->assembly_code;
            }

            Block::create($data);

            return redirect()->route('blocks.index')->with('status', 'Block created successfully.');
        } catch (\Exception $e) {
            report($e);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create block. Please try again.');
        }
    }

    public function show(Block $block)
    {
        return view('blocks.show', compact('block'));
    }

    public function edit(Request $request, Block $block)
    {
        if (! auth()->user()->can('blocks.update')) {
            abort(403, 'Unauthorized action.');
        }

        $divisions = Division::query()
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        $assemblies = Assembly::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('blocks.edit', compact('block', 'divisions', 'assemblies'));
    }

    public function update(UpdateBlockRequest $request, Block $block)
    {
        try {
            $data = $request->validated();

            // Auto-assign assembly_code from authenticated user if not provided
            if (empty($data['assembly_code']) && ! empty($request->user()->assembly_code)) {
                $data['assembly_code'] = $request->user()->assembly_code;
            }

            $block->update($data);

            return redirect()->route('blocks.index')->with('status', 'Block updated successfully.');
        } catch (\Exception $e) {
            report($e);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update block. Please try again.');
        }
    }

    public function destroy(Block $block)
    {
        // Check for related polygons/properties
        $polygonCount = Polygon::where('block_id', $block->id)->count();

        if ($polygonCount > 0) {
            return redirect()->back()->with('error',
                "Cannot delete block. It has {$polygonCount} property(ies) assigned to it. Please reassign or delete the properties first.");
        }

        $block->delete();

        return redirect()->route('blocks.index')->with('success', 'Block deleted successfully.');
    }

    public function division(Request $request)
    {
        try {
            $assemblyCode = $request->input('assembly_code');

            if (empty($assemblyCode)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assembly code is required.',
                    'data' => [],
                ], 400);
            }

            $divisions = Division::where('assembly_code', $assemblyCode)->get();

            return response()->json([
                'success' => true,
                'message' => 'Divisions retrieved successfully.',
                'data' => $divisions,
            ]);
        } catch (\Exception $ex) {
            report($ex);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve divisions. Please try again.',
                'data' => [],
            ], 500);
        }
    }
}
