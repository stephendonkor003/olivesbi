<?php

namespace App\Jobs\Bill;

use App\Models\Bill;
use App\Models\BillAdjustment;
use App\Models\Property;
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

class GeneratePropertyBillsJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;

    public function __construct(
        public string $assemblyCode,
        public string $billYear,
        public int $userId,
        public array $propertyIds
    ) {}

    public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        try {
            if (empty($this->propertyIds)) {
                Log::info('No properties in chunk for bill generation');

                return;
            }

            $billAdjustments = BillAdjustment::with('revenueStreamType')
                ->where('assembly_code', $this->assemblyCode)
                ->where('discount_year', $this->billYear)
                ->whereHas('revenueStreamType', function ($q) {
                    $q->where('name', 'Property Rate');
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
                ->select('property_id', DB::raw('SUM(amount) as total_bills'))
                ->whereIn('property_id', $this->propertyIds)
                ->groupBy('property_id')
                ->get()
                ->keyBy('property_id');

            $paymentsData = DB::table('payments')
                ->select('bills.property_id', DB::raw('SUM(payments.amount) as total_payments'))
                ->join('bills', 'payments.bills_id', '=', 'bills.bills_id')
                ->whereIn('bills.property_id', $this->propertyIds)
                ->where(function ($query) {
                    $query->where('payments.payment_mode', '!=', 'momo')
                        ->orWhere(function ($query) {
                            $query->where('payments.payment_mode', 'momo')
                                ->where('payments.transaction_status', 'Success');
                        });
                })
                ->groupBy('bills.property_id')
                ->get()
                ->keyBy('property_id');

            $properties = Property::with(['polygon.block.division', 'assembly'])
                ->whereIn('id', $this->propertyIds)
                ->where('assembly_code', $this->assemblyCode)
                ->get();

            $billsToInsert = [];
            $billsCreated = 0;
            $billsSkipped = 0;

            foreach ($properties as $property) {
                if ($this->batch()?->cancelled()) {
                    return;
                }

                $rateRecord = RevenueStream::where('id', $property->prop_rev_id)
                    ->where('assembly_code', $this->assemblyCode)
                    ->first();

                if (! $rateRecord) {
                    $billsSkipped++;

                    continue;
                }

                if ($rateRecord->is_rated === 'Y') {
                    $calculatedBill = $rateRecord->rate_impost * ($property->rateable_value ?? 0);
                } else {
                    $calculatedBill = $rateRecord->flat_rate ?? 0;
                }

                $yearlyBill = max($calculatedBill, $rateRecord->min_amount ?? 0);

                $applicableDiscount = $assemblyDiscounts;
                $propertyBlockId = optional($property->polygon?->block)->id;
                $propertyDivisionId = optional(optional($property->polygon?->block)->division)->id;

                if ($propertyDivisionId && isset($divisionDiscounts[$propertyDivisionId])) {
                    $applicableDiscount += $divisionDiscounts[$propertyDivisionId];
                }
                if ($propertyBlockId && isset($blockDiscounts[$propertyBlockId])) {
                    $applicableDiscount += $blockDiscounts[$propertyBlockId];
                }
                if (isset($ermsDiscounts[$property->erms_id])) {
                    $applicableDiscount += $ermsDiscounts[$property->erms_id];
                }

                $discountedBill = max(0, $yearlyBill - $applicableDiscount);

                $totalBills = $arrearsData[$property->id]->total_bills ?? 0;
                $totalPayments = $paymentsData[$property->id]->total_payments ?? 0;
                $arrears = $totalBills - $totalPayments;

                do {
                    $billID = $property->assembly->assembly_code;
                    $billID .= 'PR';
                    $billID .= substr($this->billYear, -2);
                    $billID .= $property->polygon?->block?->division?->code ?? '';
                    $billID .= $property->polygon?->block?->code ?? '';
                    $billID .= str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
                } while (Bill::where('bills_id', $billID)->exists());

                $billsToInsert[] = [
                    'property_id' => $property->id,
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

            Log::info('Property bills chunk completed', [
                'assembly_code' => $this->assemblyCode,
                'bill_year' => $this->billYear,
                'chunk_size' => count($this->propertyIds),
                'bills_created' => $billsCreated,
                'bills_skipped' => $billsSkipped,
            ]);
        } catch (\Exception $e) {
            Log::error('Property bills chunk job failed', [
                'assembly_code' => $this->assemblyCode,
                'bill_year' => $this->billYear,
                'chunk_size' => count($this->propertyIds),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
