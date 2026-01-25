<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\Dashboard\Api;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath',],
    ],
    function () {
        foreach (get_guard() as $guard) {
            Route::prefix($guard)->name($guard . '.')->group(function () {
                Route::post('login', [Dashboard\AuthController::class, 'login']);
                Route::post('logout', [Dashboard\AuthController::class, 'logout']);
            });
        }
    }
);
Route::prefix('locales')->controller(Api\LocalizationController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('active', 'active');
    Route::get('inactive', 'inactive');
    Route::patch('toggle-status', 'toggleStatus');
});