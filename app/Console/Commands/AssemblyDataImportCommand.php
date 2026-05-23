<?php

namespace App\Console\Commands;

use App\Actions\Properties\GeneratePropertyIDAction;
use App\Models\Assembly;
use App\Models\Block;
use App\Models\Division;
use App\Models\Owner;
use App\Models\Polygon;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function Laravel\Prompts\search;

class AssemblyDataImportCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:data-import
                            {assembly : The assembly code of the assembly you want to import data for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import assembly data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $assemblyCode = $this->argument('assembly');

        $assembly = Assembly::where('assembly_code', $assemblyCode)->exists();

        if (! $assembly) {
            $this->error($assemblyCode.' does not exist.');

            return Command::FAILURE;
        }

        // Initialize shared block counts tracker (used by both businesses and properties)
        $blockCounts = [];

        DB::transaction(function () use ($assemblyCode, &$blockCounts) {
            // 1. Update divisions ERMS IDs
            $this->updateDivisions($assemblyCode);

            // 2. Update blocks division_id and ERMS IDs
            $this->updateBlocks($assemblyCode);

            // 3. Update polygons block_id and ERMS IDs
            $this->updatePolygons($assemblyCode);

            // 4. Update owners ERMS IDs
            $this->updateOwners($assemblyCode);

            // 5. Update businesses owner_id and polygon_id
            $this->updateBusinesses($assemblyCode, $blockCounts);

            // 6. Update properties data
            $this->updateProperties($assemblyCode, $blockCounts);
        });

        $this->info('Data import completed successfully for assembly: '.$assemblyCode);

        return Command::SUCCESS;
    }

    protected function updateDivisions(string $assemblyCode): void
    {
        $this->info('Updating divisions...');

        $divisions = Division::where('assembly_code', $assemblyCode)
            ->whereNull('erms_id')
            ->orderBy('id')
            ->get();

        if ($divisions->isEmpty()) {
            $this->line('  No divisions to update');

            return;
        }

        $cases = [];
        $ids = [];

        foreach ($divisions as $index => $division) {
            $ermsId = $assemblyCode.($index + 1);
            $cases[] = "WHEN {$division->id} THEN ".DB::connection()->getPdo()->quote($ermsId);
            $ids[] = $division->id;
        }

        if (! empty($cases)) {
            DB::update('
                UPDATE divisions
                SET erms_id = CASE id
                    '.implode(' ', $cases).'
                END
                WHERE id IN ('.implode(',', $ids).')
            ');

            $this->line("  Updated {$divisions->count()} divisions");
        }
    }

    protected function updateBlocks(string $assemblyCode): void
    {
        $this->info('Updating blocks...');

        // Pre-load division old_id to id mapping
        $divisionMap = Division::where('assembly_code', $assemblyCode)
            ->get()
            ->keyBy('old_id')
            ->map(fn ($d) => $d->id)
            ->toArray();

        $blocks = Block::where('assembly_code', $assemblyCode)
            ->where(function ($query) {
                $query->whereNull('erms_id')
                    ->orWhereNull('division_id')
                    ->orWhere('division_id', 0);
            })
            ->orderBy('old_division_id')
            ->orderBy('id')
            ->get();

        if ($blocks->isEmpty()) {
            $this->line('  No blocks to update');

            return;
        }

        $ermsIdCases = [];
        $divisionIdCases = [];
        $ids = [];

        // Group blocks by division for sequential numbering
        $blocksByDivision = $blocks->groupBy('old_division_id');

        foreach ($blocksByDivision as $oldDivisionId => $divisionBlocks) {
            $divisionIndex = Division::where('assembly_code', $assemblyCode)
                ->where('old_id', $oldDivisionId)
                ->value('id');

            $divisionNumber = Division::where('assembly_code', $assemblyCode)
                ->where('id', '<=', $divisionIndex)
                ->count();

            foreach ($divisionBlocks as $blockIndex => $block) {
                $blockNumber = $blockIndex + 1;
                $ermsId = "{$assemblyCode}/{$divisionNumber}/{$blockNumber}";

                if (is_null($block->erms_id)) {
                    $ermsIdCases[] = "WHEN {$block->id} THEN ".DB::connection()->getPdo()->quote($ermsId);
                }

                if ((is_null($block->division_id) || $block->division_id == 0) && isset($divisionMap[$block->old_division_id])) {
                    $divisionIdCases[] = "WHEN {$block->id} THEN {$divisionMap[$block->old_division_id]}";
                }

                $ids[] = $block->id;
            }
        }

        if (! empty($ids)) {
            $setParts = [];

            if (! empty($ermsIdCases)) {
                $setParts[] = 'erms_id = CASE id '.implode(' ', $ermsIdCases).' ELSE erms_id END';
            }

            if (! empty($divisionIdCases)) {
                $setParts[] = 'division_id = CASE id '.implode(' ', $divisionIdCases).' ELSE division_id END';
            }

            if (! empty($setParts)) {
                DB::update('
                    UPDATE blocks
                    SET '.implode(', ', $setParts).'
                    WHERE id IN ('.implode(',', array_unique($ids)).')
                ');

                $this->line("  Updated {$blocks->count()} blocks");
            }
        }
    }

    protected function updatePolygons(string $assemblyCode): void
    {
        $this->info('Updating polygons...');

        // Pre-load block old_id to id mapping
        $blockMap = Block::where('assembly_code', $assemblyCode)
            ->get()
            ->keyBy('old_id')
            ->map(fn ($b) => $b->id)
            ->toArray();

        $totalPolygons = Polygon::where('assembly_code', $assemblyCode)
            ->where(function ($query) {
                $query->whereNull('erms_id')
                    ->orWhereNull('block_id');
            })
            ->count();

        if ($totalPolygons === 0) {
            $this->line('  No polygons to update');

            return;
        }

        $bar = $this->output->createProgressBar($totalPolygons);
        $bar->start();

        $chunkSize = 1000;
        $processed = 0;

        Polygon::where('assembly_code', $assemblyCode)
            ->where(function ($query) {
                $query->whereNull('erms_id')
                    ->orWhereNull('block_id');
            })
            ->orderBy('id')
            ->chunk($chunkSize, function ($polygons) use ($assemblyCode, $blockMap, $bar, &$processed) {
                $ermsIdCases = [];
                $blockIdCases = [];
                $ids = [];

                foreach ($polygons as $index => $polygon) {
                    $globalIndex = $processed + $index + 1;

                    if (is_null($polygon->erms_id)) {
                        $ermsId = "{$assemblyCode}{$globalIndex}";
                        $ermsIdCases[] = "WHEN {$polygon->id} THEN ".DB::connection()->getPdo()->quote($ermsId);
                    }

                    if (is_null($polygon->block_id) && isset($blockMap[$polygon->old_block_id])) {
                        $blockIdCases[] = "WHEN {$polygon->id} THEN {$blockMap[$polygon->old_block_id]}";
                    }

                    $ids[] = $polygon->id;
                }

                if (! empty($ids)) {
                    $setParts = [];

                    if (! empty($ermsIdCases)) {
                        $setParts[] = 'erms_id = CASE id '.implode(' ', $ermsIdCases).' ELSE erms_id END';
                    }

                    if (! empty($blockIdCases)) {
                        $setParts[] = 'block_id = CASE id '.implode(' ', $blockIdCases).' ELSE block_id END';
                    }

                    if (! empty($setParts)) {
                        DB::update('
                            UPDATE polygons
                            SET '.implode(', ', $setParts).'
                            WHERE id IN ('.implode(',', $ids).')
                        ');
                    }
                }

                $processed += count($polygons);
                $bar->advance(count($polygons));
            });

        $bar->finish();
        $this->newLine();
        $this->line("  Updated {$totalPolygons} polygons");
    }

    protected function updateOwners(string $assemblyCode): void
    {
        $this->info('Updating owners...');

        $owners = Owner::where('assembly_code', $assemblyCode)
            ->whereNull('erms_id')
            ->orderBy('id')
            ->get();

        if ($owners->isEmpty()) {
            $this->line('  No owners to update');

            return;
        }

        $cases = [];
        $ids = [];

        foreach ($owners as $index => $owner) {
            $ermsId = 'ERMS'.Str::padLeft($index + 1, 4, '0');
            $cases[] = "WHEN {$owner->id} THEN ".DB::connection()->getPdo()->quote($ermsId);
            $ids[] = $owner->id;
        }

        if (! empty($cases)) {
            DB::update('
                UPDATE entity_owners
                SET erms_id = CASE id
                    '.implode(' ', $cases).'
                END
                WHERE id IN ('.implode(',', $ids).')
            ');

            $this->line("  Updated {$owners->count()} owners");
        }
    }

    protected function updateBusinesses(string $assemblyCode, array &$blockCounts): void
    {
        $this->info('Updating businesses...');

        // Preload owners old_id to erms_id mapping
        $ownerMap = DB::table('entity_owners')
            ->where('assembly_code', $assemblyCode)
            ->pluck('erms_id', 'old_id')
            ->toArray();

        // Preload polygon old_id to id mapping with block and division codes
        $polygonMap = DB::table('polygons')
            ->join('blocks', 'polygons.block_id', '=', 'blocks.id')
            ->join('divisions', 'blocks.division_id', '=', 'divisions.id')
            ->where('polygons.assembly_code', $assemblyCode)
            ->select(['polygons.id', 'polygons.old_id', 'divisions.code as division_code', 'blocks.code as block_code'])
            ->get()
            ->keyBy('old_id')
            ->toArray();

        $totalBusinesses = DB::table('businesses')
            ->where('assembly_code', $assemblyCode)
            ->where(function ($query) {
                $query->whereNull('erms_id')
                    ->orWhereNull('polygon_id')
                    ->orWhereNull('owner_id');
            })
            ->count();

        if ($totalBusinesses === 0) {
            $this->line('  No businesses to update');

            return;
        }

        $bar = $this->output->createProgressBar($totalBusinesses);
        $bar->start();

        $chunkSize = 1000;
        $processed = 0;
        $generatePropertyIDAction = new GeneratePropertyIDAction;

        DB::table('businesses')
            ->where('assembly_code', $assemblyCode)
            ->where(function ($query) {
                $query->whereNull('erms_id')
                    ->orWhereNull('polygon_id')
                    ->orWhereNull('owner_id');
            })
            ->orderBy('id')
            ->chunk($chunkSize, function ($businesses) use ($assemblyCode, $polygonMap, $ownerMap, $bar, &$processed, &$blockCounts, $generatePropertyIDAction) {
                $ermsIdCases = [];
                $polygonIdCases = [];
                $ownerIdCases = [];
                $ids = [];

                // Convert to array for batch processing
                $businessArray = iterator_to_array($businesses);

                // Generate ERMS IDs for this batch
                $ermsIds = $generatePropertyIDAction->executeBatch(
                    $assemblyCode,
                    $businessArray,
                    $polygonMap,
                    $blockCounts
                );

                foreach ($businesses as $business) {
                    // Set ERMS ID
                    if (is_null($business->erms_id) && isset($ermsIds[$business->id])) {
                        $ermsIdCases[] = "WHEN {$business->id} THEN ".DB::connection()->getPdo()->quote($ermsIds[$business->id]);
                    }

                    // Set polygon_id
                    if (is_null($business->polygon_id) && isset($polygonMap[$business->old_polygon_id])) {
                        $polygonIdCases[] = "WHEN {$business->id} THEN {$polygonMap[$business->old_polygon_id]->id}";
                    }

                    // Set owner_id
                    if (is_null($business->owner_id) && isset($ownerMap[$business->old_owner_id])) {
                        $ownerIdCases[] = "WHEN {$business->id} THEN ".DB::connection()->getPdo()->quote($ownerMap[$business->old_owner_id]);
                    }

                    $ids[] = $business->id;
                }

                if (! empty($ids)) {
                    $setParts = [];

                    if (! empty($ermsIdCases)) {
                        $setParts[] = 'erms_id = CASE id '.implode(' ', $ermsIdCases).' ELSE erms_id END';
                    }

                    if (! empty($polygonIdCases)) {
                        $setParts[] = 'polygon_id = CASE id '.implode(' ', $polygonIdCases).' ELSE polygon_id END';
                    }

                    if (! empty($ownerIdCases)) {
                        $setParts[] = 'owner_id = CASE id '.implode(' ', $ownerIdCases).' ELSE owner_id END';
                    }

                    if (! empty($setParts)) {
                        DB::update('
                            UPDATE businesses
                            SET '.implode(', ', $setParts).'
                            WHERE id IN ('.implode(',', $ids).')
                        ');
                    }
                }

                $processed += count($businesses);
                $bar->advance(count($businesses));
            });

        $bar->finish();
        $this->newLine();
        $this->line("  Updated {$totalBusinesses} businesses");
    }

    protected function updateProperties(string $assemblyCode, array &$blockCounts): void
    {
        $this->info('Updating properties...');

        // Pre-load owners old_id to erms_id mapping
        $ownerMap = DB::table('entity_owners')
            ->where('assembly_code', $assemblyCode)
            ->pluck('erms_id', 'old_id')
            ->toArray();

        // Pre-load polygon old_id to id mapping with block and division codes
        $polygonMap = DB::table('polygons')
            ->join('blocks', 'polygons.block_id', '=', 'blocks.id')
            ->join('divisions', 'blocks.division_id', '=', 'divisions.id')
            ->where('polygons.assembly_code', $assemblyCode)
            ->select(['polygons.id', 'polygons.old_id', 'divisions.code as division_code', 'blocks.code as block_code'])
            ->get()
            ->keyBy('old_id')
            ->toArray();

        $totalProperties = DB::table('properties')
            ->where('assembly_code', $assemblyCode)
            ->where(function ($query) {
                $query->whereNull('erms_id')
                    ->orWhereNull('polygon_id')
                    ->orWhereNull('owner_id');
            })
            ->count();

        if ($totalProperties === 0) {
            $this->line('  No properties to update');

            return;
        }

        $bar = $this->output->createProgressBar($totalProperties);
        $bar->start();

        $chunkSize = 1000;
        $processed = 0;
        $generatePropertyIDAction = new GeneratePropertyIDAction;

        DB::table('properties')
            ->where('assembly_code', $assemblyCode)
            ->where(function ($query) {
                $query->whereNull('erms_id')
                    ->orWhereNull('polygon_id')
                    ->orWhereNull('owner_id');
            })
            ->orderBy('id')
            ->chunk($chunkSize, function ($properties) use ($assemblyCode, $polygonMap, $ownerMap, $bar, &$processed, &$blockCounts, $generatePropertyIDAction) {
                $ermsIdCases = [];
                $polygonIdCases = [];
                $ownerIdCases = [];
                $ids = [];

                // Convert to array for batch processing
                $propertyArray = iterator_to_array($properties);

                // Generate ERMS IDs for this batch (uses shared blockCounts with businesses)
                $ermsIds = $generatePropertyIDAction->executeBatch(
                    $assemblyCode,
                    $propertyArray,
                    $polygonMap,
                    $blockCounts
                );

                foreach ($properties as $property) {
                    // Set ERMS ID
                    if (is_null($property->erms_id) && isset($ermsIds[$property->id])) {
                        $ermsIdCases[] = "WHEN {$property->id} THEN ".DB::connection()->getPdo()->quote($ermsIds[$property->id]);
                    }

                    // Set polygon_id
                    if (is_null($property->polygon_id) && isset($polygonMap[$property->old_polygon_id])) {
                        $polygonIdCases[] = "WHEN {$property->id} THEN {$polygonMap[$property->old_polygon_id]->id}";
                    }

                    // Set owner_id
                    if (is_null($property->owner_id) && isset($ownerMap[$property->old_owner_id])) {
                        $ownerIdCases[] = "WHEN {$property->id} THEN ".DB::connection()->getPdo()->quote($ownerMap[$property->old_owner_id]);
                    }

                    $ids[] = $property->id;
                }

                if (! empty($ids)) {
                    $setParts = [];

                    if (! empty($ermsIdCases)) {
                        $setParts[] = 'erms_id = CASE id '.implode(' ', $ermsIdCases).' ELSE erms_id END';
                    }

                    if (! empty($polygonIdCases)) {
                        $setParts[] = 'polygon_id = CASE id '.implode(' ', $polygonIdCases).' ELSE polygon_id END';
                    }

                    if (! empty($ownerIdCases)) {
                        $setParts[] = 'owner_id = CASE id '.implode(' ', $ownerIdCases).' ELSE owner_id END';
                    }

                    if (! empty($setParts)) {
                        DB::update('
                            UPDATE properties
                            SET '.implode(', ', $setParts).'
                            WHERE id IN ('.implode(',', $ids).')
                        ');
                    }
                }

                $processed += count($properties);
                $bar->advance(count($properties));
            });

        $bar->finish();
        $this->newLine();
        $this->line("  Updated {$totalProperties} properties");
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array
     */
    protected function promptForMissingArgumentsUsing()
    {
        return [
            'assembly' => fn () => search(
                label: 'Search for assembly:',
                options: fn ($value) => strlen($value) > 0
                    ? Assembly::where('assembly_code', 'like', "%{$value}%")->pluck('name', 'assembly_code')->all()
                    : [],
                hint: 'Please enter the assembly code.',
                required: true
            ),
        ];
    }
}
