<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
Route::group([
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect','localizationRedirect','localeViewPath',],
    ],function () {
        foreach (get_guard() as $guard) {
            Route::prefix($guard)->name($guard . '.')->group(function () {
                Route::get('login', [Dashboard\AuthController::class, 'showLoginForm'])->name('login');
                Route::post('login', [Dashboard\AuthController::class, 'login'])->name('login.submit');
                Route::post('logout', [Dashboard\AuthController::class, 'logout'])->name('logout');
            });
        }
    }
);
