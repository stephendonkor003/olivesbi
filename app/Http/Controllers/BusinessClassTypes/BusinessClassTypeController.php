<?php

namespace App\Http\Controllers\BusinessClassTypes;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessClassType\CreateBusinessClassTypeRequest;
use App\Http\Requests\BusinessClassType\UpdateBusinessClassTypeRequest;
use App\Models\BusinessClassType;

class BusinessClassTypeController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth:sanctum']);
    }

    public function index()
    {
        if (! auth()->user()->can('property-types.view')) {
            abort(403, 'Unauthorized action.');
        }

        $businessClassTypes = BusinessClassType::orderBy('created_at', 'DESC')->get();

        return view('business-class-types.index', compact('businessClassTypes'));
    }

    public function create()
    {
        if (! auth()->user()->can('property-types.create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('business-class-types.create');
    }

    public function store(CreateBusinessClassTypeRequest $request)
    {
        // Generate a random alphanumeric 7-digit number starting with 'IABNC'
        $classcode = 'PC'.substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4);

        // Check if the generated classcode already exists
        while (BusinessClassType::where('classcode', $classcode)->exists()) {
            $classcode = 'PC'.substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4);
        }

        // Add created_by field
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $data['classcode'] = $classcode;

        BusinessClassType::create($data);

        return redirect()->route('business-class-types.index')->with('success', 'Business class type created successfully!');
    }

    public function show(BusinessClassType $businessClassType)
    {
        return view('business-class-types.show', compact('businessClassType'));
    }

    public function edit(BusinessClassType $businessClassType)
    {
        if (! auth()->user()->can('property-types.update')) {
            abort(403, 'Unauthorized action.');
        }

        return view('business-class-types.edit', compact('businessClassType'));
    }

    public function update(UpdateBusinessClassTypeRequest $request, BusinessClassType $businessClassType)
    {

        $businessClassType->update($request->validated());

        return redirect()->route('business-class-types.index')->with('success', 'Business class type updated successfully!');
    }

    public function destroy(BusinessClassType $businessClassType)
    {
        $businessClassType->delete();

        return redirect()->route('business-class-types.index')
            ->with('success', 'Business class type deleted successfully!');
    }
}
