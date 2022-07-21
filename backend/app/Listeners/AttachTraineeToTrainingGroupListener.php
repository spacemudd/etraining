<?php

namespace App\Listeners;

use App\Models\Back\Company;
use App\Models\Back\Trainee;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AttachTraineeToTrainingGroupListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $trainee = Trainee::withTrashed()->find($event->trainee_id);

        $company = Company::withTrashed()->find($event->company_id);
        if ($company) {
            $contract = $company->contracts()->first();
            if ($contract) {
                $instructor = optional($contract->instructors())->first();
                $trainee->update(['instructor_id' => optional($instructor)->id]);
            }
        } else {
            $trainee->update(['instructor_id' => null]);
        }
    }
}
