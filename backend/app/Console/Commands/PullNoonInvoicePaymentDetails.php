<?php

namespace App\Console\Commands;

use App\Models\Back\Invoice;
use App\Services\NoonService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PullNoonInvoicePaymentDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'noon:invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates invoices with payment details from noon (payment_method, payment_brand)';

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

        $invoices = [
'9682538925904559',
'9682420211256204',
'9682936176653224',
'9682400984460819',
'9682211537579420',
'9682940573464206',
'9682819581277442',
'9682813199610040',
'9682663549301181',
'9682677930513045',
'9682830497001404',
'9682834613635236',
'9682699235027721',
'9682836788311472',
'9682800500463540',
'9682542755189934',
'9682250583332653',
'9682572716290106',
'9682697154046110',
'9682420754657343',
'9682228647904780',
'9682830414378295',
'9682710826087552',
'9682937541800883',
'9682212004937912',
'9682664796048529',
'9682972436231482',
'9682249037664428',
'9682523630278200',
'9682562380524706',
'9682991625541297',
'9682227373051827',
'9682525476941725',
'9682383855955383',
'9682423132547458',
'9682133141342653',
'9682175749529663',
'9682298042963352',
'9682424438297749',
'9682956919400864',
'9682147401174300',
'9682832897404461',
'9682441470168355',
'9682527886610362',
'9682936217515162',
'9682813257394177',
'9682190612849042',
'9682401287475488',
'9682950842995109',
'9682573291710512',
'9682390551081501',
'9682284902779291',
'9682280811263632',
'9682695024252198',
'9682167415064856',
'9682523289547020',
'9682424713808188',
'9682834563313451',
'9682246096235011',
'9682521411555337',
'9682817830456344',
'9682440245472272',
'9682710725423361',
'9682813403369861',
'9682968309508762',
'9682388708863374',
'9682662660894907',
'9682665479321000',
'9682298480151580',
'9682954839282697',
'9682716782996026',
'9682574684065538',
'9682985436481539',
'9682715158841908',
'9682270010173444',
'9682150350735371',
'9682265563237547',
'9682246825071541',
'9682543003561143',
'9682387837351199',
'9682957153618050',
'9682418284941488',
'9682207276785705',
'9682819731353991',
'9682132772303628',
'9682390585043486',
'9682813940075161',
'9682263747991055',
'9682418839890110',
'9682163021949443',
'9682834926392462',
'9682697003062810',
'9682390972559669',
'9682697168637100',
'9682850414900367',
'9682167296741132',
'9682542500579645',
'9682229251195809',
'9682187980006968',
'9682695023955850',
'9682128609474231',
'9682170881114122',
'9682819579325328',
'9682555503652532',
'9682205493291958',
'9682573056036224',
        ];



        $url = 'https://prod.jisr-ksa.com/noon';

        foreach ($invoices as $ok => $in) {
            $response = Http::post($url, json_decode('{"orderId": '.$in.',"orderStatus": "CAPTURED","eventType": "Sale","eventId": "da42a2e9-397b-4d50-b339-53e9d751a9e0","timeStamp": "2024-12-15T08:56:38.5732066Z","signature": "ZDYoQ+punJ8llb6ZwkG3XW4KIfORIH0DPRyU1Nxip5q4s/Uy8aqU5FmvqTf0HNRtjeohp0sdQaBeO55FslcYUA=="}', true));
            $this->info('Finish: '.$ok);
        }


        return 1;


        $q = Invoice::whereRaw('LENGTH(payment_reference_id) < 20')
            ->where('payment_detail_brand', null)
            ->paid();

        $bar = $this->output->createProgressBar($q->count());

        $bar->start();

        $q->chunk(20, function ($invoices) use (&$bar) {
                foreach ($invoices as $invoice) {
                    $noon = app()->make(NoonService::class)->getOrder($invoice->payment_reference_id);
                    $invoice->update([
                        'payment_detail_method' => $noon->result->paymentDetails->mode,
                        'payment_detail_brand' => $noon->result->paymentDetails->brand,
                    ]);
                    $bar->advance();
                }
            });

        return 1;
    }
}
