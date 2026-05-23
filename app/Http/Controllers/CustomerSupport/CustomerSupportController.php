<?php

namespace App\Http\Controllers\CustomerSupport;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerSupport\UpdateCustomerSupportRequest;
use App\Models\CustomerSupport;
use Illuminate\Http\Request;

class CustomerSupportController extends Controller
{
    public function index(Request $request)
    {
        $customerSupports = CustomerSupport::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('customer-supports.index', compact('customerSupports'));
    }

    public function store() {}

    public function show(CustomerSupport $customerSupport)
    {
        return view('customer-supports.show', compact('customerSupport'));
    }

    public function edit(CustomerSupport $customerSupport)
    {
        return view('customer-supports.edit', compact('customerSupport'));
    }

    public function update(UpdateCustomerSupportRequest $request, CustomerSupport $customerSupport)
    {
        $data = $request->validated();
        $data['response_by'] = $request->user()->id;
        $data['response_date'] = now()->format('Y-m-d');

        $customerSupport->update($data);

        return redirect()->route('customer-supports.index')->with('status', 'Customer support updated successfully!');
    }
}
