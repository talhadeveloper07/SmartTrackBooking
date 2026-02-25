<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\EmployeeSetPasswordController;

Route::get('/set-password/{user}/{token}', [EmployeeSetPasswordController::class, 'show'])
    ->name('password.set');

Route::post('/set-password/{user}/{token}', [EmployeeSetPasswordController::class, 'update'])
    ->name('password.set.update');