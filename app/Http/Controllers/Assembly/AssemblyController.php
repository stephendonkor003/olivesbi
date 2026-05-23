<?php

namespace App\Http\Controllers\Assembly;

use App\Http\Controllers\Controller;
use App\Http\Requests\Assembly\UpdateAssemblyRequest;
use App\Http\Requests\Import\ImportAssemblyRequest;
use App\Imports\Assembly\AssemblyImport;
use App\Models\Assembly;
use App\Models\GhanaRegion;
use App\Models\InvoiceLayoutTemplate;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File as FileDelete;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AssemblyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum']);
    }

    public function index(Request $request)
    {
        if (! auth()->user()->can('assemblies.view')) {
            abort(403, 'Unauthorized action.');
        }

        $assemblies = Assembly::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('assembly.index', compact('assemblies'));
    }

    public function create(Request $request)
    {
        if (! auth()->user()->can('assemblies.create')) {
            abort(403, 'Unauthorized action.');
        }

        $regions = GhanaRegion::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->get();

        $regions->push((object) [
            'id' => $regions->max('id') + 1,
            'regional_code' => 'OTH',
            'name' => 'Other',
            'created_at' => null,
            'updated_at' => null,
        ]);

        $supervisors = User::select('id', 'name', 'phone')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->where('status', 'Active')
            ->get()
            ->map(function ($supervisor) {
                return [
                    'id' => $supervisor->id,
                    'name' => $supervisor->name.' - '.$supervisor->phone,
                ];
            });

        return view('assembly.create')->with([
            'regions' => $regions,
            'supervisors' => $supervisors,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $assembly = Assembly::find($request->assembly_id);

            $rules = [
                'name' => ['required', 'string', Rule::unique('assemblies', 'name')->ignore($assembly->id ?? null)],
                'assembly_code' => ['required', 'string', Rule::unique('assemblies', 'assembly_code')->ignore($assembly->id ?? null)],
                'regional_code' => ['required', 'string'],
                'supervisor' => ['required', 'string', 'exists:users,id'],
                'logo' => ['nullable', 'file', 'mimes:jpeg,jpg,png,gif'],
                'invoice_layout' => ['nullable', 'string'],
                'address' => ['required', 'string'],
                'assembly_category' => ['required', 'string', 'in:Municipal,Metropolitan,District'],
                'phone' => ['required', 'string'],
                'geo_coordinate' => ['nullable', 'string'],
                'bank_name' => ['nullable', 'string'],
                'bank_number' => ['nullable', 'numeric'],
                'bank_branch' => ['nullable', 'string'],
            ];

            // Validate first
            $validated = $request->validate($rules);

            // Handle logo upload AFTER validation
            $image_name = $assembly->logo ?? null;
            if ($request->hasFile('logo')) {
                $image = $request->file('logo');
                $image_name = Assembly::generateImageFileName().'.'.$image->getClientOriginalExtension();
                $logoDestinationPath = storage_path('app/public/images/logo');
                $image->move($logoDestinationPath, $image_name);

                // Delete old logo if updating
                if ($assembly && $assembly->logo) {
                    $oldPath = $logoDestinationPath.'/'.$assembly->logo;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
            }

            // Prepare data
            $data = $validated;
            $data['logo'] = $image_name;
            $data['status'] = 'Active';

            // Update or create assembly
            if ($assembly) {
                $assembly->update($data);
                $message = 'Assembly updated successfully.';
            } else {
                Assembly::create($data);
                $message = 'Assembly created successfully.';
            }

            // Update supervisor user
            $user = User::find($data['supervisor']);
            if ($user) {
                $user->update([
                    'access_level' => 'Assembly_Administrator',
                    'assembly_code' => $data['assembly_code'],
                    'status' => 'Active',
                ]);

                $role = Role::where('name', 'like', '%administrator%')->first();
                if ($role) {
                    $user->roles()->sync($role->id);
                }
            }

            return redirect()->route('assembly.index')->with('status', $message);
        } catch (\Exception $ex) {
            return back()->withErrors(['error' => $ex->getMessage()]);
        }
    }

    public function edit(Request $request, Assembly $assembly)
    {
        if (! auth()->user()->can('assemblies.update')) {
            abort(403, 'Unauthorized action.');
        }

        $regions = GhanaRegion::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->get();

        $supervisors = User::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->where('status', 'Active')
            ->get()
            ->map(function ($supervisor) {
                return [
                    'id' => $supervisor->id,
                    'name' => $supervisor->name.' - '.$supervisor->phone,
                ];
            });

        return view('assembly.edit', compact('assembly', 'supervisors', 'regions'));
    }

    public function show(Assembly $assembly)
    {
        $invoiceLayoutTemplates = InvoiceLayoutTemplate::all();

        return view('assembly.show', compact('assembly', 'invoiceLayoutTemplates'));
    }

    public function update(UpdateAssemblyRequest $request, Assembly $assembly)
    {
        try {
            if ($request->hasFile('logo')) {
                $image = $request->file('logo');
                $image_name = Assembly::generateImageFileName();
                $image_name = $image_name.'.'.$image->getClientOriginalExtension();
                $logoDestinationPath = storage_path('app/public/images/logo');
                $image->move($logoDestinationPath, $image_name);
            }

            if ($request->hasFile('logo')) {
                $image_path = storage_path().'/app/public/images/logo/'.$assembly->logo;
                FileDelete::delete($image_path);
            }

            $data = $request->validated();
            $data['logo'] = $image_name ?? $assembly->logo;

            if ($assembly->supervisor !== $data['supervisor']) {
                // Update assembly admin user details
                $userData = [
                    'access_level' => 'Assembly_Administrator',
                    'assembly_code' => $data['assembly_code'],
                    'status' => 'Active',
                ];

                $user = User::where('id', $data['supervisor'])->first();
                $user->update($userData);
                $role = Role::where('name', 'like', '%administrator%')->first();

                if ($role) {
                    $user->roles()->sync($role->id);
                }
            }

            $assembly->update($data);

            return redirect()->route('assembly.index')->with('status', 'Assembly updated successfully.');
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    // Remove the specified assembly from the database
    public function destroy(Assembly $assembly)
    {
        $assembly->delete();

        return redirect()->route('assembly.index')
            ->with('success', 'Assembly deleted successfully.');
    }

    public function fetchAssembly(Request $request)
    {
        $region = GhanaRegion::where('regional_code', $request->regional_code)->first();

        $assemblies = Assembly::where('regional_code', $region->regional_code)->get();

        $assemblies->push((object) [
            'id' => $assemblies->max('id') + 1,
            'regional_code' => 'OTH',
            'assembly_code' => 'OTH',
            'name' => 'Other',
            'created_at' => null,
            'updated_at' => null,
        ]);

        return response()->json([
            'message' => $assemblies,
        ]);
    }

    public function getSelectedAssembly(Request $request)
    {
        $assembly = Assembly::with(['assemblySupervisor'])
            ->where('assembly_code', $request->assembly_code)
            ->first();

        return response()->json([
            'message' => $assembly,
        ]);
    }

    public function import()
    {
        return view('assembly.import');
    }

    public function importData(ImportAssemblyRequest $request)
    {
        try {
            $data = $request->validated();
            $createdBy = $request->user()->id;

            $import = (new AssemblyImport($createdBy));
            $import->import($request->file('file'));

            return redirect()->route('assembly.index')->with('status', 'Assembly data uploaded successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $exception) {
            throw ValidationException::withMessages([
                'file' => collect($exception->errors())->flatten()->toArray(),
            ]);
        }
    }
}
