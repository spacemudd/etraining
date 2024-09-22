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
            '16badc43-5ee9-4d60-be33-c6e75919b09d',
            '2bb5a906-982a-4e93-b7f1-a2e0c4eb2afd',
            'a35988eb-9813-4a4f-a83e-9d41736f0295',
            'ef49f8be-1b2b-437b-8bc1-a742cf7bb01d',
//            '000000000000000000000000000000000000',
        ])->get();

        $this->info('Found: '.$invoicesIds->count());
        $FromToDate = ['from_date' => '2022-12-01', 'to_date' => '2022-12-31'];

        DB::beginTransaction();
        foreach ($invoicesIds as $invoiceId) {
            $invoiceId->update($FromToDate);
        }
        DB::commit();

        $this->info('Updated: '.$invoicesIds->count());

        return 1;
    }
}
