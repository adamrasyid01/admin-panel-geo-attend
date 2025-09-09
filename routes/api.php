<?php

use App\Http\Controllers\Api\AnnouncementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\DiskonController;
use App\Http\Controllers\Api\LeaveRequestController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\OjolController;
use App\Http\Controllers\Api\OvertimeRequestController;
use App\Http\Controllers\Api\PajakController;
use App\Http\Controllers\Api\UserController;

// AUTH API
Route::name('auth.')->group(function () {
    Route::post('/login', [UserController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [UserController::class, 'logout']);
    });
});


// Route Announcements
Route::prefix('announcements')->middleware('auth:sanctum')->name('announcements.')->group(function () {
    Route::get('/', [AnnouncementController::class, 'index']);
});

// Route Attendances
Route::prefix('attendances')->middleware('auth:sanctum')->name('attendances.')->group(function () {
    Route::get('/', [AttendanceController::class, 'index']);
    Route::get('/{attendance}', [AttendanceController::class, 'show']);
    Route::post('/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/check-out/{attendance}', [AttendanceController::class, 'checkOut']);
});

// Leave Requests
Route::prefix('leave-requests')->middleware('auth:sanctum')->name('leave-requests.')->group(function () {
    Route::post('/', [LeaveRequestController::class, 'store']);
});

// Note 
Route::prefix('notes')->middleware('auth:sanctum')->name('notes.')->group(function () {
    Route::post('/', [NoteController::class, 'store']);
});

// Overtime Request
Route::prefix('overtime-requests')->middleware('auth:sanctum')->name('overtime-requests.')->group(function () {
    Route::post('/', [OvertimeRequestController::class, 'createOvertimeRequest']);
});

// 


