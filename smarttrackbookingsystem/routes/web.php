<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\Organization\BusinessController;
use App\Http\Controllers\Organization\BusinessAdminController;
use App\Http\Controllers\Business\AdminController;
use Illuminate\Support\Str;

Route::get('/', function () {
    return view('home');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::middleware(['auth', 'usertype:org_admin'])
    ->prefix('org')        // adds /org before all URLs
    ->name('org.')         // adds org. before all route names
    ->group(function () {

        Route::get('/dashboard', [OrganizationController::class, 'index'])->name('dashboard');

        // Business Routes in Organization
        Route::get('/business-accounts',[BusinessController::class,'all_business_accounts'])->name('business-accounts');
        Route::get('/businesses/data', [BusinessController::class,'getBusinesses'])->name('business.data');
        Route::get('/add-new-business',[BusinessController::class,'add_new_business'])->name('add-new-business');
        Route::post('/store-business',[BusinessController::class,'store_business'])->name('store.business');
        Route::get('/businesses/{business:slug}/edit', [BusinessController::class,'edit'])->name('business.edit');
        Route::put('/businesses/{business:slug}', [BusinessController::class,'update'])->name('business.update');

        Route::get('/businesses/{business:slug}', [BusinessController::class,'show'])->name('business.show');

        // Business Admin Routes in Organization 
        Route::get('/businesses/{business:slug}/admins/create', [BusinessAdminController::class,'create'])->name('business.admins.create');
        Route::post('/businesses/{business:slug}/admins', [BusinessAdminController::class,'store'])->name('business.admins.store');
        

});


Route::middleware(['auth', 'usertype:employee'])
    ->get('/employee/dashboard', fn () => view('business.employee.dashboard'))
    ->name('employee.dashboard');

Route::middleware(['auth', 'usertype:customer'])
    ->get('/customer/dashboard', fn () => view('business.customer.dashboard'))
    ->name('customer.dashboard');

require __DIR__.'/business.php';
require __DIR__.'/employee.php';