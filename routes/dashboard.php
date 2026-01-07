<?php
use App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath',],
    ],
    function () {
        Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
            Route::get('dashboard', Dashboard\DashboardController::class)->name('dashboard');
        });
        Route::prefix('client')->name('client.')->middleware('auth:client')->group(function () {
            Route::get('dashboard', Dashboard\DashboardController::class)->name('dashboard');
        });
    }
);
