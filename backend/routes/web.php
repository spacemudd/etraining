<?php

use App\Models\Back\Trainee;

Route::get('instructor-company-report', function() {
    $contracts = \App\Models\Back\CompanyContract::get();
    $data = [];
    foreach ($contracts as $contract) {
        if ($contract->instructors()->count()) {
            foreach ($contract->instructors as $instructor) {
                $data[] = [
                    'company_id' => $contract->company_id,
                    'company_name_en' => optional($contract->company)->name_en,
                    'company_name' => optional($contract->company)->name_ar,
                    'instructor_name' => optional($instructor)->name,
                ];
            }
        }
    }

    return response()->json($data);
});

Route::get('zzwarnings', function() {
    $trainee_ids = \App\Models\Back\AttendanceReportRecord::where('attendance_report_id', 'd66634c9-d756-49c2-9181-00371fce6c4e')
        ->pluck('trainee_id');

    foreach ($trainee_ids as $trainee) {
        $trainee_model = Trainee::find($trainee);
        if ($trainee_model) {
            $warnings = \App\Models\Back\AttendanceReportRecordWarning::where('trainee_id', $trainee_model->id)
                ->where('attendance_report_id', 'd66634c9-d756-49c2-9181-00371fce6c4e')
                ->count();

            if ($warnings > 1) {
                $warnings = \App\Models\Back\AttendanceReportRecordWarning::where('trainee_id', $trainee_model->id)
                    ->where('attendance_report_id', 'd66634c9-d756-49c2-9181-00371fce6c4e')
                    ->first()
                    ->delete();
            }
        }
    }
});

Route::get('ttreport', function() {
    $trainees = Trainee::where('company_id', '!=', null)
        ->where('suspended_at', null)
        ->where('deleted_remark', null)
        ->where('trainee_group_id', '!=', null)
        ->where('deleted_at', null)
        ->with('company')
        ->whereHas('company', function($q) {$q->where('deleted_at', null);})
        //->select(['id', 'name', 'phone', 'email'])
        ->with([
            'absences_14to20',
            //'attendances_14to20',
        ])
        ->withCount([
            'absences_14to20',
            //'attendances_14to20',
        ])
        //->take(5)
        ->get();

    $traineeData = [];

    foreach ($trainees as $trainee) {
        $traineeData[] = [
            'name' => $trainee->name,
            'company' => optional($trainee->company)->name_ar,
            'email' => $trainee->email,
            'phone' => $trainee->phone,
            'instructor' => optional($trainee->instructor)->name,
            'group' => optional($trainee->trainee_group)->name,
            'absences_14to20' => $trainee->absences_14to20_count,
            //'attendances_30to5_count' => $trainee->attendances_7to11_count,
        ];
    }

    return $traineeData;
});

Route::impersonate();

Route::get('/disabled', [\App\Http\Controllers\Back\DisableWebsiteController::class, 'showDisabledPage'])->name('disabled');

Route::get('/loginas/{user_id}', [\App\Http\Controllers\DebugController::class, 'loginAsUser'])->middleware('auth');

Route::get('invite/{invite_id}', [\App\Http\Controllers\InviteController::class, 'show'])->name('invite');
Route::post('invite/{invite_id}/accept', [\App\Http\Controllers\InviteController::class, 'accept'])->name('invite.accept');

Route::get('setup-account/{user_id}', [\App\Http\Controllers\ProfileController::class, 'setupAccount'])->name('setup-account')->middleware('signed');
Route::post('setup-account', [\App\Http\Controllers\ProfileController::class, 'updateAccount'])->name('setup-account.update')->middleware('auth');

Route::get('language/{language}', [\App\Http\Controllers\LanguageController::class, 'changeLanguage'])->name('language');

Route::get('/requirements', [\App\Http\Controllers\RequirementsController::class, 'index'])->name('requirements');
Route::get('/terms', [\App\Http\Controllers\TermsController::class, 'index'])->name('terms');

