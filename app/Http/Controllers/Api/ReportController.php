<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    //Dapatkan laporan dari user yang login
    public function getYourReport(){
        $user = Auth::user();
        $reports = $user->leaveRequests()->get();
        return ResponseFormatter::success($reports, 'Your report retrieved successfully.');
    }
}
