<?php

namespace App\Http\Controllers\BillsManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bill\CreateBlockBillRequest;
use App\Http\Requests\Bill\CreateBulkBillRequest;
use App\Http\Requests\Bill\CreateDivisionBillRequest;
use App\Http\Requests\Bill\CreateSingleBillRequest;
use App\Jobs\Bill\GeneratePropertyBillsJob;
use App\Models\Assembly;
use App\Models\Bill;
use App\Models\BillAdjustment;
use App\Models\Block;
use App\Models\Division;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Rate;
use App\Models\RevenueStream;
use App\Notifications\BillGenerationCompletedNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BillsManagementController extends Controller
{
    public function index(Request $request)
    {
        if (! auth()->user()->can('bills.view')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Bill::with([
            'property.customer',
            'property.polygon.block.division',
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
            ->whereNotNull('property_id')
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

        $totalArrears = $query->clone()->sum('arrears');
        $totalAmount = $query->clone()->sum('amount');
        $totalDiscount = $query->clone()->sum('discount_amount');

        $totalExpectedPayments = $query->clone()->whereYear('created_at', Carbon::now()->year)
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
                $query->whereNotNull('property_id');
            })
            ->where('transaction_status', 'Success')
            ->sum('amount');

        $totalUnpaidBills = 0;
        if (isset($totalExpectedPayments) && isset($totalPayments)) {
            $totalUnpaidBills = $totalExpectedPayments - $totalPayments;
        }

        $paymentPercentage = $totalExpectedPayments > 0 ? ($totalPayments / $totalExpectedPayments) * 100 : 0;

        $totalBill = $query->clone()->whereNull('business_id')
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        $totalBillArrears = $query->clone()->whereNull('business_id')
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('arrears');

        $totalBillVariance = 0;
        if (isset($totalBill) && isset($totalBillArrears)) {
            $totalBillVariance = $totalBillArrears - $totalBill;
        }

        $total = [
            'totalArrears' => isset($totalArrears) ? number_format($totalArrears, 2) : 0,
            'totalAmount' => isset($totalAmount) ? number_format($totalAmount, 2) : 0,
            'totalDue' => isset($totalArrears) && isset($totalAmount) ? number_format($totalArrears + $totalAmount, 2) : 0,
            'totalDiscount' => isset($totalDiscount) ? number_format($totalDiscount, 2) : 0,
            'totalExpectedPayments' => isset($totalExpectedPayments) ? number_format($totalExpectedPayments, 2) : 0,
            'totalPayments' => isset($totalPayments) ? number_format($totalPayments, 2) : 0,
            'totalUnpaidBills' => isset($totalUnpaidBills) ? number_format($totalUnpaidBills, 2) : 0,
            'paymentPercentage' => isset($paymentPercentage) ? round($paymentPercentage, 2) : 0,
            'totalBill' => isset($totalBill) ? number_format($totalBill, 2) : 0,
            'totalBillArrears' => isset($totalBillArrears) ? number_format($totalBillArrears, 2) : 0,
            'totalBillVariance' => isset($totalBillVariance) ? number_format($totalBillVariance, 2) : 0,
        ];

        return view('bills.index', compact('request', 'total'));
    }

    public function fetchBill(Request $request)
    {
        if (! auth()->user()->can('bills.view')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Bill::with([
            'property.customer',
            'property.polygon.block.division',
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
            ->when($request->display == 'business', function ($query) {
                $query->whereNotNull('business_id')
                    ->whereYear('created_at', Carbon::now()->year);
            })
            ->when($request->display == 'property', function ($query) {
                $query->whereNotNull('property_id')
                    ->whereYear('created_at', Carbon::now()->year);
            })
            ->when($request->display == 'fetch_bill', function ($query) {
                $query->whereYear('created_at', Carbon::now()->year);
            })
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

        $totalArrears = $query->sum('arrears');
        $totalAmount = $query->sum('amount');
        $totalDiscount = $query->sum('discount_amount');

        $total = [
            'totalArrears' => isset($totalArrears) ? number_format($totalArrears, 2) : 0,
            'totalAmount' => isset($totalAmount) ? number_format($totalAmount, 2) : 0,
            'totalDue' => isset($totalArrears) && isset($totalAmount) ? number_format($totalArrears + $totalAmount, 2) : 0,
            'totalDiscount' => isset($totalDiscount) ? number_format($totalDiscount, 2) : 0,
        ];

        return view('bills.fetch', compact('total', 'request'));
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

        return view('bills.bulk', compact('assemblies'));
    }

    public function store(CreateBulkBillRequest $request)
    {
        try {
            $assemblyCode = $request->assembly_code;
            $billYear = $request->bills_year;
            $userId = $request->user()->id;

            // Get all properties that don't have bills yet
            $propertyIds = Property::where('assembly_code', $assemblyCode)
                ->whereNotIn('id', function ($query) use ($billYear) {
                    $query->select('property_id')
                        ->from('bills')
                        ->whereNotNull('property_id')
                        ->where('bills_year', $billYear);
                })
                ->pluck('id')
                ->toArray();

            if (empty($propertyIds)) {
                return redirect()->route('bills.index')->with('error', 'No properties found for bill generation.');
            }

            // Split into chunks of 50 records per job
            $chunks = array_chunk($propertyIds, 50);
            $jobs = [];

            foreach ($chunks as $chunk) {
                $jobs[] = new GeneratePropertyBillsJob($assemblyCode, $billYear, $userId, $chunk);
            }

            // Dispatch the batch
            $batch = Bus::batch($jobs)
                ->name("Property Bills Generation - {$assemblyCode} - {$billYear}")
                ->then(function ($batch) use ($userId) {
                    $user = \App\Models\User::find($userId);
                    if ($user) {
                        $user->notify(new BillGenerationCompletedNotification(
                            $batch->name,
                            $batch->totalJobs,
                            $batch->processedJobs(),
                            $batch->failedJobs,
                            'property'
                        ));
                    }
                })
                ->catch(function () {
                    \Log::error('Property bill generation batch failed');
                })
                ->dispatch();

            return redirect()->route('bills.index')->with('status', "Bill generation has been queued ({$batch->totalJobs} jobs). You will be notified when complete.");

        } catch (\Exception $e) {
            \Log::error('Bulk bill generation error: '.$e->getMessage(), [
                'assembly_code' => $request->assembly_code ?? null,
                'bill_year' => $request->bills_year ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('bills.create')->with('error', 'An error occurred while queueing bill generation. Please try again.');
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

        return view('bills.single', compact('assemblies'));
    }

    public function singleStore(CreateSingleBillRequest $request)
    {
        try {
            // Handle both JSON and form data
            $data = $request->isJson() ? $request->json()->all() : $request->all();

            $assemblyCode = $data['assembly_code'] ?? null;
            $billYear = $data['bills_year'] ?? null;
            $propertyIds = $data['property_id'] ?? [];

            $userId = $request->user()->id;
            $jobs[] = new GeneratePropertyBillsJob($assemblyCode, $billYear, $userId, $propertyIds);

            Bus::batch($jobs)
                ->name("Property Bills (Division) - {$assemblyCode} - {$billYear}")
                ->then(function ($batch) use ($userId) {
                    $user = \App\Models\User::find($userId);
                    if ($user) {
                        $user->notify(new BillGenerationCompletedNotification(
                            $batch->name,
                            $batch->totalJobs,
                            $batch->processedJobs(),
                            $batch->failedJobs,
                            'property'
                        ));
                    }
                })
                ->catch(function () {
                    \Log::error('Division property bill generation batch failed');
                })
                ->dispatch();

            $message = 'Bill generation has been queued and will be processed in the background. You will be notified when complete.';

            // Return JSON for AJAX requests
            if ($request->isJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect' => route('bills.index'),
                ]);
            }

            return redirect()->route('bills.index')->with('status', $message);

        } catch (\Exception $e) {
            \Log::error('Bill generation error: '.$e->getMessage(), [
                'assembly_code' => $data['assembly_code'] ?? null,
                'bill_year' => $data['bills_year'] ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            $errorMsg = 'An error occurred while queueing bill generation. Please try again.';

            if ($request->isJson()) {
                return response()->json(['error' => $errorMsg], 500);
            }

            return redirect()->route('bills.singleCreate')->with('error', $errorMsg);
        }
    }

    public function divisionCreate(Request $request)
    {
        if (! auth()->user()->can('bills.create')) {
            abort(403, 'Unauthorized action.');
        }

        $total = 0;
        $properties = Property::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->whereNotIn('id', function ($query) {
                $query->select('property_id')
                    ->from('bills')
                    ->whereNotNull('property_id')
                    ->where('bills_year', Carbon::now()->year);
            })
            ->get();

        $divisions = Division::orderBy('division_name', 'ASC')
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->get();

        $total = $properties->count('*');

        return view('bills.division', compact('divisions', 'total'));
    }

    public function divisionStore(CreateDivisionBillRequest $request)
    {
        try {
            $propertyIds = Property::query()
                ->when($request->division_id, function ($query) use ($request) {
                    $query->whereHas('polygon.block.division', function ($q) use ($request) {
                        $q->where('id', $request->division_id);
                    });
                })
                ->whereNotIn('id', function ($query) use ($request) {
                    $query->select('property_id')
                        ->from('bills')
                        ->whereNotNull('property_id')
                        ->where('bills_year', $request->bills_year);
                })
                ->pluck('id')
                ->toArray();

            if (empty($propertyIds)) {
                return redirect()->route('bills.index')->with('error', 'No properties found for bill generation in this division.');
            }

            $assemblyCode = Property::whereIn('id', $propertyIds)->first()?->assembly_code;
            $billYear = $request->bills_year;
            $userId = $request->user()->id;

            $chunks = array_chunk($propertyIds, 50);
            $jobs = [];

            foreach ($chunks as $chunk) {
                $jobs[] = new GeneratePropertyBillsJob($assemblyCode, $billYear, $userId, $chunk);
            }

            $batch = Bus::batch($jobs)
                ->name("Property Bills (Division) - {$assemblyCode} - {$billYear}")
                ->then(function ($batch) use ($userId) {
                    $user = \App\Models\User::find($userId);
                    if ($user) {
                        $user->notify(new BillGenerationCompletedNotification(
                            $batch->name,
                            $batch->totalJobs,
                            $batch->processedJobs(),
                            $batch->failedJobs,
                            'property'
                        ));
                    }
                })
                ->catch(function () {
                    \Log::error('Division property bill generation batch failed');
                })
                ->dispatch();

            return redirect()->route('bills.index')->with('status', "Bill generation has been queued ({$batch->totalJobs} jobs). You will be notified when complete.");

        } catch (\Exception $e) {
            \Log::error('Division bill generation error: '.$e->getMessage(), [
                'division_id' => $request->division_id ?? null,
                'bill_year' => $request->bills_year ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('bills.divisionCreate')->with('error', 'An error occurred while queueing bill generation. Please try again.');
        }
    }

    public function blockCreate(Request $request)
    {
        if (! auth()->user()->can('bills.create')) {
            abort(403, 'Unauthorized action.');
        }

        $total = 0;
        $properties = Property::orderBy('created_at', 'DESC')
            ->when(! empty($request->user()->regional_code), function ($query) use ($request) {
                $query->whereHas('assembly', function ($q) use ($request) {
                    $q->where('regional_code', $request->user()->regional_code);
                });
            })
            ->when(! empty($request->user()->assembly_code), function ($query) use ($request) {
                $query->where('assembly_code', $request->user()->assembly_code);
            })
            ->whereNotIn('id', function ($query) {
                $query->select('property_id')
                    ->from('bills')
                    ->whereNotNull('property_id')
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

        $total = $properties->count('*');

        return view('bills.block', compact('blocks', 'total'));
    }

    public function blockStore(CreateBlockBillRequest $request)
    {
        try {
            $propertyIds = Property::query()
                ->when($request->block_id, function ($query) use ($request) {
                    $query->whereHas('polygon.block', function ($q) use ($request) {
                        $q->where('id', $request->block_id);
                    });
                })
                ->whereNotIn('id', function ($query) use ($request) {
                    $query->select('property_id')
                        ->from('bills')
                        ->whereNotNull('property_id')
                        ->where('bills_year', $request->bills_year);
                })
                ->pluck('id')
                ->toArray();

            if (empty($propertyIds)) {
                return redirect()->route('bills.index')->with('error', 'No properties found for bill generation in this block.');
            }

            $assemblyCode = Property::whereIn('id', $propertyIds)->first()?->assembly_code;
            $billYear = $request->bills_year;
            $userId = $request->user()->id;

            $chunks = array_chunk($propertyIds, 50);
            $jobs = [];

            foreach ($chunks as $chunk) {
                $jobs[] = new GeneratePropertyBillsJob($assemblyCode, $billYear, $userId, $chunk);
            }

            $batch = Bus::batch($jobs)
                ->name("Property Bills (Block) - {$assemblyCode} - {$billYear}")
                ->then(function ($batch) use ($userId) {
                    $user = \App\Models\User::find($userId);
                    if ($user) {
                        $user->notify(new BillGenerationCompletedNotification(
                            $batch->name,
                            $batch->totalJobs,
                            $batch->processedJobs(),
                            $batch->failedJobs,
                            'property'
                        ));
                    }
                })
                ->catch(function () {
                    \Log::error('Block property bill generation batch failed');
                })
                ->dispatch();

            return redirect()->route('bills.index')->with('status', "Bill generation has been queued ({$batch->totalJobs} jobs). You will be notified when complete.");

        } catch (\Exception $e) {
            \Log::error('Block bill generation error: '.$e->getMessage(), [
                'block_id' => $request->block_id ?? null,
                'bill_year' => $request->bills_year ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('bills.blockCreate')->with('error', 'An error occurred while queueing bill generation. Please try again.');
        }
    }

    public function property(Request $request)
    {
        $billYear = $request->bills_year ?? Carbon::now()->year;

        $properties = DB::table('properties')
            ->select('properties.id')
            ->selectRaw("CONCAT(entity_owners.fname, ' ', entity_owners.lname, ' (', properties.erms_id, ')') AS name")
            ->join('entity_owners', 'properties.owner_id', '=', 'entity_owners.erms_id')
            ->where('properties.assembly_code', $request->assembly_code)
            ->whereNotIn('properties.id', function ($query) use ($billYear) {
                $query->select('property_id')
                    ->from('bills')
                    ->whereNotNull('property_id')
                    ->where('bills_year', $billYear);
            })->get();

        return response()->json([
            'message' => $properties,
        ]);
    }

    public function receipt(Bill $bill)
    {
        if ($bill->property && $bill->property->customer) {
            $rateImposed = RevenueStream::where('id', $bill->property->prop_rev_id)
                ->where('assembly_code', $bill->property->assembly_code)
                ->first();
        } elseif ($bill->business && $bill->business->customer) {
            $rateImposed = RevenueStream::where('id', $bill->business->buss_rev_id)
                ->where('assembly_code', $bill->business->assembly_code)
                ->first();
        }

        $bill['rate_imposed'] = isset($rateImposed) ? ($rateImposed->rate_impost ?? $rateImposed->flat_rate) : 0;

        return view('bills.receipt', compact('bill'));
    }

    public function show(Bill $bill)
    {
        if ($bill->property && $bill->property->customer) {
            $rateImposed = RevenueStream::where('id', $bill->property->prop_rev_id)
                ->where('assembly_code', $bill->property->assembly_code)
                ->first();
        } elseif ($bill->business && $bill->business->customer) {
            $rateImposed = Rate::where('zone_id', $bill->business->zone_id)
                ->where('property_use_id', $bill->business->property_use_id)
                ->where('assembly_code', $bill->business->assembly->assembly_code)
                ->first();
        }

        $bill['rate_imposed'] = isset($rateImposed) ? $rateImposed->rate_imposed : 0;

        return view('bills.show', compact('bill'));
    }

    public function publicShow($bill)
    {
        $bill = Bill::query()->where(function (Builder $query) use ($bill) {
            $query->where('bills_id', $bill)
                ->orWhere('id', $bill);
        })->firstOrFail();

        if ($bill->property && $bill->property->customer) {
            $rateImposed = RevenueStream::where('id', $bill->property->prop_rev_id)
                ->where('assembly_code', $bill->property->assembly_code)
                ->first();
        } elseif ($bill->business && $bill->business->customer) {
            $rateImposed = Rate::where('zone_id', $bill->business->zone_id)
                ->where('property_use_id', $bill->business->property_use_id)
                ->where('assembly_code', $bill->business->assembly->assembly_code)
                ->first();
        }

        $bill['rate_imposed'] = isset($rateImposed) ? $rateImposed->rate_imposed : 0;

        return view('bills.public-show', compact('bill'));
    }

    public function edit($id)
    {
        // Logic for displaying the form to edit a bill
    }

    public function update(Request $request, $id)
    {
        // Logic for updating a bill
    }

    public function destroy($id)
    {
        // Logic for deleting a bill
    }

    public function ajaxList(Request $request)
    {
        $user = $request->user();

        $billYear = $request->bills_year ?? Carbon::now()->year;

        $properties = DB::table('properties')
            ->select(['properties.id', 'properties.erms_id'])
            ->selectRaw("CONCAT(entity_owners.fname, ' ', entity_owners.lname) AS fullname")
            ->join('assemblies', 'properties.assembly_code', '=', 'assemblies.assembly_code')
            ->join('ghana_regions', 'assemblies.regional_code', '=', 'ghana_regions.regional_code')
            ->join('entity_owners', 'properties.owner_id', '=', 'entity_owners.erms_id')
            ->when(! empty($user->regional_code), function ($query) use ($user) {
                $query->where('ghana_regions.regional_code', $user->regional_code);
            })
            ->when(! empty($user->assembly_code), function ($query) use ($user) {
                $query->where('properties.assembly_code', $user->assembly_code);
            })
            ->whereNotIn('properties.id', function ($query) use ($billYear) {
                $query->select('property_id')
                    ->from('bills')
                    ->whereNotNull('property_id')
                    ->where('bills_year', $billYear);
            })
            ->orderBy('properties.created_at', 'desc')
            ->get();

        return response()->json($properties);
    }

    public function preview(Request $request)
    {
        try {
            // Handle both JSON and form data
            $data = $request->isJson() ? $request->json()->all() : $request->all();

            $assemblyCode = $data['assembly_code'] ?? null;
            $billYear = $data['bills_year'] ?? null;
            $propertyIds = $data['property_id'] ?? [];

            if (empty($assemblyCode) || empty($billYear)) {
                return response()->json(['error' => 'Assembly code and bill year are required', 'data' => []], 400);
            }

            // If no property IDs provided (bulk generation), get all properties for assembly
            if (empty($propertyIds)) {
                $allPropertyIds = DB::table('properties')
                    ->select('id')
                    ->where('assembly_code', $assemblyCode)
                    ->whereNotIn('id', function ($query) use ($billYear) {
                        $query->select('property_id')
                            ->from('bills')
                            ->whereNotNull('property_id')
                            ->where('bills_year', $billYear);
                    })
                    ->pluck('id')
                    ->toArray();

                if (empty($allPropertyIds)) {
                    return response()->json(['error' => 'No properties available for billing in this assembly for the selected year', 'data' => []], 400);
                }

                $propertyIds = $allPropertyIds;
            }

            // Calculate bills directly in the database using raw SQL for performance
            $previewData = DB::table('properties as p')
                ->select([
                    'p.erms_id as property_no',
                    DB::raw("CONCAT(COALESCE(eo.fname, ''), ' ', COALESCE(eo.lname, '')) as owner_name"),
                    'p.rateable_value',
                    'rs.rate_impost',
                    'rs.flat_rate',
                    'rs.min_amount',
                    DB::raw("
                        CASE
                            WHEN rs.is_rated = 'Y' THEN
                                GREATEST(
                                    rs.rate_impost * COALESCE(p.rateable_value, 0),
                                    COALESCE(rs.min_amount, 0)
                                )
                            WHEN rs.is_rated = 'N' THEN
                                GREATEST(
                                    COALESCE(rs.flat_rate, 0),
                                    COALESCE(rs.min_amount, 0)
                                )
                            ELSE
                                GREATEST(
                                    rs.rate_impost * COALESCE(p.rateable_value, 0),
                                    COALESCE(rs.min_amount, 0)
                                )
                        END as calculated_bill
                    "),
                    'pol.block_id',
                    'b.division_id',
                ])
                ->leftJoin('entity_owners as eo', 'p.owner_id', '=', 'eo.erms_id')
                ->leftJoin('revenue_streams as rs', function ($join) use ($assemblyCode) {
                    $join->on('p.prop_rev_id', '=', 'rs.id')
                        ->where('rs.assembly_code', '=', $assemblyCode);
                })
                ->leftJoin('polygons as pol', 'p.polygon_id', '=', 'pol.id')
                ->leftJoin('blocks as b', 'pol.block_id', '=', 'b.id')
                ->where('p.assembly_code', $assemblyCode)
                ->whereIn('p.id', $propertyIds)
                ->whereNotNull('rs.id')
                ->get();

            // Get bill adjustments for discount calculation
            $billAdjustments = BillAdjustment::with('revenueStreamType')
                ->where('assembly_code', $assemblyCode)
                ->where('discount_year', $billYear)
                ->whereHas('revenueStreamType', function ($q) {
                    $q->where('name', 'Property Rate');
                })
                ->get();

            // Build discount lookup maps for efficient matching
            $assemblyDiscounts = 0;
            $divisionDiscounts = [];
            $blockDiscounts = [];
            $ermsDiscounts = [];

            foreach ($billAdjustments as $discount) {
                if ($discount->discount_type === 'Assembly Discount') {
                    $assemblyDiscounts += $discount->discount_amount;
                } elseif ($discount->discount_type === 'Division Discount' && $discount->division_id) {
                    if (! isset($divisionDiscounts[$discount->division_id])) {
                        $divisionDiscounts[$discount->division_id] = 0;
                    }
                    $divisionDiscounts[$discount->division_id] += $discount->discount_amount;
                } elseif ($discount->discount_type === 'Block Discount' && $discount->block_id) {
                    if (! isset($blockDiscounts[$discount->block_id])) {
                        $blockDiscounts[$discount->block_id] = 0;
                    }
                    $blockDiscounts[$discount->block_id] += $discount->discount_amount;
                } elseif ($discount->erms_id && is_array($discount->erms_id)) {
                    // Handle JSON array of erms_ids
                    foreach ($discount->erms_id as $ermsId) {
                        if (! isset($ermsDiscounts[$ermsId])) {
                            $ermsDiscounts[$ermsId] = 0;
                        }
                        $ermsDiscounts[$ermsId] += $discount->discount_amount;
                    }
                }
            }

            // Apply discounts and format results
            $results = [];
            foreach ($previewData as $property) {
                $discount = $assemblyDiscounts;

                // Add division discount
                if ($property->division_id && isset($divisionDiscounts[$property->division_id])) {
                    $discount += $divisionDiscounts[$property->division_id];
                }

                // Add block discount
                if ($property->block_id && isset($blockDiscounts[$property->block_id])) {
                    $discount += $blockDiscounts[$property->block_id];
                }

                // Add ERMS-specific discount
                if (isset($ermsDiscounts[$property->property_no])) {
                    $discount += $ermsDiscounts[$property->property_no];
                }

                $finalBill = max(0, $property->calculated_bill - $discount);

                $results[] = [
                    'property_no' => $property->property_no,
                    'owner_name' => trim($property->owner_name),
                    'rateable_value' => number_format($property->rateable_value ?? 0, 2),
                    'rate_impost' => number_format($property->rate_impost ?? 0, 2),
                    'flat_rate' => number_format($property->flat_rate ?? 0, 2),
                    'min_amount' => number_format($property->min_amount ?? 0, 2),
                    'discount' => number_format($discount, 2),
                    'final_bill' => number_format($finalBill, 2),
                ];
            }

            return response()->json(['data' => $results]);

        } catch (\Exception $e) {
            \Log::error('Bill preview error: '.$e->getMessage(), [
                'assembly_code' => $request->assembly_code ?? null,
                'bill_year' => $request->bills_year ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'An error occurred while generating preview. Please try again.',
                'data' => [],
            ], 500);
        }
    }

    public function initializePublicPayment(Request $request, Bill $bill)
    {
        try {
            // Validate request
            $request->validate([
                'amount' => 'required|numeric|min:1',
            ]);

            // Generate unique reference
            $reference = 'BILL-'.$bill->bills_id.'-'.time();

            $phone = $bill->property?->customer?->phone_no_primary
                ?? $bill->business?->customer?->phone_no_primary
                ?? null;

            $email =
                $bill->property?->customer?->email
                ?? $bill->business?->customer?->email
                ?? null;

            $payerEmail = ! blank($email) ? $email : ($phone ?? Str::random(6)).'@ermsplus.africa';

            $validator = Validator::make(['email' => $payerEmail], ['email' => 'required|email:dns']);

            if ($validator->fails()) {
                $payerEmail = Str::random(6).'@ermsplus.africa';
            }

            // Initialize Paystack payment
            $paystackData = [
                'email' => $payerEmail,
                'amount' => $request->amount * 100, // Convert to pesewas
                'reference' => $reference,
                'callback_url' => route('public.payments.verify').'?is_public=1',
                'currency' => 'GHS',
            ];

            $response = Http::withToken(config('services.paystack.secret_key'))
                ->baseUrl(config('services.paystack.url'))
                ->throw()
                ->post('transaction/initialize', $paystackData);

            if ($response->successful()) {
                // Create payment record
                $payment = Payment::create([
                    'bills_id' => $bill->bills_id,
                    'assembly_code' => $bill->assembly_code,
                    'amount' => $request->amount,
                    'payment_mode' => 'pending',
                    'payment_source' => 'Paystack',
                    'transaction_status' => 'Pending',
                    'transaction_id' => $reference,
                ]);

                $authUrl = $response->json()['data']['authorization_url'];
                $payment->update(['authorization_url' => $authUrl]);

                return response()->json([
                    'success' => true,
                    'authorization_url' => $authUrl,
                    'message' => 'Payment initialized successfully',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to initialize payment. Please try again.',
            ], 500);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while initializing payment. Please try again.',
            ], 500);
        }
    }
}
