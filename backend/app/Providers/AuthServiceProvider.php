<?php

namespace App\Providers;

use App\Models\Back\Company;
use App\Models\Team;
use App\Models\User;
use App\Policies\TeamPolicy;
use Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Team::class => TeamPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('view-company', function (User $user, Company $company) {
            return $user->hasPermissionTo('view-all-companies') || $company->allowed_users()->where('user_id', $user->id)->exists();
        });
    }
}
