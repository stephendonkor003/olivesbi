<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Assembly;
use App\Models\Block;
use App\Models\TaskAssignment;
use App\Models\User;
use Illuminate\Http\Request;

class JobAllocationReportController extends Controller
{
    public function index(Request $request)
    {
        try {
            if (! auth()->user()->can('reports.view')) {
                abort(403, 'Unauthorized action.');
            }

            $assemblies = Assembly::orderBy('name', 'ASC')
                ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                    $query->where('regional_code', $request->user()->regional_code);
                })
                ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                    $query->where('assembly_code', $request->user()->assembly_code);
                })
                ->get();

            $agents = User::where('access_level', 'Assembly_Agent')
                ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                    $query->whereHas('assembly', function ($q) use ($request) {
                        $q->where('regional_code', $request->user()->regional_code);
                    });
                })
                ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                    $query->where('assembly_code', $request->user()->assembly_code);
                })
                ->where('status', 'Active')
                ->get();

            $supervisors = User::where('access_level', 'Assembly_Supervisor')
                ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                    $query->whereHas('assembly', function ($q) use ($request) {
                        $q->where('regional_code', $request->user()->regional_code);
                    });
                })
                ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                    $query->where('assembly_code', $request->user()->assembly_code);
                })
                ->where('status', 'Active')
                ->get();

            if (request()->ajax()) {

                if ($request->report_type == 1) {

                    $data = TaskAssignment::orderBy('created_at', 'DESC')
                        ->when(($request->filled('from_date') && $request->filled('to_date')), function ($query) use ($request) {
                            $query->whereBetween('created_at', [$request->from_date.' 00:00:00', $request->to_date.' 23:59:59']);
                        })
                        ->when($request->filled('supervisor'), function ($query) use ($request) {
                            $query->where('supervisor_id', $request->supervisor);
                        })
                        ->when($request->filled('agent'), function ($query) use ($request) {
                            $query->where('agent_id', $request->agent);
                        })
                        ->get();

                    return datatables()->of($data)
                        ->addIndexColumn()
                        ->editColumn('supervisor', function (TaskAssignment $taskAssignment) {
                            return $taskAssignment->supervisor->name ?? 'N/A';
                        })
                        ->editColumn('agent', function (TaskAssignment $taskAssignment) {
                            return $taskAssignment->agent->name ?? 'N/A';
                        })
                        ->editColumn('block', function (TaskAssignment $taskAssignment) {
                            $blockData = $taskAssignment->block_data;

                            if (is_string($blockData)) {
                                $blockData = json_decode($blockData, true);
                            }

                            if (empty($blockData)) {
                                return 'N/A';
                            }

                            $blockIds = collect($blockData)->pluck('block_id')->unique();

                            $blocks = Block::whereIn('id', $blockIds)->pluck('name', 'id');

                            $formatted = collect($blockData)->map(function ($item) use ($blocks) {
                                $blockName = $blocks[$item['block_id']] ?? 'Unknown Block';

                                return "{$blockName} - {$item['status']}";
                            });

                            return $formatted->implode(', ');
                        })
                        ->editColumn('assembly', function (TaskAssignment $taskAssignment) {
                            return $taskAssignment->assembly->name ?? 'N/A';
                        })
                        ->editColumn('assigned_by', function (TaskAssignment $taskAssignment) {
                            return $taskAssignment->createdBy->name ?? 'N/A';
                        })
                        ->editColumn('created_at', function (TaskAssignment $taskAssignment) {
                            return $taskAssignment->created_at;
                        })
                        ->make(true);
                } else {

                    $levelMapping = [
                        'cluster_level' => ['table' => 'clusters', 'column' => 'name', 'id' => 'cluster_id'],
                        'business_center_level' => ['table' => 'business_centers', 'column' => 'name', 'id' => 'business_center_id'],
                        'branch_level' => ['table' => 'branches', 'column' => 'name', 'id' => 'branch_id'],
                        'district_level' => ['table' => 'districts', 'column' => 'name', 'id' => 'district_id'],
                        'community_level' => ['table' => 'communities', 'column' => 'name', 'id' => 'community_id'],
                    ];

                    $selectedLevel = $request->level_type;
                    $level = $levelMapping[$selectedLevel] ?? null;

                    if ($level) {
                        try {
                            $data = DB::table('customers')
                                ->join($level['table'], 'customers.'.$level['id'], '=', $level['table'].'.id')
                                ->select(
                                    $level['table'].'.'.$level['column'].' as name',
                                    $level['table'].'.uuid as uuid',
                                    DB::raw('COUNT(*) as total_customer')
                                )
                                ->groupBy($level['table'].'.'.$level['column'], $level['table'].'.uuid')
                                ->get();

                            return datatables()->of($data)
                                ->addIndexColumn()
                                ->editColumn('name', function ($row) {
                                    return $row->name;
                                })
                                ->editColumn('link', function ($row) use ($request) {
                                    return '<a href="'.route('citizens.index', ['param' => $request->name, 'param_uuid' => $row->uuid]).'" class="btn btn-success btn-sm" target="_blank">View Details</a>';
                                })
                                ->rawColumns(['link'])
                                ->make(true);
                        } catch (\Exception $e) {
                            return response()->json(['error' => $e->getMessage()], 500);
                        }
                    } else {
                        return response()->json(['error' => 'Invalid level selected'], 400);
                    }
                }
            }

            return view('reports.job-allocation-report', compact('assemblies', 'supervisors', 'agents'));
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }
}
