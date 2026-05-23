<?php

namespace App\Http\Controllers\BusinessType;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessType\CreateBusinessTypeRequest;
use App\Http\Requests\BusinessType\UpdateBusinessTypeRequest;
use App\Models\BusinessType;

class BusinessTypeController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        if (! auth()->user()->can('business-types.view')) {
            abort(403, 'Unauthorized action.');
        }

        $businessTypes = BusinessType::orderBy('created_at', 'DESC')->get();

        return view('business_types.index', compact('businessTypes'));
    }

    // Show the form for creating a new resource.
    public function create()
    {
        if (! auth()->user()->can('business-types.create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('business_types.create');
    }

    // Store a newly created resource in storage.
    public function store(CreateBusinessTypeRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        BusinessType::create($data);

        return redirect()->route('business-types.index')->with('status', 'Business Type created successfully.');
    }

    // Display the specified resource.
    public function show(BusinessType $businessType)
    {
        $subCategoriesString = implode(', ', $businessType->sub_categories);

        return view('business_types.show', compact('businessType', 'subCategoriesString'));
    }

    // Show the form for editing the specified resource.
    public function edit(BusinessType $businessType)
    {
        if (! auth()->user()->can('business-types.update')) {
            abort(403, 'Unauthorized action.');
        }

        return view('business_types.edit', compact('businessType'));
    }

    // Update the specified resource in storage.
    public function update(UpdateBusinessTypeRequest $request, BusinessType $businessType)
    {
        $businessType->update($request->validated());

        return redirect()->route('business-types.index')->with('status', 'Business Type updated successfully.');
    }

    // Remove the specified resource from storage.
    public function destroy($id)
    {
        $businessType = BusinessType::find($id);
        $businessType->delete();

        return redirect()->route('business_types.index')->with('success', 'Business Type deleted successfully.');
    }
}
