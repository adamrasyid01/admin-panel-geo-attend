<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreOvertimeRequest;
use App\Http\Requests\Api\UpdateOvertimeRequest;
use App\Http\Resources\Api\OvertimeRequestResource;
use App\Models\OvertimeRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class OvertimeRequestController extends Controller
{
    // Menampilkan daftar pengajuan lembur milik user login atau semua pengajuan untuk admin.
    public function index()
    {
        try {
            $user = Auth::user();
            $overtimeRequests = $this->hasOvertimePermission('view_any_overtime::request')
                ? OvertimeRequest::latest()->get()
                : $user->overtimeRequests()->latest()->get();

            return ResponseFormatter::success(OvertimeRequestResource::collection($overtimeRequests), 'Overtime requests retrieved successfully.');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    // Membuat pengajuan lembur baru dengan status awal pending.
    public function store(StoreOvertimeRequest $request)
    {
        try {
            $data = $request->validated();
            $data['status'] = 'pending';
            $data['user_id'] = Auth::id();

            $overtimeRequest = OvertimeRequest::create($data);

            return ResponseFormatter::success(new OvertimeRequestResource($overtimeRequest), 'Overtime request created successfully.');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    // Menjaga kompatibilitas dengan route atau client lama yang memakai nama method createOvertimeRequest.
    public function createOvertimeRequest(StoreOvertimeRequest $request)
    {
        return $this->store($request);
    }

    // Menampilkan detail pengajuan lembur jika user login adalah pemilik atau admin.
    public function show(string $id)
    {
        try {
            $overtimeRequest = OvertimeRequest::findOrFail($id);

            if (!$this->canAccessOvertimeRequest($overtimeRequest, 'view_overtime::request')) {
                return ResponseFormatter::error('Unauthorized.', 403);
            }

            return ResponseFormatter::success(new OvertimeRequestResource($overtimeRequest), 'Overtime request retrieved successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseFormatter::error('Overtime request not found.', 404);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    // Mengubah pengajuan lembur; user biasa mengubah data sendiri saat pending, admin dapat mengubah status.
    public function update(UpdateOvertimeRequest $request, string $id)
    {
        try {
            $overtimeRequest = OvertimeRequest::findOrFail($id);
            $isAdmin = $this->hasOvertimePermission('update_overtime::request');
            $isOwner = $overtimeRequest->user_id === Auth::id();

            if (!$isAdmin && !$isOwner) {
                return ResponseFormatter::error('Unauthorized.', 403);
            }

            if (!$isAdmin && $overtimeRequest->status !== 'pending') {
                return ResponseFormatter::error('Only pending requests can be updated.', 403);
            }

            $data = $request->validated();

            if ($isAdmin && $request->has('status')) {
                $data['approved_by'] = $request->input('status') === 'approved' ? Auth::id() : null;
            }

            $overtimeRequest->update($data);

            return ResponseFormatter::success(new OvertimeRequestResource($overtimeRequest), 'Overtime request updated successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseFormatter::error('Overtime request not found.', 404);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    // Menghapus pengajuan lembur jika user login adalah pemilik atau admin.
    public function destroy(string $id)
    {
        try {
            $overtimeRequest = OvertimeRequest::findOrFail($id);

            if (!$this->canAccessOvertimeRequest($overtimeRequest, 'delete_overtime::request')) {
                return ResponseFormatter::error('Unauthorized.', 403);
            }

            $overtimeRequest->delete();

            return ResponseFormatter::success(null, 'Overtime request deleted successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseFormatter::error('Overtime request not found.', 404);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    // Mengecek apakah user login bisa mengakses pengajuan lembur tertentu.
    private function canAccessOvertimeRequest(OvertimeRequest $overtimeRequest, string $permission): bool
    {
        return $this->hasOvertimePermission($permission) || $overtimeRequest->user_id === Auth::id();
    }

    // Mengecek permission overtime, dengan fallback role super_admin.
    private function hasOvertimePermission(string $permission): bool
    {
        $user = Auth::user();

        return $user->can($permission) || $user->hasRole('super_admin');
    }
}
