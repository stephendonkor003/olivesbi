<?php

namespace App\Http\Controllers\Mmda;

use App\Http\Controllers\Controller;
use App\Http\Requests\Import\ImportMMDARequest;
use App\Http\Requests\MMDA\CreateMMDARequest;
use App\Http\Requests\MMDA\UpdateMMDARequest;
use App\Imports\MMDA\MMDAImport;
use App\Models\GhanaRegion;
use App\Models\Mmda;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MmdaController extends Controller
{
    public function index(Request $request)
    {
        $mmdas = Mmda::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        return view('mmdas.index', compact('mmdas'));
    }

    public function create()
    {
        $regions = GhanaRegion::orderBy('name', 'ASC')->get();

        return view('mmdas.create', compact('regions'));
    }

    public function store(CreateMMDARequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        Mmda::create($data);

        return redirect()->route('mmdas.index')->with('status', 'MMDA created successfully!');
    }

    public function show(Mmda $mmda)
    {
        return view('mmdas.show', compact('mmda'));
    }

    public function edit(Mmda $mmda)
    {
        $regions = GhanaRegion::orderBy('name', 'ASC')->get();

        return view('mmdas.edit', compact('mmda', 'regions'));
    }

    public function update(UpdateMMDARequest $request, Mmda $mmda)
    {
        $mmda->update($request->validated());

        return redirect()->route('mmdas.index')->with('status', 'MMDA updated successfully!');
    }

    public function import()
    {
        return view('mmdas.import');
    }

    public function importData(ImportMMDARequest $request)
    {
        try {
            $data = $request->validated();
            $createdBy = $request->user()->id;

            $import = (new MMDAImport($createdBy));
            $import->import($request->file('file'));

            return redirect()->route('mmdas.index')->with('status', 'MMDAs data uploaded successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $exception) {
            throw ValidationException::withMessages([
                'file' => collect($exception->errors())->flatten()->toArray(),
            ]);
        }
    }
}
