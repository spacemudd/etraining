<?php

namespace App\Console\Commands;

use App\Models\Back\Trainee;
use Carbon\Carbon;
use Illuminate\Console\Command;

class InvoiceToPaidCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:paid';

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
        $data = [
            'وعد سعود مسفر القحطاني' => 'chg_LV02G2020231300Xx831104316',
            'أسماء نزال دهيم العتيبي' => 'chg_LV05G0420231300Xb381104887',
        ];

        foreach ($data as $name => $payment_reference_id) {
            $trainee = Trainee::where('name', $name)->first();
            if ($trainee) {

                if ($trainee->invoices()->notPaid()->count() > 1) {
                    $this->info('More than 1 invoice: '.$name);
                    continue;
                }

                $trainee->invoices()->notPaid()->first()->update([
                   'status' => 1,
                    'payment_method' => 1,
                    'payment_reference_id' => $payment_reference_id,
                    'paid_at' => Carbon::now(),
                    'trainee_bank_payment_receipt_id' => null,
                ]);
            }
        }

        //$data = ['status' => 1, 'payment_method' => 1, 'payment_reference_id' => 'chg_LV03G5420232233Ze8b1004061', 'paid_at' => '2023-04-10 10:35:00', 'trainee_bank_payment_receipt_id' => null]
        return 1;
    }
}
