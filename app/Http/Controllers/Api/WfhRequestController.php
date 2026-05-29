<?php

namespace App\Http\Controllers\Api;


use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreWfhRequest;
use App\Http\Requests\Api\UpdateWfhRequest;
use App\Http\Resources\Api\WfhRequestResource;
use App\Models\WfhRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class WfhRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Menampilkan daftar pengajuan WFH milik user login atau semua pengajuan untuk admin.
    public function index()
    {
        try {
            $user = Auth::user();
            $wfhRequests = $this->hasWfhPermission('view_any_wfh::request')
                ? WfhRequest::latest()->get()
                : $user->wfhRequests()->latest()->get();

            return ResponseFormatter::success(WfhRequestResource::collection($wfhRequests), 'Work from home requests retrieved successfully.');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // Membuat pengajuan WFH baru dengan status awal pending.
    public function store(StoreWfhRequest $request)
    {
        try {
            $data = $request->validated();
            $data['status'] = 'pending';
            $data['user_id'] = Auth::id();

            $wfhRequest = WfhRequest::create($data);

            return ResponseFormatter::success(new WfhRequestResource($wfhRequest), 'Work from home request created successfully.');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    // Menampilkan detail pengajuan WFH jika user login adalah pemilik atau admin.
    public function show(string $id)
    {
        try {
            $wfhRequest = WfhRequest::findOrFail($id);

            if (!$this->canAccessWfhRequest($wfhRequest, 'view_wfh::request')) {
                return ResponseFormatter::error('Unauthorized.', 403);
            }

            return ResponseFormatter::success(new WfhRequestResource($wfhRequest), 'Work from home request retrieved successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseFormatter::error('Work from home request not found.', 404);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    // Mengubah pengajuan WFH; user biasa mengubah data sendiri, admin dapat mengubah status dan catatan.
    public function update(UpdateWfhRequest $request, string $id)
    {
        try {
            $wfhRequest = WfhRequest::findOrFail($id);
            $isAdmin = $this->hasWfhPermission('update_wfh::request');
            $isOwner = $wfhRequest->user_id === Auth::id();

            if (!$isAdmin && !$isOwner) {
                return ResponseFormatter::error('Unauthorized.', 403);
            }

            if (!$isAdmin && $wfhRequest->status !== 'pending') {
                return ResponseFormatter::error('Only pending requests can be updated.', 403);
            }

            $data = $request->validated();

            if ($isAdmin && $request->has('status')) {
                $data['approved_by'] = $request->input('status') === 'approved' ? Auth::id() : null;
            }

            if ($isAdmin && $request->has('admin_notes')) {
                $data['notes_by'] = filled($request->input('admin_notes')) ? Auth::id() : null;
            }

            $wfhRequest->update($data);

            return ResponseFormatter::success(new WfhRequestResource($wfhRequest), 'Work from home request updated successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseFormatter::error('Work from home request not found.', 404);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    // Menghapus pengajuan WFH jika user login adalah pemilik atau admin.
    public function destroy(string $id)
    {
        try {
            $wfhRequest = WfhRequest::findOrFail($id);

            if (!$this->canAccessWfhRequest($wfhRequest, 'delete_wfh::request')) {
                return ResponseFormatter::error('Unauthorized.', 403);
            }

            $wfhRequest->delete();

            return ResponseFormatter::success(null, 'Work from home request deleted successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseFormatter::error('Work from home request not found.', 404);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    // Mengecek apakah user login bisa mengakses pengajuan WFH tertentu.
    private function canAccessWfhRequest(WfhRequest $wfhRequest, string $permission): bool
    {
        return $this->hasWfhPermission($permission) || $wfhRequest->user_id === Auth::id();
    }

    // Mengecek permission WFH, dengan fallback role super_admin.
    private function hasWfhPermission(string $permission): bool
    {
        $user = Auth::user();

        return $user->can($permission) || $user->hasRole('super_admin');
    }
}
