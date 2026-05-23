<?php

namespace App\Http\Controllers\ItemDescription;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemDescription\CreateItemDescriptionRequest;
use App\Http\Requests\ItemDescription\UpdateItemDescriptionRequest;
use App\Models\Assembly;
use App\Models\ItemDescription;
use Illuminate\Http\Request;

class ItemDescriptionController extends Controller
{
    public function index(Request $request)
    {
        $itemDescriptions = ItemDescription::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('item-descriptions.index', compact('itemDescriptions'));
    }

    public function create(Request $request)
    {
        $assemblies = Assembly::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('item-descriptions.create', compact('assemblies'));
    }

    public function store(CreateItemDescriptionRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id ?? null;

        // Auto-assign assembly_code from authenticated user if not provided
        if (! empty($request->user()->assembly_code)) {
            $data['assembly_code'] = $request->user()->assembly_code;
        }

        ItemDescription::create($data);

        return redirect()->route('item-descriptions.index')->with('status', 'Item description created successfully.');
    }

    public function show(ItemDescription $itemDescription)
    {
        return view('item-descriptions.show', compact('itemDescription'));
    }

    public function edit(Request $request, ItemDescription $itemDescription)
    {
        $assemblies = Assembly::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('item-descriptions.edit', compact('itemDescription', 'assemblies'));
    }

    public function update(UpdateItemDescriptionRequest $request, ItemDescription $itemDescription)
    {
        $data = $request->validated();

        // Auto-assign assembly_code from authenticated user if not provided
        if (empty($data['assembly_code']) && ! empty($request->user()->assembly_code)) {
            $data['assembly_code'] = $request->user()->assembly_code;
        }

        $itemDescription->update($data);

        return redirect()->route('item-descriptions.index')->with('status', 'Item description updated successfully.');
    }
}
