<?php

namespace App\Jobs;

use App\Models\Back\Company;
use App\Models\Back\Trainee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CompanyTraineeLinkAuditJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Throwable
     */
    public function handle()
    {
        $date = now()->toDateTimeString();
        DB::beginTransaction();
        Company::withoutGlobalScopes()->chunk(20, function($companies) use ($date) {
            foreach ($companies as $company) {
                $trainees_ids = Trainee::withoutGlobalScopes()
                    ->select('id AS trainee_id', 'company_id')
                    ->where('company_id', $company->id)
                    ->toBase()
                    ->get()
                    ->map(function($model) {
                        return (array) $model;
                    })
                    ->toArray();

                foreach ($trainees_ids as &$id) {
                    $id['created_at'] = $date;
                    $id['updated_at'] = $date;
                }

                if (count($trainees_ids)) {
                    DB::table('company_trainee_link_audits')
                        ->insert($trainees_ids);
                }
            }
        });
        DB::commit();
    }
}
