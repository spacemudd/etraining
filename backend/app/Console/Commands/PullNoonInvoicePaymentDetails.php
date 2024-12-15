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


        $url = 'https://prod.jisr-ksa.com/noon';
        $response = Http::post($url, json_decode('{"orderId": 9682538925904559,"orderStatus": "CAPTURED","eventType": "Sale","eventId": "da42a2e9-397b-4d50-b339-53e9d751a9e0","timeStamp": "2024-12-15T08:56:38.5732066Z","signature": "ZDYoQ+punJ8llb6ZwkG3XW4KIfORIH0DPRyU1Nxip5q4s/Uy8aqU5FmvqTf0HNRtjeohp0sdQaBeO55FslcYUA=="}', true));








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
