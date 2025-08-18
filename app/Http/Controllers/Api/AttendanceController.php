<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\AttendanceResource;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    //
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseFormatter::error('Unauthenticated.', 401);
        }

        // Ambil hanya data absensi milik pengguna yang login
        $attendances = $user->attendances()->with('userShift')->get();

        return AttendanceResource::collection($attendances);
    }

    // Tampilkan detail absensi tunggal
    public function show(Attendance $attendance)
    {
        // Pastikan relasi di-load sebelum dikirim
        $attendance->load(['user', 'userShift']);

        return new AttendanceResource($attendance);
    }

    // Simpan check-in baru
    public function checkIn(Request $request)
    {
        // Logika validasi dan penyimpanan data absensi dari Flutter
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'user_shift_id' => 'required|exists:user_shifts,id',
            'photo' => 'required|string',
            'check_in_time' => 'required|date',
            'check_in_location' => 'required|string',
            'status' => 'required|string',
        ]);

        $attendance = Attendance::create($data);

        return ResponseFormatter::success(new AttendanceResource($attendance), 'Attendance created successfully.');
    }

    // simpan checkout
    public function checkOut(Request $request, Attendance $attendance)
    {
        $data = $request->validate([
            'check_out_time' => 'required|date',
            'check_out_location' => 'required|string',
        ]);

        $attendance->update($data);

        return ResponseFormatter::success(new AttendanceResource($attendance), 'Attendance updated successfully.');
    }

}
