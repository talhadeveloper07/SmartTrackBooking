<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Frontend\Service\ServiceController;
use App\Http\Controllers\Api\Frontend\Appointment\AvailabilityController;
use App\Http\Controllers\Api\Frontend\Auth\AuthController;
use App\Http\Controllers\Api\Frontend\Appointment\BookingController;
use App\Http\Controllers\Api\Organization\PlanController;
use App\Http\Controllers\Organization\BusinessSubscriptionController;


Route::get('/business/{business:slug}/services', [ServiceController::class, 'byBusiness']);
Route::get('/business/{business:slug}/services/{service}', [ServiceController::class, 'serviceDetails']);
Route::get('/business/{business:slug}/available-dates', [AvailabilityController::class, 'availableDates']);
Route::get('/business/{business:slug}/available-slots', [AvailabilityController::class, 'availableSlots']);

// User Auth routes
Route::post('/business/{business:slug}/auth/login', [AuthController::class, 'login']);
Route::post('/business/{business:slug}/auth/register', [AuthController::class, 'register']);
Route::post('/business/{business:slug}/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Appointment Booking Routes 
Route::post('/business/{business:slug}/appointments/book', [BookingController::class, 'store']);

Route::prefix('admin')->group(function(){

    Route::get('plans', [PlanController::class,'index']);

    Route::get('plans/{id}', [PlanController::class,'show']);

    Route::post('plans', [PlanController::class,'store']);

    Route::put('plans/{id}', [PlanController::class,'update']);

    Route::delete('plans/{id}', [PlanController::class,'destroy']);

});

// routes/api.php

Route::prefix('business/{business}')->group(function () {
    Route::post('/subscribe', [BusinessSubscriptionController::class, 'store']);
    Route::get('/subscription', [BusinessSubscriptionController::class, 'show']);
    Route::post('/cancel-subscription', [BusinessSubscriptionController::class, 'cancel']);
});