<?php

namespace App\Http\Controllers\MainItem;

use App\Http\Controllers\Controller;
use App\Http\Requests\MainItem\CreateMainItemRequest;
use App\Http\Requests\MainItem\UpdateMainItemRequest;
use App\Models\Assembly;
use App\Models\MainItem;
use Illuminate\Http\Request;

class MainItemController extends Controller
{
    public function index(Request $request)
    {
        $mainItems = MainItem::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('main-items.index', compact('mainItems'));
    }

    public function create(Request $request)
    {
        $assemblies = Assembly::orderBy('name', 'ASC')
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('main-items.create', compact('assemblies'));
    }

    public function store(CreateMainItemRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id ?? null;

        // Auto-assign assembly_code from authenticated user if not provided
        if (! empty($request->user()->assembly_code)) {
            $data['assembly_code'] = $request->user()->assembly_code;
        }

        MainItem::create($data);

        return redirect()->route('main-items.index')->with('status', 'Main item created successfully.');
    }

    public function show(MainItem $mainItem)
    {
        return view('main-items.show', compact('mainItem'));
    }

    public function edit(Request $request, MainItem $mainItem)
    {
        $assemblies = Assembly::orderBy('name', 'ASC')
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('main-items.edit', compact('mainItem', 'assemblies'));
    }

    public function update(UpdateMainItemRequest $request, MainItem $mainItem)
    {
        $data = $request->validated();

        // Auto-assign assembly_code from authenticated user if not provided
        if (empty($data['assembly_code']) && ! empty($request->user()->assembly_code)) {
            $data['assembly_code'] = $request->user()->assembly_code;
        }

        $mainItem->update($data);

        return redirect()->route('main-items.index')->with('status', 'Main item updated successfully.');
    }
}
