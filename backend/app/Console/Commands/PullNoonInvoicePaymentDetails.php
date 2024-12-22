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
'9682678349876366',
'9682111532281366',
'9682175078336941',
'9682205748112808',
'9682403132844087',
'9682252677991959',
'9682438033870476',
'9682418202127136',
'9682298112003988',
'9682384428741280',
'9682132706644526',
'9682541008489232',
'9682712069793836',
'9682933931455006',
'9682578913137439',
'9682420684025768',
'9682299853754557',
'9682813139062686',
'9682525823617414',
'9682853871864722',
'9682170918471860',
'9682572751661750',
'9682798188328458',
'9682675969611528',
'9682850076167352',
'9682222457374100',
'9682682022955826',
'9682383990057406',
'9682851672888213',
'9682418047797397',
'9682561983772083',
'9682575608114228',
'9682130823302202',
'9682211604522769',
'9682264016631103',
'9682162687275658',
'9682572955209630',
'9682854725423403',
'9682251117923842',
'9682813155849147',
'9682390585043486',
'9682523630278200',
'9682710725423361',
'9682542500579645',
'9682265563237547',
'9682662660894907',
'9682968309508762',
'9682175749529663',
'9682800500463540',
'9682212004937912',
'9682441470168355',
'9682132772303628',
'9682401287475488',
'9682250583332653',
'9682167415064856',
'9682170881114122',
'9682972436231482',
'9682163021949443',
'9682715158841908',
'9682128609474231',
'9682390551081501',
'9682246825071541',
'9682677930513045',
'9682418839890110',
'9682813940075161',
'9682957153618050',
'9682424438297749',
'9682937541800883',
'9682573056036224',
'9682424713808188',
'9682420754657343',
'9682383855955383',
'9682388708863374',
'9682663549301181',
'9682813257394177',
'9682538925904559',
'9682813403369861',
'9682249037664428',
'9682190612849042',
'9682400984460819',
'9682813199610040',
'9682188737350572',
'9682817343853953',
'9682661148053764',
'9682558361300363',
'9682383806558981',
'9682574919398296',
'9682110956910497',
'9682540426146745',
'9682383658814125',
'9682207361099444',
'9682207275806105',
'9682831000464280',
'9682803122066833',
'9682222208772355',
'9682222271923119',
'9682815639597243',

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
