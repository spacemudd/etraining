<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\PaymentOutageInterest;
use Illuminate\Console\Command;

class ExportPaymentOutageInterestCommand extends Command
{
    protected $signature = 'etraining:payment-outage-interest {--csv : Print CSV to stdout}';

    protected $description = 'List trainees who hit card payment during the gateway outage (for later WhatsApp follow-up)';

    public function handle(): int
    {
        $rows = PaymentOutageInterest::query()
            ->with(['trainee' => function ($q) {
                $q->withTrashed();
            }])
            ->orderBy('last_seen_at')
            ->get();

        if ($rows->isEmpty()) {
            $this->info('No trainees recorded.');

            return 0;
        }

        $table = $rows->map(function (PaymentOutageInterest $row) {
            return [
                'trainee_id' => $row->trainee_id,
                'name' => optional($row->trainee)->name,
                'phone' => optional($row->trainee)->phone,
                'invoice_id' => $row->invoice_id,
                'last_seen_at' => optional($row->last_seen_at)->toDateTimeString(),
                'notified_at' => optional($row->notified_at)->toDateTimeString(),
            ];
        })->all();

        if ($this->option('csv')) {
            $this->line('trainee_id,name,phone,invoice_id,last_seen_at,notified_at');
            foreach ($table as $row) {
                $this->line(implode(',', array_map(static function ($value) {
                    $value = (string) $value;

                    return str_contains($value, ',') ? '"'.str_replace('"', '""', $value).'"' : $value;
                }, $row)));
            }

            return 0;
        }

        $this->info('Count: '.$rows->count());
        $this->table(array_keys($table[0]), $table);

        return 0;
    }
}
