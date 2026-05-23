<?php

namespace App\Http\Controllers\PropertyType;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyType\CreatePropertyTypeRequest;
use App\Http\Requests\PropertyType\UpdatePropertyTypeRequest;
use App\Models\Assembly;
use App\Models\PropertyType;
use Illuminate\Http\Request;

class PropertyTypeController extends Controller
{
    public function index(Request $request)
    {
        $propertyTypes = PropertyType::query()
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('property-types.index', compact('propertyTypes'));
    }

    public function create(Request $request)
    {
        $assemblies = Assembly::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('property-types.create', compact('assemblies'));
    }

    public function store(CreatePropertyTypeRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id ?? null;

        // Auto-assign assembly_code from authenticated user if not provided
        if (! empty($request->user()->assembly_code)) {
            $data['assembly_code'] = $request->user()->assembly_code;
        }

        PropertyType::create($data);

        return redirect()->route('property-types.index')->with('status', 'Property type created successfully.');
    }

    public function show(PropertyType $propertyType)
    {
        return view('property-types.show', compact('propertyType'));
    }

    public function edit(Request $request, PropertyType $propertyType)
    {
        $assemblies = Assembly::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('property-types.edit', compact('propertyType', 'assemblies'));
    }

    public function update(UpdatePropertyTypeRequest $request, PropertyType $propertyType)
    {
        $data = $request->validated();

        // Auto-assign assembly_code from authenticated user if not provided
        if (empty($data['assembly_code']) && ! empty($request->user()->assembly_code)) {
            $data['assembly_code'] = $request->user()->assembly_code;
        }

        $propertyType->update($data);

        return redirect()->route('property-types.index')->with('status', 'Property type updated successfully.');
    }
}
