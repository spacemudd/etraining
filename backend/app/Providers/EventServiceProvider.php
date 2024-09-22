<?php

namespace App\Providers;

use App\Events\TraineeAttachedToCompany;
use App\Listeners\AttachTraineeToTrainingGroupListener;
use App\Listeners\RecordLastLoginTimestamp;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Login::class => [
            RecordLastLoginTimestamp::class,
        ],
        TraineeAttachedToCompany::class => [
            AttachTraineeToTrainingGroupListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
