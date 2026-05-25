<?php

namespace App\Services;

use App\Jobs\Bill\SendGeneratedBillNotificationsJob;
use App\Models\Bill;
use App\Models\BillAdjustment;
use App\Models\Property;
use App\Models\RevenueStream;
use App\Models\RevenueStreamType;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PropertyRateBillGenerator
{
    public function generate(
        string $assemblyCode,
        string $billYear,
        string $createdBy,
        array $options = [],
        array $propertyIds = []
    ): array {
        $properties = $this->billableProperties($assemblyCode, $billYear, $propertyIds, (bool) ($options['only_unbilled'] ?? true));
        $discounts = $this->discounts($assemblyCode, $billYear);
        $rows = [];
        $createdBillIds = [];
        $summary = [
            'processed' => 0,
            'created' => 0,
            'skipped' => 0,
            'total_amount' => 0.0,
            'total_arrears' => 0.0,
            'total_discount' => 0.0,
            'items' => [],
        ];

        foreach ($properties as $property) {
            $summary['processed']++;
            $billLine = $this->billLine($property, $assemblyCode, $billYear, $discounts, $options);

            if (! $billLine['billable']) {
                $summary['skipped']++;
                $summary['items'][] = $billLine;
                continue;
            }

            $summary['total_amount'] += $billLine['amount'];
            $summary['total_arrears'] += $billLine['arrears'];
            $summary['total_discount'] += $billLine['discount_amount'];
            $summary['items'][] = $billLine;

            if ($options['dry_run'] ?? false) {
                continue;
            }

            $rows[] = [
                'uuid' => $billLine['uuid'],
                'property_id' => $property->uuid,
                'assembly_code' => $assemblyCode,
                'bills_id' => $billLine['bills_id'],
                'bills_year' => $billYear,
                'arrears' => round($billLine['arrears'], 2),
                'amount' => round($billLine['amount'], 2),
                'discount_amount' => round($billLine['discount_amount'], 2),
                'billing_date' => $options['billing_date'] ?? Carbon::now()->toDateString(),
                'created_by' => $createdBy,
                'issued_by' => $createdBy,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        if ($rows) {
            Bill::insert($rows);
            $createdBillIds = Bill::whereIn('bills_id', array_column($rows, 'bills_id'))->pluck('id')->all();

            if ($createdBillIds && ($options['notify'] ?? true)) {
                SendGeneratedBillNotificationsJob::dispatch($createdBillIds);
            }
        }

        $summary['created'] = count($rows);
        $summary['created_bill_ids'] = $createdBillIds;

        return $summary;
    }

    public function billableProperties(string $assemblyCode, string $billYear, array $propertyIds = [], bool $onlyUnbilled = true): Collection
    {
        return Property::with(['assembly', 'customer', 'polygon.block.division'])
            ->where('assembly_code', $assemblyCode)
            ->when($propertyIds, function ($query) use ($propertyIds) {
                $references = collect($propertyIds)
                    ->map(fn ($reference) => trim((string) $reference))
                    ->filter()
                    ->unique()
                    ->values();

                $numericIds = $references
                    ->filter(fn ($reference) => ctype_digit($reference))
                    ->map(fn ($reference) => (int) $reference)
                    ->values();

                $uuids = $references
                    ->filter(fn ($reference) => Str::isUuid($reference))
                    ->values();

                $externalRefs = $references
                    ->reject(fn ($reference) => ctype_digit($reference) || Str::isUuid($reference))
                    ->values();

                $query->where(function ($query) use ($numericIds, $uuids, $externalRefs) {
                    $hasCondition = false;

                    if ($numericIds->isNotEmpty()) {
                        $query->whereIn('id', $numericIds);
                        $hasCondition = true;
                    }

                    if ($uuids->isNotEmpty()) {
                        $method = $hasCondition ? 'orWhereIn' : 'whereIn';
                        $query->{$method}('uuid', $uuids);
                        $hasCondition = true;
                    }

                    if ($externalRefs->isNotEmpty()) {
                        $method = $hasCondition ? 'orWhereIn' : 'whereIn';
                        $query->{$method}('erms_id', $externalRefs);
                        $hasCondition = true;
                    }

                    if (! $hasCondition) {
                        $query->whereRaw('1 = 0');
                    }
                });
            })
            ->when($onlyUnbilled, function ($query) use ($billYear) {
                $query->whereNotIn('uuid', function ($subQuery) use ($billYear) {
                    $subQuery->select('property_id')
                        ->from('bills')
                        ->whereNotNull('property_id')
                        ->where('bills_year', $billYear);
                });
            })
            ->orderBy('created_at')
            ->get();
    }

    private function billLine(Property $property, string $assemblyCode, string $billYear, array $discounts, array $options): array
    {
        $rate = $this->rateFor($property, $assemblyCode, $options);
        $propertyLabel = $property->account_no ?: ($property->erms_id ?: $property->uuid);
        $ownerName = trim(($property->customer?->fname ?? '').' '.($property->customer?->lname ?? ''));

        if (! $rate) {
            return [
                'billable' => false,
                'property_uuid' => $property->uuid,
                'property' => $propertyLabel,
                'owner_name' => $ownerName,
                'rateable_value' => (float) ($property->rateable_value ?? 0),
                'reason' => 'No property rate or fallback rate was configured.',
                'amount' => 0.0,
                'arrears' => 0.0,
                'discount_amount' => 0.0,
            ];
        }

        $calculated = $rate['mode'] === 'flat'
            ? $rate['flat_rate']
            : $rate['rate_impost'] * (float) ($property->rateable_value ?? 0);

        $yearlyBill = max($calculated, $rate['min_amount']);

        if ($rate['max_amount'] > 0) {
            $yearlyBill = min($yearlyBill, $rate['max_amount']);
        }

        $discount = min($yearlyBill, $this->discountFor($property, $discounts) + (float) ($options['discount_amount'] ?? 0));
        $netAmount = max(0, $yearlyBill - $discount);
        $arrears = $this->arrearsFor($property->uuid, $billYear);

        return [
            'billable' => true,
            'uuid' => (string) Str::uuid(),
            'bills_id' => $this->billNumber($property, $billYear),
            'property_uuid' => $property->uuid,
            'property' => $propertyLabel,
            'owner_name' => $ownerName,
            'rate_source' => $rate['source'],
            'mode' => $rate['mode'],
            'rateable_value' => (float) ($property->rateable_value ?? 0),
            'rate_impost' => $rate['rate_impost'],
            'flat_rate' => $rate['flat_rate'],
            'min_amount' => $rate['min_amount'],
            'max_amount' => $rate['max_amount'],
            'gross_amount' => round($yearlyBill, 2),
            'discount_amount' => round($discount, 2),
            'amount' => round($yearlyBill, 2),
            'net_amount' => round($netAmount, 2),
            'arrears' => round($arrears, 2),
            'total_due' => round($netAmount + $arrears, 2),
            'reason' => null,
        ];
    }

    private function rateFor(Property $property, string $assemblyCode, array $options): ?array
    {
        $stream = $this->revenueStreamFor($property, $assemblyCode);

        if ($stream) {
            $mode = $this->isRated($stream->is_rated) ? 'rateable' : 'flat';

            return [
                'source' => $stream->description ?: ($stream->code ?: 'Revenue stream'),
                'mode' => $mode,
                'rate_impost' => $this->money($stream->rate_impost),
                'flat_rate' => $this->money($stream->flat_rate),
                'min_amount' => $this->money($stream->min_amount),
                'max_amount' => $this->money($stream->max_amount),
            ];
        }

        $mode = $options['calculation_mode'] ?? 'rateable';
        $rateImpost = $this->money($options['rate_impost'] ?? 0);
        $flatRate = $this->money($options['flat_rate'] ?? 0);
        $hasFallback = $mode === 'flat' ? $flatRate > 0 : $rateImpost > 0;

        if (! $hasFallback) {
            return null;
        }

        return [
            'source' => 'Olives Pro fallback rate',
            'mode' => $mode,
            'rate_impost' => $rateImpost,
            'flat_rate' => $flatRate,
            'min_amount' => $this->money($options['min_amount'] ?? 0),
            'max_amount' => $this->money($options['max_amount'] ?? 0),
        ];
    }

    private function revenueStreamFor(Property $property, string $assemblyCode): ?RevenueStream
    {
        $reference = trim((string) $property->prop_rev_id);

        if ($reference !== '') {
            $query = RevenueStream::where('assembly_code', $assemblyCode);

            $query->where(function ($query) use ($reference) {
                $hasCondition = false;

                if (ctype_digit($reference)) {
                    $query->where('id', (int) $reference);
                    $hasCondition = true;
                }

                if (Str::isUuid($reference) && Schema::hasColumn('revenue_streams', 'uuid')) {
                    $method = $hasCondition ? 'orWhere' : 'where';
                    $query->{$method}('uuid', $reference);
                    $hasCondition = true;
                }

                if (Schema::hasColumn('revenue_streams', 'erms_id')) {
                    $method = $hasCondition ? 'orWhere' : 'where';
                    $query->{$method}('erms_id', $reference);
                    $hasCondition = true;
                }

                if (! $hasCondition) {
                    $query->whereRaw('1 = 0');
                }
            });

            $stream = $query->first();

            if ($stream) {
                return $stream;
            }
        }

        return RevenueStream::where('assembly_code', $assemblyCode)
            ->where(function ($query) {
                foreach (['description', 'code', 'name'] as $column) {
                    if (! Schema::hasColumn('revenue_streams', $column)) {
                        continue;
                    }

                    $query->orWhere($column, 'ILIKE', '%property%')
                        ->orWhere($column, 'ILIKE', '%rate%');
                }
            })
            ->orderByDesc('is_rated')
            ->first();
    }

    private function arrearsFor(string $propertyUuid, string $billYear): float
    {
        $priorBills = Bill::where('property_id', $propertyUuid)
            ->where('bills_year', '<', $billYear)
            ->get(['bills_id', 'amount', 'discount_amount']);

        if ($priorBills->isEmpty()) {
            return 0.0;
        }

        $priorBillIds = $priorBills->pluck('bills_id')->filter()->values();
        $charges = $priorBills->sum(fn ($bill) => max(0, (float) $bill->amount - (float) $bill->discount_amount));
        $payments = DB::table('payments')
            ->whereIn('bills_id', $priorBillIds)
            ->where(function ($query) {
                $query->whereRaw('LOWER(COALESCE(transaction_status, \'\')) = ?', ['success'])
                    ->orWhere(function ($query) {
                        $query->whereRaw('LOWER(COALESCE(payment_mode, \'\')) != ?', ['momo'])
                            ->whereRaw('LOWER(COALESCE(transaction_status, \'\')) NOT IN (?, ?)', ['failed', 'cancelled']);
                    });
            })
            ->pluck('amount')
            ->sum(fn ($amount) => $this->money($amount));

        return max(0, (float) $charges - (float) $payments);
    }

    private function discounts(string $assemblyCode, string $billYear): array
    {
        $discounts = [
            'assembly' => 0.0,
            'division' => [],
            'block' => [],
            'property' => [],
        ];

        $streamTypeReferences = RevenueStreamType::query()
            ->where(function ($query) {
                $query->where('name', 'ILIKE', '%Property%')
                    ->orWhere('name', 'ILIKE', '%Rate%');
            })
            ->get(['id', 'uuid'])
            ->flatMap(fn ($type) => array_filter([(string) $type->id, (string) $type->uuid]))
            ->unique()
            ->values();

        if ($streamTypeReferences->isEmpty()) {
            return $discounts;
        }

        $adjustments = BillAdjustment::query()
            ->where('assembly_code', $assemblyCode)
            ->where('discount_year', $billYear)
            ->whereIn('revenue_stream_type_id', $streamTypeReferences)
            ->get();

        foreach ($adjustments as $adjustment) {
            $amount = (float) $adjustment->discount_amount;

            if ($adjustment->discount_type === 'Assembly Discount') {
                $discounts['assembly'] += $amount;
                continue;
            }

            if ($adjustment->discount_type === 'Division Discount' && $adjustment->division_id) {
                $discounts['division'][$adjustment->division_id] = ($discounts['division'][$adjustment->division_id] ?? 0) + $amount;
                continue;
            }

            if ($adjustment->discount_type === 'Block Discount' && $adjustment->block_id) {
                $discounts['block'][$adjustment->block_id] = ($discounts['block'][$adjustment->block_id] ?? 0) + $amount;
                continue;
            }

            foreach ((array) $adjustment->erms_id as $ermsId) {
                $discounts['property'][$ermsId] = ($discounts['property'][$ermsId] ?? 0) + $amount;
            }
        }

        return $discounts;
    }

    private function discountFor(Property $property, array $discounts): float
    {
        $block = $property->polygon?->block;
        $division = $block?->division;

        return (float) $discounts['assembly']
            + (float) ($discounts['division'][$division?->id] ?? 0)
            + (float) ($discounts['block'][$block?->id] ?? 0)
            + (float) ($discounts['property'][$property->erms_id] ?? 0);
    }

    private function billNumber(Property $property, string $billYear): string
    {
        do {
            $billId = ($property->assembly?->assembly_code ?: $property->assembly_code ?: 'ASM')
                .'PR'
                .substr($billYear, -2)
                .($property->polygon?->block?->division?->code ?: '')
                .($property->polygon?->block?->code ?: '')
                .str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (Bill::where('bills_id', $billId)->exists());

        return $billId;
    }

    private function isRated(mixed $value): bool
    {
        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'y', 'rated'], true);
    }

    private function money(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        return (float) str_replace(',', '', (string) $value);
    }
}
