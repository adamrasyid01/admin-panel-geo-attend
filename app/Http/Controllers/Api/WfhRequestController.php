<?php

namespace App\Http\Controllers\Api;


use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\WfhRequestResource;
use App\Models\WfhRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WfhRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $data = $request->validate([
            'tanggal' => 'required|date',
            'reason' => 'nullable|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);
        $data['status'] = 'pending';
        $data['user_id'] = Auth::user()->id;

        try {
            $wfhRequest = WfhRequest::create($data);
            return ResponseFormatter::success(new WfhRequestResource($wfhRequest), 'Work from home request created successfully.', 201);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 'Failed to create work from home request.', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
