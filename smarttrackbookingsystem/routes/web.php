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

Route::prefix('{business:slug}/admin')
    ->name('business.')
    ->middleware(['auth', 'usertype:business_admin'])
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Business\AdminController::class, 'index'])->name('dashboard');
        Route::get('/settings', [App\Http\Controllers\Business\BusinessSettingsController::class, 'index'])->name('settings');
        Route::put('/settings/general', [App\Http\Controllers\Business\BusinessSettingsController::class, 'updateGeneral'])->name('settings.general');
        Route::put('/settings/appearance', [App\Http\Controllers\Business\BusinessSettingsController::class, 'updateAppearance'])->name('settings.appearance');
        Route::post('/settings/remove-logo', [App\Http\Controllers\Business\BusinessSettingsController::class, 'removeLogo'])->name('settings.remove-logo');
        
        // API endpoint for dynamic updates
        Route::get('/settings/data', function($business) {
            return response()->json([
                'colors' => App\Helpers\BusinessSettingsHelper::getColors($business),
                'font_family' => App\Helpers\BusinessSettingsHelper::get($business, 'font_family', 'Inter, sans-serif'),
                'settings' => App\Helpers\BusinessSettingsHelper::getAll($business)
            ]);
        })->name('settings.data');
    });

Route::middleware(['auth', 'usertype:employee'])
    ->get('/employee/dashboard', fn () => view('business.employee.dashboard'))
    ->name('employee.dashboard');

Route::middleware(['auth', 'usertype:customer'])
    ->get('/customer/dashboard', fn () => view('business.customer.dashboard'))
    ->name('customer.dashboard');