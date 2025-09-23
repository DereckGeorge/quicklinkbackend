<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HospitalController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\HomeVisitController;
use App\Http\Controllers\Api\EmergencyController;

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

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Protected routes
Route::middleware('auth:api')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });
    
    Route::get('user/profile', [AuthController::class, 'profile']);
    
    // Appointment routes
    Route::post('appointments', [AppointmentController::class, 'store']);
    Route::get('appointments', [AppointmentController::class, 'index']);

    // Home visits protected routes
    Route::post('home-visits/book', [HomeVisitController::class, 'book']);
    Route::get('home-visits/bookings', [HomeVisitController::class, 'myBookings']);
});

// Public hospital and doctor routes
Route::get('hospitals', [HospitalController::class, 'index']);
Route::get('hospitals/{hospitalId}/doctors', [HospitalController::class, 'getDoctors']);
Route::get('doctors/{doctorId}/availability', [DoctorController::class, 'getAvailability']);

// Home visits public route
Route::get('home-visits', [HomeVisitController::class, 'index']);

// Emergency route
Route::post('emergency/request', [EmergencyController::class, 'requestEmergency']);
