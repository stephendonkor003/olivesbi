<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Assembly;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationAnalysisReportController extends Controller
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

                    $data = DB::table('payments as pym')
                        ->rightJoin('bills as b', 'pym.bills_id', '=', 'b.bills_id')
                        ->rightJoin('properties as p', 'b.property_id', '=', 'p.id')
                        ->rightJoin('assemblies as a', 'p.assembly_code', '=', 'a.assembly_code')
                        ->rightJoin('ghana_regions as gr', 'a.regional_code', '=', 'gr.regional_code')
                        ->select(
                            'gr.name as region',
                            DB::raw('COUNT(DISTINCT CASE WHEN p.owner_id IS NOT NULL THEN p.owner_id END) AS number_of_taxpayers'),
                            DB::raw('COUNT(p.id) AS number_of_properties'),
                            DB::raw('SUM(b.amount) AS total_billed_amount'),
                            DB::raw('SUM(CASE 
                            WHEN pym.payment_mode = "momo" AND pym.transaction_status = "Success" THEN pym.amount 
                            WHEN pym.payment_mode != "momo" THEN pym.amount 
                            ELSE 0 
                        END) AS total_collected_amount'),
                            DB::raw('SUM(b.amount) - 
                            SUM(CASE 
                                WHEN pym.payment_mode = "momo" AND pym.transaction_status = "Success" THEN pym.amount 
                                WHEN pym.payment_mode != "momo" THEN pym.amount 
                                ELSE 0 
                            END) AS outstanding_amount'),
                            DB::raw('CASE WHEN SUM(b.amount) > 0 THEN ROUND((SUM(CASE 
                            WHEN pym.payment_mode = "momo" AND pym.transaction_status = "Success" THEN pym.amount 
                            WHEN pym.payment_mode != "momo" THEN pym.amount 
                            ELSE 0 
                        END) / SUM(b.amount)) * 100, 2) ELSE 0 END AS collection_rate'),
                            DB::raw('CASE WHEN COUNT(p.id) > 0 THEN ROUND(SUM(b.amount) / COUNT(p.id), 2) ELSE 0 END AS average_tax_per_property')
                        )
                        ->when($request->filled('from_date') && $request->filled('to_date'), function ($query) use ($request) {
                            $query->whereBetween('payments.created_at', [
                                $request->from_date.' 00:00:00',
                                $request->to_date.' 23:59:59',
                            ]);
                        })
                        ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                            $query->whereHas('assembly', function ($q) use ($request) {
                                $q->where('regional_code', $request->user()->regional_code);
                            });
                        })
                        ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                            $query->where('payments.assembly_code', $request->user()->assembly_code);
                        })
                        ->when($request->filled('assembly_code'), function ($query) use ($request) {
                            $query->where('payments.assembly_code', $request->assembly_code);
                        })
                        ->when($request->filled('status'), function ($query) use ($request) {
                            $query->where('payments.transaction_status', $request->status);
                        })
                        ->groupBy('gr.name')
                        ->orderBy('gr.name')
                        ->get();

                    return datatables()->of($data)
                        ->addIndexColumn()
                        ->editColumn('total_billed_amount', function ($row) {
                            return number_format($row->total_billed_amount, 2) ?? 0;
                        })
                        ->editColumn('total_collected_amount', function ($row) {
                            return number_format($row->total_collected_amount, 2) ?? 0;
                        })
                        ->editColumn('outstanding_amount', function ($row) {
                            return number_format($row->outstanding_amount, 2) ?? 0;
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
                                    $level['table'].'.id as id',
                                    DB::raw('COUNT(*) as total_customer')
                                )
                                ->groupBy($level['table'].'.'.$level['column'])
                                ->get();

                            return datatables()->of($data)
                                ->addIndexColumn()
                                ->editColumn('name', function ($row) {
                                    return $row->name;
                                })
                                ->editColumn('link', function ($row) use ($request) {
                                    return '<a href="'.route('customers.index', ['param' => $request->name, 'param_id' => $row->id]).'" class="btn btn-success btn-sm" target="_blank">View Details</a>';
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

            return view('reports.location-analysis-report', compact('assemblies'));
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }
}
