<?php

namespace App\Http\Controllers\SubItem;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubItem\CreateSubItemRequest;
use App\Http\Requests\SubItem\UpdateSubItemRequest;
use App\Models\Assembly;
use App\Models\SubItem;
use Illuminate\Http\Request;

class SubItemController extends Controller
{
    public function index(Request $request)
    {
        $subItems = SubItem::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('sub-items.index', compact('subItems'));
    }

    public function create(Request $request)
    {
        $assemblies = Assembly::orderBy('name', 'ASC')
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('sub-items.create', compact('assemblies'));
    }

    public function store(CreateSubItemRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id ?? null;

        // Auto-assign assembly_code from authenticated user if not provided
        if (! empty($request->user()->assembly_code)) {
            $data['assembly_code'] = $request->user()->assembly_code;
        }

        SubItem::create($data);

        return redirect()->route('sub-items.index')->with('status', 'Sub item created successfully.');
    }

    public function show(SubItem $subItem)
    {
        return view('sub-items.show', compact('subItem'));
    }

    public function edit(Request $request, SubItem $subItem)
    {
        $assemblies = Assembly::orderBy('name', 'ASC')
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('sub-items.edit', compact('subItem', 'assemblies'));
    }

    public function update(UpdateSubItemRequest $request, SubItem $subItem)
    {
        $data = $request->validated();

        // Auto-assign assembly_code from authenticated user if not provided
        if (empty($data['assembly_code']) && ! empty($request->user()->assembly_code)) {
            $data['assembly_code'] = $request->user()->assembly_code;
        }

        $subItem->update($data);

        return redirect()->route('sub-items.index')->with('status', 'Sub item updated successfully.');
    }
}
