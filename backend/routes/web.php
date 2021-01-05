<?php

use Illuminate\Notifications\Messages\MailMessage;

Route::get('mail', function() {
    return (new MailMessage)
        ->line(trans('words.welcome-to-our-center-we-will-inform-you-when-your-application-is-approved'))
        ->action(trans('words.access-the-platform'), url('/'))
        ->line(trans('words.thank-you-for-applying'))
        ->salutation(trans('words.with-regards'))
        ->render();
});

Route::get('language/{language}', function ($language) {
    session()->put('locale', $language);
    return redirect()->back();
})->name('language');

Route::get('/terms', [\App\Http\Controllers\TermsController::class, 'index'])->name('terms');

Route::post('/register/trainees', [\App\Http\Controllers\Auth\RegisterTraineeController::class, 'store'])->name('register.trainees');
Route::get('/register/trainees', [\App\Http\Controllers\Auth\RegisterTraineeController::class, 'show'])->name('register.trainees');

Route::post('/register/instructors', [\App\Http\Controllers\Auth\RegisterInstructorController::class, 'store'])->name('register.instructors');
Route::get('/register/instructors', [\App\Http\Controllers\Auth\RegisterInstructorController::class, 'show'])->name('register.instructors');

Route::middleware(['guest'])->get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum'])->group(function() {
    Route::get('/register/instructors/application', [\App\Http\Controllers\Auth\RegisterInstructorController::class, 'application'])->name('register.instructors.application');
});

Route::get('onboarding', [\App\Http\Controllers\OnboardingController::class, 'index']);

Route::middleware(['auth:sanctum'])->get('/trainees/application', [\App\Http\Controllers\TraineesApplicationController::class, 'index']);

