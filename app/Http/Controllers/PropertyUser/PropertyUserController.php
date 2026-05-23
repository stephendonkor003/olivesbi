<?php

namespace App\Http\Controllers\PropertyUser;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyUser\CreatePropertyUserRequest;
use App\Http\Requests\PropertyUser\UpdatePropertyUserRequest;
use App\Models\Assembly;
use App\Models\PropertyUser;
use App\Models\Zone;
use Illuminate\Http\Request;

class PropertyUserController extends Controller
{
    public function index(Request $request)
    {
        if (! auth()->user()->can('property-uses.view')) {
            abort(403, 'Unauthorized action.');
        }

        $propertyUsers = PropertyUser::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('property-users.index', compact('propertyUsers'));
    }

    public function create(Request $request)
    {
        if (! auth()->user()->can('property-uses.create')) {
            abort(403, 'Unauthorized action.');
        }

        $zones = Zone::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        $assemblies = Assembly::orderBy('name', 'ASC')
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('property-users.create', compact('zones', 'assemblies'));
    }

    public function store(CreatePropertyUserRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        PropertyUser::create($data);

        return redirect()->route('property-users.index')->with('status', 'Property user created successfully!');
    }

    public function show(PropertyUser $propertyUser)
    {
        return view('property-users.show', compact('propertyUser'));
    }

    public function edit(Request $request, PropertyUser $propertyUser)
    {
        if (! auth()->user()->can('property-uses.update')) {
            abort(403, 'Unauthorized action.');
        }

        $zones = Zone::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        $assemblies = Assembly::orderBy('name', 'ASC')
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('property-users.edit', compact('propertyUser', 'zones', 'assemblies'));
    }

    public function update(UpdatePropertyUserRequest $request, PropertyUser $propertyUser)
    {
        $propertyUser->update($request->validated());

        return redirect()->route('property-users.index')->with('status', 'Property user updated successfully!');
    }
}
