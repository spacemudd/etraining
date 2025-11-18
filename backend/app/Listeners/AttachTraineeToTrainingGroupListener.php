<?php

namespace App\Listeners;

use App\Models\Back\Company;
use App\Models\Back\Trainee;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

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
        $trainee = Trainee::find($event->trainee_id);
        
        if (!$trainee) {
            Log::warning('AttachTraineeToTrainingGroupListener: Trainee not found', [
                'trainee_id' => $event->trainee_id,
                'company_id' => $event->company_id,
                'location' => __FILE__ . ':' . __LINE__,
            ]);
            return;
        }
        
        $oldInstructorId = $trainee->instructor_id;
        
        // Only proceed if company_id is not null
        if ($event->company_id) {
            $company = Company::find($event->company_id);
            if ($company) {
                $contract = $company->contracts()->first();
                if ($contract) {
                    $instructor = optional($contract->instructors())->first();
                    $newInstructorId = optional($instructor)->id;
                    
                    $trainee->update(['instructor_id' => $newInstructorId]);
                    
                    Log::info('INSTRUCTOR_ID_CHANGED: AttachTraineeToTrainingGroupListener - Company has contract', [
                        'trainee_id' => $trainee->id,
                        'trainee_name' => $trainee->name,
                        'old_instructor_id' => $oldInstructorId,
                        'new_instructor_id' => $newInstructorId,
                        'company_id' => $event->company_id,
                        'company_name' => $company->name_ar ?? null,
                        'contract_id' => $contract->id ?? null,
                        'instructor_name' => optional($instructor)->name ?? null,
                        'reason' => 'تغيير company_id للمتدرب - الشركة لديها عقد وتم ربط المدرب من العقد',
                        'location' => __FILE__ . ':' . __LINE__,
                        'method' => 'AttachTraineeToTrainingGroupListener::handle',
                        'user_id' => auth()->id(),
                        'user_name' => auth()->user()->name ?? null,
                    ]);
                } else {
                    Log::info('INSTRUCTOR_ID_CHANGED: AttachTraineeToTrainingGroupListener - Company has no contract', [
                        'trainee_id' => $trainee->id,
                        'trainee_name' => $trainee->name,
                        'old_instructor_id' => $oldInstructorId,
                        'new_instructor_id' => null,
                        'company_id' => $event->company_id,
                        'company_name' => $company->name_ar ?? null,
                        'reason' => 'تغيير company_id للمتدرب - الشركة لا تملك عقد',
                        'location' => __FILE__ . ':' . __LINE__,
                        'method' => 'AttachTraineeToTrainingGroupListener::handle',
                        'user_id' => auth()->id(),
                        'user_name' => auth()->user()->name ?? null,
                    ]);
                }
            } else {
                Log::warning('AttachTraineeToTrainingGroupListener: Company not found', [
                    'trainee_id' => $trainee->id,
                    'company_id' => $event->company_id,
                    'location' => __FILE__ . ':' . __LINE__,
                ]);
            }
        } else {
            // If company_id is null, remove the instructor_id
            $trainee->update(['instructor_id' => null]);
            
            Log::info('INSTRUCTOR_ID_CHANGED: AttachTraineeToTrainingGroupListener - Company ID is null', [
                'trainee_id' => $trainee->id,
                'trainee_name' => $trainee->name,
                'old_instructor_id' => $oldInstructorId,
                'new_instructor_id' => null,
                'company_id' => null,
                'reason' => 'تغيير company_id للمتدرب إلى null - تم إزالة instructor_id تلقائياً',
                'location' => __FILE__ . ':' . __LINE__,
                'method' => 'AttachTraineeToTrainingGroupListener::handle',
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? null,
            ]);
        }
    }
}
