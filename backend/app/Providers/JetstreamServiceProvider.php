<?php

namespace App\Providers;

use App\Actions\Jetstream\AddTeamMember;
use App\Actions\Jetstream\CreateTeam;
use App\Actions\Jetstream\DeleteTeam;
use App\Actions\Jetstream\DeleteUser;
use App\Actions\Jetstream\RemoveTeamMember;
use App\Actions\Jetstream\UpdateTeamName;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Fortify;
use Illuminate\Http\Request;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePermissions();

        Jetstream::createTeamsUsing(CreateTeam::class);
        Jetstream::updateTeamNamesUsing(UpdateTeamName::class);
        Jetstream::addTeamMembersUsing(AddTeamMember::class);
        Jetstream::removeTeamMembersUsing(RemoveTeamMember::class);
        Jetstream::deleteTeamsUsing(DeleteTeam::class);
        Jetstream::deleteUsersUsing(DeleteUser::class);

        // https://github.com/laravel/jetstream/blob/2.x/UPGRADE.md#authentication-views
        Fortify::loginView(function () {
            return view('auth/login', [
                'canResetPassword' => Route::has('password.request'),
                'status' => session('status'),
            ]);
        });

        Fortify::requestPasswordResetLinkView(function () {
            return view('auth/forgot-password', [
                'status' => session('status'),
            ]);
        });

        Fortify::resetPasswordView(function (Request $request) {
            return view('auth/reset-password', [
                'request' => $request,
                'email' => $request->input('email'),
                'token' => $request->route('token'),
            ]);
        });

        Fortify::registerView(function () {
            return view('auth/register');
        });

        Fortify::verifyEmailView(function () {
            return view('auth/verify-email', [
                'status' => session('status'),
            ]);
        });

        Fortify::twoFactorChallengeView(function () {
            return view('auth/two-factor-challenge');
        });

        Fortify::confirmPasswordView(function () {
            return view('auth/confirm-password');
        });
    }

    /**
     * Configure the roles and permissions that are available within the application.
     *
     * @return void
     */
    protected function configurePermissions()
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::role('admin', 'Administrator', [
            'create',
            'read',
            'update',
            'delete',
        ])->description('Administrator users can perform any action.');

        Jetstream::role('editor', 'Editor', [
            'read',
            'create',
            'update',
        ])->description('Editor users have the ability to read, create, and update.');

        Jetstream::role('instructor', 'Instructor', [
            'create-instructor-courses',
            'broadcast-course',
            'read',
            'create',
            'update',
        ])->description('Instructors users have the ability to read, create, and update.');

        Jetstream::role('trainee', 'Trainee', [
            'view-assigned-trainee-courses',
        ])->description('Trainees can join their assigned courses');
    }
}
