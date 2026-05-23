<?php

namespace App\Http\Controllers\RevenueStreamType;

use App\Http\Controllers\Controller;
use App\Http\Requests\RevenueStreamType\CreateRevenueStreamTypeRequest;
use App\Http\Requests\RevenueStreamType\UpdateRevenueStreamTypeRequest;
use App\Models\RevenueStreamType;
use Illuminate\Http\Request;

class RevenueStreamTypeController extends Controller
{
    public function index(Request $request)
    {
        $revenueStreamTypes = RevenueStreamType::query()->orderBy('created_at', 'DESC')
            ->get();

        return view('revenue-stream-types.index', compact('revenueStreamTypes'));
    }

    public function create(Request $request)
    {
        if ($request->user()->access_level !== 'Super_User') {
            abort(403, 'Unauthorized action.');
        }

        return view('revenue-stream-types.create');
    }

    public function store(CreateRevenueStreamTypeRequest $request)
    {
        if ($request->user()->access_level !== 'Super_User') {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validated();
        $data['created_by'] = $request->user()->id ?? null;

        RevenueStreamType::create($data);

        return redirect()->route('revenue-stream-types.index')->with('status', 'Revenue stream type created successfully.');
    }

    public function show(RevenueStreamType $revenueStreamType)
    {
        return view('revenue-stream-types.show', compact('revenueStreamType'));
    }

    public function edit(Request $request, RevenueStreamType $revenueStreamType)
    {
        if ($request->user()->access_level !== 'Super_User') {
            abort(403, 'Unauthorized action.');
        }

        return view('revenue-stream-types.edit', compact('revenueStreamType'));
    }

    public function update(UpdateRevenueStreamTypeRequest $request, RevenueStreamType $revenueStreamType)
    {
        if ($request->user()->access_level !== 'Super_User') {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validated();

        $revenueStreamType->update($data);

        return redirect()->route('revenue-stream-types.index')->with('status', 'Revenue stream type updated successfully.');
    }
}
