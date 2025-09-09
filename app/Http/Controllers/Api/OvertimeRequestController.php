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
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'nullable|string',
        ]);
        $data['status'] = 'pending';
        $data['user_id'] = $user->id;

        try{
            $overtimeRequest = $user->overtimeRequests()->create($data);
            return ResponseFormatter::success(new OvertimeRequestResource($overtimeRequest), 'Overtime request created successfully.', 201);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 'Failed to create overtime request.', 500);
        }
       
    }
}
