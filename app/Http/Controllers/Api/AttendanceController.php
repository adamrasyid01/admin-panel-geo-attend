<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CheckInAttendanceRequest;
use App\Http\Requests\Api\CheckOutAttendanceRequest;
use App\Http\Resources\Api\AttendanceResource;
use App\Http\Resources\Api\LeaveRequestResource;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    //Daftar semua absensi milik pengguna yang sedang login.
    public function index()
    {
        $user = Auth::user();

        // Ambil hanya data absensi milik pengguna yang login
        $attendances = $user->attendances()->with('userShift')->get();

        return ResponseFormatter::success(AttendanceResource::collection($attendances));
    }

    // Tampilkan absensi tunggal KETIKA BERHASIL ABSEN
    public function show(Attendance $attendance)
    {
        // Pastikan relasi di-load sebelum dikirim
        $attendance->load(['user', 'userShift']);

        return ResponseFormatter::success(new AttendanceResource($attendance));
    }

    // Simpan check-in baru
    public function checkIn(CheckInAttendanceRequest $request)
    {
        $user = Auth::user();

        $attendance = $user->attendances()->create($request->validated());

        return ResponseFormatter::success(new AttendanceResource($attendance), 'Attendance created successfully.');
    }

    // simpan checkout
    public function checkOut(CheckOutAttendanceRequest $request, Attendance $attendance)
    {
        // Pastikan pengguna yang login adalah pemilik absensi ini
        if ($attendance->user_id !== Auth::id()) {
            return ResponseFormatter::error('Unauthorized.', 403);
        }

        $attendance->update($request->validated());

        return ResponseFormatter::success(new AttendanceResource($attendance), 'Attendance updated successfully.');
    }    
}
