<?php

namespace App\Jobs\Bill;

use App\Services\PropertyRateBillGenerator;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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

            $createdBy = (string) (User::find($this->userId)?->uuid ?: $this->userId);

            $summary = app(PropertyRateBillGenerator::class)->generate(
                $this->assemblyCode,
                $this->billYear,
                $createdBy,
                ['only_unbilled' => true, 'notify' => true],
                $this->propertyIds
            );

            Log::info('Property bills chunk completed', [
                'assembly_code' => $this->assemblyCode,
                'bill_year' => $this->billYear,
                'chunk_size' => count($this->propertyIds),
                'bills_created' => $summary['created'],
                'bills_skipped' => $summary['skipped'],
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
