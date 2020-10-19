<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('language/{language}', function ($language) {
    session()->put('locale', $language);
    return redirect()->back();
})->name('language');

Route::get('onboarding', [\App\Http\Controllers\OnboardingController::class, 'index']);

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {

    if (\Illuminate\Support\Str::contains(auth()->user()->roles()->first()->name, 'instructors')) {
        return \Inertia\Inertia::render('Teaching/Dashboard', [
            'courses' => \App\Models\Back\Course::paginate(15),
        ]);
    }

    return Inertia\Inertia::render('Dashboard', [
        'companies_count' => \App\Models\Back\Company::count(),
        'trainees_count' => \App\Models\Back\Trainee::count(),
        'instructors_count' => \App\Models\Back\Instructor::count(),
        'courses_count' => \App\Models\Back\Course::count(),
    ]);
})->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    Route::prefix('back')->name('back.')->group(function() {
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

        Route::post('trainees/assign-instructor', [\App\Http\Controllers\Back\TraineesController::class, 'assignInstructor'])->name('trainees.assign-instructor');
        Route::post('trainees/{trainee_id}/attachments/identity', [\App\Http\Controllers\Back\TraineesController::class, 'storeIdentity'])->name('trainees.attachments.identity');
        Route::delete('trainees/{trainee_id}/attachments/identity', [\App\Http\Controllers\Back\TraineesController::class, 'deleteIdentity'])->name('trainees.attachments.identity.destroy');
        Route::post('trainees/{trainee_id}/attachments/qualification', [\App\Http\Controllers\Back\TraineesController::class, 'storeQualification'])->name('trainees.attachments.qualification');
        Route::delete('trainees/{trainee_id}/attachments/qualification', [\App\Http\Controllers\Back\TraineesController::class, 'deleteQualification'])->name('trainees.attachments.qualification.destroy');
        Route::post('trainees/{trainee_id}/attachments/bank-account', [\App\Http\Controllers\Back\TraineesController::class, 'storeBankAccount'])->name('trainees.attachments.bank-account');
        Route::delete('trainees/{trainee_id}/attachments/bank-account', [\App\Http\Controllers\Back\TraineesController::class, 'deleteBankAccount'])->name('trainees.attachments.bank-account.destroy');
        Route::resource('trainees', \App\Http\Controllers\Back\TraineesController::class);

        Route::post('instructors/{instructor_id}/create-user', [\App\Http\Controllers\Back\InstructorsController::class, 'createUser'])->name('instructors.create-user');
        Route::post('instructors/{instructor_id}/attachments/cv-full', [\App\Http\Controllers\Back\InstructorsController::class, 'storeCvFull'])->name('instructors.attachments.cv-full');
        Route::delete('instructors/{instructor_id}/attachments/cv-full', [\App\Http\Controllers\Back\InstructorsController::class, 'deleteCvFull'])->name('instructors.attachments.cv-full.destroy');
        Route::post('instructors/{instructor_id}/attachments/cv-summary', [\App\Http\Controllers\Back\InstructorsController::class, 'storeCvSummary'])->name('instructors.attachments.cv-summary');
        Route::delete('instructors/{instructor_id}/attachments/cv-summary', [\App\Http\Controllers\Back\InstructorsController::class, 'deleteCvSummary'])->name('instructors.attachments.cv-summary.destroy');
        Route::resource('instructors', \App\Http\Controllers\Back\InstructorsController::class);


        Route::resource('courses/{course_id}/course-batches/{course_batch_id}/course-batch-sessions', \App\Http\Controllers\Back\CourseBatchSessionsController::class);

        Route::resource('courses/{course_id}/course-batches', \App\Http\Controllers\Back\CourseBatchesController::class);

        Route::post('courses/{course_id}/training-package', [\App\Http\Controllers\Back\CoursesController::class, 'storeTrainingPackage'])->name('courses.training-package');
        Route::delete('courses/{course_id}/training-package', [\App\Http\Controllers\Back\CoursesController::class, 'deleteTrainingPackage'])->name('courses.training-package.destroy');
        Route::resource('courses', \App\Http\Controllers\Back\CoursesController::class);
    });

    Route::prefix('teaching')->name('teaching.')->group(function() {
        Route::get('/', [\App\Http\Controllers\Teaching\TeachingController::class, 'index'])->name('index');
        Route::resource('courses', \App\Http\Controllers\Teaching\CoursesController::class);
    });
});
