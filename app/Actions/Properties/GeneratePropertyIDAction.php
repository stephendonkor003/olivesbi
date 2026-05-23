<?php

namespace App\Actions\Properties;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class GeneratePropertyIDAction
{
    /**
     * @throws InvalidArgumentException
     */
    public function execute($assemblyCode, $divisionCode, $blockCode): string
    {
        $prefix = $assemblyCode.$divisionCode.Str::after($blockCode, '_');

        $propertiesCount = DB::table('properties')
            ->join('polygons', 'properties.polygon_id', '=', 'polygons.id')
            ->join('blocks', 'polygons.block_id', '=', 'blocks.id')
            ->join('divisions', 'blocks.division_id', '=', 'divisions.id')
            ->where('properties.assembly_code', $assemblyCode)
            ->where('divisions.code', $divisionCode)
            ->where('blocks.code', $blockCode)
            ->whereNull('properties.deleted_on')
            ->count();

        $businessesCount = DB::table('businesses')
            ->join('polygons', 'businesses.polygon_id', '=', 'polygons.id')
            ->join('blocks', 'polygons.block_id', '=', 'blocks.id')
            ->join('divisions', 'blocks.division_id', '=', 'divisions.id')
            ->where('businesses.assembly_code', $assemblyCode)
            ->where('divisions.code', $divisionCode)
            ->where('blocks.code', $blockCode)
            ->whereNull('businesses.deleted_on')
            ->count();

        $totalPropertyCount = $propertiesCount + $businessesCount + 1;

        $code = $prefix.Str::padLeft($totalPropertyCount, 3, 0);

        while (
            DB::table('properties')->where('erms_id', $code)->exists()
            || DB::table('businesses')->where('erms_id', $code)->exists()
        ) {
            if ($totalPropertyCount >= 1_000) {
                throw new InvalidArgumentException('There are more than 1000 properties associated with this area.');
            }

            $code = $prefix.Str::padLeft($totalPropertyCount, 3, 0);

            $totalPropertyCount++;
        }

        return $code;
    }

    /**
     * Generate ERMS IDs for a batch of records
     *
     * @param  array  $records  Array of objects with polygon_id
     * @param  array  $polygonMapWithCodes  Map of polygon_id => object with division_code and block_code
     * @param  array  &$blockCounts  Reference to block counts array to maintain state across batches
     * @return array Map of record_id => erms_id
     */
    public function executeBatch(string $assemblyCode, array $records, array $polygonMapWithCodes, array &$blockCounts): array
    {
        $ermsIds = [];

        // Group records by block
        $recordsByBlock = [];
        foreach ($records as $record) {
            if (isset($polygonMapWithCodes[$record->old_polygon_id])) {
                $polygon = $polygonMapWithCodes[$record->old_polygon_id];
                $blockKey = $polygon->division_code.'|'.$polygon->block_code;

                if (! isset($recordsByBlock[$blockKey])) {
                    $recordsByBlock[$blockKey] = [
                        'division_code' => $polygon->division_code,
                        'block_code' => $polygon->block_code,
                        'records' => [],
                    ];
                }

                $recordsByBlock[$blockKey]['records'][] = $record;
            }
        }

        // Generate IDs for each block group
        foreach ($recordsByBlock as $blockKey => $blockData) {
            // Get or initialize count for this block
            if (! isset($blockCounts[$blockKey])) {
                $blockCounts[$blockKey] = $this->getBlockCount(
                    $assemblyCode,
                    $blockData['division_code'],
                    $blockData['block_code']
                );
            }

            $prefix = $assemblyCode.$blockData['division_code'].Str::after($blockData['block_code'], '_');

            foreach ($blockData['records'] as $record) {
                $blockCounts[$blockKey]++;
                $ermsId = $prefix.Str::padLeft($blockCounts[$blockKey], 3, '0');
                $ermsIds[$record->id] = $ermsId;
            }
        }

        return $ermsIds;
    }

    /**
     * Get the current count of properties and businesses for a block
     */
    protected function getBlockCount(string $assemblyCode, string $divisionCode, string $blockCode): int
    {
        $propertiesCount = DB::table('properties')
            ->join('polygons', 'properties.polygon_id', '=', 'polygons.id')
            ->join('blocks', 'polygons.block_id', '=', 'blocks.id')
            ->join('divisions', 'blocks.division_id', '=', 'divisions.id')
            ->where('properties.assembly_code', $assemblyCode)
            ->where('divisions.code', $divisionCode)
            ->where('blocks.code', $blockCode)
            ->whereNull('properties.deleted_on')
            ->count();

        $businessesCount = DB::table('businesses')
            ->join('polygons', 'businesses.polygon_id', '=', 'polygons.id')
            ->join('blocks', 'polygons.block_id', '=', 'blocks.id')
            ->join('divisions', 'blocks.division_id', '=', 'divisions.id')
            ->where('businesses.assembly_code', $assemblyCode)
            ->where('divisions.code', $divisionCode)
            ->where('blocks.code', $blockCode)
            ->whereNull('businesses.deleted_on')
            ->count();

        return $propertiesCount + $businessesCount;
    }
}
