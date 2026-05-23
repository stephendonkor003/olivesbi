<?php

namespace App\Http\Controllers\Divisions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Division\CreateDivisionRequest;
use App\Http\Requests\Division\UpdateDivisionRequest;
use App\Models\Assembly;
use App\Models\Block;
use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index(Request $request)
    {
        if (! auth()->user()->can('divisions.view')) {
            abort(403, 'Unauthorized action.');
        }

        $divisions = Division::orderBy('created_at', 'DESC')
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

        return view('divisions.index', compact('divisions', 'assemblies'));
    }

    public function create(Request $request)
    {
        if (! auth()->user()->can('divisions.create')) {
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

        return view('divisions.create')->with([
            'assemblies' => $assemblies,
        ]);
    }

    public function store(CreateDivisionRequest $request)
    {
        try {
            $data = $request->validated();

            // Auto-assign assembly_code from authenticated user if not provided
            if (! empty($request->user()->assembly_code)) {
                $data['assembly_code'] = $request->user()->assembly_code;
            }

            Division::create($data);

            return redirect()->route('divisions.index')->with('status', 'Division created successfully.');
        } catch (\Exception $e) {
            report($e);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create division. Please try again.');
        }
    }

    public function show(Division $division)
    {
        return view('divisions.show', compact('division'));
    }

    public function edit(Request $request, Division $division)
    {
        if (! auth()->user()->can('divisions.update')) {
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

        return view('divisions.edit', compact('division', 'assemblies'));
    }

    public function update(UpdateDivisionRequest $request, Division $division)
    {
        try {
            $data = $request->validated();

            // Auto-assign assembly_code from authenticated user if not provided
            if (empty($data['assembly_code']) && ! empty($request->user()->assembly_code)) {
                $data['assembly_code'] = $request->user()->assembly_code;
            }

            $division->update($data);

            return redirect()->route('divisions.index')->with('status', 'Division updated successfully.');
        } catch (\Exception $e) {
            report($e);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update division. Please try again.');
        }
    }

    public function destroy(Division $division)
    {
        // Check for related blocks
        $blockCount = Block::where('division_id', $division->id)->count();

        if ($blockCount > 0) {
            return redirect()->back()->with('error',
                "Cannot delete division. It has {$blockCount} block(s) assigned to it. Please reassign or delete the blocks first.");
        }

        $division->delete();

        return redirect()->route('divisions.index')->with('success', 'Division deleted successfully.');
    }
}
