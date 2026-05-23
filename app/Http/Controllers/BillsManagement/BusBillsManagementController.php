<?php

namespace App\Http\Controllers\BillsManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusBill\CreateBlockBillRequest;
use App\Http\Requests\BusBill\CreateBulkBillRequest;
use App\Http\Requests\BusBill\CreateDivisionBillRequest;
use App\Http\Requests\BusBill\CreateSingleBillRequest;
use App\Jobs\Bill\GenerateBusinessBillsJob;
use App\Models\Assembly;
use App\Models\Bill;
use App\Models\BillAdjustment;
use App\Models\Block;
use App\Models\Business;
use App\Models\Division;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\RevenueStream;
use App\Notifications\BillGenerationCompletedNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class BusBillsManagementController extends Controller
{
    public function index(Request $request)
    {
        if (! auth()->user()->can('bills.view')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Bill::with([
            'business.customer',
            'business.polygon.block.division',
        ])
            ->orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->whereNotNull('business_id')
            ->when($request->filled(['from_date', 'end_date']), function ($query) use ($request) {
                if ($request->date('from_date') && $request->date('end_date')) {
                    $query->whereBetween('billing_date', [$request->from_date, $request->end_date]);
                }
            })
            ->when($request->filled('arrears'), function ($query) use ($request) {
                if ($request->boolean('arrears')) {
                    $query->where('arrears', '>', 0);
                } else {
                    $query->where('arrears', 0);
                }
            });

        if ($request->expectsJson()) {
            return datatables()->eloquent($query)
                ->addIndexColumn()
                ->addColumn('action', function (Bill $bill) {
                    return '
                        <div class="dropdown">
                            <div class="btn-link" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M11 12C11 12.5523 11.4477 13 12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12Z"
                                        stroke="#737B8B" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                    </path>
                                    <path
                                        d="M18 12C18 12.5523 18.4477 13 19 13C19.5523 13 20 12.5523 20 12C20 11.4477 19.5523 11 19 11C18.4477 11 18 11.4477 18 12Z"
                                        stroke="#737B8B" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                    </path>
                                    <path
                                        d="M4 12C4 12.5523 4.44772 13 5 13C5.55228 13 6 12.5523 6 12C6 11.4477 5.55228 11 5 11C4.44772 11 4 11.4477 4 12Z"
                                        stroke="#737B8B" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                    </path>
                                </svg>
                            </div>
                            <div class="dropdown-menu dropdown-menu-end">
                                <div class="py-2">
                                    <a class="dropdown-item"
                                        href="'.route('bills.show', $bill).'"
                                        target="_blank">Print Bill
                                    </a>
                                    <a class="dropdown-item"
                                        href="'.route('payments.create', $bill).'">Make
                                        Payment
                                    </a>
                                </div>
                            </div>
                        </div>
                    ';
                })
                ->addColumn('name', function (Bill $bill) {
                    if ($bill->property_id !== null) {
                        $firstname = $bill->property->customer->fname ?? '';
                        $lastname = $bill->property->customer->lname ?? '';
                    } else {
                        $firstname = $bill->business->customer->fname ?? '';
                        $lastname = $bill->business->customer->lname ?? '';
                    }

                    return $firstname.' '.$lastname;
                })
                ->addColumn('phone_number', function (Bill $bill) {
                    if ($bill->property_id !== null) {
                        $phoneNumber = $bill->property->customer->phone_no_primary ?? 'N/A';
                    } else {
                        $phoneNumber = $bill->business->customer->phone_no_primary ?? 'N/A';
                    }

                    return $phoneNumber;
                })
                ->addColumn('division', function (Bill $bill) {
                    if ($bill->property_id !== null) {
                        $division = $bill->property->polygon->block->division->name ?? 'N/A';
                    } else {
                        $division = $bill->business->polygon->block->division->name ?? 'N/A';
                    }

                    return $division;
                })
                ->addColumn('block', function (Bill $bill) {
                    if ($bill->property_id !== null) {
                        $block = $bill->property->polygon->block->name ?? 'N/A';
                    } else {
                        $block = $bill->business->polygon->block->name ?? 'N/A';
                    }

                    return $block;
                })
                ->addColumn('bill_type', function (Bill $bill) {
                    if ($bill->property_id !== null) {
                        $billType = 'Property Bill';
                    } else {
                        $billType = 'Business Bill';
                    }

                    return $billType;
                })
                ->addColumn('amount_due', function (Bill $bill) {
                    return $bill->arrears + $bill->amount - $bill->discount_amount;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $invoiceExpectedAmount = Invoice::sum('amount_due');
        $invoicesPaid = Invoice::where('status', 'paid')->sum('amount_paid');
        $invoicesUnPaid = Invoice::where('status', null)->sum('amount_due');
        $invoicesArrears = Invoice::where('status', null)->sum('arrears');
        $currentAmount = Invoice::where('status', null)->sum('current_amount');
        $varianceofbill = $invoicesArrears - $currentAmount;
        $totalArrears = $query->clone()->sum('arrears');
        $totalAmount = $query->clone()->sum('amount');
        $totalDiscount = $query->clone()->sum('discount_amount');

        $totalExpectedPayments = $query->clone()
            ->whereYear('created_at', Carbon::now()->year)
            ->select(DB::raw('SUM(amount + arrears) as expectedPayment'))
            ->value('expectedPayment');

        $totalPayments = Payment::query()
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->whereHas('bill', function ($query) {
                $query->whereNotNull('business_id');
            })
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        $totalUnpaidBills = 0;
        if (isset($totalExpectedPayments) && isset($totalPayments)) {
            $totalUnpaidBills = $totalExpectedPayments - $totalPayments;
        }

        $paymentPercentage = $totalExpectedPayments > 0 ? ($totalPayments / $totalExpectedPayments) * 100 : 0;

        $totalBill = $query->clone()
            ->whereNull('property_id')
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        $totalBillArrears = $query->clone()
            ->whereNull('property_id')
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('arrears');

        $totalBillVariance = 0;
        if (isset($totalBill) && isset($totalBillArrears)) {
            $totalBillVariance = $totalBillArrears - $totalBill;
        }

        $total = [
            'totalArrears' => isset($totalArrears) ? number_format($totalArrears, 2) : 0,
            'totalAmount' => isset($totalAmount) ? number_format($totalAmount, 2) : 0,
            'totalDiscount' => isset($totalDiscount) ? number_format($totalDiscount, 2) : 0,
            'totalDue' => isset($totalArrears) && isset($totalAmount) ? number_format($totalArrears + $totalAmount, 2) : 0,
            'totalExpectedPayments' => isset($totalExpectedPayments) ? number_format($totalExpectedPayments, 2) : 0,
            'totalPayments' => isset($totalPayments) ? number_format($totalPayments, 2) : 0,
            'totalUnpaidBills' => isset($totalUnpaidBills) ? number_format($totalUnpaidBills, 2) : 0,
            'paymentPercentage' => isset($paymentPercentage) ? round($paymentPercentage, 2) : 0,
            'totalBill' => isset($totalBill) ? number_format($totalBill, 2) : 0,
            'totalBillArrears' => isset($totalBillArrears) ? number_format($totalBillArrears, 2) : 0,
            'totalBillVariance' => isset($totalBillVariance) ? number_format($totalBillVariance, 2) : 0,
        ];

        return view('bills-business.index', compact(
            'request',
            'invoiceExpectedAmount',
            'invoicesPaid',
            'invoicesUnPaid',
            'invoicesArrears',
            'currentAmount',
            'varianceofbill',
            'total'
        ));
    }

    public function create(Request $request)
    {
        if (! auth()->user()->can('bills.create')) {
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

        return view('bills-business.bulk', compact('assemblies'));
    }

    public function store(CreateBulkBillRequest $request)
    {
        try {
            $assemblyCode = $request->assembly_code;
            $billYear = $request->bills_year;
            $userId = $request->user()->id;

            $businessIds = Business::where('assembly_code', $assemblyCode)
                ->whereNotIn('id', function ($query) use ($billYear) {
                    $query->select('business_id')
                        ->from('bills')
                        ->whereNotNull('business_id')
                        ->where('bills_year', $billYear);
                })
                ->pluck('id')
                ->toArray();

            if (empty($businessIds)) {
                return redirect()->route('bills.bus.index')->with('error', 'No businesses found for bill generation.');
            }

            $chunks = array_chunk($businessIds, 50);
            $jobs = [];

            foreach ($chunks as $chunk) {
                $jobs[] = new GenerateBusinessBillsJob($assemblyCode, $billYear, $userId, $chunk);
            }

            $batch = Bus::batch($jobs)
                ->name("Business Bills Generation - {$assemblyCode} - {$billYear}")
                ->then(function ($batch) use ($userId) {
                    $user = \App\Models\User::find($userId);
                    if ($user) {
                        $user->notify(new BillGenerationCompletedNotification(
                            $batch->name,
                            $batch->totalJobs,
                            $batch->processedJobs(),
                            $batch->failedJobs,
                            'business'
                        ));
                    }
                })
                ->catch(function () {
                    \Log::error('Business bill generation batch failed');
                })
                ->dispatch();

            return redirect()->route('bills.bus.index')->with('status', "Bill generation has been queued ({$batch->totalJobs} jobs). You will be notified when complete.");

        } catch (\Exception $e) {
            \Log::error('Bulk business bill generation error: '.$e->getMessage(), [
                'assembly_code' => $request->assembly_code ?? null,
                'bill_year' => $request->bills_year ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('bills.bus.create')->with('error', 'An error occurred while queueing bill generation. Please try again.');
        }
    }

    public function singleCreate(Request $request)
    {
        if (! auth()->user()->can('bills.create')) {
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

        return view('bills-business.single', compact('assemblies'));
    }

    public function singleStore(CreateSingleBillRequest $request)
    {
        try {
            // Handle both JSON and form data
            $data = $request->isJson() ? $request->json()->all() : $request->all();

            $assemblyCode = $data['assembly_code'];
            $billYear = $data['bills_year'];
            $businessIds = $data['business_id'];
            $userId = $request->user()->id;
            $jobs[] = new GenerateBusinessBillsJob($assemblyCode, $billYear, $userId, $businessIds);

            Bus::batch($jobs)
                ->name("Business Bills Generation - {$assemblyCode} - {$billYear}")
                ->then(function ($batch) use ($userId) {
                    $user = \App\Models\User::find($userId);
                    if ($user) {
                        $user->notify(new BillGenerationCompletedNotification(
                            $batch->name,
                            $batch->totalJobs,
                            $batch->processedJobs(),
                            $batch->failedJobs,
                            'business'
                        ));
                    }
                })
                ->catch(function () {
                    \Log::error('Business bill generation batch failed');
                })
                ->dispatch();

            $message = 'Bill generation has been queued and will be processed in the background. You will be notified when complete.';

            // Return appropriate response based on request type
            if ($request->isJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect' => route('bills.bus.index'),
                ]);
            }

            return redirect()->route('bills.bus.index')->with('status', $message);

        } catch (\Exception $e) {
            \Log::error('Single business bill generation error: '.$e->getMessage(), [
                'assembly_code' => $request->assembly_code ?? null,
                'bill_year' => $request->bills_year ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            $errorMsg = 'An error occurred while queueing bill generation. Please try again.';

            if ($request->isJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $errorMsg,
                ], 500);
            }

            return redirect()->route('bills.bus.singleCreate')->with('error', $errorMsg);
        }
    }

    public function divisionCreate(Request $request)
    {
        if (! auth()->user()->can('bills.create')) {
            abort(403, 'Unauthorized action.');
        }

        $total = 0;
        $businesses = Business::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->whereNotIn('id', function ($query) {
                $query->select('business_id')
                    ->from('bills')
                    ->whereNotNull('business_id')
                    ->where('bills_year', Carbon::now()->year);
            })
            ->get();

        $divisions = Division::orderBy('division_name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        $total = $businesses->count('*');

        return view('bills-business.division', compact('divisions', 'total'));
    }

    public function divisionStore(CreateDivisionBillRequest $request)
    {
        try {
            $businessIds = Business::query()
                ->when($request->division_id, function ($query) use ($request) {
                    $query->whereHas('polygon.block.division', function ($q) use ($request) {
                        $q->where('id', $request->division_id);
                    });
                })
                ->whereNotIn('id', function ($query) use ($request) {
                    $query->select('business_id')
                        ->from('bills')
                        ->whereNotNull('business_id')
                        ->where('bills_year', $request->bills_year);
                })
                ->pluck('id')
                ->toArray();

            if (empty($businessIds)) {
                return redirect()->route('bills.bus.index')->with('error', 'No businesses found for bill generation in this division.');
            }

            $assemblyCode = Business::whereIn('id', $businessIds)->first()?->assembly_code;
            $billYear = $request->bills_year;
            $userId = $request->user()->id;

            $chunks = array_chunk($businessIds, 50);
            $jobs = [];

            foreach ($chunks as $chunk) {
                $jobs[] = new GenerateBusinessBillsJob($assemblyCode, $billYear, $userId, $chunk);
            }

            $batch = Bus::batch($jobs)
                ->name("Business Bills (Division) - {$assemblyCode} - {$billYear}")
                ->then(function ($batch) use ($userId) {
                    $user = \App\Models\User::find($userId);
                    if ($user) {
                        $user->notify(new BillGenerationCompletedNotification(
                            $batch->name,
                            $batch->totalJobs,
                            $batch->processedJobs(),
                            $batch->failedJobs,
                            'business'
                        ));
                    }
                })
                ->catch(function () {
                    \Log::error('Division business bill generation batch failed');
                })
                ->dispatch();

            return redirect()->route('bills.bus.index')->with('status', "Bill generation has been queued ({$batch->totalJobs} jobs). You will be notified when complete.");

        } catch (\Exception $e) {
            \Log::error('Division business bill generation error: '.$e->getMessage(), [
                'division_id' => $request->division_id ?? null,
                'bill_year' => $request->bills_year ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('bills.bus.divisionCreate')->with('error', 'An error occurred while queueing bill generation. Please try again.');
        }
    }

    public function blockCreate(Request $request)
    {
        if (! auth()->user()->can('bills.create')) {
            abort(403, 'Unauthorized action.');
        }

        $total = 0;
        $businesses = Business::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->whereNotIn('id', function ($query) {
                $query->select('business_id')
                    ->from('bills')
                    ->whereNotNull('business_id')
                    ->where('bills_year', Carbon::now()->year);
            })
            ->get();

        $blocks = Block::orderBy('block_name', 'ASC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        $total = $businesses->count('*');

        return view('bills-business.block', compact('blocks', 'total'));
    }

    public function blockStore(CreateBlockBillRequest $request)
    {
        try {
            $businessIds = Business::query()
                ->when($request->block_id, function ($query) use ($request) {
                    $query->whereHas('polygon.block', function ($q) use ($request) {
                        $q->where('id', $request->block_id);
                    });
                })
                ->whereNotIn('id', function ($query) use ($request) {
                    $query->select('business_id')
                        ->from('bills')
                        ->whereNotNull('business_id')
                        ->where('bills_year', $request->bills_year);
                })
                ->pluck('id')
                ->toArray();

            if (empty($businessIds)) {
                return redirect()->route('bills.bus.index')->with('error', 'No businesses found for bill generation in this block.');
            }

            $assemblyCode = Business::whereIn('id', $businessIds)->first()?->assembly_code;
            $billYear = $request->bills_year;
            $userId = $request->user()->id;

            $chunks = array_chunk($businessIds, 50);
            $jobs = [];

            foreach ($chunks as $chunk) {
                $jobs[] = new GenerateBusinessBillsJob($assemblyCode, $billYear, $userId, $chunk);
            }

            $batch = Bus::batch($jobs)
                ->name("Business Bills (Block) - {$assemblyCode} - {$billYear}")
                ->then(function ($batch) use ($userId) {
                    $user = \App\Models\User::find($userId);
                    if ($user) {
                        $user->notify(new BillGenerationCompletedNotification(
                            $batch->name,
                            $batch->totalJobs,
                            $batch->processedJobs(),
                            $batch->failedJobs,
                            'business'
                        ));
                    }
                })
                ->catch(function () {
                    \Log::error('Block business bill generation batch failed');
                })
                ->dispatch();

            return redirect()->route('bills.bus.index')->with('status', "Bill generation has been queued ({$batch->totalJobs} jobs). You will be notified when complete.");

        } catch (\Exception $e) {
            \Log::error('Block business bill generation error: '.$e->getMessage(), [
                'block_id' => $request->block_id ?? null,
                'bill_year' => $request->bills_year ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('bills.bus.blockCreate')->with('error', 'An error occurred while queueing bill generation. Please try again.');
        }
    }

    public function business(Request $request)
    {
        $billYear = $request->bills_year ?? Carbon::now()->year;

        $businesses = DB::table('businesses')
            ->select('businesses.id')
            ->selectRaw("CONCAT(entity_owners.fname, ' ', entity_owners.lname, ' (', businesses.erms_id, ')') AS name")
            ->join('entity_owners', 'businesses.owner_id', '=', 'entity_owners.erms_id')
            ->where('businesses.assembly_code', $request->assembly_code)
            ->whereNotIn('businesses.id', function ($query) use ($billYear) {
                $query->select('business_id')
                    ->from('bills')
                    ->whereNotNull('business_id')
                    ->where('bills_year', $billYear);
            })->get();

        return response()->json([
            'message' => $businesses,
        ]);
    }

    public function show() {}

    public function edit() {}

    public function update() {}

    public function ajaxList(Request $request)
    {
        $user = $request->user();

        $businesses = Business::with('customer')
            ->orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($user) {
                $query->whereHas('assembly', function ($q) use ($user) {
                    $q->where('regional_code', $user->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($user) {
                $query->where('assembly_code', $user->assembly_code);
            })
            ->whereNotIn('id', function ($query) {
                $query->select('business_id')
                    ->from('bills')
                    ->whereNotNull('business_id')
                    ->where('bills_year', Carbon::now()->year);
            })
            ->get();

        $data = $businesses->map(function ($business) {
            $fullname = trim(($business->customer->fname ?? '').' '.($business->customer->lname ?? ''));

            return [
                'id' => $business->id,
                'erms_id' => $business->erms_id,
                'fullname' => $fullname,
            ];
        });

        return response()->json($data);
    }

    public function preview(Request $request)
    {
        try {
            // Handle both JSON and form data
            $data = $request->isJson() ? $request->json()->all() : $request->all();

            $assemblyCode = $data['assembly_code'] ?? null;
            $billYear = $data['bills_year'] ?? null;
            $businessIds = $data['business_id'] ?? [];

            if (empty($assemblyCode) || empty($billYear)) {
                return response()->json(['error' => 'Assembly code and bill year are required', 'data' => []], 400);
            }

            // If no business IDs provided (bulk generation), get all businesses for assembly
            if (empty($businessIds)) {
                $allBusinessIds = DB::table('businesses')
                    ->select('id')
                    ->where('assembly_code', $assemblyCode)
                    ->whereNotIn('id', function ($query) use ($billYear) {
                        $query->select('business_id')
                            ->from('bills')
                            ->whereNotNull('business_id')
                            ->where('bills_year', $billYear);
                    })
                    ->pluck('id')
                    ->toArray();

                if (empty($allBusinessIds)) {
                    return response()->json(['error' => 'No businesses available for billing in this assembly for the selected year', 'data' => []], 400);
                }

                $businessIds = $allBusinessIds;
            }

            $billAdjustments = BillAdjustment::with('revenueStreamType')
                ->where('assembly_code', $assemblyCode)
                ->where('discount_year', $billYear)
                ->whereHas('revenueStreamType', function ($q) {
                    $q->where('name', 'LIKE', '%Business%')
                        ->orWhere('name', 'LIKE', '%BOP%');
                })
                ->orderBy('created_at', 'DESC')
                ->get();

            $businesses = Business::with(['customer', 'assembly', 'polygon.block.division'])
                ->whereIn('id', $businessIds)
                ->where('assembly_code', $assemblyCode)
                ->get();

            $previewData = [];

            foreach ($businesses as $business) {
                $rateRecord = RevenueStream::where('id', $business->buss_rev_id)
                    ->where('assembly_code', $assemblyCode)
                    ->first();

                if (! $rateRecord) {
                    continue;
                }

                $flatRate = $rateRecord->flat_rate ?? 0;
                $minAmount = $rateRecord->min_amount ?? 0;
                $calculated = $flatRate;
                $finalAmount = ($calculated < $minAmount) ? $minAmount : $calculated;

                // Calculate applicable discounts for business
                $businessBlockId = optional($business->polygon->block)->id;
                $businessDivisionId = optional(optional($business->polygon->block)->division)->id;
                $applicableDiscount = 0;

                foreach ($billAdjustments as $discount) {
                    $applies = false;

                    // Assembly level discount (applies to all businesses in assembly)
                    if ($discount->discount_type === 'Assembly Discount') {
                        $applies = true;
                    }

                    // Division level discount
                    if (
                        $discount->discount_type === 'Division Discount' &&
                        $businessDivisionId == $discount->division_id
                    ) {
                        $applies = true;
                    }

                    // Block level discount
                    if (
                        $discount->discount_type === 'Block Discount' &&
                        $businessBlockId == $discount->block_id
                    ) {
                        $applies = true;
                    }

                    // ERMS level discount (most specific) - handle JSON array
                    if ($discount->erms_id && is_array($discount->erms_id) && in_array($business->erms_id, $discount->erms_id)) {
                        $applies = true;
                    }

                    if ($applies) {
                        $applicableDiscount += $discount->discount_amount;
                    }
                }

                // Apply discount to the bill (ensure it doesn't go below 0)
                $discountedBill = max(0, $finalAmount - $applicableDiscount);

                // Calculate arrears
                $totalBills = Bill::where('business_id', $business->id)->sum('amount');

                $totalPayments = Payment::whereIn('bills_id', function ($query) use ($business) {
                    $query->select('bills_id')->from('bills')->where('business_id', $business->id);
                })
                    ->where('transaction_status', 'Success')
                    ->sum('amount');

                $arrears = $totalBills - $totalPayments;

                $previewData[] = [
                    'business_no' => $business->erms_id,
                    'owner_name' => optional($business->customer)->fname.' '.optional($business->customer)->lname,
                    'flat_rate' => number_format($flatRate, 2),
                    'min_amount' => number_format($minAmount, 2),
                    'discount' => number_format($applicableDiscount, 2),
                    'arrears' => number_format($arrears, 2),
                    'final_bill' => number_format($discountedBill, 2),
                ];
            }

            return response()->json(['data' => $previewData]);

        } catch (\Exception $e) {
            \Log::error('Business bill preview error: '.$e->getMessage(), [
                'assembly_code' => $assemblyCode ?? null,
                'bill_year' => $billYear ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'An error occurred while loading preview. Please try again.',
                'data' => [],
            ], 500);
        }
    }
}
