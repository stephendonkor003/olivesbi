<?php

namespace App\Http\Controllers\NewDivision;

use App\Http\Controllers\Controller;
use App\Http\Requests\Division\CreateDivisionRequest;
use App\Http\Requests\Division\UpdateDivisionRequest;
use App\Models\Assembly;
use App\Models\Division;
use Illuminate\Http\Request;

class NewDivisionController extends Controller
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

        // $assemblies = Assembly::orderBy('name', 'ASC')
        //     ->when(!empty($request->user()->regional_code), function ($query) use ($request) {
        //         $query->where('regional_code', $request->user()->regional_code);
        //     })
        //     ->when(!empty($request->user()->assembly_code), function ($query) use ($request) {
        //         $query->where('assembly_code', $request->user()->assembly_code);
        //     })
        //     ->get();

        return view('divisions-new.index', compact('divisions'));
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

        return view('divisions-new.create', compact('assemblies'));
    }

    public function store(CreateDivisionRequest $request)
    {
        $randomNumbers = '';
        for ($i = 0; $i < 6; $i++) {
            $randomNumbers .= mt_rand(0, 9);
        }

        do {
            $erms_id = 'ERMS'.$randomNumbers;
        } while (Division::where('erms_id', $erms_id)->exists());

        $data = $request->validated();
        $data['created_by'] = $request->user()->id ?? '';
        $data['created_on'] = now();
        $data['erms_id'] = $erms_id;
        $data['geofence'] = json_decode($data['geofence'], true);

        Division::create($data);

        return redirect()->route('new-divisions.index')->with('status', 'Division created successfully.');
    }

    public function show(Division $division)
    {
        return view('divisions-new.show', compact('division'));
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

        return view('divisions-new.edit', compact('division', 'assemblies'));
    }

    public function update(UpdateDivisionRequest $request, Division $division)
    {
        $data = $request->validated();
        $data['updated_on'] = now();
        $data['geofence'] = json_decode($data['geofence'], true);

        $division->update($data);

        return redirect()->route('new-divisions.index')->with('status', 'Division updated successfully.');
    }

    public function getDivisionsByAssembly(Request $request, $assemblyCode)
    {
        $divisions = Division::where('assembly_code', $assemblyCode)
            ->whereNotNull('geofence')
            ->select('id', 'name', 'code', 'geofence')
            ->get();

        return response()->json($divisions);
    }
}
