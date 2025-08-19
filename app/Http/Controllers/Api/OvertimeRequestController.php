<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\OvertimeRequestResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OvertimeRequestController extends Controller
{
    //CREATE
    public function createOvertimeRequest(Request $request){
        $user = Auth::user();
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'nullable|string',
        ]);

        $overtimeRequest = $user->overtimeRequests()->create($data);

        return ResponseFormatter::success(new OvertimeRequestResource($overtimeRequest), 'Overtime request created successfully.');
    }
}
