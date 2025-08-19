<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\AttendanceResource;
use App\Http\Resources\Api\LeaveRequestResource;
use App\Models\Attendance;
use Illuminate\Http\Request;
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
    public function checkIn(Request $request)
    {
        $user = Auth::user();

        // Logika validasi dan penyimpanan data absensi dari Flutter
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'user_shift_id' => 'required|exists:user_shifts,id',
            'photo' => 'required|string',
            'check_in_time' => 'required|date',
            'check_in_location' => 'required|string',
            'status' => 'required|string',
        ]);

        $attendance = $user->attendances()->create($data);

        return ResponseFormatter::success(new AttendanceResource($attendance), 'Attendance created successfully.');
    }

    // simpan checkout
    public function checkOut(Request $request, Attendance $attendance)
    {
        // Pastikan pengguna yang login adalah pemilik absensi ini
        if ($attendance->user_id !== Auth::id()) {
            return ResponseFormatter::error('Unauthorized.', 403);
        }

        $data = $request->validate([
            'check_out_time' => 'required|date',
            'check_out_location' => 'required|string',
        ]);

        $attendance->update($data);

        return ResponseFormatter::success(new AttendanceResource($attendance), 'Attendance updated successfully.');
    }

    // POST Leave Request
    public function createLeaveRequest(Request $request){
        $user = Auth::user();

        $data = $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'reason' => 'required|string',
        ]);

        $leaveRequest = $user->leaveRequests()->create($data);

        return ResponseFormatter::success(new LeaveRequestResource($leaveRequest), 'Leave request created successfully.');
    }
}
