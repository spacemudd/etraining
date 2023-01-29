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
            '01a2541b-a356-4987-b460-64c667dd9ccb',
            '07766a88-e7c0-42c4-8069-b758cb326a65',
            '1026cc66-3f89-4216-9433-8549783f810e',
            '110735fb-1661-42ef-9f6a-c37ee86d1703',
            '14a00462-fc4e-4928-8cde-30dbf89162fe',
            '17c83986-55d4-4097-b601-a5f8175e6b2f',
            '1bd8c100-8037-4beb-824c-1f2633948961',
            '1f0fb40f-ce45-4a71-aba0-175cfd095998',
            '20f3b81e-16b6-49b3-ae50-64f20c5ea297',
            '233f2a8a-0222-4d37-84d3-b388fb93e931',
            '2434cd75-8435-43e7-8ced-e73cccdc5f7a',
            '27590059-13ac-4664-9e62-dd57ae50c0f0',
            '27940590-3d4d-4fcd-b8fe-7a0e7905d905',
            '27999130-5582-4d6b-96b0-8321e916241f',
            '30b4dd18-96ec-48ad-94a3-15cb4407c8c5',
            '36b8b429-d18e-403a-adeb-70b05fcad80a',
            '386d4b52-5a94-4590-a9cd-f456217e8c68',
            '417053cb-ebd6-4efc-a0f9-f145fb51633f',
            '4a48d166-d3b0-4f9b-ae07-b1b19f8a59a8',
            '4e01f24e-b74c-454c-a9cb-90150df09298',
            '4e08308c-832e-45bb-9bf4-ed6f0745f23b',
            '633a8edc-8bb5-451b-a334-039d0bd73e5f',
            '68c339fb-832d-455f-a74b-0840ade46e15',
            '7cd1922b-4c3e-4766-98be-feca068fc88b',
            '7d937d53-337b-43fe-8798-2fdc70b8adb3',
            '8a2066f3-1291-4753-ab70-0783a6407247',
            '965ef9bd-1dc1-429a-adb5-3dd79f03c9f5',
            '9bbd7f6e-b368-4075-b012-b3b32e30740e',
            'a0963e5e-cb77-4381-9b08-720af2caf072',
            'a63127c8-8780-4f5a-952f-49cd42d8b36e',
            'a6c21ccd-cab7-4c38-add7-212fc54112ef',
            'a8c84b32-85b5-4dc6-817c-c8cd98dd5002',
            'b0f39a4a-aae5-4a25-b83c-bb298ca400f7',
            'b5708939-84f7-4cdc-b676-2ae508bbc1ca',
            'bb334abd-4272-4641-a4da-cafc84443ed2',
            'bc32ee52-3fb9-4137-97c0-4cf3fb06d178',
            'c7d7c426-07d5-44ac-880c-244530c2cded',
            'cb4e2c53-8805-4f23-953f-aff7d4c35c15',
            'cea03730-2d09-4d52-85d0-4dc699e9b882',
            'd2642336-751e-42f8-b644-35885ebe526f',
            'd895a2ef-1205-431c-8a20-0e7b46b94f27',
            'e35f113f-ec6c-41eb-adb8-490141aa91a3',
            'eb5c1ed6-32b4-4c1d-a4f6-47b4ec66dc36',
            'f0d7afc7-3e50-4e43-905d-538b89f1f5a1',
//            '000000000000000000000000000000000000',
//            '000000000000000000000000000000000000',
//            '000000000000000000000000000000000000',
//            '000000000000000000000000000000000000',
        ])->get();

        $this->info('Found: '.$invoicesIds->count());
        $FromToDate = ['from_date' => '2022-10-01', 'to_date' => '2022-10-31'];

//        DB::beginTransaction();
//        foreach ($invoicesIds as $invoiceId) {
//            $invoiceId->update($FromToDate);
//        }
//        DB::commit();
//
//        $this->info('Updated: '.$invoicesIds->count());

        return 1;
    }
}
