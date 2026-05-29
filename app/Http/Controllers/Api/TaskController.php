<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreTaskRequest;
use App\Http\Requests\Api\UpdateTaskRequest;
use App\Http\Resources\Api\TaskResource;
use App\Models\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Menampilkan daftar tugas milik user login atau semua tugas untuk user yang punya akses.
    public function index()
    {
        try {
            $user = Auth::user();
            $tasks = $this->hasTaskPermission('view_any_task')
                ? Task::latest()->get()
                : Task::where('user_id', $user->id)
                    ->orWhere('created_by', $user->id)
                    ->latest()
                    ->get();

            return ResponseFormatter::success(TaskResource::collection($tasks), 'Tasks retrieved successfully.');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // Membuat tugas baru dan mencatat user login sebagai pembuat tugas.
    public function store(StoreTaskRequest $request)
    {
        try {
            if (!$this->hasTaskPermission('create_task')) {
                return ResponseFormatter::error('Unauthorized.', 403);
            }

            $data = $request->validated();
            $data['created_by'] = Auth::id();
            $task = Task::create($data);

            return ResponseFormatter::success(new TaskResource($task), 'Task created successfully.');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    // Menampilkan detail tugas jika user login adalah penerima, pembuat, atau punya akses.
    public function show(string $id)
    {
        try {
            $task = Task::findOrFail($id);

            if (!$this->canAccessTask($task, 'view_task')) {
                return ResponseFormatter::error('Unauthorized.', 403);
            }

            return ResponseFormatter::success(new TaskResource($task), 'Task retrieved successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseFormatter::error('Task not found.', 404);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    // Mengubah data tugas jika user login adalah pembuat atau punya akses update.
    public function update(UpdateTaskRequest $request, string $id)
    {
        try {
            $task = Task::findOrFail($id);

            if (!$this->canAccessTask($task, 'update_task')) {
                return ResponseFormatter::error('Unauthorized.', 403);
            }

            $task->update($request->validated());

            return ResponseFormatter::success(new TaskResource($task), 'Task updated successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseFormatter::error('Task not found.', 404);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    // Menghapus tugas jika user login adalah pembuat atau punya akses delete.
    public function destroy(string $id)
    {
        try {
            $task = Task::findOrFail($id);

            if (!$this->canAccessTask($task, 'delete_task')) {
                return ResponseFormatter::error('Unauthorized.', 403);
            }

            $task->delete();

            return ResponseFormatter::success(null, 'Task deleted successfully.');
        } catch (ModelNotFoundException $e) {
            return ResponseFormatter::error('Task not found.', 404);
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    // Mengecek apakah user login bisa mengakses tugas tertentu.
    private function canAccessTask(Task $task, string $permission): bool
    {
        $user = Auth::user();

        return $this->hasTaskPermission($permission)
            || $task->user_id === $user->id
            || $task->created_by === $user->id;
    }

    // Mengecek permission task, dengan fallback role super_admin.
    private function hasTaskPermission(string $permission): bool
    {
        $user = Auth::user();

        return $user->can($permission) || $user->hasRole('super_admin');
    }
}
