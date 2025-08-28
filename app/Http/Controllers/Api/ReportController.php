<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    //Dapatkan laporan dari user yang login
    public function getYourReport(Request $request){
        $user = Auth::user();

        
        if($request->has('status')) {
            $reports = $user->leaveRequests()->where('status', $request->input('status'))->get();
        } else {
            $reports = $user->leaveRequests()->get();
        }
        return ResponseFormatter::success($reports, 'Your report retrieved successfully.');
    }

    // QUERY PARAMS UNTUK FILTER TYPE
    
    // QUERY PARAMS UNTUK SEMUA,PENDING

}
