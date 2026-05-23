<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Assembly;
use App\Models\Payment;
use Illuminate\Http\Request;

class RevenueCollectionEfficiencyReportController extends Controller
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

                    $data = Payment::selectRaw("
                            citizens.account_number AS taxpayer_id,
                            CONCAT(citizens.first_name, ' ', citizens.last_name) AS taxpayer_name,
                            properties.property_number AS property_id,
                            assemblies.name AS assembly_name,
                            SUM(bills.amount) AS total_bill_amount,
                            SUM(payments.amount) AS total_amount_paid,
                            (SUM(bills.amount) - SUM(payments.amount)) AS outstanding_amount,
                            DATEDIFF(MAX(payments.created_at), MAX(bills.created_at)) AS days_to_payment
                        ")
                        ->join('bills', 'payments.bills_id', '=', 'bills.bills_id')
                        ->join('properties', 'bills.property_id', '=', 'properties.id')
                        ->join('citizens', 'properties.customer_name', '=', 'citizens.id')
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
                        ->join('assemblies', 'payments.assembly_code', '=', 'assemblies.assembly_code')
                        ->groupBy('citizens.account_number', 'citizens.first_name', 'citizens.last_name', 'properties.property_number', 'assemblies.name')
                        ->orderBy('citizens.first_name')
                        ->get();

                    return datatables()->of($data)
                        ->addIndexColumn()
                        ->editColumn('frequently_used', function ($row) {
                            return isset($row->frequently_used) ? ucfirst($row->frequently_used) : 'N/A';
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

            return view('reports.revenue-collection-efficiency-report', compact('assemblies'));
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }
}
