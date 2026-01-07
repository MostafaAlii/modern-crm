<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard;
//$guards = get_guard();
$languages = ['en', 'ar'];

/*foreach ($languages as $lang) {
    Route::prefix($lang)->group(function () use ($guards) {
        foreach ($guards as $guard) {
            Route::prefix($guard)->name($guard . '.')->group(function () use ($guard) {
                Route::get('login', [Dashboard\AuthController::class, 'showLoginForm'])->name('login');
                Route::post('login', [Dashboard\AuthController::class, "login" . ucfirst($guard)])->name('login.submit');
                Route::post('logout', [Dashboard\AuthController::class, "logout" . ucfirst($guard)])->middleware('auth:' . $guard)->name('logout');
            });
        }
    });
}*/

foreach (get_guard() as $guard) {
    Route::prefix($guard)->name($guard . '.')->group(function () use ($guard) {
        Route::get('login', [Dashboard\AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [Dashboard\AuthController::class, "login" . ucfirst($guard)])->name('login.submit');
        Route::post('logout', [Dashboard\AuthController::class, "logout" . ucfirst($guard)])->middleware('auth:' . $guard)->name('logout');
    });
}