<?php

namespace App\Http\Controllers\PropertyRevenue;

use App\Http\Controllers\Controller;
use App\Models\PropertyRevenue;
use Illuminate\Http\Request;

class PropertyRevenueController extends Controller
{
    public function index(Request $request)
    {
        $propertyRevenues = PropertyRevenue::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('property-revenues.index', compact('propertyRevenues'));
    }

    public function create() {}

    public function store() {}

    public function show() {}

    public function edit() {}

    public function update() {}
}
