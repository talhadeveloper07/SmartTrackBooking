<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Business\AdminController;
use App\Http\Controllers\Business\ServiceController;
use App\Http\Controllers\Business\EmployeeController;
use App\Http\Controllers\Business\CustomerController;
use App\Http\Controllers\Business\AppointmentController;

Route::prefix('{business:slug}/admin')
    ->name('business.')
    ->middleware(['auth','usertype:business_admin','inject.business'])
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

         // Customers
        Route::get('customers', [CustomerController::class,'index'])->name('customers.index');
        Route::get('customers/create', [CustomerController::class,'create'])->name('customers.create');
        Route::post('customers', [CustomerController::class,'store'])->name('customers.store');
        Route::get('customers/{customer}', [CustomerController::class,'show'])->name('customers.show');
        Route::get('customers/{customer}/edit', [CustomerController::class,'edit'])->name('customers.edit');
        Route::put('customers/{customer}', [CustomerController::class,'update'])->name('customers.update');
        Route::delete('customers/{customer}', [CustomerController::class,'destroy'])->name('customers.destroy');

        // DataTable Ajax
        Route::get('customers-dt', [CustomerController::class,'datatable'])->name('customers.dt');

         // Dashbaord Settings
        Route::get('/settings', [AdminController::class, 'edit'])
            ->name('settings.edit');

        Route::put('/settings', [AdminController::class, 'update'])
            ->name('settings.update');

        // Profile Settings

        Route::get('/profile', [AdminController::class, 'edit_profile'])->name('profile.edit');
        Route::put('/profile', [AdminController::class, 'update_profile'])->name('profile.update');
        Route::post('/profile/password/email', [AdminController::class, 'sendPasswordResetLink'])
            ->name('profile.password.email');

        // Appointment routes
        Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
        Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
       Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])
            ->whereNumber('appointment')
            ->name('appointments.show');

        Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])
            ->whereNumber('appointment')
            ->name('appointments.cancel');

        // Appointment AJAX
        Route::get('/appointments/service/{service}', [AppointmentController::class, 'serviceDetails'])
            ->name('appointments.service.details');

        Route::get('/appointments/available-slots', [AppointmentController::class, 'availableSlots'])
            ->name('appointments.available.slots');
        Route::get('/appointments/data', [AppointmentController::class, 'data'])->name('appointments.data');


});