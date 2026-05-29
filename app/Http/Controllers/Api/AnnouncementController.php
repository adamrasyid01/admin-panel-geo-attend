<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreAnnouncementRequest;
use App\Http\Requests\Api\UpdateAnnouncementRequest;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $announcements = Announcement::all();
            return ResponseFormatter::success(AnnouncementResource::collection($announcements), 'Announcements retrieved successfully.');

        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAnnouncementRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = Auth::id();
            $data['is_published'] = $request->boolean('is_published', false);
            $data['attachment_path'] = $request->hasFile('attachment_path')
                ? $request->file('attachment_path')->store('announcements', 'public')
                : '';

            $announcement = Announcement::create($data);

            return ResponseFormatter::success(new AnnouncementResource($announcement), 'Announcement created successfully.');
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
            $announcement = Announcement::findOrFail($id);

            return ResponseFormatter::success(new AnnouncementResource($announcement), 'Announcement retrieved successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseFormatter::error('Announcement not found.', 404);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAnnouncementRequest $request, string $id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            $data = $request->validated();

            if ($request->has('is_published')) {
                $data['is_published'] = $request->boolean('is_published');
            }

            if ($request->hasFile('attachment_path')) {
                if ($announcement->attachment_path) {
                    Storage::disk('public')->delete($announcement->attachment_path);
                }

                $data['attachment_path'] = $request->file('attachment_path')->store('announcements', 'public');
            }

            $announcement->update($data);

            return ResponseFormatter::success(new AnnouncementResource($announcement), 'Announcement updated successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseFormatter::error('Announcement not found.', 404);
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
            $announcement = Announcement::findOrFail($id);
            $announcement->delete();

            return ResponseFormatter::success(null, 'Announcement deleted successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseFormatter::error('Announcement not found.', 404);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
