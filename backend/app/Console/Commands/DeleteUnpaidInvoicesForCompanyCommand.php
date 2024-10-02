<?php

namespace App\Console\Commands;

use App\Models\Back\Company;
use App\Models\Back\Invoice;
use App\Models\Back\Trainee;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteUnpaidInvoicesForCompanyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices-company:delete';

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
        $inv_deleted = [];
        $not_deleted = [];

        $invoices = Invoice::where('trainee_id', 'c5ad08c7-89be-4569-8a34-7b73d5938df7')
            ->where('status', '!=', Invoice::STATUS_PAID)
            ->get();

        foreach ($invoices as $inv) {
            $inv->deleted_reason = 'استبعاد من الشركة';
            $inv->save();

            $inv_deleted[] = [
                'id' => $inv->trainee->identity_number,
                'trainee' => $inv->trainee->name,
                'company' => $inv->company->name_ar,
                'reason' => 'استبعاد من الشركة',
                'invoice_number' => $inv->number,
                'invoice_period' => $inv->from_date,
                'grand_total' => $inv->grand_total,
            ];

            $inv->delete();
        }

        $this->info(json_encode($inv_deleted, JSON_UNESCAPED_UNICODE));
        $this->info('----------');
        $this->info(json_encode($not_deleted, JSON_UNESCAPED_UNICODE));

        return 1;
    }
}
