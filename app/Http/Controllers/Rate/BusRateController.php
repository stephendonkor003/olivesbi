<?php

namespace App\Http\Controllers\Rate;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusRate\CreateBusRateRequest;
use App\Http\Requests\BusRate\UpdateBusRateRequest;
use App\Http\Requests\Import\ImportBusinessRateRequest;
use App\Imports\BusinessRate\BusinessRateImport;
use App\Models\Assembly;
use App\Models\BOPRate;
use App\Models\PropertyUser;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class BusRateController extends Controller
{
    public function index(Request $request)
    {
        if (! auth()->user()->can('rates.view')) {
            abort(403, 'Unauthorized action.');
        }

        $rates = BOPRate::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('bop-rates.index', compact('rates'));
    }

    public function create(Request $request)
    {
        if (! auth()->user()->can('rates.create')) {
            abort(403, 'Unauthorized action.');
        }

        $zones = Zone::orderBy('name', 'ASC')
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

        return view('bop-rates.create', compact('assemblies', 'zones'));
    }

    public function store(CreateBusRateRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        BOPRate::create($data);

        return redirect()->route('rates.bus.index')->with('status', 'Business operating permit rate created successfully!');
    }

    public function show(BOPRate $rate)
    {
        return view('bop-rates.show', compact('rate'));
    }

    public function edit(Request $request, BOPRate $rate)
    {
        if (! auth()->user()->can('rates.update')) {
            abort(403, 'Unauthorized action.');
        }

        $zones = Zone::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        $propertyUsers = PropertyUser::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->where('zone_id', $rate->zone_id)
            ->get();

        $assemblies = Assembly::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('bop-rates.edit', compact('rate', 'zones', 'assemblies', 'propertyUsers'));
    }

    public function update(UpdateBusRateRequest $request, BOPRate $rate)
    {
        $rate->update(($request->validated()));

        return redirect()->route('rates.bus.index')->with('status', 'Business operating permit rate updated successfully!');
    }

    public function import()
    {
        return view('bop-rates.import');
    }

    public function importData(ImportBusinessRateRequest $request)
    {
        try {
            $data = $request->validated();
            $createdBy = $request->user()->id;

            $import = (new BusinessRateImport($createdBy));
            $import->import($request->file('file'));

            return redirect()->route('rates.bus.index')->with('status', 'BOP rate data uploaded successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $exception) {
            throw ValidationException::withMessages([
                'file' => collect($exception->errors())->flatten()->toArray(),
            ]);
        }
    }

    public function downloadTemplate()
    {
        $filePath = public_path('assets/templates/bop_rate_template.xlsx');

        if (! file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return Response::download($filePath, 'bop_rate_template.xlsx');
    }
}
