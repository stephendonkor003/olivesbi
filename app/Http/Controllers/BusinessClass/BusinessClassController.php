<?php

namespace App\Http\Controllers\BusinessClass;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessClass\CreateBusinessClassRequest;
use App\Http\Requests\BusinessClass\UpdateBusinessClassRequest;
use App\Models\BusinessClass;
use App\Models\BusinessType;

class BusinessClassController extends Controller
{
    // Display a list of all business classes
    public function index()
    {
        if (! auth()->user()->can('business-classes.view')) {
            abort(403, 'Unauthorized action.');
        }

        $businessClasses = BusinessClass::orderBy('created_at', 'DESC')->get();

        return view('business_classes.index', compact('businessClasses'));
    }

    // Show the form for creating a new business class
    public function create()
    {
        if (! auth()->user()->can('business-classes.create')) {
            abort(403, 'Unauthorized action.');
        }

        $businessTypes = BusinessType::get();

        return view('business_classes.create', compact('businessTypes'));
    }

    // Store a newly created business class in the database
    public function store(CreateBusinessClassRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        BusinessClass::create($data);

        return redirect()->route('business-classes.index')->with('status', 'Business class created successfully.');
    }

    // Display the specified business class
    public function show(BusinessClass $businessClass)
    {
        return view('business_classes.show', compact('businessClass'));
    }

    // Show the form for editing the specified business class
    public function edit(BusinessClass $businessClass)
    {
        if (! auth()->user()->can('business-classes.update')) {
            abort(403, 'Unauthorized action.');
        }

        $businessTypes = BusinessType::get();

        return view('business_classes.edit', compact('businessClass', 'businessTypes'));
    }

    // Update the specified business class in the database
    public function update(UpdateBusinessClassRequest $request, BusinessClass $businessClass)
    {
        $data = $request->validated();

        $businessClass->update($data);

        return redirect()->route('business-classes.index')->with('success', 'Business class updated successfully.');
    }

    // Remove the specified business class from the database
    public function destroy($id)
    {
        $businessClass = BusinessClass::findOrFail($id);
        $businessClass->delete();

        // Redirect to the index page with a success message
        return redirect()->route('business_classes.index')->with('success', 'Business class deleted successfully.');
    }
}
