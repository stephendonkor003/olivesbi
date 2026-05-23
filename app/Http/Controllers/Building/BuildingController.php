<?php

namespace App\Http\Controllers\Building;

use App\Http\Controllers\Controller;
use App\Imports\Block\BlockImport;
use App\Imports\Building\BuildingImport;
use App\Models\Building;
use App\Models\PolygonBlock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BuildingController extends Controller
{
    public function index()
    {
        $buildings = Building::orderBy('created_at', 'DESC')->get();

        return view('buildings.index', compact('buildings'));
    }

    public function create()
    {
        return view('buildings.create');
    }

    public function store(Request $request)
    {
        try {

            $request->validate([
                'file' => 'required|mimes:xlsx,csv',
            ]);

            $createdBy = $request->user()->id;

            $import = (new BuildingImport($createdBy));
            $import->import($request->file('file'));

            return redirect()->route('buildings.index')->with('status', 'Blocks building data uploaded successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $exception) {
            throw ValidationException::withMessages([
                'file' => collect($exception->errors())->flatten()->toArray(),
            ]);
        }
    }

    public function importBlock()
    {
        return view('buildings.import');
    }

    public function importBlockStore(Request $request)
    {
        try {

            $request->validate([
                'file' => 'required|mimes:xlsx,csv',
            ]);

            $createdBy = $request->user()->id;

            $import = (new BlockImport($createdBy));
            $import->import($request->file('file'));

            return redirect()->route('buildings.index')->with('status', 'Assembly blocks data uploaded successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $exception) {
            throw ValidationException::withMessages([
                'file' => collect($exception->errors())->flatten()->toArray(),
            ]);
        }
    }

    public function polygons()
    {
        // Blocks
        $blocks = PolygonBlock::select('id', 'name', 'boundary', 'block_number')->get()->map(function ($block) {
            return [
                'id' => $block->id,
                'name' => $block->name,
                'block_number' => $block->block_number,
                'boundary' => json_decode($block->boundary),
            ];
        });

        // Buildings
        $buildings = Building::select('id', 'name', 'boundary', 'block_id')->get()->map(function ($building) {
            return [
                'id' => $building->id,
                'name' => $building->name,
                'block_id' => $building->block_id,
                'boundary' => json_decode($building->boundary),
            ];
        });

        return response()->json([
            'blocks' => $blocks,
            'buildings' => $buildings,
        ]);
    }

    public function map(Request $request)
    {
        $agents = User::orderBy('name', 'ASC')
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->where('access_level', 'Assembly_Agent')
            ->where('status', 'Active')
            ->get();

        return view('buildings.map', compact('agents'));
    }

    public function allocations()
    {
        return response()->json([
            'status' => 'ok',
            'code' => '00',
            'message' => 'Data Fetched Successfully',
            'data' => [
                'jobName' => 'Hello there',
                'block' => '1234',
                'assignedTo' => 'Samuel Otu',
            ],
        ]);
    }
}
