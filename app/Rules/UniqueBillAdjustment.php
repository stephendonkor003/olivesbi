<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class UniqueBillAdjustment implements Rule
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function passes($attribute, $value)
    {
        $revenueStreamId = Arr::get($this->data, 'revenue_stream_type_id');
        $discountYear = Arr::get($this->data, 'discount_year');
        $assemblyCode = Arr::get($this->data, 'assembly_code');
        $divisionId = Arr::get($this->data, 'division_id');
        $blockId = Arr::get($this->data, 'block_id');
        $ermsId = Arr::get($this->data, 'erms_id');

        // If required fields are missing, let other validation handle it
        if (! $revenueStreamId || ! $discountYear || ! $assemblyCode) {
            return true;
        }

        // Base query for common fields
        $baseQuery = DB::table('bill_adjustments')
            ->where('revenue_stream_type_id', $revenueStreamId)
            ->where('discount_year', $discountYear)
            ->where('assembly_code', $assemblyCode);

        // Check for ANY existing record with the same base combination
        // This will catch all 4 scenarios and prevent conflicts
        $existingRecords = (clone $baseQuery)->get();

        if ($existingRecords->isEmpty()) {
            return true; // No conflicts, allow creation
        }

        // Determine what type of record we're trying to create
        $currentRecordType = $this->getRecordType($divisionId, $blockId, $ermsId);

        // Check each existing record for conflicts
        foreach ($existingRecords as $existing) {
            $existingRecordType = $this->getRecordType(
                $existing->division_id,
                $existing->block_id,
                $existing->erms_id
            );

            // If we're trying to create the exact same combination, it's a duplicate
            if ($this->isSameCombination($currentRecordType, $divisionId, $blockId, $ermsId, $existing)) {
                return false;
            }

            // ANY existing combination conflicts with ANY other different combination
            // Only allow if it's the exact same record (which should be handled by ID exclusion elsewhere)
            if ($currentRecordType !== $existingRecordType) {
                return false;
            }
        }

        return true; // No conflicts found
    }

    /**
     * Check if we're trying to create the exact same combination as existing record
     */
    private function isSameCombination($currentType, $divisionId, $blockId, $ermsId, $existing)
    {
        switch ($currentType) {
            case 'base':
                return is_null($existing->division_id) && is_null($existing->block_id) && is_null($existing->erms_id);
            case 'division':
                return $existing->division_id == $divisionId && is_null($existing->block_id) && is_null($existing->erms_id);
            case 'block':
                return $existing->block_id == $blockId && is_null($existing->division_id) && is_null($existing->erms_id);
            case 'erms':
                return $existing->erms_id == $ermsId && is_null($existing->division_id) && is_null($existing->block_id);
            default:
                return false;
        }
    }

    /**
     * Determine the type of record based on which optional fields are present
     */
    private function getRecordType($divisionId, $blockId, $ermsId)
    {
        if ($ermsId) {
            return 'erms';
        }

        if ($blockId) {
            return 'block';
        }

        if ($divisionId) {
            return 'division';
        }

        return 'base'; // assembly only
    }

    public function message()
    {
        return 'A bill adjustment with this Revenue Stream, Year, and Assembly combination already exists. Only one type of adjustment (base, division, block, or ERMS) is allowed per combination.';
    }
}
