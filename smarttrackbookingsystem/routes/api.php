<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Frontend\Service\ServiceController;
use App\Http\Controllers\Api\Frontend\Appointment\AvailabilityController;


Route::get('/business/{business:slug}/services', [ServiceController::class, 'byBusiness']);
Route::get('/business/{business:slug}/services/{service}', [ServiceController::class, 'serviceDetails']);
Route::get('/business/{business:slug}/available-dates', [AvailabilityController::class, 'availableDates']);
Route::get('/business/{business:slug}/available-slots', [AvailabilityController::class, 'availableSlots']);