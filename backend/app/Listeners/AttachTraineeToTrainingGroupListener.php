<?php

namespace App\Listeners;

use App\Models\Back\Company;
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
        $company = Company::
    }
}
