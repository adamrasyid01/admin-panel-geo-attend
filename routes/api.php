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
    Route::post('/', [AnnouncementController::class, 'store']);
    Route::get('/{announcement}', [AnnouncementController::class, 'show']);
    Route::put('/{announcement}', [AnnouncementController::class, 'update']);
    Route::patch('/{announcement}', [AnnouncementController::class, 'update']);
    Route::delete('/{announcement}', [AnnouncementController::class, 'destroy']);
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
    Route::get('/', [LeaveRequestController::class, 'index']);
    Route::post('/', [LeaveRequestController::class, 'store']);
    Route::get('/{leaveRequest}', [LeaveRequestController::class, 'show']);
    Route::put('/{leaveRequest}', [LeaveRequestController::class, 'update']);
    Route::patch('/{leaveRequest}', [LeaveRequestController::class, 'update']);
    Route::delete('/{leaveRequest}', [LeaveRequestController::class, 'destroy']);
});

// Note 
Route::prefix('notes')->middleware('auth:sanctum')->name('notes.')->group(function () {
    Route::get('/', [NoteController::class, 'index']);
    Route::post('/', [NoteController::class, 'store']);
    Route::get('/{note}', [NoteController::class, 'show']);
    Route::put('/{note}', [NoteController::class, 'update']);
    Route::patch('/{note}', [NoteController::class, 'update']);
    Route::delete('/{note}', [NoteController::class, 'destroy']);
});

// Overtime Request
Route::prefix('overtime-requests')->middleware('auth:sanctum')->name('overtime-requests.')->group(function () {
    Route::get('/', [OvertimeRequestController::class, 'index']);
    Route::post('/', [OvertimeRequestController::class, 'store']);
    Route::get('/{overtimeRequest}', [OvertimeRequestController::class, 'show']);
    Route::put('/{overtimeRequest}', [OvertimeRequestController::class, 'update']);
    Route::patch('/{overtimeRequest}', [OvertimeRequestController::class, 'update']);
    Route::delete('/{overtimeRequest}', [OvertimeRequestController::class, 'destroy']);
});

// Shift 
Route::prefix('shifts')->middleware('auth:sanctum')->name('shifts.')->group(function () {
    Route::get('/', [ShiftController::class, 'index']);
    Route::post('/', [ShiftController::class, 'store']);
    Route::get('/{shift}', [ShiftController::class, 'show']);
    Route::put('/{shift}', [ShiftController::class, 'update']);
    Route::patch('/{shift}', [ShiftController::class, 'update']);
    Route::delete('/{shift}', [ShiftController::class, 'destroy']);
});

// Task
Route::prefix('tasks')->middleware('auth:sanctum')->name('tasks.')->group(function () {
    Route::get('/', [TaskController::class, 'index']);
    Route::post('/', [TaskController::class, 'store']);
    Route::get('/{task}', [TaskController::class, 'show']);
    Route::put('/{task}', [TaskController::class, 'update']);
    Route::patch('/{task}', [TaskController::class, 'update']);
    Route::delete('/{task}', [TaskController::class, 'destroy']);
});

// WFH Request
Route::prefix('wfh-requests')->middleware('auth:sanctum')->name('wfh-requests.')->group(function () {
    Route::get('/', [WfhRequestController::class, 'index']);
    Route::post('/', [WfhRequestController::class, 'store']);
    Route::get('/{wfhRequest}', [WfhRequestController::class, 'show']);
    Route::put('/{wfhRequest}', [WfhRequestController::class, 'update']);
    Route::patch('/{wfhRequest}', [WfhRequestController::class, 'update']);
    Route::delete('/{wfhRequest}', [WfhRequestController::class, 'destroy']);
});
