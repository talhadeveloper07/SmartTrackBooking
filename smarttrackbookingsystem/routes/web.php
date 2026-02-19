<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::middleware(['auth', 'usertype:org_admin'])
    ->get('/org/dashboard', fn () => view('organization.dashboard'))
    ->name('org.dashboard');

Route::middleware(['auth', 'usertype:business_admin'])
    ->get('/business/dashboard', fn () => view('business.dashboard'))
    ->name('business.dashboard');

Route::middleware(['auth', 'usertype:employee'])
    ->get('/employee/dashboard', fn () => view('business.employee.dashboard'))
    ->name('employee.dashboard');

Route::middleware(['auth', 'usertype:customer'])
    ->get('/customer/dashboard', fn () => view('business.customer.dashboard'))
    ->name('customer.dashboard');