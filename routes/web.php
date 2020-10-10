<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('language/{language}', function ($language) {
    session()->put('locale', $language);
    return redirect()->back();
})->name('language');

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia\Inertia::render('Dashboard');
})->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    Route::prefix('back')->name('back.')->group(function() {
        Route::resource('companies', \App\Http\Controllers\Back\CompaniesController::class);

        Route::prefix('companies')->name('companies.')->group(function() {
            Route::get('{company_id}/contracts/{contract_id}/attachments', [\App\Http\Controllers\Back\CompaniesContractsController::class, 'attachments'])->name('contracts.attachments');
            Route::resource('{company_id}/contracts', \App\Http\Controllers\Back\CompaniesContractsController::class);
        });

        Route::get('finance', [\App\Http\Controllers\Back\FinanceController::class, 'index'])->name('finance');

        Route::prefix('finance')->name('finance.')->group(function() {
            Route::resource('accounts', \App\Http\Controllers\Back\FinancialAccountsController::class);
            Route::resource('invoices', \App\Http\Controllers\Back\FinancialInvoicesController::class);
            Route::get('monthly-subscription/edit', [\App\Http\Controllers\Back\FinancialMonthlySubscriptionController::class, 'edit'])->name('monthly-subscription.edit');
            Route::put('monthly-subscription', [\App\Http\Controllers\Back\FinancialMonthlySubscriptionController::class, 'update'])->name('monthly-subscription.update');
        });
    });
});
