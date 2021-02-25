<?php

Route::get('/d8JAfGDxskMR9Y', function() {
    \Log::info($_ENV);
    return 'Done';
});

Route::get('/d8JAfGDxskMR9Y-1', function() {
    return response()->json($_ENV);
});

Route::get('/loginas/{user_id}', [\App\Http\Controllers\DebugController::class, 'loginAsUser'])->middleware('auth');

Route::get('invite/{invite_id}', [\App\Http\Controllers\InviteController::class, 'show'])->name('invite')->middleware('signed');
Route::post('invite/{invite_id}/accept', [\App\Http\Controllers\InviteController::class, 'accept'])->name('invite.accept');

Route::get('setup-account/{user_id}', [\App\Http\Controllers\ProfileController::class, 'setupAccount'])->name('setup-account')->middleware('signed');
Route::post('setup-account', [\App\Http\Controllers\ProfileController::class, 'updateAccount'])->name('setup-account.update')->middleware('auth');

Route::get('language/{language}', [\App\Http\Controllers\LanguageController::class, 'changeLanguage'])->name('language');

Route::get('/terms', [\App\Http\Controllers\TermsController::class, 'index'])->name('terms');

Route::post('/register/trainees', [\App\Http\Controllers\Auth\RegisterTraineeController::class, 'store'])->name('register.trainees.store');
Route::get('/register/trainees', [\App\Http\Controllers\Auth\RegisterTraineeController::class, 'show'])->name('register.trainees');

Route::post('/register/instructors', [\App\Http\Controllers\Auth\RegisterInstructorController::class, 'store'])->name('register.instructors.store');
Route::get('/register/instructors', [\App\Http\Controllers\Auth\RegisterInstructorController::class, 'show'])->name('register.instructors');

Route::middleware(['guest'])->get('/', [\App\Http\Controllers\WelcomeController::class, 'welcome']);

Route::middleware(['auth:sanctum'])->group(function() {
    Route::get('/register/instructors/application', [\App\Http\Controllers\Auth\RegisterInstructorController::class, 'application'])->name('register.instructors.application');
    Route::get('/register/trainees/application', [\App\Http\Controllers\Auth\RegisterTraineeController::class, 'application'])->name('register.trainees.application');
});

Route::middleware(['auth:sanctum'])->get('/trainees/application', [\App\Http\Controllers\TraineesApplicationController::class, 'index']);

