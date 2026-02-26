<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Business\AdminController;
use App\Http\Controllers\Business\ServiceController;
use App\Http\Controllers\Business\EmployeeController;

Route::prefix('{business:slug}/admin')
    ->name('business.')
    ->middleware(['auth','usertype:business_admin'])
    ->group(function () {

        // Business Service Routes

        Route::get('dashboard',[AdminController::class,'index'])->name('dashboard');
        Route::get('services',[ServiceController::class,'index'])->name('services');
        Route::get('add-service',[ServiceController::class,'add_service'])->name('add.service');
        Route::post('insert-service',[ServiceController::class,'store'])->name('insert.services');
        Route::get('services/{service}/edit', [ServiceController::class,'edit'])->name('services.edit');
        Route::delete('services/{service}', [ServiceController::class,'destroy'])->name('services.destroy');
        Route::put('services/{service}',[ServiceController::class,'update'])->name('services.update');

        // Business Employee Routes

        Route::get('employees',[EmployeeController::class,'index'])->name('employees');
        Route::get('employees/data', [EmployeeController::class,'data'])->name('employees.data');
        Route::get('employees/create', [EmployeeController::class,'create'])->name('employees.create');
        Route::post('employees', [EmployeeController::class,'store'])->name('employees.store');
        Route::get('employees/{employee}', [EmployeeController::class, 'show_employee'])->name('employees.show');
        Route::get('employees/{employee}/edit',[EmployeeController::class,'edit'])->name('employees.edit');
        Route::put('employees/{employee}',[EmployeeController::class,'update'])->name('employees.update');

});