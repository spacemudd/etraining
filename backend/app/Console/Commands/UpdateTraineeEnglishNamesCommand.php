<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Data\IdentityEnglishNameMapping;
use App\Jobs\UpdateTraineeEnglishNamesFromMappingJob;
use Illuminate\Console\Command;

class UpdateTraineeEnglishNamesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trainees:update-english-names-from-mapping
                            {--chunk=500 : Number of identities per job}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update trainee english_name from identity-to-name mapping via background jobs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $mapping = IdentityEnglishNameMapping::getMapping();

        $mapping = array_filter($mapping, function ($englishName, $identityNumber) {
            return trim((string) $identityNumber) !== '' && trim((string) $englishName) !== '';
        }, ARRAY_FILTER_USE_BOTH);

        if ($mapping === []) {
            $this->warn('المصفوفة فارغة، أضف أزواج الهوية والاسم في App\Data\IdentityEnglishNameMapping ثم شغّل الأمر مرة أخرى.');
            return self::FAILURE;
        }

        $chunkSize = (int) $this->option('chunk');
        $chunkSize = $chunkSize > 0 ? $chunkSize : 500;

        $chunks = array_chunk($mapping, $chunkSize, true);
        $totalIdentities = count($mapping);
        $jobsCount = count($chunks);

        foreach ($chunks as $chunk) {
            UpdateTraineeEnglishNamesFromMappingJob::dispatch($chunk);
        }

        $this->info("تم إطلاق {$jobsCount} مهمة خلفية لتحديث {$totalIdentities} هوية. تأكد من تشغيل queue worker: php artisan queue:work");

        return self::SUCCESS;
    }
}
