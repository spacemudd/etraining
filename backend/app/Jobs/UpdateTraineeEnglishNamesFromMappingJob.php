<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Back\Trainee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateTraineeEnglishNamesFromMappingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Chunk of identity_number => english_name mapping.
     *
     * @var array<string, string>
     */
    protected array $mappingChunk;

    public int $timeout = 600;

    public int $tries = 2;

    /**
     * Create a new job instance.
     *
     * @param  array<string, string>  $mappingChunk
     */
    public function __construct(array $mappingChunk)
    {
        $this->mappingChunk = $mappingChunk;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $updated = 0;
        $notFound = 0;

        foreach ($this->mappingChunk as $identityNumber => $englishName) {
            $identityNumber = trim((string) $identityNumber);
            $englishName = trim((string) $englishName);

            if ($identityNumber === '' || $englishName === '') {
                continue;
            }

            $affected = Trainee::where('identity_number', $identityNumber)
                ->update(['english_name' => $englishName]);

            if ($affected > 0) {
                $updated += $affected;
            } else {
                $notFound++;
            }
        }

        Log::info('UpdateTraineeEnglishNamesFromMappingJob completed', [
            'chunk_size' => count($this->mappingChunk),
            'updated' => $updated,
            'not_found' => $notFound,
        ]);
    }
}
