<?php

namespace App\Console\Commands;

use App\Models\Back\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateInvoiceFromToDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $invoicesIds = Invoice::whereIn('id', [
            '050bcaef-e494-4c01-acc7-cc820e65cc0c',
            '06893e53-8d59-4364-9c8e-7a1b838f5a96',
            '0b186fb3-a3c9-447a-9d34-f43cace17c34',
            '33f3c0b1-674c-4c5a-90c5-0d5a94b07efc',
            '44d2aa77-74ec-4957-b1c7-a01265c55f46',
            '4a276b95-997d-4f15-a5fb-66663e786ce3',
            '52493798-1ab3-4873-804b-2a8311c5b05c',
            '67b84722-a018-4339-95ea-ca8c4fad397e',
            'ccaf17dc-a4e0-4585-bd6b-936b6936aceb',
//            '000000000000000000000000000000000000',
//            '000000000000000000000000000000000000',
//            '000000000000000000000000000000000000',
//            '000000000000000000000000000000000000',
//            '000000000000000000000000000000000000',
//            '000000000000000000000000000000000000',
//            '000000000000000000000000000000000000',
//            '000000000000000000000000000000000000',
//            '000000000000000000000000000000000000',
//            '000000000000000000000000000000000000',
//            '000000000000000000000000000000000000',
//            '000000000000000000000000000000000000',
//            '000000000000000000000000000000000000',
//            '000000000000000000000000000000000000',
//            '000000000000000000000000000000000000',
//            '000000000000000000000000000000000000',
//            '000000000000000000000000000000000000',
//            '000000000000000000000000000000000000',
//            '000000000000000000000000000000000000',
//            '000000000000000000000000000000000000',
        ])->get();

        $this->info('Found: '.$invoicesIds->count());
        $FromToDate = ['from_date' => '2022-09-01', 'to_date' => '2022-09-30'];

        DB::beginTransaction();
        foreach ($invoicesIds as $invoiceId) {
            $invoiceId->update($FromToDate);
        }
        DB::commit();

        $this->info('Updated: '.$invoicesIds->count());

        return 1;
    }
}