Route::middleware(['auth:sanctum', 'verified', 'approved-instructor'])->get('/dashboard', function () {

    if (\Illuminate\Support\Str::contains(auth()->user()->roles()->first()->name, 'instructors')) {
        return app()->make(\App\Http\Controllers\Teaching\TeachingController::class)->dashboard();
    }

    if (\Illuminate\Support\Str::contains(auth()->user()->roles()->first()->name, 'trainees')) {
        return app()->make(\App\Http\Controllers\Trainees\DashboardController::class)->dashboard();
    }

    return Inertia\Inertia::render('Dashboard', [
        'companies_count' => \App\Models\Back\Company::count(),
        'trainees_count' => \App\Models\Back\Trainee::count(),
        'instructors_count' => \App\Models\Back\Instructor::count(),
        'courses_count' => \App\Models\Back\Course::count(),
    ]);
})->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    // For everyone
    Route::get('inbox', [\App\Http\Controllers\InboxController::class, 'index'])->name('inbox.index');

    // For admins
    Route::prefix('back')->name('back.')->group(function() {
        Route::get('/settings', [\App\Http\Controllers\Back\SettingsController::class, 'index'])->name('settings');

        Route::get('/settings/trainees-applications', [\App\Http\Controllers\Back\SettingsTraineesApplication::class, 'index'])->name('settings.trainees-application');
        Route::get('/settings/trainees-applications/required-files', [\App\Http\Controllers\Back\SettingsTraineesApplication::class, 'requiredFiles'])->name('settings.trainees-application.required-files');
        Route::post('/settings/trainees-applications/required-files', [\App\Http\Controllers\Back\SettingsTraineesApplication::class, 'store'])->name('settings.trainees-application.required-files');
        Route::delete('/settings/trainees-applications/required-files/{id}', [\App\Http\Controllers\Back\SettingsTraineesApplication::class, 'delete'])->name('settings.trainees-application.required-files.delete');

        Route::post('/zoom/signature', [\App\Http\Controllers\ZoomController::class, 'signature'])->name('zoom.signature');
        Route::post('/zoom/meetings', [\App\Http\Controllers\ZoomMeetingsController::class, 'store'])->name('zoom.meetings.store');
        Route::post('/zoom/meetings/configs', [\App\Http\Controllers\ZoomMeetingsController::class, 'configs'])->name('zoom.meetings.configs');

        Route::get('media/{media_id}', [\App\Http\Controllers\MediaController::class, 'download'])->name('media.download');
        Route::delete('media/{media_id}', [\App\Http\Controllers\MediaController::class, 'delete'])->name('media.delete');

        Route::resource('companies', \App\Http\Controllers\Back\CompaniesController::class);
        Route::prefix('companies')->name('companies.')->group(function() {
            Route::get('{company_id}/contracts/{contract_id}/attachments', [\App\Http\Controllers\Back\CompaniesContractsController::class, 'attachments'])->name('contracts.attachments');
            Route::resource('{company_id}/contracts', \App\Http\Controllers\Back\CompaniesContractsController::class);
        });

        Route::post('company-contracts/attach-instructor', [\App\Http\Controllers\Back\CompaniesContractsController::class, 'attachInstructor'])->name('company-contracts.attach-instructor');
        Route::post('company-contracts/detach-instructor', [\App\Http\Controllers\Back\CompaniesContractsController::class, 'detachInstructor'])->name('company-contracts.detach-instructor');

        Route::get('trainee-groups', [\App\Http\Controllers\Back\TraineesController::class, 'withGroups'])->name('trainee-groups.index');

        Route::get('finance', [\App\Http\Controllers\Back\FinanceController::class, 'index'])->name('finance');

        Route::prefix('finance')->name('finance.')->group(function() {
            Route::resource('accounts', \App\Http\Controllers\Back\FinancialAccountsController::class);
            Route::resource('invoices', \App\Http\Controllers\Back\FinancialInvoicesController::class);
            Route::get('monthly-subscription/edit', [\App\Http\Controllers\Back\FinancialMonthlySubscriptionController::class, 'edit'])->name('monthly-subscription.edit');
            Route::put('monthly-subscription', [\App\Http\Controllers\Back\FinancialMonthlySubscriptionController::class, 'update'])->name('monthly-subscription.update');
        });

        Route::post('trainees/{trainee_id}/create-user', [\App\Http\Controllers\Back\TraineesController::class, 'createUser'])->name('trainees.create-user');
        Route::post('trainees/assign-instructor', [\App\Http\Controllers\Back\TraineesController::class, 'assignInstructor'])->name('trainees.assign-instructor');
        Route::post('trainees/{trainee_id}/attachments/identity', [\App\Http\Controllers\Back\TraineesController::class, 'storeIdentity'])->name('trainees.attachments.identity');
        Route::delete('trainees/{trainee_id}/attachments/identity', [\App\Http\Controllers\Back\TraineesController::class, 'deleteIdentity'])->name('trainees.attachments.identity.destroy');
        Route::post('trainees/{trainee_id}/attachments/qualification', [\App\Http\Controllers\Back\TraineesController::class, 'storeQualification'])->name('trainees.attachments.qualification');
        Route::delete('trainees/{trainee_id}/attachments/qualification', [\App\Http\Controllers\Back\TraineesController::class, 'deleteQualification'])->name('trainees.attachments.qualification.destroy');
        Route::post('trainees/{trainee_id}/attachments/bank-account', [\App\Http\Controllers\Back\TraineesController::class, 'storeBankAccount'])->name('trainees.attachments.bank-account');
        Route::delete('trainees/{trainee_id}/attachments/bank-account', [\App\Http\Controllers\Back\TraineesController::class, 'deleteBankAccount'])->name('trainees.attachments.bank-account.destroy');
        Route::resource('trainees', \App\Http\Controllers\Back\TraineesController::class);

        Route::post('instructors/{instructor_id}/approve-user', [\App\Http\Controllers\Back\InstructorsController::class, 'approveUser'])->name('instructors.approve-user');
        Route::post('instructors/{instructor_id}/create-user', [\App\Http\Controllers\Back\InstructorsController::class, 'createUser'])->name('instructors.create-user');
        Route::post('instructors/{instructor_id}/attachments/cv-full', [\App\Http\Controllers\Back\InstructorsController::class, 'storeCvFull'])->name('instructors.attachments.cv-full');
        Route::delete('instructors/{instructor_id}/attachments/cv-full', [\App\Http\Controllers\Back\InstructorsController::class, 'deleteCvFull'])->name('instructors.attachments.cv-full.destroy');
        Route::post('instructors/{instructor_id}/attachments/cv-summary', [\App\Http\Controllers\Back\InstructorsController::class, 'storeCvSummary'])->name('instructors.attachments.cv-summary');
        Route::delete('instructors/{instructor_id}/attachments/cv-summary', [\App\Http\Controllers\Back\InstructorsController::class, 'deleteCvSummary'])->name('instructors.attachments.cv-summary.destroy');
        Route::resource('instructors', \App\Http\Controllers\Back\InstructorsController::class);


        Route::resource('courses/{course_id}/course-batches/{course_batch_id}/course-batch-sessions', \App\Http\Controllers\Back\CourseBatchSessionsController::class);

        Route::resource('courses/{course_id}/course-batches', \App\Http\Controllers\Back\CourseBatchesController::class);

        Route::post('courses/{course_id}/approve', [\App\Http\Controllers\Back\CoursesController::class, 'approve'])->name('courses.approve');
        Route::post('courses/{course_id}/edit', [\App\Http\Controllers\Back\CoursesController::class, 'edit'])->name('courses.edit');
        Route::post('courses/{course_id}/training-package', [\App\Http\Controllers\Back\CoursesController::class, 'storeTrainingPackage'])->name('courses.training-package');
        Route::delete('courses/{course_id}/training-package', [\App\Http\Controllers\Back\CoursesController::class, 'deleteTrainingPackage'])->name('courses.training-package.destroy');
        Route::resource('courses', \App\Http\Controllers\Back\CoursesController::class);
    });

    // For instructors
    Route::prefix('teaching')->name('teaching.')->group(function() {
        Route::get('/', [\App\Http\Controllers\Teaching\TeachingController::class, 'index'])->name('index');
        Route::resource('courses', \App\Http\Controllers\Teaching\CoursesController::class);

        Route::post('/trainee-groups/{trainee_group_id}/trainees/{id}/send-message', [\App\Http\Controllers\Teaching\TraineeGroupTraineesController::class, 'sendMessage'])->name('trainee-groups.trainees.send-message');
        Route::get('/trainee-groups/{trainee_group_id}/trainees/{id}', [\App\Http\Controllers\Teaching\TraineeGroupTraineesController::class, 'show'])->name('trainee-groups.trainees.show');
        Route::get('/trainee-groups/{trainee_group_id}/trainees', [\App\Http\Controllers\Teaching\TraineeGroupTraineesController::class, 'index'])->name('trainee-groups.trainees.index');

        Route::get('/trainee-groups/{trainee_group_id}/announcements/create', [\App\Http\Controllers\Teaching\TraineeGroupsController::class, 'createAnnouncement'])->name('trainee-groups.announcements.create');
        Route::post('/trainee-groups/{trainee_group_id}/announcements/send', [\App\Http\Controllers\Teaching\TraineeGroupsController::class, 'sendAnnouncement'])->name('trainee-groups.announcements.send');
        Route::get('/trainee-groups', [\App\Http\Controllers\Teaching\TraineeGroupsController::class, 'index'])->name('trainee-groups.index');
    });

    // For trainees
    Route::prefix('trainees')->name('trainees.')->group(function() {
        Route::get('/courses/{course_id}/training-package', [\App\Http\Controllers\Teaching\CoursesController::class, 'trainingPackage'])->name('courses.training-package');
        Route::resource('courses', \App\Http\Controllers\Trainees\CoursesController::class);
    });
});

Route::middleware(['auth:sanctum'])->group(function() {
    // Moved API call here because I don't have time to figure the token stuff out that work under /api/ url space.
    Route::post('/api/instructors/uploadcv', [\App\Http\Controllers\Back\InstructorsController::class, 'storeCvFromApplication'])->name('api.register.instructors.upload-cv');
});
