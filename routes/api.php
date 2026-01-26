<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\Dashboard\Api;

// ============================================
// ملاحظة مهمة:
// Laravel بيضيف 'api' prefix تلقائياً من bootstrap/app.php
// فمتحطش 'api' في الـ prefix هنا
// ============================================

// Auth Routes - بدون LaravelLocalization
Route::post('auth/refresh', [Dashboard\TokenController::class, 'refresh'])->name('auth.refresh');

foreach (get_guard() as $guard) {
    Route::prefix($guard)->name($guard . '.')->group(function () use ($guard) {
        // Public routes
        Route::post('login', [Dashboard\AuthController::class, 'login'])->name('login');
        Route::post('logout', [Dashboard\AuthController::class, 'logout'])->name('logout');

        // Protected routes
        Route::middleware(['auth:' . $guard . '_api'])->group(function () {
            Route::post('logout-all', [Dashboard\TokenController::class, 'logoutAllDevices'])->name('logout.all');
            Route::get('sessions', [Dashboard\TokenController::class, 'listDevices'])->name('sessions');
            Route::post('revoke-session', [Dashboard\TokenController::class, 'revokeSession'])->name('revoke.session');
            Route::get('statistics', [Dashboard\TokenController::class, 'statistics'])->name('statistics');
        });
    });
}

// Locales Routes
Route::prefix('locales')->name('locales.')->controller(Api\LocalizationController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('active', 'active')->name('active');
    Route::get('inactive', 'inactive')->name('inactive');
    Route::patch('toggle-status', 'toggleStatus')->name('toggle');
});