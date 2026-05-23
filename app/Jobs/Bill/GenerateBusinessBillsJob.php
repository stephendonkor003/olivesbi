<?php

namespace App\Jobs\Bill;

use App\Models\Bill;
use App\Models\BillAdjustment;
use App\Models\Business;
use App\Models\RevenueStream;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateBusinessBillsJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;

    public function __construct(
        public string $assemblyCode,
        public string $billYear,
        public int $userId,
        public array $businessIds
    ) {}

    public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        try {
            if (empty($this->businessIds)) {
                Log::info('No businesses in chunk for bill generation');

                return;
            }

            $billAdjustments = BillAdjustment::with('revenueStreamType')
                ->where('assembly_code', $this->assemblyCode)
                ->where('discount_year', $this->billYear)
                ->whereHas('revenueStreamType', function ($q) {
                    $q->where('name', 'LIKE', '%Business%')
                        ->orWhere('name', 'LIKE', '%BOP%');
                })
                ->orderBy('created_at', 'DESC')
                ->get();

            $assemblyDiscounts = 0;
            $divisionDiscounts = [];
            $blockDiscounts = [];
            $ermsDiscounts = [];

            foreach ($billAdjustments as $discount) {
                if ($discount->discount_type === 'Assembly Discount') {
                    $assemblyDiscounts += $discount->discount_amount;
                } elseif ($discount->discount_type === 'Division Discount' && $discount->division_id) {
                    $divisionDiscounts[$discount->division_id] = ($divisionDiscounts[$discount->division_id] ?? 0) + $discount->discount_amount;
                } elseif ($discount->discount_type === 'Block Discount' && $discount->block_id) {
                    $blockDiscounts[$discount->block_id] = ($blockDiscounts[$discount->block_id] ?? 0) + $discount->discount_amount;
                } elseif ($discount->erms_id && is_array($discount->erms_id)) {
                    foreach ($discount->erms_id as $ermsId) {
                        $ermsDiscounts[$ermsId] = ($ermsDiscounts[$ermsId] ?? 0) + $discount->discount_amount;
                    }
                }
            }

            $arrearsData = DB::table('bills')
                ->select('business_id', DB::raw('SUM(amount) as total_bills'))
                ->whereIn('business_id', $this->businessIds)
                ->groupBy('business_id')
                ->get()
                ->keyBy('business_id');

            $paymentsData = DB::table('payments')
                ->select('bills.business_id', DB::raw('SUM(payments.amount) as total_payments'))
                ->join('bills', 'payments.bills_id', '=', 'bills.bills_id')
                ->whereIn('bills.business_id', $this->businessIds)
                ->where(function ($query) {
                    $query->where('payments.payment_mode', '!=', 'momo')
                        ->orWhere(function ($query) {
                            $query->where('payments.payment_mode', 'momo')
                                ->where('payments.transaction_status', 'Success');
                        });
                })
                ->groupBy('bills.business_id')
                ->get()
                ->keyBy('business_id');

            $businesses = Business::with(['polygon.block.division', 'assembly'])
                ->whereIn('id', $this->businessIds)
                ->where('assembly_code', $this->assemblyCode)
                ->get();

            $billsToInsert = [];
            $billsCreated = 0;
            $billsSkipped = 0;

            foreach ($businesses as $business) {
                if ($this->batch()?->cancelled()) {
                    return;
                }

                $rateRecord = RevenueStream::where('id', $business->buss_rev_id)
                    ->where('assembly_code', $this->assemblyCode)
                    ->first();

                if (! $rateRecord) {
                    $billsSkipped++;

                    continue;
                }

                $flatRate = $rateRecord->flat_rate ?? 0;
                $minAmount = $rateRecord->min_amount ?? 0;
                $yearlyBill = max($flatRate, $minAmount);

                $applicableDiscount = $assemblyDiscounts;
                $businessBlockId = optional($business->polygon->block)->id;
                $businessDivisionId = optional(optional($business->polygon->block)->division)->id;

                if ($businessDivisionId && isset($divisionDiscounts[$businessDivisionId])) {
                    $applicableDiscount += $divisionDiscounts[$businessDivisionId];
                }
                if ($businessBlockId && isset($blockDiscounts[$businessBlockId])) {
                    $applicableDiscount += $blockDiscounts[$businessBlockId];
                }
                if (isset($ermsDiscounts[$business->erms_id])) {
                    $applicableDiscount += $ermsDiscounts[$business->erms_id];
                }

                $discountedBill = max(0, $yearlyBill - $applicableDiscount);

                $totalBills = $arrearsData[$business->id]->total_bills ?? 0;
                $totalPayments = $paymentsData[$business->id]->total_payments ?? 0;
                $arrears = $totalBills - $totalPayments;

                do {
                    $billID = $business->assembly->assembly_code;
                    $billID .= 'BoP';
                    $billID .= substr($this->billYear, -2);
                    $billID .= $business->polygon->block?->division?->code ?? '';
                    $billID .= $business->polygon->block?->code ?? '';
                    $billID .= str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
                } while (Bill::where('bills_id', $billID)->exists());

                $billsToInsert[] = [
                    'business_id' => $business->id,
                    'assembly_code' => $this->assemblyCode,
                    'bills_id' => $billID,
                    'bills_year' => $this->billYear,
                    'arrears' => $arrears ?? 0,
                    'amount' => round($discountedBill, 2),
                    'discount_amount' => $applicableDiscount,
                    'billing_date' => Carbon::now(),
                    'created_by' => $this->userId,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                $billsCreated++;
            }

            if (! empty($billsToInsert)) {
                Bill::insert($billsToInsert);

                $insertedBillIds = Bill::whereIn('bills_id', array_column($billsToInsert, 'bills_id'))
                    ->pluck('id')
                    ->toArray();

                if (! empty($insertedBillIds)) {
                    SendGeneratedBillNotificationsJob::dispatch($insertedBillIds);
                }
            }

            Log::info('Business bills chunk completed', [
                'assembly_code' => $this->assemblyCode,
                'bill_year' => $this->billYear,
                'chunk_size' => count($this->businessIds),
                'bills_created' => $billsCreated,
                'bills_skipped' => $billsSkipped,
            ]);
        } catch (\Exception $e) {
            Log::error('Business bills chunk job failed', [
                'assembly_code' => $this->assemblyCode,
                'bill_year' => $this->billYear,
                'chunk_size' => count($this->businessIds),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