Route::post('/register/trainees', [\App\Http\Controllers\Auth\RegisterTraineeController::class, 'store'])->name('register.trainees.store');
Route::get('/register/trainees', [\App\Http\Controllers\Auth\RegisterTraineeController::class, 'show'])->name('register.trainees');

Route::post('/register/instructors', [\App\Http\Controllers\Auth\RegisterInstructorController::class, 'store'])->name('register.instructors.store');
Route::get('/register/instructors', [\App\Http\Controllers\Auth\RegisterInstructorController::class, 'show'])->name('register.instructors');

Route::middleware(['guest'])->get('/', [\App\Http\Controllers\WelcomeController::class, 'welcome']);

Route::middleware(['auth:sanctum'])->group(function() {
    Route::get('/register/instructors/application', [\App\Http\Controllers\Auth\RegisterInstructorController::class, 'application'])->name('register.instructors.application');
    Route::get('/register/trainees/application', [\App\Http\Controllers\Auth\RegisterTraineeController::class, 'application'])->name('register.trainees.application');

    Route::get('complaints', [\App\Http\Controllers\ComplaintsController::class, 'index'])->name('complaints.index');
    Route::post('complaints', [\App\Http\Controllers\ComplaintsController::class, 'store'])->name('complaints.store');

    Route::get('suggestions', [\App\Http\Controllers\SuggestionsController::class, 'index'])->name('suggestions.index');

    Route::get('company-roles', [\App\Http\Controllers\CompanyRolesController::class, 'index'])->name('company-roles.index');

    Route::get('company-roles', [\App\Http\Controllers\CompanyRolesController::class, 'index'])->name('company-roles.index');

    Route::get('management-roles', [\App\Http\Controllers\ManagementRolesController::class, 'index'])->name('management-roles.index');

    Route::get('management-roles', [\App\Http\Controllers\ManagementRolesController::class, 'index'])->name('management-roles.index');

    Route::get('obligations', [\App\Http\Controllers\ObligationsController::class, 'index'])->name('obligations.index');

    Route::get('user-guides', [\App\Http\Controllers\UserGuidesController::class, 'index'])->name('user-guides.index');

    Route::get('training-plan', [\App\Http\Controllers\TrainingPlanController::class, 'index'])->name('training-plan.index');

    Route::get('training-schedule', [\App\Http\Controllers\TrainingScheduleController::class, 'index'])->name('training-schedule.index');

    Route::get('survey', [\App\Http\Controllers\SurveyController::class, 'index'])->name('survey.index');
});

Route::middleware(['auth:sanctum'])->get('/trainees/application', [\App\Http\Controllers\TraineesApplicationController::class, 'index']);

