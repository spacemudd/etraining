<?php

namespace App\Console\Commands;

use App\Models\Back\AttendanceReport;
use App\Models\Back\AttendanceReportRecord;
use App\Models\Back\CourseBatchSession;
use App\Models\Back\CertificatesImportsRow;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CertificatesSentAt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'certificates:sent_at';

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

        DB::beginTransaction();
        $posts = CertificatesImportsRow::whereNotNull('sent_at')->get();
        foreach ($posts as $post) {
            $post->update([
                'sent_at' => null,
            ]);
        }
        DB::commit();

        $this->info('updates: '.$posts->count());

        return 0;
    }
}
