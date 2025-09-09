<?php

namespace App\Http\Controllers\Api;

use App\Filament\Resources\ShiftResource;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        try {
            $shifts = Shift::where('user_id', $user->id)->get();
            return ResponseFormatter::success(ShiftResource::collection($shifts), 'Shifts retrieved successfully.');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 'Failed to retrieve shifts.', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */

    public function show(Shift $shift)
    {
        $user = Auth::user();

        try {
            if ($shift->user_id !== $user->id) {
                return ResponseFormatter::error('Unauthorized access to this shift.', 403);
            }
            
            return ResponseFormatter::success(new ShiftResource($shift), 'Shift retrieved successfully.');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 'Failed to retrieve shift.', 500);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
