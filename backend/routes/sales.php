<?php
Route::middleware(['auth:sanctum'])->prefix('sales')->group(function() {
    Route::get('dashboard', [\App\Http\Controllers\SalesDashboardController::class, 'index']);
});
