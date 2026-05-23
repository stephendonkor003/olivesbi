<?php

namespace App\Http\Controllers\BillAdjustment;

use App\Http\Controllers\Controller;
use App\Http\Requests\BillAdjustment\CreateBillAdjustmentRequest;
use App\Http\Requests\BillAdjustment\UpdateBillAdjustmentRequest;
use App\Models\Assembly;
use App\Models\BillAdjustment;
use App\Models\RevenueStreamType;
use Illuminate\Http\Request;

class BillAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        if (! auth()->user()->can('bills.view')) {
            abort(403, 'Unauthorized action.');
        }

        $billAdjustments = BillAdjustment::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('bill-adjustments.index', compact('billAdjustments'));
    }

    public function create(Request $request)
    {
        $revenueStreamTypes = RevenueStreamType::orderBy('name', 'ASC')->get();

        $assemblies = Assembly::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('bill-adjustments.create', compact('revenueStreamTypes', 'assemblies'));
    }

    public function store(CreateBillAdjustmentRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id ?? null;

        // Handle field nulling based on discount type
        switch ($data['discount_type']) {
            case 'Assembly Discount':
                $data['division_id'] = null;
                $data['block_id'] = null;
                $data['erms_id'] = null;
                break;
            case 'Division Discount':
                $data['block_id'] = null;
                $data['erms_id'] = null;
                break;
            case 'Block Discount':
                $data['erms_id'] = null;
                break;
            case 'Property Discount':
            case 'BOP Discount':
                $data['division_id'] = null;
                $data['block_id'] = null;
                // Keep erms_id as array - Laravel will auto-cast to JSON
                break;
        }

        // Remove erms_id if it's empty
        if (empty($data['erms_id'])) {
            unset($data['erms_id']);
        }

        BillAdjustment::create($data);

        return redirect()->route('bill-adjustments.index')->with('status', 'Bill adjustment created successfully!');
    }

    public function show(BillAdjustment $billAdjustment)
    {
        return view('bill-adjustments.show', compact('billAdjustment'));
    }

    public function edit(Request $request, BillAdjustment $billAdjustment)
    {
        $revenueStreamTypes = RevenueStreamType::orderBy('name', 'ASC')->get();

        $assemblies = Assembly::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('bill-adjustments.edit', compact('billAdjustment', 'revenueStreamTypes', 'assemblies'));
    }

    public function update(UpdateBillAdjustmentRequest $request, BillAdjustment $billAdjustment)
    {
        $data = $request->validated();

        // Handle field nulling based on discount type
        switch ($data['discount_type']) {
            case 'Assembly Discount':
                $data['division_id'] = null;
                $data['block_id'] = null;
                break;
            case 'Division Discount':
                $data['block_id'] = null;
                break;
            case 'Block Discount':
                // division_id is already set, no changes needed
                break;
            case 'Property Discount':
            case 'BOP Discount':
                $data['division_id'] = null;
                $data['block_id'] = null;
                // Keep erms_id as array - Laravel will auto-cast to JSON
                break;
        }

        $billAdjustment->update($data);

        return redirect()->route('bill-adjustments.index')->with('status', 'Bill adjustment updated successfully!');
    }
}
