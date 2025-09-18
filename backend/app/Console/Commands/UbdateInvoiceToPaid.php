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
            '5ebc2511-69e6-4017-a4f9-a2c5731f56c3',
            '70135f65-5531-4ffb-ac74-37473df576f7',
            'c02c971d-af4b-4093-90c1-ebd0875e79c3',
            'c8fa36ac-1f0b-4f9f-bd53-03a9e17597bc',
            'b691e440-d94f-4450-857d-f2b972802935',
            '5742186d-aa01-4623-a338-d5a70d6d7f65',
            '2193a9ab-295c-4fc5-80c0-d546e3c77d3b',
            '663a6cf3-e9dd-494b-af0f-4e51e6206e3e',
            'dee1adb8-a47e-421e-a3a4-7b00f4ec12df',
            '716228ac-25ba-43a4-8dd4-d1f2da8b509f',
            '275e0444-2e7f-4a2b-81cb-6713fd7061f6',
            '44a30b5e-a01e-4334-a1fd-4f87598a9def',
            '8e47f5de-bc64-4832-b344-303f6a096695',
            '502cbdc3-3df7-4dbc-aa73-ce4cbd77ecb3',
            '206fe1ee-d0c7-486f-ba68-c8ead40f8fa6',
            '10f4b4ed-230e-45a7-af8f-1bfb00953d14',
            '1d7ceb97-4f99-436e-8389-5c8801028bbd',
            '9536b74a-3913-4ac3-ac9a-c01e33a71d2c',
            '2fc80d8b-599a-4f05-8fe4-4ca18ed263f7',
            'eb819cc2-895f-4cd5-98a2-fea69bcb3e00',
            '600a9072-8b25-4aab-856a-7c3a308e0353',
            '94c8ffb0-b144-41c4-a541-3cdae15bd6fb',
            'f193b39c-de03-4b2d-9905-8e5c62dbf189',
            'c7991f95-1f87-482d-846e-326ba6f43609',
            'fdb1744e-7d3c-4815-b110-2db52d7e8ac7',
            '0d5ff7e9-9f21-4125-9e18-3eaa3557e5ba',
            'bad665e6-182d-4739-85c3-6d6b4c17b74d',
            'fd4613ab-bc3f-4755-8649-90d23b8e0480',
            '25c9b7d2-74b0-4e2d-93b9-d3498dbae27a',
            'ddc50f3d-ff95-4f62-aae3-2f50cf22588c',
            '99f46764-aa50-4f26-b9cf-4ff5cd5cb10d',
            'f290971c-52b1-4298-a7c9-b48a163c5c48',
            '180b7e41-0825-4ccc-b698-758c98766a27',
            'd2899641-7b5f-4b7c-9660-92fcf8200642',
            '4e9985c2-bb96-40bc-9563-7e13180d29dd',
            'b1073fb8-a98f-4f20-9fc9-49622c27cd43',
            'c170a985-d879-40c5-a478-e7f08021f1e6',
            '369a3e47-ae24-400a-a92e-feff52ac0c42',
            '957325f9-20f7-4b85-a11d-76890b9b0dcd',
            'c611a0b5-46b8-443c-b3b8-97b589dcc78a',
            '051d92c5-33f8-44c8-9195-b64e91df277b',
            'c15e31ac-980b-448f-adc2-cf61ce6efd5f',
            '104cdb77-e224-42c8-897f-5ab8b8dd5ee9',
            '54149c47-ee88-4618-9ce3-b3b071d6ef95',
            'c624fa83-2e0c-42ce-844c-ea8db139b57d',
            '42d04032-bc54-4aea-a3bd-7fdda46d2bc8',
            '690a24b7-aa0a-4eb7-b7da-d1ad869aa868',
            '972ca23e-0aa6-470f-9f37-7eb3eba043ed',
            '2021f777-8698-4710-aa04-6f63bc236c85',
            '138a81d2-6ddf-4a62-a1a4-a5c0b1cbaec2',
            '59a6c769-87eb-4f27-9383-b61ca6aeeb19',
            'f2d99a7c-ebd7-49c2-a418-3df93e703c10',
            '22589afd-83ba-42ac-a745-19a9744dc2eb',
            '58b1cbb4-4f76-4ac8-83ac-f19af5c413bd',
            '6abef95b-4bb9-4cff-8f0a-b1f82203e61d',
            '2c935d91-e0a1-44c0-af4b-29e4fcf2c0ec',
            '509be03d-ee11-411d-916b-362d5cf1599b',
            '87a5af49-fccc-4b66-8d36-01c908d27e61',
            '88fafd48-e91a-44b8-a38c-e03cefc2888d',
            '1e7e69b7-c419-4168-943e-7510302a5016',
            '23c1bf7c-f698-47f9-ad5f-523f6e72da68',
            'c988e7b4-6755-4de9-90b9-e0f88d0585ab',
            'd9f8f687-395d-4a67-98d3-16cab44f5a28',
            'dd69a484-f282-4a35-a8a1-8346576ea9e1',
            '291a070f-c6c4-47a3-a220-473dd9d1ea1d',
            '4f62cf57-898e-44b6-9440-660a9a30f661',
            '5de1eb74-63d1-4496-9784-0e35cfab6260',
            '1de0437c-6bbe-456b-8d93-256b2a1fde05',
            'bfa628ce-76e3-4d1f-a598-13ee9a657190',
            '625fedcb-11de-482c-bae5-87fca1af7589',
            'ffcda6f5-8fcc-413f-bba6-c02a38fb1ce7',
            'd3f2e4ac-c394-44e9-8654-75629aab2f33',
            '6968b6a4-e577-4910-9513-456b9c6b615e',
            '0a552de7-393d-43fe-9db6-ca4ac37b9a5d',
            '3573451f-b17c-4a2b-95ff-2a17be176c28',
            '126b1510-6495-4e98-8a50-5c99bdeb4644',
            '48d5a359-ce0a-44dc-a3ca-f33a34c8f366',
            '0260435a-dbc1-44bf-8880-f9155ea6ac7e',
            '936cb63d-a40b-4e61-b38c-961850d435dd',
            '2115c8b6-6b33-4e66-a79f-fe54291e42e4',
            'bea550b6-c469-4f8b-ba92-e8a4f97e373f',
            '228e3ca9-de6b-4701-a02a-c69a5b9c6d29',
            'de850485-e8f8-4a11-91ef-b18a2b543a24',
            '8600080e-b768-469a-bf16-72c7ac5dbcd8',
            '2659be2e-a1a8-4bff-a58a-da3dd54e90de',
            '5ce0f887-be01-46fc-a41e-f7be4de4e639',
            'ca880ffe-0674-403f-8137-e1e9e1589c68',
            'dc7708fa-b45d-4ed1-9516-9e8790c07813',
            '98b85384-13fd-42e6-bc94-cf40e400bafc',
            'c6ca9f78-b440-4bf5-9690-7058aacb0802',
            'dccbcba2-ccc5-4297-b4be-03f85c0cf0f7',
            '444c0c3e-7e06-439f-94e2-9822df096e19',
            'd564f3e2-b6dc-43b7-baa1-24ea0d7cfd86',
            '50e5a8a4-d268-417f-8fdf-ae46af6ad51d',
            '34e881bb-c65a-466f-ba36-cb90b98b6684',
            'cda4a5b0-a96b-4b37-8438-d83dd71a3f8c',
            '84ff2f25-aa1f-4890-941c-d0f842a14e2f',
            '95de7334-02d0-4c39-97e5-7963909a8b84',
            '388ef926-cda5-4b5e-a2a4-408e0e39b6ba',
            'c16f90c7-af2f-41d7-b8a1-7f6918f600eb',
            '56c50efd-1b33-4f4d-8a75-a15c7af328e9',
            '5793f14e-cad6-49b3-95b6-7a7e625d0961',
            '13077387-7623-446d-b758-d32caf144cc5',
            'ff2b9584-903d-425d-bc33-0a3d16f3475f',
            '408df8eb-9858-44f8-9461-1d085a59d0f0',
            'f55586ae-65d3-4071-825a-b9deae107581',
            'd564f3e2-b6dc-43b7-baa1-24ea0d7cfd86',
            '34e881bb-c65a-466f-ba36-cb90b98b6684',
            '50a996bb-15ea-4df4-8564-3d125093bd13',
            '50e5a8a4-d268-417f-8fdf-ae46af6ad51d',
            '873c9f77-c12a-407d-af70-4aecf5b3ac0c',
            'cf0b4e14-b547-4153-87dd-f984cd912c00',
            'd82195eb-be3f-4234-a0d4-979bd6cf9232',
            '98b85384-13fd-42e6-bc94-cf40e400bafc',
            '142fd24a-a652-4c3d-9b44-4631fa7cd33f',
            '0c332be9-b35e-4dd4-b4f9-abf5b4a66095',
            'e6eeb448-4aed-4445-97bd-5e556ae49854',
  
        ];

        $updatedCount = Invoice::whereIn('id',$Ids)
                              ->update(['status' =>1 , 'updated_at'=>now()]);


        $this->info("succefully updated {$updatedCount} invoices");



    }
}
