<?php

namespace App\Http\Controllers\API\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Payment;
use App\Models\TaskAssignment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function agentPayment($id)
    {
        $agent = User::where('id', $id)
            ->where('access_level', 'Assembly_Agent')
            ->first();

        if (empty($agent)) {
            return response()->json([
                'message' => 'Agent not found, enter agent account id, or the user is not an agent',
            ], 422);
        }

        $payments = Payment::orderBy('created_at', 'DESC')
            ->with(['assembly', 'bill'])
            ->where('created_by', $agent->id)
            ->get()
            ->map(function ($payment) {
                if (is_null($payment->bill)) {
                    $payment->bill_type = 'No Bill Type';
                } elseif (is_null($payment->bill->property_id)) {
                    $payment->bill_type = 'Business Bill';
                } elseif (is_null($payment->bill->business_id)) {
                    $payment->bill_type = 'Property Bill';
                } else {
                    $payment->bill_type = 'Unknown';
                }

                return $payment;
            });

        $dailyTotal = Payment::where('created_by', $agent->id)
            ->whereDate('created_at', now()->toDateString())
            ->where(function ($query) {
                $query->where('payment_mode', 'momo')
                    ->where('transaction_status', 'Success')
                    ->orWhere('payment_mode', '<>', 'momo');
            })
            ->sum('amount');

        $weeklyTotal = Payment::where('created_by', $agent->id)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->where(function ($query) {
                $query->where('payment_mode', 'momo')
                    ->where('transaction_status', 'Success')
                    ->orWhere('payment_mode', '<>', 'momo');
            })
            ->sum('amount');

        $monthlyTotal = Payment::where('created_by', $agent->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where(function ($query) {
                $query->where('payment_mode', 'momo')
                    ->where('transaction_status', 'Success')
                    ->orWhere('payment_mode', '<>', 'momo');
            })
            ->sum('amount');

        $yearlyTotal = Payment::where('created_by', $agent->id)
            ->whereYear('created_at', now()->year)
            ->where(function ($query) {
                $query->where('payment_mode', 'momo')
                    ->where('transaction_status', 'Success')
                    ->orWhere('payment_mode', '<>', 'momo');
            })
            ->sum('amount');

        $totals = [
            'daily_payments' => isset($dailyTotal) ? $dailyTotal : 0,
            'weekly_payments' => isset($weeklyTotal) ? $weeklyTotal : 0,
            'monthly_payments' => isset($monthlyTotal) ? $monthlyTotal : 0,
            'year_payments' => isset($yearlyTotal) ? $yearlyTotal : 0,
        ];

        return response()->json([
            'message' => 'Get agent payment collections',
            'data' => $payments,
            'totals' => $totals,
        ]);
    }

    public function agentTask(Request $request, $id)
    {
        $agent = User::where('id', $id)
            ->where('access_level', 'Assembly_Agent')
            ->first();

        if (empty($agent)) {
            return response()->json([
                'message' => 'Agent not found or the user is not an agent, check and try again',
            ], 422);
        }

        $taskAssignments = TaskAssignment::orderBy('created_at', 'DESC')
            ->with(['supervisor', 'agent', 'assembly'])
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->when($request->user()->access_level == 'Assembly_Supervisor', function ($query) use ($request) {
                $query->where('supervisor_id', $request->user()->id);
            })
            ->when($request->user()->access_level == 'Assembly_Agent', function ($query) use ($request) {
                $query->where('agent_id', $request->user()->id);
            })
            ->where('agent_id', $agent->id)
            ->get()
            ->map(function ($assignment) {
                $assignment->block_data = collect($assignment->block_data)->map(function ($block) {
                    $blockModel = Block::find($block['block_id']);
                    $block['block_name'] = $blockModel ? $blockModel->block_name : 'Unknown';

                    return $block;
                })->toArray();

                $assignment->block_count = count($assignment->block_data);

                return $assignment;
            });

        return response()->json([
            'message' => 'Get all agent task assignments',
            'data' => $taskAssignments,
        ]);
    }

    public function dashboardData($id)
    {
        $now = Carbon::now();
        $startOfYear = $now->copy()->firstOfYear();

        $agent = User::where('id', $id)
            ->where('access_level', 'Assembly_Agent')
            ->first();

        if (empty($agent)) {
            return response()->json([
                'message' => 'Agent not found or the user is not an agent, check and try again',
            ], 422);
        }

        $totalProperties = DB::table('properties')->where('created_by', $agent->id)->count();

        $totalBillsDistributed = DB::table('bills')
            ->where('issue_bill', 'Yes')
            ->where('issued_by', $agent->id)
            ->count();

        $totalBillsAmount = DB::table('bills')
            ->join('properties', 'bills.property_id', '=', 'properties.id')
            ->where('properties.created_by', $agent->id)
            ->sum('bills.amount');

        $paymentsQuery = DB::table('payments')
            ->where('transaction_status', 'Success')
            ->where('created_by', $agent->id);

        $totalPaymentsCount = $paymentsQuery->count();
        $totalAmountCollected = $paymentsQuery->sum('amount');

        $monthlyPayments = DB::table('payments')
            ->whereBetween('created_at', [$startOfYear, $now])
            ->where('transaction_status', 'Success')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%b \'%y') as month"),
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderByRaw('STR_TO_DATE(month, \'%b \\\'%y\')')
            ->where('created_by', $agent->id)
            ->get();

        $propertyTypes = DB::table('properties')
            ->join('revenue_streams', 'properties.prop_rev_id', '=', 'revenue_streams.id')
            ->join('zones', 'revenue_streams.zone_type_id', '=', 'zones.id')
            ->select('zones.name as type', DB::raw('COUNT(*) as count'))
            ->where('properties.created_by', $agent->id)
            ->groupBy('zones.name')
            ->get();

        $paymentsByType = DB::table('payments')
            ->join('bills', 'payments.bills_id', '=', 'bills.bills_id')
            ->join('properties', 'bills.property_id', '=', 'properties.id')
            ->join('revenue_streams', 'properties.prop_rev_id', '=', 'revenue_streams.id')
            ->join('zones', 'revenue_streams.zone_type_id', '=', 'zones.id')
            ->where('transaction_status', 'Success')
            ->select('zones.name as type', DB::raw('SUM(payments.amount) as total'))
            ->where('payments.created_by', $agent->id)
            ->groupBy('zones.name')
            ->get();

        return response()->json([
            'message' => 'Get agent dashboard data',
            'data' => [
                'summary' => [
                    'total_properties' => $totalProperties,
                    'total_payments' => $totalPaymentsCount,
                    'total_bills_distributed' => $totalBillsDistributed,
                    'total_bills_amount' => (float) $totalBillsAmount,
                    'total_amount_collected' => (float) $totalAmountCollected,
                ],
                'monthly_payments' => $monthlyPayments,
                'property_types' => $propertyTypes,
                'payments_by_type' => $paymentsByType,
            ],
        ]);
    }

    public function customerDashboardData($id)
    {
        $now = Carbon::now();
        $startOfYear = $now->copy()->firstOfYear();

        $customer = User::where('id', $id)
            ->where('access_level', 'customer')
            ->first();

        if (empty($customer)) {
            return response()->json([
                'message' => 'Rate payer not found or the user is not a rate payer, check and try again',
            ], 422);
        }

        $totalProperties = DB::table('properties')->where('owner_id', $customer->erms_id)->count();

        // Get latest bills with their total payments
        $currentBillData = DB::table('bills')
            ->join('properties', 'bills.property_id', '=', 'properties.id')
            ->leftJoin('payments', function ($join) {
                $join->on('payments.bills_id', '=', 'bills.bills_id')
                    ->where(function ($q) {
                        $q->where('payments.payment_mode', '!=', 'momo')
                            ->orWhere(function ($q2) {
                                $q2->where('payments.payment_mode', 'momo')
                                    ->where('payments.transaction_status', 'Success');
                            });
                    });
            })
            ->where('properties.owner_id', $customer->erms_id)
            ->select(
                'bills.*',
                'properties.id as property_id',
                DB::raw('COALESCE(SUM(payments.amount), 0) as total_paid')
            )
            ->whereIn('bills.id', function ($query) use ($customer) {
                $query->select(DB::raw('MAX(b1.id)'))
                    ->from('bills as b1')
                    ->join('properties as p1', 'b1.property_id', '=', 'p1.id')
                    ->where('p1.owner_id', $customer->erms_id)
                    ->groupBy('b1.property_id');
            })
            ->groupBy('bills.id', 'bills.bills_id', 'bills.property_id', 'bills.business_id',
                'bills.amount', 'bills.arrears', 'bills.bills_year', 'bills.issue_bill',
                'bills.issued_by', 'bills.created_at', 'bills.updated_at', 'properties.id')
            ->get();

        // Calculate totals from the bill data
        $currentBill = $currentBillData->sum('amount');
        $billArrears = $currentBillData->sum('arrears');
        $totalPaidOnCurrentBills = $currentBillData->sum('total_paid');

        // Calculate actual arrears: (current bill + arrears) - payments on those bills
        $currentBillArrears = ($currentBill + $billArrears) - $totalPaidOnCurrentBills;

        // Get total payments count and amount for all bills
        $paymentsQuery = DB::table('payments')
            ->join('bills', 'payments.bills_id', '=', 'bills.bills_id')
            ->join('properties', 'bills.property_id', '=', 'properties.id')
            ->where(function ($q) {
                $q->where('payments.payment_mode', '!=', 'momo')
                    ->orWhere(function ($q2) {
                        $q2->where('payments.payment_mode', 'momo')
                            ->where('payments.transaction_status', 'Success');
                    });
            })
            ->where('properties.owner_id', $customer->erms_id);

        $totalAmountPaid = $paymentsQuery->sum('payments.amount');

        $monthlyBills = DB::table('bills')
            ->join('properties', 'bills.property_id', '=', 'properties.id')
            ->where('properties.owner_id', $customer->erms_id)
            ->whereBetween('bills.created_at', [$startOfYear, $now])
            ->select(
                DB::raw("DATE_FORMAT(bills.created_at, '%b \'%y') as month"),
                DB::raw('SUM(bills.amount) as total_bill')
            )
            ->groupBy('month')
            ->pluck('total_bill', 'month');

        $monthlyPayments = DB::table('payments')
            ->join('bills', 'payments.bills_id', '=', 'bills.bills_id')
            ->join('properties', 'bills.property_id', '=', 'properties.id')
            ->where('properties.owner_id', $customer->erms_id)
            ->whereBetween('payments.created_at', [$startOfYear, $now])
            ->where('transaction_status', 'Success')
            ->select(
                DB::raw("DATE_FORMAT(payments.created_at, '%b \'%y') as month"),
                DB::raw('SUM(payments.amount) as total_payment')
            )
            ->groupBy('month')
            ->pluck('total_payment', 'month');

        $allMonths = collect($monthlyBills->keys())
            ->merge($monthlyPayments->keys())
            ->unique()
            ->sortBy(function ($month) {
                return Carbon::createFromFormat("M 'y", $month)->startOfMonth()->timestamp;
            });

        $monthlyData = [];
        foreach ($allMonths as $month) {
            $bill = round($monthlyBills[$month] ?? 0, 2);
            $payment = round($monthlyPayments[$month] ?? 0, 2);
            $balance = round($bill - $payment, 2);

            $monthlyData[] = [
                'month' => $month,
                'bill' => $bill,
                'payments' => $payment,
                'balance' => $balance,
            ];
        }

        return response()->json([
            'message' => 'Get customer dashboard data',
            'data' => [
                'summary' => [
                    'total_properties' => $totalProperties,
                    'total_payments' => (float) $totalAmountPaid,
                    'current_bill' => (float) $currentBill,
                    'current_arrears' => (float) $currentBillArrears,
                    'current_bill_data' => $currentBillData,
                ],
                'monthly_data' => $monthlyData,
            ],
        ]);
    }
}
