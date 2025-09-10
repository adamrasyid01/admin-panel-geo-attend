<?php

use App\Http\Controllers\Api\AnnouncementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DiskonController;
use App\Http\Controllers\Api\LeaveRequestController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\OjolController;
use App\Http\Controllers\Api\OvertimeRequestController;
use App\Http\Controllers\Api\PajakController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WfhRequestController;

// AUTH API
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});


// Route Announcements
Route::prefix('announcements')->middleware('auth:api')->name('announcements.')->group(function () {
    Route::get('/', [AnnouncementController::class, 'index']);
});

// Route Attendances
Route::prefix('attendances')->middleware('auth:api')->name('attendances.')->group(function () {
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

// Shift 
Route::prefix('shifts')->middleware('auth:sanctum')->name('shifts.')->group(function () {
    Route::get('/', [ShiftController::class, 'show']);
});

// Task
Route::prefix('tasks')->middleware('auth:sanctum')->name('tasks.')->group(function () {
    Route::get('/', [TaskController::class, 'index']);
});

// WFH Request
Route::prefix('wfh-requests')->middleware('auth:sanctum')->name('wfh-requests.')->group(function () {
    Route::post('/', [WfhRequestController::class, 'store']);
});


