<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreNoteRequest;
use App\Http\Requests\Api\UpdateNoteRequest;
use App\Http\Resources\Api\NoteResource;
use App\Models\Note;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $notes = Auth::user()->notes()->latest()->get();

            return ResponseFormatter::success(NoteResource::collection($notes), 'Notes retrieved successfully.');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNoteRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = Auth::id();

            $note = Note::create($data);

            return ResponseFormatter::success(new NoteResource($note), 'Note created successfully.');
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
            $note = Note::where('user_id', Auth::id())->findOrFail($id);

            return ResponseFormatter::success(new NoteResource($note), 'Note retrieved successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseFormatter::error('Note not found.', 404);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNoteRequest $request, string $id)
    {
        try {
            $note = Note::where('user_id', Auth::id())->findOrFail($id);
            $data = $request->validated();

            $note->update($data);

            return ResponseFormatter::success(new NoteResource($note), 'Note updated successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseFormatter::error('Note not found.', 404);
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
            $note = Note::where('user_id', Auth::id())->findOrFail($id);
            $note->delete();

            return ResponseFormatter::success(null, 'Note deleted successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseFormatter::error('Note not found.', 404);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
