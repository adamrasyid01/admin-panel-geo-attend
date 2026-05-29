<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreLeaveRequest;
use App\Http\Requests\Api\UpdateLeaveRequest;
use App\Http\Resources\Api\LeaveRequestResource;
use App\Models\LeaveRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $leaveRequests = $user->can('view_any_leave::request')
                ? LeaveRequest::latest()->get()
                : $user->leaveRequests()->latest()->get();

            return ResponseFormatter::success(LeaveRequestResource::collection($leaveRequests), 'Leave requests retrieved successfully.');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLeaveRequest $request)
    {
        try{
            $data = $request->validated();
            $data['user_id'] = Auth::id();
            $data['status'] = 'pending';
            $data['attachment'] = $request->hasFile('attachment')
                ? $request->file('attachment')->store('leave-requests', 'public')
                : null;

            $leaveRequest = LeaveRequest::create($data);

            return ResponseFormatter::success(new LeaveRequestResource($leaveRequest), 'Leave request created successfully.');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $leaveRequest = LeaveRequest::findOrFail($id);

            if (!$this->canAccessLeaveRequest($leaveRequest, 'view_leave::request')) {
                return ResponseFormatter::error('Unauthorized.', 403);
            }

            return ResponseFormatter::success(new LeaveRequestResource($leaveRequest), 'Leave request retrieved successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseFormatter::error('Leave request not found.', 404);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeaveRequest $request, string $id)
    {
        try {
            $leaveRequest = LeaveRequest::findOrFail($id);

            if (!$this->canAccessLeaveRequest($leaveRequest, 'update_leave::request')) {
                return ResponseFormatter::error('Unauthorized.', 403);
            }

            $data = $request->validated();

            if ($request->hasFile('attachment')) {
                if ($leaveRequest->attachment) {
                    Storage::disk('public')->delete($leaveRequest->attachment);
                }

                $data['attachment'] = $request->file('attachment')->store('leave-requests', 'public');
            }

            if ($request->has('status')) {
                $data['approved_by'] = $request->input('status') === 'approved' ? Auth::id() : null;
            }

            $leaveRequest->update($data);

            return ResponseFormatter::success(new LeaveRequestResource($leaveRequest), 'Leave request updated successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseFormatter::error('Leave request not found.', 404);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $leaveRequest = LeaveRequest::findOrFail($id);

            if (!$this->canAccessLeaveRequest($leaveRequest, 'delete_leave::request')) {
                return ResponseFormatter::error('Unauthorized.', 403);
            }

            $leaveRequest->delete();

            return ResponseFormatter::success(null, 'Leave request deleted successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseFormatter::error('Leave request not found.', 404);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    private function canAccessLeaveRequest(LeaveRequest $leaveRequest, string $permission): bool
    {
        $user = Auth::user();

        return $leaveRequest->user_id === $user->id || $user->can($permission);
    }
}
