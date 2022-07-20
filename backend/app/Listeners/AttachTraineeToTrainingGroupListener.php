<?php

namespace App\Listeners;

use App\Models\Back\Company;
use App\Models\Back\Trainee;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AttachTraineeToTrainingGroupListener
{
    public $trainee_id;

    public $company_id;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct($trainee_id, $company_id)
    {
        $this->trainee_id = $trainee_id;
        $this->company_id = $company_id;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $trainee = Trainee::find($this->trainee_id);
        $company = Company::find($this->company_id);
        $contract = $company->contracts()->first();
        $instructor = $contract->instructors()->first();

        if ($instructor) {
            $trainee->update(['instructor_id' => $instructor->id]);
        }
    }
}
