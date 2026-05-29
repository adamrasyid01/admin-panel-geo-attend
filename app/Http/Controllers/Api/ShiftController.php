<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreShiftRequest;
use App\Http\Requests\Api\UpdateShiftRequest;
use App\Http\Resources\Api\ShiftResource;
use App\Models\Shift;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $shifts = $user->can('view_any_shift')
                ? Shift::latest()->get()
                : $user->userShifts()
                    ->with('shift')
                    ->latest()
                    ->get()
                    ->pluck('shift')
                    ->filter()
                    ->values();

            return ResponseFormatter::success(ShiftResource::collection($shifts), 'Shifts retrieved successfully.');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShiftRequest $request)
    {
        try {
            if (!Auth::user()->can('create_shift')) {
                return ResponseFormatter::error('Unauthorized.', 403);
            }

            $shift = Shift::create($request->validated());

            return ResponseFormatter::success(new ShiftResource($shift), 'Shift created successfully.');
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
            $shift = Shift::findOrFail($id);

            if (!$this->canAccessShift($shift, 'view_shift')) {
                return ResponseFormatter::error('Unauthorized.', 403);
            }

            return ResponseFormatter::success(new ShiftResource($shift), 'Shift retrieved successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseFormatter::error('Shift not found.', 404);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShiftRequest $request, string $id)
    {
        try {
            $shift = Shift::findOrFail($id);

            if (!Auth::user()->can('update_shift')) {
                return ResponseFormatter::error('Unauthorized.', 403);
            }

            $shift->update($request->validated());

            return ResponseFormatter::success(new ShiftResource($shift), 'Shift updated successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseFormatter::error('Shift not found.', 404);
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
            $shift = Shift::findOrFail($id);

            if (!Auth::user()->can('delete_shift')) {
                return ResponseFormatter::error('Unauthorized.', 403);
            }

            $shift->delete();

            return ResponseFormatter::success(null, 'Shift deleted successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseFormatter::error('Shift not found.', 404);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    private function canAccessShift(Shift $shift, string $permission): bool
    {
        $user = Auth::user();

        return $user->can($permission)
            || $user->userShifts()->where('shift_id', $shift->id)->exists();
    }
}
