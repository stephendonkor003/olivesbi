<?php

namespace App\Http\Controllers\PropertyClasses;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyClasses\CreatePropertyClassesRequest;
use App\Http\Requests\PropertyClasses\UpdatePropertyClassesRequest;
use App\Models\Assembly;
use App\Models\PropertyClasses;
use Illuminate\Http\Request;

class PropertyClassesController extends Controller
{
    public function index(Request $request)
    {
        $propertyClasses = PropertyClasses::query()
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('property-classes.index', compact('propertyClasses'));
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

        return view('property-classes.create', compact('assemblies'));
    }

    public function store(CreatePropertyClassesRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id ?? null;

        // Auto-assign assembly_code from authenticated user if not provided
        if (! empty($request->user()->assembly_code)) {
            $data['assembly_code'] = $request->user()->assembly_code;
        }

        PropertyClasses::create($data);

        return redirect()->route('property-classes.index')->with('status', 'Property classes created successfully.');
    }

    public function show(PropertyClasses $propertyClass)
    {
        return view('property-classes.show', compact('propertyClass'));
    }

    public function edit(Request $request, PropertyClasses $propertyClass)
    {
        $assemblies = Assembly::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('property-classes.edit', compact('propertyClass', 'assemblies'));
    }

    public function update(UpdatePropertyClassesRequest $request, PropertyClasses $propertyClass)
    {
        $data = $request->validated();

        // Auto-assign assembly_code from authenticated user if not provided
        if (empty($data['assembly_code']) && ! empty($request->user()->assembly_code)) {
            $data['assembly_code'] = $request->user()->assembly_code;
        }

        $propertyClass->update($data);

        return redirect()->route('property-classes.index')->with('status', 'Property classes updated successfully.');
    }
}
