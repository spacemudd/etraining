<?php
Route::middleware(['auth:sanctum'])->prefix('sales')->group(function() {
    Route::get('dashboard', [\App\Http\Controllers\SalesDashboardController::class, 'index']);

    Route::resource('companies', \App\Http\Controllers\Sales\CompaniesController::class);
});