Route::middleware(['auth:sanctum', 'verified', 'approved-application'])->get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    // For everyone
    Route::get('inbox', [\App\Http\Controllers\InboxController::class, 'index'])->name('inbox.index');
    Route::post('back/zoom/signature', [\App\Http\Controllers\ZoomController::class, 'signature'])->name('back.zoom.signature');
    Route::post('back/zoom/meetings', [\App\Http\Controllers\ZoomMeetingsController::class, 'store'])->name('back.zoom.meetings.store');
    Route::post('back/zoom/meetings/configs', [\App\Http\Controllers\ZoomMeetingsController::class, 'configs'])->name('back.zoom.meetings.configs');

    Route::get('session-completed-landing', [\App\Http\Controllers\Back\SurveyLinksController::class, 'landed'])->name('back.session-completed-landing');

    Route::get('job-trackers/{id}', [\App\Http\Controllers\JobTrackersController::class, 'show'])->name('job-trackers.show');
    Route::get('job-trackers/{id}/download', [\App\Http\Controllers\JobTrackersController::class, 'download'])->name('job-trackers.download');

    Route::get('/back/media/{media_id}', [\App\Http\Controllers\MediaController::class, 'download'])->name('back.media.download');
    Route::delete('/back/media/{media_id}', [\App\Http\Controllers\MediaController::class, 'delete'])->name('back.media.delete');

    // For admins
    Route::prefix('back')->middleware('redirect-trainees-to-dashboard')->name('back.')->group(function() {
        Route::get('/settings', [\App\Http\Controllers\Back\SettingsController::class, 'index'])->name('settings');

        Route::get('/settings/complaints', [\App\Http\Controllers\Back\ComplaintsSettingsController::class, 'index'])->name('settings.complaints.index');
        Route::put('/settings/complaints/update', [\App\Http\Controllers\Back\ComplaintsSettingsController::class, 'update'])->name('settings.complaints.update');

        Route::get('/settings/survey-links', [\App\Http\Controllers\Back\SurveyLinksController::class, 'index'])->name('settings.survey-links.index');
        Route::post('/settings/survey-links/store', [\App\Http\Controllers\Back\SurveyLinksController::class, 'store'])->name('settings.survey-links.store');

        Route::get('/settings/disable-website', [\App\Http\Controllers\Back\DisableWebsiteController::class, 'index'])->name('settings.disable-website.index');
        Route::put('/settings/disable-website', [\App\Http\Controllers\Back\DisableWebsiteController::class, 'update'])->name('settings.disable-website.update');
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

        Route::resource('companies', \App\Http\Controllers\Back\CompaniesController::class);
        Route::prefix('companies')->name('companies.')->group(function() {
            Route::get('{company_id}/contracts/{contract_id}/attachments', [\App\Http\Controllers\Back\CompaniesContractsController::class, 'attachments'])->name('contracts.attachments');
            Route::post('{company_id}/contracts/{contract}/attachments/upload', [\App\Http\Controllers\Back\CompaniesContractsController::class, 'storeAttachments'])->name('contracts.attachments.store');
            Route::delete('{company_id}/contracts/{contract}/attachments/delete', [\App\Http\Controllers\Back\CompaniesContractsController::class, 'deleteAttachments'])->name('contracts.attachments.delete');
            Route::get('{company_id}/trainees/excel', [\App\Http\Controllers\Back\CompaniesContractsController::class, 'excel'])->name('trainees.excel');
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

        Route::get('/trainees/{id}/send-private-notification', [\App\Http\Controllers\Back\TraineesController::class, 'sendPrivateNotificationForm'])->name('trainees.private-notifications.create');
        Route::post('/trainees/{id}/send-private-notification/send', [\App\Http\Controllers\Back\TraineesController::class, 'sendPrivateNotification'])->name('trainees.private-notifications.send');
        Route::get('trainees/send-notification', [\App\Http\Controllers\Back\TraineesController::class, 'sendNotificationForm'])->name('trainees.send-notification');
        Route::post('trainees/send-notification/send', [\App\Http\Controllers\Back\TraineesController::class, 'sendNotification'])->name('trainees.send-notification.send');
        Route::post('trainees/{trainee_id}/set-password', [\App\Http\Controllers\Back\TraineesController::class, 'setPassword'])->name('trainees.set-password');
        Route::post('trainees/{trainee_id}/re-send-invitation', [\App\Http\Controllers\Back\TraineesController::class, 'resendInvitation'])->name('trainees.re-send-invitation');
        Route::put('/trainees/{id}/update-deleted-remark', [\App\Http\Controllers\Back\TraineesController::class, 'updatedDeletedRemark'])->name('trainees.update-deleted-remark');

        // Attendance management of trainee.
        Route::get('trainees/{trainee_id}/warnings', [\App\Http\Controllers\Back\TraineesController::class, 'warnings'])->name('trainees.warnings');
        Route::delete('trainees/{trainee_id}/warnings/all', [\App\Http\Controllers\Back\TraineesController::class, 'warningDeleteAll'])->name('trainees.warnings.delete.all');
        Route::delete('trainees/{trainee_id}/warnings/{id}', [\App\Http\Controllers\Back\TraineesController::class, 'warningDelete'])->name('trainees.warnings.delete');

        // Export For Trainees.
        Route::get('trainees/excel/{id}/download', [\App\Http\Controllers\Back\TraineesController::class, 'excelJobDownload'])->name('trainees.excel.job.download');
        Route::get('trainees/excel/{id}', [\App\Http\Controllers\Back\TraineesController::class, 'excelJob'])->name('trainees.excel.job');
        Route::post('trainees/excel', [\App\Http\Controllers\Back\TraineesController::class, 'excel'])->name('trainees.excel');

        // Trainees
        Route::get('trainees/block-list', [\App\Http\Controllers\Back\TraineesBlockListController::class, 'index'])->name('trainees.block-list.index');
        Route::get('trainees/block-list/create', [\App\Http\Controllers\Back\TraineesBlockListController::class, 'create'])->name('trainees.block-list.create');
        Route::post('trainees/block-list', [\App\Http\Controllers\Back\TraineesBlockListController::class, 'store'])->name('trainees.block-list.store');
        Route::delete('trainees/block-list/{id}', [\App\Http\Controllers\Back\TraineesBlockListController::class, 'destroy'])->name('trainees.block-list.delete');
        Route::get('trainees/block-list/import', [\App\Http\Controllers\Back\TraineesBlockListController::class, 'import'])->name('trainees.block-list.import');
        Route::post('trainees/block-list/import', [\App\Http\Controllers\Back\TraineesBlockListController::class, 'importCsv'])->name('trainees.block-list.import-csv');
        Route::get('trainees/block-list/download', [\App\Http\Controllers\Back\TraineesBlockListController::class, 'download'])->name('trainees.block-list.download');
        Route::get('trainees/archived/excel/{id}/download', [\App\Http\Controllers\Back\TraineesController::class, 'excelJobDownload'])->name('trainees.archived.excel.job.download');
        Route::get('trainees/archived/excel/{id}', [\App\Http\Controllers\Back\TraineesController::class, 'excelJob'])->name('trainees.archived.excel.job');
        Route::post('trainees/archived/excel', [\App\Http\Controllers\Back\TraineesController::class, 'archivedExcel'])->name('trainees.archived.excel');
        Route::post('trainees/{trainee_id}/approve-user', [\App\Http\Controllers\Back\TraineesController::class, 'approveUser'])->name('trainees.approve-user');
        Route::post('trainees/{trainee_id}/create-user', [\App\Http\Controllers\Back\TraineesController::class, 'createUser'])->name('trainees.create-user');
        Route::post('trainees/assign-instructor', [\App\Http\Controllers\Back\TraineesController::class, 'assignInstructor'])->name('trainees.assign-instructor');
        Route::post('trainees/{trainee_id}/attachments/identity', [\App\Http\Controllers\Back\TraineesController::class, 'storeIdentity'])->name('trainees.attachments.identity');
        Route::delete('trainees/{trainee_id}/attachments/identity', [\App\Http\Controllers\Back\TraineesController::class, 'deleteIdentity'])->name('trainees.attachments.identity.destroy');
        Route::post('trainees/{trainee_id}/attachments/qualification', [\App\Http\Controllers\Back\TraineesController::class, 'storeQualification'])->name('trainees.attachments.qualification');
        Route::delete('trainees/{trainee_id}/attachments/qualification', [\App\Http\Controllers\Back\TraineesController::class, 'deleteQualification'])->name('trainees.attachments.qualification.destroy');
        Route::post('trainees/{trainee_id}/attachments/bank-account', [\App\Http\Controllers\Back\TraineesController::class, 'storeBankAccount'])->name('trainees.attachments.bank-account');
        Route::delete('trainees/{trainee_id}/attachments/bank-account', [\App\Http\Controllers\Back\TraineesController::class, 'deleteBankAccount'])->name('trainees.attachments.bank-account.destroy');
        Route::post('trainees/{trainee_id}/attachments/national-address', [\App\Http\Controllers\Back\TraineesController::class, 'storeNationalAddress'])->name('trainees.attachments.national-address');
        Route::delete('trainees/{trainee_id}/attachments/national-address', [\App\Http\Controllers\Back\TraineesController::class, 'deleteNationalAddress'])->name('trainees.attachments.national-address.destroy');
        Route::post('trainees/{trainee_id}/attachments/cv', [\App\Http\Controllers\Back\TraineesController::class, 'storeCv'])->name('trainees.attachments.cv');
        Route::delete('trainees/{trainee_id}/attachments/cv', [\App\Http\Controllers\Back\TraineesController::class, 'deleteCv'])->name('trainees.attachments.cv.destroy');
        Route::get('trainees/{trainee_id}/block', [\App\Http\Controllers\Back\TraineesController::class, 'blockView'])->name('trainees.block');
        Route::post('trainees/{trainee_id}/block', [\App\Http\Controllers\Back\TraineesController::class, 'block'])->name('trainees.block.store');
        Route::post('trainees/{trainee_id}/suspend', [\App\Http\Controllers\Back\TraineesController::class, 'suspend'])->name('trainees.suspend.store');
        Route::get('trainees/{trainee_id}/suspend/create', [\App\Http\Controllers\Back\TraineesController::class, 'suspendCreate'])->name('trainees.suspend.create');
        Route::get('trainees/suspend/{trainee_block_list_id}/edit', [\App\Http\Controllers\Back\TraineesController::class, 'suspendEdit'])->name('trainees.suspend.edit');
        Route::put('trainees/suspend/{trainee_block_list_id}/update', [\App\Http\Controllers\Back\TraineesController::class, 'suspendUpdate'])->name('trainees.suspend.update');
        Route::get('trainees/blocked/show/{trainee_id}', [\App\Http\Controllers\Back\TraineesController::class, 'showBlocked'])->name('trainees.show.blocked');
        Route::post('trainees/blocked/show/{trainee_id}', [\App\Http\Controllers\Back\TraineesController::class, 'unblock'])->name('trainees.unblock');
        Route::get('trainees/archived', [\App\Http\Controllers\Back\TraineesController::class, 'indexArchived'])->name('trainees.index.archived');
        Route::resource('trainees', \App\Http\Controllers\Back\TraineesController::class);
        Route::get('candidates', [\App\Http\Controllers\Back\CandidatesController::class, 'index'])->name('candidates.index');

        // Export For Candidates.
        Route::get('candidates/excel/{id}/download', [\App\Http\Controllers\Back\TraineesController::class, 'excelJobDownload'])->name('candidates.excel.job.download');
        Route::get('candidates/excel/{id}', [\App\Http\Controllers\Back\TraineesController::class, 'excelJob'])->name('candidates.excel.job');
        Route::post('candidates/excel', [\App\Http\Controllers\Back\CandidatesController::class, 'excel'])->name('candidates.excel');

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
        Route::get('courses/today', [\App\Http\Controllers\Back\CoursesController::class, 'indexToday'])->name('courses.today');
        Route::resource('courses', \App\Http\Controllers\Back\CoursesController::class);

        Route::prefix('reports')->group(function() {

            Route::get('/', [\App\Http\Controllers\Back\ReportsController::class, 'index'])->name('reports.index');
            Route::post('course-attendances/generate', [\App\Http\Controllers\Back\ReportsController::class, 'generateCourseAttendanceReport'])->name('reports.course-attendances.generate');
            Route::get('course-attendances', [\App\Http\Controllers\Back\ReportsController::class, 'formCourseAttendanceReport'])->name('reports.course-attendances.index');
            Route::get('contracts', [\App\Http\Controllers\Back\ReportsController::class, 'formContractsReport'])->name('reports.contracts.index');
            Route::post('contracts/generate', [\App\Http\Controllers\Back\ReportsController::class, 'generateContractsReport'])->name('reports.contracts.generate');

        });
    });

    // For instructors
    Route::prefix('teaching')->middleware('redirect-trainees-to-dashboard')->name('teaching.')->group(function() {
        Route::get('/', [\App\Http\Controllers\Teaching\TeachingController::class, 'index'])->name('index');
        Route::resource('courses', \App\Http\Controllers\Teaching\CoursesController::class);

        // WIP
        Route::put('/course-batch-sessions/{course_batch_session_id}/attendance/trainee', [\App\Http\Controllers\CourseBatchSessionsAttendanceController::class, 'updateTraineeAttendance'])->name('course-batch-sessions.attendance.trainee.status');

        // Preparing attendance
        Route::put('/attendance-report-record/{attendance_report_record_id}', [\App\Http\Controllers\AttendanceReportRecordsController::class, 'update'])->name('attendance-report-records.update');
        Route::get('/course-batch-sessions/{course_batch_session_id}/attendance-reports', [\App\Http\Controllers\AttendanceReportsController::class, 'show'])->name('course-batch-sessions.attendance-reports.show');
        Route::post('/attendance-reports/{attendance_report_id}/close-attendance', [\App\Http\Controllers\AttendanceReportsController::class, 'close'])->name('course-batch-sessions.attendance-reports.close-attendance');
        Route::get('/attendance-reports/{attendance_report_id}', [\App\Http\Controllers\AttendanceReportsController::class, 'showReport'])->name('course-batch-sessions.attendance-reports.show-report');
        Route::get('/attendance-reports/{attendance_report_id}/attendances', [\App\Http\Controllers\AttendanceReportsController::class, 'attendances'])->name('attendance-reports.attendances');
        Route::get('/attendance-reports/{attendance_report_id}/confirm', [\App\Http\Controllers\AttendanceReportsController::class, 'confirm'])->name('attendance-reports.confirm');
        Route::post('/attendance-reports/{attendance_report_id}/approve', [\App\Http\Controllers\AttendanceReportsController::class, 'approve'])->name('attendance-reports.approve');
        Route::get('/attendance-reports/{attendance_report_id}/excel', [\App\Http\Controllers\AttendanceReportsController::class, 'excel'])->name('attendance-reports.excel');

        Route::get('/course-batch-sessions/{course_batch_session_id}/attendance/excel', [\App\Http\Controllers\CourseBatchSessionsAttendanceController::class, 'excel'])->name('course-batch-sessions.attendance.excel');
        Route::get('/course-batch-sessions/{course_batch_session_id}/attendance/export', [\App\Http\Controllers\CourseBatchSessionsAttendanceController::class, 'attendingExcel'])->name('course-batch-sessions.attendance.export');
        Route::post('/course-batch-sessions/{course_batch_session_id}/confirm/approve', [\App\Http\Controllers\CourseBatchSessionsAttendanceController::class, 'approve'])->name('course-batch-sessions.attendance.confirm.approve');
        Route::get('/course-batch-sessions/{course_batch_session_id}/confirm', [\App\Http\Controllers\CourseBatchSessionsAttendanceController::class, 'confirm'])->name('course-batch-sessions.attendance.confirm');
        Route::get('/course-batch-sessions/{course_batch_session_id}/attendance', [\App\Http\Controllers\CourseBatchSessionsAttendanceController::class, 'index'])->name('course-batch-sessions.attendance.index');
        Route::put('/course-batch-sessions/{course_batch_session_id}/attendance/{id}', [\App\Http\Controllers\CourseBatchSessionsAttendanceController::class, 'update'])->name('course-batch-sessions.attendances.update');

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

        Route::get('attendance-sheet', [\App\Http\Controllers\Trainees\AttendanceSheetController::class, 'index'])->name('attendance-sheet.index');
        Route::get('training-packages', [\App\Http\Controllers\Trainees\TrainingPackagesController::class, 'index'])->name('training-packages.index');

        Route::get('training-plan', [\App\Http\Controllers\Trainees\TrainingPlanController::class, 'index'])->name('training-plan.index');

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
