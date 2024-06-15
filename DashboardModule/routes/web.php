<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Modules\DashboardModule\Livewire\Dashboard;
use Modules\DashboardModule\Livewire\Home;

if (setting('dashboardmodule.routes.dashboard')) {
    Route::get('/dashboard', Dashboard::class)->name('dashboard')->middleware('auth');
}

if (setting('dashboardmodule.routes.home')) {
    Route::get('/', Home::class)->name('home');
}