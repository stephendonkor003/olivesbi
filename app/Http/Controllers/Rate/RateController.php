<?php

namespace App\Http\Controllers\Rate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Import\ImportPropertyRateRequest;
use App\Http\Requests\Rate\CreateRateRequest;
use App\Http\Requests\Rate\UpdateRateRequest;
use App\Imports\PropertyRate\PropertyRateImport;
use App\Models\Assembly;
use App\Models\PropertyUser;
use App\Models\Rate;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class RateController extends Controller
{
    public function index(Request $request)
    {
        if (! auth()->user()->can('rates.view')) {
            abort(403, 'Unauthorized action.');
        }

        $rates = Rate::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('rates.index', compact('rates'));
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

        return view('rates.create', compact('zones', 'assemblies'));
    }

    public function store(CreateRateRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        Rate::create($data);

        return redirect()->route('rates.index')->with('status', 'Property rate created successfully!');
    }

    public function show(Rate $rate)
    {
        return view('rates.show', compact('rate'));
    }

    public function edit(Request $request, Rate $rate)
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

        return view('rates.edit', compact('rate', 'zones', 'assemblies', 'propertyUsers'));
    }

    public function update(UpdateRateRequest $request, Rate $rate)
    {
        $rate->update($request->validated());

        return redirect()->route('rates.index')->with('status', 'Property rate updated successfully!');
    }

    public function propertyUse(Request $request)
    {
        $propertyUse = PropertyUser::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->where('zone_id', $request->zone_id)
            ->get();

        return response()->json([
            'message' => $propertyUse,
        ]);
    }

    public function zone(Request $request)
    {
        $zones = Zone::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->where('assembly_code', $request->assembly_code)
            ->get();

        return response()->json([
            'message' => $zones,
        ]);
    }

    public function import()
    {
        return view('rates.import');
    }

    public function importData(ImportPropertyRateRequest $request)
    {
        try {
            $data = $request->validated();
            $createdBy = $request->user()->id;

            $import = (new PropertyRateImport($createdBy));
            $import->import($request->file('file'));

            return redirect()->route('rates.index')->with('status', 'Property rate data uploaded successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $exception) {
            throw ValidationException::withMessages([
                'file' => collect($exception->errors())->flatten()->toArray(),
            ]);
        }
    }

    public function downloadTemplate()
    {
        $filePath = public_path('assets/templates/property_rates_template.xlsx');

        if (! file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return Response::download($filePath, 'property_rates_template.xlsx');
    }
}
