<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessHourController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/appointments/check', [AppointmentController::class,'validateCreation']);
    Route::post('/appointments', [AppointmentController::class,'store']);
    Route::post('/appointments/{appointment}/add-services', [AppointmentController::class, 'addServices']);
    Route::get('/appointments', [AppointmentController::class,'index']);
    Route::get('/appointments/{appointment}', [AppointmentController::class,'show']);
    Route::put('/appointments/{appointment}', [AppointmentController::class,'update']);
    Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class,'cancel']);

    Route::get('/business-hours', [BusinessHourController::class, 'index']);

    Route::middleware('role:admin')->group(function () {
        Route::get('/services', [ServiceController::class, 'index']);
        Route::get('/services/{service}', [ServiceController::class, 'show']);
        Route::post('/services', [ServiceController::class, 'store']);
        Route::put('/services/{service}', [ServiceController::class, 'update']);
        Route::delete('/services/{service}', [ServiceController::class, 'destroy']);

        Route::post('/business-hours', [BusinessHourController::class, 'store']);
        Route::put('/business-hours/{businessHour}', [BusinessHourController::class, 'update']);
    });
});
