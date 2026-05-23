<?php

namespace App\Http\Controllers\RevenueStream;

use App\Http\Controllers\Controller;
use App\Http\Requests\Import\ImportRevenueStreamRequest;
use App\Http\Requests\RevenueStream\CreateRevenueStreamRequest;
use App\Http\Requests\RevenueStream\CreateRevenueStreamRequestBOP;
use App\Http\Requests\RevenueStream\UpdateRevenueStreamRequest;
use App\Http\Requests\RevenueStream\UpdateRevenueStreamRequestBOP;
use App\Imports\RevenueStream\BOPRevenueStreamImport;
use App\Imports\RevenueStream\RevenueStreamImport;
use App\Models\Assembly;
use App\Models\Category;
use App\Models\ItemDescription;
use App\Models\MainItem;
use App\Models\PropertyClasses;
use App\Models\PropertyType;
use App\Models\RevenueStream;
use App\Models\RevenueStreamType;
use App\Models\SubItem;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class RevenueStreamController extends Controller
{
    public function index(Request $request)
    {
        $revenueStreams = RevenueStream::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->whereHas('revenueStreamType', function ($query) {
                $query->where('name', 'Property Rate');
            })
            ->get();

        return view('revenue-streams.index', compact('revenueStreams'));
    }

    public function indexBOP(Request $request)
    {
        $revenueStreams = RevenueStream::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->whereHas('revenueStreamType', function ($query) {
                $query->where('name', 'BOP (Licenses)');
            })
            ->get();

        return view('revenue-streams-bop.index', compact('revenueStreams'));
    }

    public function create(Request $request)
    {
        $zoneTypes = Zone::query()
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })->orderBy('name', 'ASC')->get();

        $propertyClasses = PropertyClasses::query()
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })->orderBy('name', 'ASC')->get();

        $propertyTypes = PropertyType::query()
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })->orderBy('name', 'ASC')->get();

        $assemblies = Assembly::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('revenue-streams.create', compact('zoneTypes', 'propertyClasses', 'propertyTypes', 'assemblies'));
    }

    public function createBOP(Request $request)
    {
        $itemDescriptions = ItemDescription::query()
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->orderBy('name', 'ASC')
            ->get();

        $categories = Category::query()
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->orderBy('name', 'DESC')
            ->get();

        $assemblies = Assembly::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        $mainItems = MainItem::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        $subItems = SubItem::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('revenue-streams-bop.create', compact(
            'assemblies',
            'itemDescriptions',
            'categories',
            'mainItems',
            'subItems'
        ));
    }

    public function store(CreateRevenueStreamRequest $request)
    {
        $data = $request->validated();

        // Auto-assign assembly_code from authenticated user if not provided
        if (! empty($request->user()->assembly_code)) {
            $data['assembly_code'] = $request->user()->assembly_code;
        }

        $data['revenue_stream_type_id'] = RevenueStreamType::where('name', 'Property Rate')->first()?->id;

        $data['created_by'] = $request->user()->id ?? null;

        if (
            RevenueStream::query()
                ->where('is_rated', $data['is_rated'])
                ->where('revenue_stream_type_id', $data['revenue_stream_type_id'])
                ->where('zone_type_id', $data['zone_type_id'])
                ->where('assembly_code', $data['assembly_code'])
                ->when(isset($data['property_classes_id']),
                    fn ($query) => $query->where('property_classes_id', $data['property_classes_id']),
                    fn ($query) => $query->whereNull('property_classes_id')
                )
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'is_rated' => 'A revenue stream with these information already exists.',
            ]);
        }

        RevenueStream::create($data);

        return redirect()->route('revenue-streams.index')->with('status', 'Revenue stream created successfully.');
    }

    public function storeBOP(CreateRevenueStreamRequestBOP $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id ?? null;

        // Auto-assign assembly_code from authenticated user if not provided
        if (! empty($request->user()->assembly_code)) {
            $data['assembly_code'] = $request->user()->assembly_code;
        }

        $data['revenue_stream_type_id'] = RevenueStreamType::where('name', 'BOP (Licenses)')->first()?->id;

        if (
            RevenueStream::query()
                ->where('revenue_stream_type_id', $data['revenue_stream_type_id'])
                ->where('description_id', $data['description_id'])
                ->where('category_id', $data['category_id'])
                ->where('assembly_code', $data['assembly_code'])
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'description_id' => 'A revenue stream with these information already exists.',
            ]);
        }

        RevenueStream::create($data);

        return redirect()->route('revenue-streams.indexBOP')->with('status', 'Revenue stream created successfully.');
    }

    public function show(RevenueStream $revenueStream)
    {
        return view('revenue-streams.show', compact('revenueStream'));
    }

    public function showBOP(RevenueStream $revenueStream)
    {
        return view('revenue-streams-bop.show', compact('revenueStream'));
    }

    public function edit(Request $request, RevenueStream $revenueStream)
    {
        $zoneTypes = Zone::query()
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->orderBy('name', 'ASC')->get();

        $propertyClasses = PropertyClasses::query()
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->orderBy('name', 'ASC')->get();

        $propertyTypes = PropertyType::query()
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->orderBy('name', 'ASC')->get();

        $assemblies = Assembly::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('revenue-streams.edit', compact(
            'revenueStream',
            'zoneTypes',
            'propertyClasses',
            'propertyTypes',
            'assemblies'
        ));
    }

    public function editBOP(Request $request, RevenueStream $revenueStream)
    {
        $itemDescriptions = ItemDescription::query()
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->orderBy('name', 'ASC')
            ->get();

        $categories = Category::query()
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->orderBy('name', 'DESC')
            ->get();

        $assemblies = Assembly::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        $mainItems = MainItem::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        $subItems = SubItem::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('revenue-streams-bop.edit', compact(
            'revenueStream',
            'assemblies',
            'itemDescriptions',
            'categories',
            'mainItems',
            'subItems'
        ));
    }

    public function update(UpdateRevenueStreamRequest $request, RevenueStream $revenueStream)
    {
        $data = $request->validated();

        // Auto-assign assembly_code from authenticated user if not provided
        if (empty($data['assembly_code']) && ! empty($request->user()->assembly_code)) {
            $data['assembly_code'] = $request->user()->assembly_code;
        }

        if (
            RevenueStream::query()
                ->whereNot('id', $revenueStream->id)
                ->where('revenue_stream_type_id', $revenueStream->revenue_stream_type_id)
                ->where('is_rated', $data['is_rated'])
                ->where('zone_type_id', $data['zone_type_id'])
                ->where('assembly_code', $data['assembly_code'])
                ->when(isset($data['property_classes_id']),
                    fn ($query) => $query->where('property_classes_id', $data['property_classes_id']),
                    fn ($query) => $query->whereNull('property_classes_id')
                )
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'is_rated' => 'A revenue stream with these information already exists.',
            ]);
        }

        $revenueStream->update($data);

        return redirect()->route('revenue-streams.index')->with('status', 'Revenue stream updated successfully.');
    }

    public function updateBOP(UpdateRevenueStreamRequestBOP $request, RevenueStream $revenueStream)
    {
        $data = $request->validated();

        // Auto-assign assembly_code from authenticated user if not provided
        if (empty($data['assembly_code']) && ! empty($request->user()->assembly_code)) {
            $data['assembly_code'] = $request->user()->assembly_code;
        }

        if (
            RevenueStream::query()
                ->whereNot('id', $revenueStream->id)
                ->where('revenue_stream_type_id', $revenueStream->revenue_stream_type_id)
                ->where('description_id', $data['description_id'])
                ->where('category_id', $data['category_id'])
                ->where('assembly_code', $data['assembly_code'])
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'description_id' => 'A revenue stream with these information already exists.',
            ]);
        }

        $revenueStream->update($data);

        return redirect()->route('revenue-streams.indexBOP')->with('status', 'Revenue stream updated successfully.');
    }

    public function import(Request $request)
    {
        $assemblies = Assembly::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('revenue-streams.import', compact('assemblies'));
    }

    public function importData(ImportRevenueStreamRequest $request)
    {
        try {
            $data = $request->validated();
            $createdBy = $request->user()->id;

            // Get assembly_code from request or from user
            $assemblyCode = $data['assembly_code'] ?? $request->user()->assembly_code;

            $import = (new RevenueStreamImport($createdBy, $assemblyCode));
            $import->import($request->file('file'));

            return redirect()->route('revenue-streams.index')->with('status', 'Revenue stream data uploaded successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $exception) {
            throw ValidationException::withMessages([
                'file' => collect($exception->errors())->flatten()->toArray(),
            ]);
        }
    }

    public function downloadTemplate()
    {
        $filePath = public_path('assets/templates/revenue_streams_template.xlsx');

        if (! file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return Response::download($filePath, 'revenue_streams_template.xlsx');
    }

    public function importBOP(Request $request)
    {
        $assemblies = Assembly::orderBy('name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->where('regional_code', $request->user()->regional_code);
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('revenue-streams-bop.import', compact('assemblies'));
    }

    public function importBOPData(ImportRevenueStreamRequest $request)
    {
        try {
            $data = $request->validated();
            $createdBy = $request->user()->id;

            // Get assembly_code from request or from user
            $assemblyCode = $data['assembly_code'] ?? $request->user()->assembly_code;

            $import = (new BOPRevenueStreamImport($createdBy, $assemblyCode));
            $import->import($request->file('file'));

            return redirect()->route('revenue-streams.indexBOP')->with('status', 'Revenue stream data uploaded successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $exception) {
            throw ValidationException::withMessages([
                'file' => collect($exception->errors())->flatten()->toArray(),
            ]);
        }
    }

    public function downloadBOPTemplate()
    {
        $filePath = public_path('assets/templates/bop_revenue_streams_template.xlsx');

        if (! file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return Response::download($filePath, 'bop_revenue_streams_template.xlsx');
    }
}