Route::middleware(['auth:sanctum', 'verified', 'approved-application'])->get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    // For everyone
    Route::get('inbox', [\App\Http\Controllers\InboxController::class, 'index'])->name('inbox.index');
    Route::post('back/zoom/signature', [\App\Http\Controllers\ZoomController::class, 'signature'])->name('zoom.signature');
    Route::post('back/zoom/meetings', [\App\Http\Controllers\ZoomMeetingsController::class, 'store'])->name('zoom.meetings.store');
    Route::post('back/zoom/meetings/configs', [\App\Http\Controllers\ZoomMeetingsController::class, 'configs'])->name('zoom.meetings.configs');

    // For admins
    Route::prefix('back')->middleware('redirect-trainees-to-dashboard')->name('back.')->group(function() {
        Route::get('/settings', [\App\Http\Controllers\Back\SettingsController::class, 'index'])->name('settings');

        Route::delete('/settings/roles/{role_id}/users/{user_id}', [\App\Http\Controllers\Back\RolesController::class, 'deleteUser'])->name('settings.roles.users.delete');
        Route::post('/settings/roles/{id}/users/invite', [\App\Http\Controllers\Back\RolesController::class, 'sendInvite'])->name('settings.roles.users.invite.send');
        Route::get('/settings/roles/{id}/users/invite', [\App\Http\Controllers\Back\RolesController::class, 'invite'])->name('settings.roles.users.invite');
        Route::get('/settings/roles/{id}', [\App\Http\Controllers\Back\RolesController::class, 'show'])->name('settings.roles.show');
        Route::get('/settings/roles', [\App\Http\Controllers\Back\RolesController::class, 'index'])->name('settings.roles.index');

        Route::post('/settings/roles/attach-permission', [\App\Http\Controllers\Back\RolesPermissionsController::class, 'attachPermission'])->name('settings.roles.attach-permission');
        Route::post('/settings/roles/detach-permission', [\App\Http\Controllers\Back\RolesPermissionsController::class, 'detachPermission'])->name('settings.roles.detach-permission');
        Route::get('/settings/roles/{id}/permissions', [\App\Http\Controllers\Back\RolesPermissionsController::class, 'index'])->name('settings.roles.permissions.index');

        Route::get('/settings/trainees-applications', [\App\Http\Controllers\Back\SettingsTraineesApplication::class, 'index'])->name('settings.trainees-application');
        Route::get('/settings/trainees-applications/required-files', [\App\Http\Controllers\Back\SettingsTraineesApplication::class, 'requiredFiles'])->name('settings.trainees-application.required-files');
        Route::post('/settings/trainees-applications/required-files', [\App\Http\Controllers\Back\SettingsTraineesApplication::class, 'store'])->name('settings.trainees-application.required-files.store');
        Route::delete('/settings/trainees-applications/required-files/{id}', [\App\Http\Controllers\Back\SettingsTraineesApplication::class, 'delete'])->name('settings.trainees-application.required-files.delete');

        Route::get('media/{media_id}', [\App\Http\Controllers\MediaController::class, 'download'])->name('media.download');
        Route::delete('media/{media_id}', [\App\Http\Controllers\MediaController::class, 'delete'])->name('media.delete');

        Route::resource('companies', \App\Http\Controllers\Back\CompaniesController::class);
        Route::prefix('companies')->name('companies.')->group(function() {
            Route::get('{company_id}/contracts/{contract_id}/attachments', [\App\Http\Controllers\Back\CompaniesContractsController::class, 'attachments'])->name('contracts.attachments');
            Route::post('{company_id}/contracts/{contract}/attachments/upload', [\App\Http\Controllers\Back\CompaniesContractsController::class, 'storeAttachments'])->name('contracts.attachments.store');
            Route::delete('{company_id}/contracts/{contract}/attachments/delete', [\App\Http\Controllers\Back\CompaniesContractsController::class, 'deleteAttachments'])->name('contracts.attachments.delete');
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

        Route::get('trainees/import', [\App\Http\Controllers\Back\TraineesImportController::class, 'index'])->name('trainees.import');
        Route::post('trainees/import', [\App\Http\Controllers\Back\TraineesImportController::class, 'store'])->name('trainees.import.store');

        Route::post('trainees/{trainee_id}/set-password', [\App\Http\Controllers\Back\TraineesController::class, 'setPassword'])->name('trainees.set-password');
        Route::post('trainees/{trainee_id}/re-send-invitation', [\App\Http\Controllers\Back\TraineesController::class, 'resendInvitation'])->name('trainees.re-send-invitation');
        Route::get('trainees/excel', [\App\Http\Controllers\Back\TraineesController::class, 'excel'])->name('trainees.excel');
        Route::post('trainees/{trainee_id}/approve-user', [\App\Http\Controllers\Back\TraineesController::class, 'approveUser'])->name('trainees.approve-user');
        Route::post('trainees/{trainee_id}/create-user', [\App\Http\Controllers\Back\TraineesController::class, 'createUser'])->name('trainees.create-user');
        Route::post('trainees/assign-instructor', [\App\Http\Controllers\Back\TraineesController::class, 'assignInstructor'])->name('trainees.assign-instructor');
        Route::post('trainees/{trainee_id}/attachments/identity', [\App\Http\Controllers\Back\TraineesController::class, 'storeIdentity'])->name('trainees.attachments.identity');
        Route::delete('trainees/{trainee_id}/attachments/identity', [\App\Http\Controllers\Back\TraineesController::class, 'deleteIdentity'])->name('trainees.attachments.identity.destroy');
        Route::post('trainees/{trainee_id}/attachments/qualification', [\App\Http\Controllers\Back\TraineesController::class, 'storeQualification'])->name('trainees.attachments.qualification');
        Route::delete('trainees/{trainee_id}/attachments/qualification', [\App\Http\Controllers\Back\TraineesController::class, 'deleteQualification'])->name('trainees.attachments.qualification.destroy');
        Route::post('trainees/{trainee_id}/attachments/bank-account', [\App\Http\Controllers\Back\TraineesController::class, 'storeBankAccount'])->name('trainees.attachments.bank-account');
        Route::delete('trainees/{trainee_id}/attachments/bank-account', [\App\Http\Controllers\Back\TraineesController::class, 'deleteBankAccount'])->name('trainees.attachments.bank-account.destroy');
        Route::get('trainees/{trainee_id}/block', [\App\Http\Controllers\Back\TraineesController::class, 'blockView'])->name('trainees.block');
        Route::post('trainees/{trainee_id}/block', [\App\Http\Controllers\Back\TraineesController::class, 'block'])->name('trainees.block.store');
        Route::get('trainees/blocked/show/{trainee_id}', [\App\Http\Controllers\Back\TraineesController::class, 'showBlocked'])->name('trainees.show.blocked');
        Route::post('trainees/blocked/show/{trainee_id}', [\App\Http\Controllers\Back\TraineesController::class, 'unblock'])->name('trainees.unblock');
        Route::resource('trainees', \App\Http\Controllers\Back\TraineesController::class);

        Route::post('instructors/{instructor_id}/approve-user', [\App\Http\Controllers\Back\InstructorsController::class, 'approveUser'])->name('instructors.approve-user');
        Route::post('instructors/{instructor_id}/create-user', [\App\Http\Controllers\Back\InstructorsController::class, 'createUser'])->name('instructors.create-user');
        Route::post('instructors/{instructor_id}/attachments/cv-full', [\App\Http\Controllers\Back\InstructorsController::class, 'storeCvFull'])->name('instructors.attachments.cv-full');
        Route::delete('instructors/{instructor_id}/attachments/cv-full', [\App\Http\Controllers\Back\InstructorsController::class, 'deleteCvFull'])->name('instructors.attachments.cv-full.destroy');
        Route::post('instructors/{instructor_id}/attachments/cv-summary', [\App\Http\Controllers\Back\InstructorsController::class, 'storeCvSummary'])->name('instructors.attachments.cv-summary');
        Route::delete('instructors/{instructor_id}/attachments/cv-summary', [\App\Http\Controllers\Back\InstructorsController::class, 'deleteCvSummary'])->name('instructors.attachments.cv-summary.destroy');
        Route::delete('instructors/{instructor_id}/block', [\App\Http\Controllers\Back\InstructorsController::class, 'block'])->name('instructors.block');
        Route::get('instructors/blocked/show/{instructor_id}', [\App\Http\Controllers\Back\InstructorsController::class, 'showBlocked'])->name('instructors.show.blocked');
        Route::post('instructors/blocked/show/{instructor_id}', [\App\Http\Controllers\Back\InstructorsController::class, 'unblock'])->name('instructors.unblock');
        Route::resource('instructors', \App\Http\Controllers\Back\InstructorsController::class);


        Route::get('/courses/{course_id}/course-batches/{course_batch_id}/course-batch-sessions/{course_batch_session}/start', [\App\Http\Controllers\Back\CourseBatchSessionsController::class, 'show'])->name('course-batch-session.start');
        Route::resource('courses/{course_id}/course-batches/{course_batch_id}/course-batch-sessions', \App\Http\Controllers\Back\CourseBatchSessionsController::class);

        Route::resource('courses/{course_id}/course-batches', \App\Http\Controllers\Back\CourseBatchesController::class);

        Route::get('search', [\App\Http\Controllers\Back\SiteSearchController::class, 'search']);

        Route::post('courses/{course_id}/approve', [\App\Http\Controllers\Back\CoursesController::class, 'approve'])->name('courses.approve');
        Route::post('courses/{course_id}/training-package', [\App\Http\Controllers\Back\CoursesController::class, 'storeTrainingPackage'])->name('courses.training-package');
        Route::delete('courses/{course_id}/training-package', [\App\Http\Controllers\Back\CoursesController::class, 'deleteTrainingPackage'])->name('courses.training-package.destroy');
        Route::resource('courses', \App\Http\Controllers\Back\CoursesController::class);
    });

    // For instructors
    Route::prefix('teaching')->middleware('redirect-trainees-to-dashboard')->name('teaching.')->group(function() {
        Route::get('/', [\App\Http\Controllers\Teaching\TeachingController::class, 'index'])->name('index');
        Route::resource('courses', \App\Http\Controllers\Teaching\CoursesController::class);

        Route::get('/course-batch-sessions/{course_batch_session_id}/attendance/excel', [\App\Http\Controllers\CourseBatchSessionsAttendanceController::class, 'excel'])->name('course-batch-sessions.attendance.excel');
        Route::get('/course-batch-sessions/{course_batch_session_id}/attendance', [\App\Http\Controllers\CourseBatchSessionsAttendanceController::class, 'index'])->name('course-batch-sessions.attendance.index');
        Route::get('/course-batch-sessions/{course_batch_session_id}/attendance/export', [\App\Http\Controllers\CourseBatchSessionsAttendanceController::class, 'attendingExcel'])->name('course-batch-sessions.attendance.export');

        Route::post('/course-batch-sessions/attendance', [\App\Http\Controllers\CourseBatchSessionsAttendanceController::class, 'store'])->name('course-batch-sessions.attendance.store');

        Route::post('/trainee-groups/{trainee_group_id}/trainees/{id}/send-message', [\App\Http\Controllers\Teaching\TraineeGroupTraineesController::class, 'sendMessage'])->name('trainee-groups.trainees.send-message');

        // These two don't exist?
        Route::get('/trainee-groups/{trainee_group_id}/trainees/{id}', [\App\Http\Controllers\Teaching\TraineeGroupDashboardController::class, 'show'])->name('trainee-groups.trainees.show');
        Route::get('/trainee-groups/{trainee_group_id}/trainees', [\App\Http\Controllers\Teaching\TraineeGroupTraineesController::class, 'index'])->name('trainee-groups.trainees.index');

        Route::get('/trainee-groups/{trainee_group_id}/announcements/create', [\App\Http\Controllers\Teaching\TraineeGroupsController::class, 'createAnnouncement'])->name('trainee-groups.announcements.create');
        Route::post('/trainee-groups/{trainee_group_id}/announcements/send', [\App\Http\Controllers\Teaching\TraineeGroupsController::class, 'sendAnnouncement'])->name('trainee-groups.announcements.send');
        Route::get('/trainee-groups', [\App\Http\Controllers\Teaching\TraineeGroupsController::class, 'index'])->name('trainee-groups.index');
    });

    // For trainees
    Route::prefix('trainees')->name('trainees.')->group(function() {

        Route::get('/courses/{course_id}/certificate', [\App\Http\Controllers\Trainees\CoursesController::class, 'generateCertificate'])->name('courses.generate-certificate');
        Route::get('/courses/{course_id}/training-package', [\App\Http\Controllers\Teaching\CoursesController::class, 'trainingPackage'])->name('courses.training-package');
        Route::resource('courses', \App\Http\Controllers\Trainees\CoursesController::class);

        Route::get('/courses/{course_id}/course-batches/{course_batch_id}/course-batch-sessions/{course_batch_session}', [\App\Http\Controllers\Trainees\CourseBatchSessionsController::class, 'show'])->name('course-batch-session.show');
    });
});

Route::middleware(['auth:sanctum'])->group(function() {
    // Moved API call here because I don't have time to figure the token stuff out that work under /api/ url space.
    Route::post('/api/instructors/uploadcv', [\App\Http\Controllers\Back\InstructorsController::class, 'storeCvFromApplication'])->name('api.register.instructors.upload-cv');
    Route::post('/api/trainees/uploadcv', [\App\Http\Controllers\Back\TraineesController::class, 'storeCvFromApplication'])->name('api.register.trainees.upload-cv');
});
