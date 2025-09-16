<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Back\Invoice; 

class UbdateInvoiceToPaid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:to-paid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ubdate invoices status to paid';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $Ids = [
            'b799bcc3-8848-4fa8-b159-1a9c21541590',
            'b9bc2920-f21b-4b7b-92af-636a569354f5',
            'f14bd62e-e99e-47cf-926e-79c5ef852cd7',
  
        ];

        $updatedCount = Invoice::whereIn('id',$Ids)
                              ->update(['status' =>1 , 'updated_at'=>now()]);


        $this->info("succefully updated {$updatedCount} invoices");



    }
}
