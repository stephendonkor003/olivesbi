<?php

namespace App\Http\Controllers\CustomerType;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerType\CreateCustomerTypeRequest;
use App\Http\Requests\CustomerType\UpdateCustomerTypeRequest;
use App\Models\CustomerType;
use Illuminate\Http\Request;

class CustomerTypeController extends Controller
{
    public function index(Request $request)
    {
        if (! auth()->user()->can('customer-types.view')) {
            abort(403, 'Unauthorized action.');
        }

        $customerTypes = CustomerType::orderBy('created_at', 'DESC')->get();

        return view('customer-types.index', compact('customerTypes'));
    }

    public function create()
    {
        if (! auth()->user()->can('customer-types.create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('customer-types.create');
    }

    public function store(CreateCustomerTypeRequest $request)
    {
        try {
            $data = $request->validated();
            $data['created_by'] = $request->user()->id;

            CustomerType::create($data);

            return redirect()->route('customer-types.index')->with('status', 'Customer type created successfully.');
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function show(CustomerType $customerType)
    {
        return view('customer-types.show', compact('customerType'));
    }

    public function edit(CustomerType $customerType)
    {
        if (! auth()->user()->can('customer-types.update')) {
            abort(403, 'Unauthorized action.');
        }

        return view('customer-types.edit', compact('customerType'));
    }

    public function update(UpdateCustomerTypeRequest $request, CustomerType $customerType)
    {
        try {
            $customerType->update($request->validated());

            return redirect()->route('customer-types.index')->with('status', 'Customer type updated successfully.');
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }
}
