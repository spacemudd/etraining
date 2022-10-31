<?php
Route::middleware(['auth:sanctum'])->prefix('sales')->group(function() {
    Route::get('dashboard', [\App\Http\Controllers\SalesDashboardController::class, 'index']);

    Route::resource('companies', \App\Http\Controllers\Sales\CompaniesController::class)
        ->names([
            'index' => 'sales.companies.index',
            'show' => 'sales.companies.show',
            'create' => 'sales.companies.create',
            'store' => 'sales.companies.store',
            'update' => 'sales.companies.update',
            'destroy' => 'sales.companies.destroy',
        ]);

    Route::resource('companies.comms', \App\Http\Controllers\Back\CompanyCommController::class);
});
