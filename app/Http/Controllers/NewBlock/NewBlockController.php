<?php

namespace App\Http\Controllers\NewBlock;

use App\Http\Controllers\Controller;
use App\Http\Requests\Block\CreateBlockRequest;
use App\Http\Requests\Block\UpdateBlockRequest;
use App\Models\Assembly;
use App\Models\Block;
use App\Models\Division;
use Illuminate\Http\Request;

class NewBlockController extends Controller
{
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

        return view('blocks-new.index', compact('blocks'));
    }

    public function create(Request $request)
    {
        if (! auth()->user()->can('blocks.create')) {
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

        return view('blocks-new.create', compact('divisions', 'assemblies'));
    }

    public function store(CreateBlockRequest $request)
    {
        $randomNumbers = '';
        for ($i = 0; $i < 6; $i++) {
            $randomNumbers .= mt_rand(0, 9);
        }

        do {
            $erms_id = 'ERMS'.$randomNumbers;
        } while (Block::where('erms_id', $erms_id)->exists());

        $data = $request->validated();
        $data['created_by'] = $request->user()->id ?? '';
        $data['created_on'] = now();
        $data['erms_id'] = $erms_id;
        $data['geofence'] = json_decode($data['geofence'], true);

        Block::create($data);

        return redirect()->route('new-blocks.index')->with('status', 'Block created successfully.');
    }

    public function show(Block $block)
    {
        return view('blocks-new.show', compact('block'));
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

        return view('blocks-new.edit', compact('block', 'divisions', 'assemblies'));
    }

    public function update(UpdateBlockRequest $request, Block $block)
    {
        $data = $request->validated();
        $data['updated_on'] = now();
        $data['geofence'] = json_decode($data['geofence'], true);

        $block->update($data);

        return redirect()->route('new-blocks.index')->with('status', 'Block updated successfully.');
    }

    public function getBlocksByDivision(Request $request, $divisionId)
    {
        $blocks = Block::where('division_id', $divisionId)
            ->whereNotNull('geofence')
            ->select('id', 'name', 'code', 'geofence')
            ->get();

        return response()->json($blocks);
    }
}
