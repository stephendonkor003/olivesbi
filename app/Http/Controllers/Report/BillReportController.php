<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Assembly;
use App\Models\Bill;
use Illuminate\Http\Request;

class BillReportController extends Controller
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
                    $data = Bill::orderBy('created_at', 'DESC')
                        ->when(($request->filled('from_date') && $request->filled('to_date')), function ($query) use ($request) {
                            $query->whereBetween('created_at', [$request->from_date.' 00:00:00', $request->to_date.' 23:59:59']);
                        })
                        ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                            $query->whereHas('assembly', function ($q) use ($request) {
                                $q->where('regional_code', $request->user()->regional_code);
                            });
                        })
                        ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                            $query->where('assembly_code', $request->user()->assembly_code);
                        })
                        ->when($request->filled('assembly_code'), function ($query) use ($request) {
                            $query->where('assembly_code', $request->assembly_code);
                        })
                        ->when($request->filled('status'), function ($query) use ($request) {
                            if ($request->status === 'Property') {
                                $query->whereNotNull('property_id');
                            } elseif ($request->status === 'Business') {
                                $query->whereNotNull('business_id');
                            }
                        })
                        ->get();

                    return datatables()->of($data)
                        ->addIndexColumn()
                        ->editColumn('name', function (Bill $bill) {

                            if ($bill->property_id !== null) {
                                $firstname = $bill->property->customer->fname ?? '';
                                $lastname = $bill->property->customer->lname ?? '';
                            } else {
                                $firstname = $bill->business->customer->fname ?? '';
                                $lastname = $bill->business->customer->lname ?? '';
                            }
                            $name = $firstname.' '.$lastname;

                            return $name;
                        })
                        ->editColumn('bill_type', function (Bill $bill) {
                            $billType = '';

                            if ($bill->property_id !== null) {
                                $billType = 'Property Bill';
                            } else {
                                $billType = 'Business Bill';
                            }

                            return $billType;
                        })
                        ->editColumn('amount_due', function (Bill $bill) {
                            $amount_due = $bill->arrears + $bill->amount;

                            return number_format($amount_due, 2);
                        })
                        ->editColumn('assembly', function (Bill $bill) {
                            return $bill->assembly->name ?? 'N/A';
                        })
                        ->editColumn('division', function (Bill $bill) {
                            $division = '';

                            if ($bill->property_id !== null) {
                                $division = $bill->property->division->division_name ?? 'N/A';
                            } else {
                                $division = $bill->business->division->division_name ?? 'N/A';
                            }

                            return $division;
                        })
                        ->editColumn('block', function (Bill $bill) {
                            $block = '';

                            if ($bill->property_id !== null) {
                                $block = $bill->property->block->block_name ?? 'N/A';
                            } else {
                                $block = $bill->business->block->block_name ?? 'N/A';
                            }

                            return $block;
                        })
                        ->editColumn('zone', function (Bill $bill) {
                            $zone = '';

                            if ($bill->property_id !== null) {
                                $zone = $bill->property->zone->name ?? 'N/A';
                            } else {
                                $zone = $bill->business->zone->name ?? 'N/A';
                            }

                            return $zone;
                        })
                        ->editColumn('property_use', function (Bill $bill) {
                            $propertyUse = '';

                            if ($bill->property_id !== null) {
                                $propertyUse = $bill->property->propertyUse->name ?? 'N/A';
                            } else {
                                $propertyUse = $bill->business->propertyUse->name ?? 'N/A';
                            }

                            return $propertyUse;
                        })
                        ->editColumn('created_by', function (Bill $bill) {
                            return $bill->createdBy->name ?? '';
                        })
                        ->editColumn('created_at', function (Bill $bill) {
                            return $bill->created_at;
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

            return view('reports.bill-report', compact('assemblies'));
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }
}
