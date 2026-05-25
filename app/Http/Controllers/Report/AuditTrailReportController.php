<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Assembly;
use App\Models\AuditTrail;
use Illuminate\Http\Request;

class AuditTrailReportController extends Controller
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

            if (request()->ajax()) {

                if ($request->report_type == 1) {

                    $data = AuditTrail::orderBy('created_at', 'DESC')
                        ->when(($request->filled('from_date') && $request->filled('to_date')), function ($query) use ($request) {
                            $query->whereBetween('created_at', [$request->from_date.' 00:00:00', $request->to_date.' 23:59:59']);
                        })
                        ->when($request->filled('status'), function ($query) use ($request) {
                            $query->where('remarks', $request->status);
                        })
                        ->get();

                    return datatables()->of($data)
                        ->addIndexColumn()
                        ->editColumn('user_name', function (AuditTrail $auditTrail) {
                            return $auditTrail->user->name ?? '';
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

            return view('reports.audit-trail-report', compact('assemblies'));
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }
}
