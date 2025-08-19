<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\OvertimeRequestController;
use App\Http\Controllers\Api\UserController;

// AUTH API
Route::name('auth.')->group(function () {
    Route::post('/login', [UserController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [UserController::class, 'logout']);
    });
});


// Route Attendances
Route::prefix('attendances')->middleware('auth:sanctum')->name('attendances.')->group(function () {
    Route::get('/', [AttendanceController::class, 'index']);
    Route::get('/{attendance}', [AttendanceController::class, 'show']);
    Route::post('/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/check-out/{attendance}', [AttendanceController::class, 'checkOut']);
    Route::post('/leave-request', [AttendanceController::class, 'createLeaveRequest']);
});

// Overtime Request
Route::prefix('overtime-requests')->middleware('auth:sanctum')->name('overtime-requests.')->group(function () {
    Route::post('/', [OvertimeRequestController::class, 'createOvertimeRequest']);
});
