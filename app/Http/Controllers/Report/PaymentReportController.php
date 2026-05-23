<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Assembly;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentReportController extends Controller
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
                    $data = Payment::orderBy('created_at', 'DESC')
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
                            $query->where('transaction_status', $request->status);
                        })
                        ->get();

                    return datatables()->of($data)
                        ->addIndexColumn()
                        ->editColumn('name', function (Payment $payment) {

                            $name = '';
                            if ($payment->bill) {
                                if ($payment->bill->property && $payment->bill->property->customer) {
                                    $firstname = $payment->bill->property->customer->fname ?? '';
                                    $lastname = $payment->bill->property->customer->lname ?? '';
                                    $name = trim($firstname.' '.$lastname);
                                } elseif ($payment->bill->business && $payment->bill->business->customer) {
                                    $firstname = $payment->bill->business->customer->fname ?? '';
                                    $lastname = $payment->bill->business->customer->lname ?? '';
                                    $name = trim($firstname.' '.$lastname);
                                }
                            }

                            return $name ?: 'N/A';
                        })
                        ->editColumn('amount', function (Payment $payment) {
                            return number_format($payment->amount, 2);
                        })
                        ->editColumn('assembly', function (Payment $payment) {
                            return $payment->assembly->name ?? 'N/A';
                        })
                        ->editColumn('division', function (Payment $payment) {
                            $division = 'N/A';

                            // if ($payment->bill->property_id !== null) {
                            //     $division = $payment->bill->property->division->division_name ?? 'N/A';
                            // } else {
                            //     $division = $payment->bill->business->division->division_name ?? 'N/A';
                            // }

                            return $division;
                        })
                        ->editColumn('block', function (Payment $payment) {
                            $block = 'N/A';

                            // if ($payment->bill->property_id !== null) {
                            //     $block = $payment->bill->property->block->block_name ?? 'N/A';
                            // } else {
                            //     $block = $payment->bill->business->block->block_name ?? 'N/A';
                            // }

                            return $block;
                        })
                        ->editColumn('zone', function (Payment $payment) {
                            $zone = 'N/A';

                            // if ($payment->bill->property_id !== null) {
                            //     $zone = $payment->bill->property->zone->name ?? 'N/A';
                            // } else {
                            //     $zone = $payment->bill->business->zone->name ?? 'N/A';
                            // }

                            return $zone;
                        })
                        ->editColumn('property_use', function (Payment $payment) {
                            $propertyUse = 'N/A';

                            // if ($payment->bill->property_id !== null) {
                            //     $propertyUse = $payment->bill->property->propertyUse->name ?? 'N/A';
                            // } else {
                            //     $propertyUse = $payment->bill->business->propertyUse->name ?? 'N/A';
                            // }

                            return $propertyUse;
                        })
                        ->editColumn('transaction_id', function (Payment $payment) {
                            return ! empty($payment->transaction_id) ? $payment->transaction_id : 'N/A';
                        })
                        ->editColumn('phone', function (Payment $payment) {
                            return $payment->phone ?? 'N/A';
                        })
                        ->editColumn('network', function (Payment $payment) {
                            return $payment->network ?? 'N/A';
                        })
                        ->editColumn('created_by', function (Payment $payment) {
                            return $payment->createdBy->name ?? '';
                        })
                        ->editColumn('created_at', function (Payment $payment) {
                            return $payment->created_at;
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

            return view('reports.payment-report', compact('assemblies'));
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }
}
