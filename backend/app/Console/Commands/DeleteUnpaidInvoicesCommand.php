<?php

namespace App\Console\Commands;

use App\Models\Back\Company;
use App\Models\Back\Invoice;
use App\Models\Back\Trainee;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteUnpaidInvoicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:delete';

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
        foreach ($this->names() as $record) {
            $company = Company::withTrashed()->where('name_ar', $record[1])->first();
            if (!$company) {
                $this->info('Error. No company found with name: ' . $record[1]);
                continue;
            }

            $trainee = Trainee::withTrashed()->where('name', $record[0])->first();
            if (!$trainee) {
                $this->info('Error. No trainee found with name: ' . $record[0]);
                continue;
            }

            $invoices = Invoice::where('trainee_id', $trainee->id)
                ->where('company_id', $company->id)
                ->where('status', '!=', Invoice::STATUS_PAID)
                ->whereBetween('from_date', [Carbon::parse('2024-08-01'), Carbon::parse('2024-08-30')])
                ->get();


            if (count($invoices) === 0) {
                $not_deleted[] = [
                    'id' => $trainee->identity_number,
                    'trainee' => $record[0],
                    'company' => $record[1],
                    'reason' => $record[2],
                    'invoice_number' => 'لم يتم العثور على الفاتورة',
                    'invoice_period' => 'لم يتم العثور على الفاتورة',
                    'grand_total' => 'لم يتم العثور على الفاتورة',
                ];
                continue;
            }

            foreach ($invoices as $inv) {
                $inv->deleted_reason = $record[2];
                $inv->save();

                $inv_deleted[] = [
                    'id' => $trainee->identity_number,
                    'trainee' => $record[0],
                    'company' => $record[1],
                    'reason' => $record[2],
                    'invoice_number' => $inv->number,
                    'invoice_period' => $inv->from_date,
                    'grand_total' => $inv->grand_total,
                ];

                $inv->delete();
            }
        }

        $this->info(json_encode($inv_deleted, JSON_UNESCAPED_UNICODE));
        $this->info('----------');
        $this->info(json_encode($not_deleted, JSON_UNESCAPED_UNICODE));

        return 1;
    }

    public function names()
    {
        return [
            [
                "عذاري محمد عبدالله آل عادي",
                "شركة ابراهيم بن محمد الحديثي العالمية",
                "لم يصلها عقد حتى الان ",
                "Aug-24"

            ],
            [
                "عبير فيصل سعيدان السلمي",
                "مؤسسة ابعاد التشغيل للتشغيل والصيانة",
                "تسجيل جديد",
                "Aug-24"
            ],
            [
                "لما نجا عزيز العتيبي",
                "مؤسسة ابعاد التشغيل للتشغيل والصيانة",
                "تسجيل جديد",
                "Aug-24"
            ],
            [
                "هيفاء مقبل عجب البقمي",
                "مؤسسة ابعاد التشغيل للتشغيل والصيانة",
                "تسجيل جديد",
                "Aug-24"
            ],
            [
                "الهام عبدالكريم شرهان البجالي ",
                "مؤسسة ابعاد التشغيل للتشغيل والصيانة",
                "تسجيل جديد",
                "Aug-24"
            ],
            [
                "سمر علي محمد السلمي",
                "مؤسسة ابعاد التشغيل للتشغيل والصيانة",
                "تسجيل جديد",
                "Aug-24"
            ],
            [
                "هند نوار فرحان الرشيدي",
                "شركة مجموعة التركي الطبيه",
                "لم يتم تسجيلها في التامينات",
                "Aug-24"
            ],
        ];

    }
}
